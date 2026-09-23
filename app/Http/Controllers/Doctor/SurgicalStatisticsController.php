<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surgery;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * SurgicalStatisticsController
 *
 * Provides comprehensive surgical analytics for the Anesthesia Audit System.
 * All statistics are derived from real database records.
 * Forecasting uses simple linear regression (least squares) — no external packages.
 */
class SurgicalStatisticsController extends Controller
{
    // ASA grades considered high-risk (ASA III and above, including Emergency modifier)
    private const HIGH_RISK_GRADES = ['III', 'IV', 'V', 'VI', 'E'];

    // Minimum months of historical data required before generating a forecast
    private const FORECAST_MIN_MONTHS = 3;

    // Number of future months to forecast
    private const FORECAST_MONTHS = 3;

    public function index(Request $request)
    {
        // ─── 1. PARSE FILTERS ────────────────────────────────────────────────
        $dateFrom   = $request->input('date_from');
        $dateTo     = $request->input('date_to');
        $period     = $request->input('period');
        $specialty  = $request->input('specialty', 'all');
        $caseType   = $request->input('case_type', 'all');   // all | Em | Elect
        $asaFilter  = $request->input('asa_grade', $request->input('asa', 'all'));   // all | I | II | III | IV | V | VI | E

        // Default: last 12 months if no date range or period preset given
        $defaultFrom = Carbon::now()->subMonths(11)->startOfMonth()->toDateString();
        $defaultTo   = Carbon::now()->endOfMonth()->toDateString();

        if ($period && !$dateFrom && !$dateTo) {
            $now = Carbon::now();
            switch ($period) {
                case '30d':
                    $filterFrom = $now->copy()->subDays(30)->toDateString();
                    $filterTo   = $now->toDateString();
                    break;
                case '90d':
                    $filterFrom = $now->copy()->subDays(90)->toDateString();
                    $filterTo   = $now->toDateString();
                    break;
                case '6m':
                    $filterFrom = $now->copy()->subMonths(5)->startOfMonth()->toDateString();
                    $filterTo   = $now->endOfMonth()->toDateString();
                    break;
                case '1y':
                default:
                    $filterFrom = $defaultFrom;
                    $filterTo   = $defaultTo;
                    break;
            }
        } else {
            $filterFrom = $dateFrom ?: $defaultFrom;
            $filterTo   = $dateTo   ?: $defaultTo;
        }

        // Defensive guard: if start date is after end date, swap them
        if (Carbon::parse($filterFrom)->gt(Carbon::parse($filterTo))) {
            [$filterFrom, $filterTo] = [$filterTo, $filterFrom];
        }

        // ─── 2. BUILD BASE QUERY ─────────────────────────────────────────────
        $baseQuery = Surgery::query()
            ->whereNotNull('date_of_surgery')
            ->whereDate('date_of_surgery', '>=', $filterFrom)
            ->whereDate('date_of_surgery', '<=', $filterTo);

        if ($specialty !== 'all') {
            $baseQuery->where('surgical_specialty', $specialty);
        }

        if ($caseType !== 'all') {
            $baseQuery->where('surgery_type', $caseType);
        }

        if ($asaFilter !== 'all') {
            $baseQuery->where('asa_grade', $asaFilter);
        }

        // ─── 3. OVERVIEW STATISTICS ──────────────────────────────────────────
        $totalSurgeries = (clone $baseQuery)->count();

        // When the user has already filtered to a single case type, querying the
        // opposite type against the same base query would always return 0, which
        // is correct but the cards need context flags so the view can relabel them.
        $caseTypeFilterActive = $caseType !== 'all';

        // Always query unfiltered by type against base (which may already restrict
        // by type), so numbers are always consistent with $totalSurgeries.
        $emergencyCount = (clone $baseQuery)->where('surgery_type', 'Em')->count();
        $electiveCount  = (clone $baseQuery)->where('surgery_type', 'Elect')->count();
        $highRiskCount  = (clone $baseQuery)->whereIn('asa_grade', self::HIGH_RISK_GRADES)->count();

        // Calculate date range in months for avg/month
        // diffInMonths truncates: Jan→Jan = 0 months; +1 gives the inclusive span.
        $rangeStart = Carbon::parse($filterFrom)->startOfMonth();
        $rangeEnd   = Carbon::parse($filterTo)->startOfMonth();
        $monthsSpan = max(1, $rangeStart->diffInMonths($rangeEnd) + 1);

        $avgPerMonth   = $monthsSpan > 0 ? round($totalSurgeries / $monthsSpan, 1) : 0;
        $emergencyRate = $totalSurgeries > 0 ? round(($emergencyCount / $totalSurgeries) * 100, 1) : 0;
        $highRiskRate  = $totalSurgeries > 0 ? round(($highRiskCount / $totalSurgeries) * 100, 1) : 0;

        // ─── 4. MONTHLY TREND & ASA METRICS (ACTUAL) ─────────────────────────
        [$trendLabels, $trendData, $trendEmergency, $trendElective, $highRiskTrend] =
            $this->buildMonthlyMetrics($filterFrom, $filterTo, $specialty, $caseType, $asaFilter);

        // ─── 5. FORECAST (LINEAR REGRESSION) ─────────────────────────────────
        // Pass $filterTo so the forecast can generate real month-name labels.
        [$forecastLabels, $forecastData, $forecastReady, $forecastMessage, $forecastSlope, $forecastRSquared, $forecastConfidence] =
            $this->buildForecast($trendData, $filterTo);

        // ─── 6. SPECIALTY DISTRIBUTION ───────────────────────────────────────
        $specialtyDistribution = (clone $baseQuery)
            ->select('surgical_specialty', DB::raw('count(*) as count'))
            ->whereNotNull('surgical_specialty')
            ->groupBy('surgical_specialty')
            ->orderBy('count', 'desc')
            ->pluck('count', 'surgical_specialty')
            ->toArray();

        // Distinct specialty list for filter dropdown (all records, no date filter)
        $allSpecialties = Surgery::select('surgical_specialty')
            ->whereNotNull('surgical_specialty')
            ->distinct()
            ->orderBy('surgical_specialty')
            ->pluck('surgical_specialty')
            ->toArray();

        // ─── 7. ASA DISTRIBUTION ──────────────────────────────────────────────
        $asaDistribution = (clone $baseQuery)
            ->select('asa_grade', DB::raw('count(*) as count'))
            ->whereNotNull('asa_grade')
            ->groupBy('asa_grade')
            ->orderBy('asa_grade')
            ->pluck('count', 'asa_grade')
            ->toArray();

        // ─── 8. PERIOD COMPARISON ────────────────────────────────────────────
        $periodComparison = $this->buildPeriodComparison($filterFrom, $filterTo, $specialty, $caseType, $asaFilter);

        // ─── 9. TOP PROCEDURES ───────────────────────────────────────────────
        $topProcedures = (clone $baseQuery)
            ->select('operation_name', DB::raw('count(*) as count'))
            ->whereNotNull('operation_name')
            ->where('operation_name', '!=', '')
            ->groupBy('operation_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        // ─── 10. COMORBIDITY (MEDICAL SYSTEMS) ──────────────────────────────
        $patientIds = (clone $baseQuery)->pluck('patient_id')->filter()->unique();
        $comorbidityData = [];
        if ($patientIds->isNotEmpty()) {
            $comorbidityData = DB::table('patient_conditions as pc')
                ->join('medical_conditions as mc', 'pc.condition_id', '=', 'mc.condition_id')
                ->join('medical_systems as ms', 'mc.system_id', '=', 'ms.system_id')
                ->whereIn('pc.patient_id', $patientIds)
                ->select('ms.system_name', DB::raw('count(distinct pc.patient_id) as count'))
                ->groupBy('ms.system_name', 'ms.system_id')
                ->orderBy('count', 'desc')
                ->limit(8)
                ->pluck('count', 'ms.system_name')
                ->toArray();
        }

        // ─── 11. AUTOMATED INSIGHTS ──────────────────────────────────────────
        $insights = $this->generateInsights(
            totalSurgeries:    $totalSurgeries,
            emergencyCount:    $emergencyCount,
            electiveCount:     $electiveCount,
            highRiskCount:     $highRiskCount,
            emergencyRate:     $emergencyRate,
            highRiskRate:      $highRiskRate,
            periodComparison:  $periodComparison,
            specialtyDist:     $specialtyDistribution,
            topProcedures:     $topProcedures,
            forecastReady:     $forecastReady,
            forecastData:      $forecastData,
            avgPerMonth:       $avgPerMonth,
            forecastSlope:     $forecastSlope,
            caseType:          $caseType
        );

        // ─── PASS TO VIEW ─────────────────────────────────────────────────────
        return view('doctor.surgical-statistics', compact(
            // Filters
            'filterFrom', 'filterTo', 'specialty', 'caseType', 'asaFilter',
            'allSpecialties', 'caseTypeFilterActive', 'period',
            // Overview
            'totalSurgeries', 'emergencyCount', 'electiveCount', 'highRiskCount',
            'avgPerMonth', 'emergencyRate', 'highRiskRate',
            // Trend chart
            'trendLabels', 'trendData', 'trendEmergency', 'trendElective',
            // Forecast
            'forecastLabels', 'forecastData', 'forecastReady', 'forecastMessage',
            'forecastSlope', 'forecastRSquared', 'forecastConfidence',
            // Distributions
            'specialtyDistribution', 'asaDistribution', 'highRiskTrend',
            // Period comparison
            'periodComparison',
            // Top procedures
            'topProcedures',
            // Comorbidity
            'comorbidityData',
            // Insights
            'insights',
            // Derived
            'monthsSpan'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Build monthly metric arrays for: total, emergency, elective, and ASA-specific counts.
     * Evaluates in a single database query per month, ensuring perfect synchrony and 50% fewer queries.
     * Returns [labels[], total[], emergency[], elective[], asaTrend[]]
     */
    private function buildMonthlyMetrics(string $from, string $to, string $specialty, string $caseType, string $asaFilter): array
    {
        $labels    = [];
        $total     = [];
        $emergency = [];
        $elective  = [];
        $asaTrend  = [];

        $current = Carbon::parse($from)->startOfMonth();
        $end     = Carbon::parse($to)->endOfMonth();

        while ($current->lte($end)) {
            $labels[] = $current->format('M Y');

            $monthStart = $current->copy()->startOfMonth()->max(Carbon::parse($from));
            $monthEnd   = $current->copy()->endOfMonth()->min(Carbon::parse($to));

            $monthBase = Surgery::query()
                ->whereNotNull('date_of_surgery')
                ->whereDate('date_of_surgery', '>=', $monthStart->toDateString())
                ->whereDate('date_of_surgery', '<=', $monthEnd->toDateString());

            if ($specialty !== 'all') {
                $monthBase->where('surgical_specialty', $specialty);
            }
            if ($caseType !== 'all') {
                $monthBase->where('surgery_type', $caseType);
            }

            // If user has filtered to a specific ASA grade, filter base query by it
            if ($asaFilter !== 'all') {
                $monthBase->where('asa_grade', $asaFilter);
            }

            $stats = $monthBase->selectRaw('
                COUNT(*) as total_count,
                SUM(CASE WHEN surgery_type = "Em" THEN 1 ELSE 0 END) as emergency_count,
                SUM(CASE WHEN surgery_type = "Elect" THEN 1 ELSE 0 END) as elective_count,
                SUM(CASE WHEN asa_grade IN ("III", "IV", "V", "VI", "E") THEN 1 ELSE 0 END) as high_risk_count
            ')->first();

            $totalCount  = (int) ($stats->total_count ?? 0);
            $total[]     = $totalCount;
            $emergency[] = ($caseType === 'all' || $caseType === 'Em') ? (int) ($stats->emergency_count ?? 0) : 0;
            $elective[]  = ($caseType === 'all' || $caseType === 'Elect') ? (int) ($stats->elective_count ?? 0) : 0;

            // When user filtered to a specific ASA grade, the trend reflects that grade's volume ($totalCount).
            // When user did not filter by ASA grade ('all'), the trend reflects High-Risk volume.
            if ($asaFilter !== 'all') {
                $asaTrend[] = $totalCount;
            } else {
                $asaTrend[] = (int) ($stats->high_risk_count ?? 0);
            }

            $current->addMonth();
        }

        return [$labels, $total, $emergency, $elective, $asaTrend];
    }

    /**
     * Period comparison: compares current filter range against the
     * immediately preceding equivalent period.
     *
     * Returns array with keys: current_*, previous_*, diff_*, pct_*
     */
    private function buildPeriodComparison(string $from, string $to, string $specialty, string $caseType, string $asaFilter): array
    {
        $currentStart = Carbon::parse($from);
        $currentEnd   = Carbon::parse($to);
        $durationDays = $currentStart->diffInDays($currentEnd) + 1;

        // Previous period = immediately before current period, same duration
        $prevEnd   = $currentStart->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($durationDays - 1);

        $currentQ = Surgery::query()
            ->whereNotNull('date_of_surgery')
            ->whereDate('date_of_surgery', '>=', $currentStart->toDateString())
            ->whereDate('date_of_surgery', '<=', $currentEnd->toDateString());

        $prevQ = Surgery::query()
            ->whereNotNull('date_of_surgery')
            ->whereDate('date_of_surgery', '>=', $prevStart->toDateString())
            ->whereDate('date_of_surgery', '<=', $prevEnd->toDateString());

        foreach ([$currentQ, $prevQ] as $q) {
            if ($specialty !== 'all') {
                $q->where('surgical_specialty', $specialty);
            }
            if ($caseType !== 'all') {
                $q->where('surgery_type', $caseType);
            }
            if ($asaFilter !== 'all') {
                $q->where('asa_grade', $asaFilter);
            }
        }

        $curTotal    = (clone $currentQ)->count();
        $curEmerg    = (clone $currentQ)->where('surgery_type', 'Em')->count();
        $curElect    = (clone $currentQ)->where('surgery_type', 'Elect')->count();
        $curHR       = (clone $currentQ)->whereIn('asa_grade', self::HIGH_RISK_GRADES)->count();

        $prevTotal   = (clone $prevQ)->count();
        $prevEmerg   = (clone $prevQ)->where('surgery_type', 'Em')->count();
        $prevElect   = (clone $prevQ)->where('surgery_type', 'Elect')->count();
        $prevHR      = (clone $prevQ)->whereIn('asa_grade', self::HIGH_RISK_GRADES)->count();

        return [
            'current_period_label'  => $currentStart->format('d M Y') . ' – ' . $currentEnd->format('d M Y'),
            'previous_period_label' => $prevStart->format('d M Y') . ' – ' . $prevEnd->format('d M Y'),
            // Total
            'current_total'   => $curTotal,
            'previous_total'  => $prevTotal,
            'diff_total'      => $curTotal - $prevTotal,
            'pct_total'       => $prevTotal > 0 ? round((($curTotal - $prevTotal) / $prevTotal) * 100, 1) : null,
            // Emergency
            'current_emerg'   => $curEmerg,
            'previous_emerg'  => $prevEmerg,
            'diff_emerg'      => $curEmerg - $prevEmerg,
            'pct_emerg'       => $prevEmerg > 0 ? round((($curEmerg - $prevEmerg) / $prevEmerg) * 100, 1) : null,
            // Elective
            'current_elect'   => $curElect,
            'previous_elect'  => $prevElect,
            'diff_elect'      => $curElect - $prevElect,
            'pct_elect'       => $prevElect > 0 ? round((($curElect - $prevElect) / $prevElect) * 100, 1) : null,
            // High-risk
            'current_hr'      => $curHR,
            'previous_hr'     => $prevHR,
            'diff_hr'         => $curHR - $prevHR,
            'pct_hr'          => $prevHR > 0 ? round((($curHR - $prevHR) / $prevHR) * 100, 1) : null,
        ];
    }

    /**
     * Linear Regression Forecast (Least Squares).
     *
     * Given an array of historical monthly counts and the end-date of the
     * filter period, estimates the next FORECAST_MONTHS months.
     * Evaluates slope (m), intercept (b), coefficient of determination (R²),
     * and statistical confidence based on sample size and variance.
     *
     * Returns [labels[], values[], ready, message, slope, rSquared, confidence].
     *
     * Method:
     *   x = month index (0-based), y = count
     *   m = (n·Σxy − Σx·Σy) / (n·Σx² − (Σx)²)
     *   b = (Σy − m·Σx) / n
     *   y_pred = m·x + b  (clamped to ≥ 0, bounded by clinical ceiling, rounded to int)
     *   R² = 1 − (SS_res / SS_tot)
     *
     * @param  array  $historicalCounts  Monthly totals in chronological order.
     * @param  string $filterTo          The end date of the selected period (to derive real month names).
     */
    private function buildForecast(array $historicalCounts, string $filterTo): array
    {
        $n = count($historicalCounts);

        if ($n < self::FORECAST_MIN_MONTHS) {
            return [[], [], false, 'Not enough historical data to generate a forecast (minimum ' . self::FORECAST_MIN_MONTHS . ' months required).', null, null, ''];
        }

        // Require at least FORECAST_MIN_MONTHS months with non-zero counts
        $nonZero = count(array_filter($historicalCounts, fn($v) => $v > 0));
        if ($nonZero < self::FORECAST_MIN_MONTHS) {
            return [[], [], false, 'Not enough non-zero monthly data to generate a reliable forecast.', null, null, ''];
        }

        // Compute sums for least squares
        $sumX  = 0;
        $sumY  = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $i;
            $y = $historicalCounts[$i];
            $sumX  += $x;
            $sumY  += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $denominator = ($n * $sumX2) - ($sumX * $sumX);

        if ($denominator == 0) {
            return [[], [], false, 'Insufficient variation in historical data to compute a forecast.', null, null, ''];
        }

        $m = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $b = ($sumY - ($m * $sumX)) / $n;

        // Calculate R² (coefficient of determination)
        $yMean = $sumY / $n;
        $ssTot = 0.0;
        $ssRes = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $yFit = ($m * $i) + $b;
            $ssTot += pow($historicalCounts[$i] - $yMean, 2);
            $ssRes += pow($historicalCounts[$i] - $yFit, 2);
        }
        $rSquared = $ssTot > 0 ? max(0.0, min(1.0, round(1 - ($ssRes / $ssTot), 2))) : 1.0;

        // Statistical confidence characterization
        if ($n >= 6 && $rSquared >= 0.5) {
            $confidence = 'Moderate–High statistical confidence (n = ' . $n . ' months, R² = ' . $rSquared . ')';
        } elseif ($n >= 6) {
            $confidence = 'Moderate confidence — variable monthly volume (n = ' . $n . ' months, R² = ' . $rSquared . ')';
        } else {
            $confidence = 'Preliminary estimate — short historical horizon (n = ' . $n . ' months, R² = ' . $rSquared . ')';
        }

        $slope = round($m, 1);

        // The last month in trendLabels corresponds to the month containing $filterTo.
        $lastMonthInRange = Carbon::parse($filterTo)->startOfMonth();

        $labels = [];
        $values = [];
        $maxHist = max($historicalCounts);
        // Realistic clinical ceiling: prevent statistical runaway if historical slope is steep on small n
        $ceiling = max(1, $maxHist * 4);

        for ($f = 1; $f <= self::FORECAST_MONTHS; $f++) {
            $xNext    = $n - 1 + $f;
            $yPred    = max(0, min((int) round($m * $xNext + $b), $ceiling));
            $values[] = $yPred;
            $labels[] = $lastMonthInRange->copy()->addMonths($f)->format('M Y') . ' (Est.)';
        }

        return [$labels, $values, true, '', $slope, $rSquared, $confidence];
    }

    /**
     * Generate automated rule-based insights from computed statistics.
     * Each insight is an associative array: ['icon' => '...', 'text' => '...', 'type' => 'info|warn|good|bad']
     */
    private function generateInsights(
        int    $totalSurgeries,
        int    $emergencyCount,
        int    $electiveCount,
        int    $highRiskCount,
        float  $emergencyRate,
        float  $highRiskRate,
        array  $periodComparison,
        array  $specialtyDist,
        array  $topProcedures,
        bool   $forecastReady,
        array  $forecastData,
        float  $avgPerMonth,
        ?float $forecastSlope = null,
        string $caseType = 'all'
    ): array {
        $insights = [];

        if ($totalSurgeries === 0) {
            $insights[] = ['icon' => '📋', 'text' => 'No surgical records found for the selected period and filters.', 'type' => 'info'];
            return $insights;
        }

        // 1. Volume change vs previous period
        $pctTotal = $periodComparison['pct_total'] ?? null;
        $diffTotal = $periodComparison['diff_total'] ?? 0;
        if ($pctTotal !== null) {
            if ($pctTotal > 5) {
                $insights[] = ['icon' => '📈', 'text' => 'Surgical volume increased by ' . abs($pctTotal) . '% compared with the previous equivalent period (' . ($diffTotal > 0 ? '+' : '') . $diffTotal . ' cases).', 'type' => 'good'];
            } elseif ($pctTotal < -5) {
                $insights[] = ['icon' => '📉', 'text' => 'Surgical volume decreased by ' . abs($pctTotal) . '% compared with the previous equivalent period (' . $diffTotal . ' cases).', 'type' => 'info'];
            } else {
                $insights[] = ['icon' => '➡️', 'text' => 'Surgical volume remained relatively stable compared with the previous equivalent period (change: ' . ($diffTotal >= 0 ? '+' : '') . $diffTotal . ' cases).', 'type' => 'info'];
            }
        }

        // 2. Emergency rate
        if ($caseType === 'Em') {
            $insights[] = ['icon' => '🚑', 'text' => 'Emergency filter active: displaying all ' . $emergencyCount . ' emergency cases recorded in this period.', 'type' => 'info'];
        } elseif ($caseType === 'Elect') {
            $insights[] = ['icon' => '🗓️', 'text' => 'Elective filter active: displaying all ' . $electiveCount . ' planned elective cases recorded in this period.', 'type' => 'info'];
        } elseif ($emergencyRate > 35) {
            $insights[] = ['icon' => '⚠️', 'text' => 'Emergency surgeries represent a high proportion (' . $emergencyRate . '%) of all cases in this period. This warrants operational attention.', 'type' => 'warn'];
        } elseif ($emergencyRate > 0) {
            $pctEmerg = $periodComparison['pct_emerg'] ?? null;
            $prevEmergRate = ($periodComparison['previous_total'] ?? 0) > 0
                ? round(($periodComparison['previous_emerg'] / $periodComparison['previous_total']) * 100, 1)
                : null;
            if ($pctEmerg !== null && $pctEmerg > 10 && $prevEmergRate !== null) {
                $insights[] = ['icon' => '⚠️', 'text' => 'Emergency surgeries represent ' . $emergencyRate . '% of cases this period, compared with ' . $prevEmergRate . '% in the previous period — an increase of ' . $pctEmerg . '%.', 'type' => 'warn'];
            } else {
                $insights[] = ['icon' => '🚑', 'text' => 'Emergency surgeries account for ' . $emergencyRate . '% of all cases (' . $emergencyCount . ' cases) in the selected period.', 'type' => 'info'];
            }
        }

        // 3. High-risk cases
        if ($highRiskCount > 0) {
            $pctHR = $periodComparison['pct_hr'] ?? null;
            if ($pctHR !== null && $pctHR > 10) {
                $insights[] = ['icon' => '🔴', 'text' => 'High-risk surgical cases (ASA III–VI/E) have increased by ' . $pctHR . '% compared with the previous period. Currently ' . $highRiskRate . '% of all cases are high-risk.', 'type' => 'warn'];
            } elseif ($pctHR !== null && $pctHR < -10) {
                $insights[] = ['icon' => '🟢', 'text' => 'High-risk surgical cases (ASA III–VI/E) decreased by ' . abs($pctHR) . '% compared with the previous period.', 'type' => 'good'];
            } else {
                $insights[] = ['icon' => '🏥', 'text' => 'High-risk cases (ASA III–VI/E) represent ' . $highRiskRate . '% of all cases (' . $highRiskCount . ' cases) in the selected period.', 'type' => 'info'];
            }
        }

        // 4. Dominant specialty
        if (!empty($specialtyDist)) {
            $topSpec     = array_key_first($specialtyDist);
            $topSpecCount = $specialtyDist[$topSpec];
            $topSpecPct  = $totalSurgeries > 0 ? round(($topSpecCount / $totalSurgeries) * 100, 1) : 0;
            $insights[] = ['icon' => '🔬', 'text' => $topSpec . ' accounts for the highest number of recorded procedures in this period (' . $topSpecCount . ' cases, ' . $topSpecPct . '% of total).', 'type' => 'info'];
        }

        // 5. Most common procedure
        if (!empty($topProcedures)) {
            $topProc      = $topProcedures[0]['operation_name'];
            $topProcCount = $topProcedures[0]['count'];
            $insights[] = ['icon' => '📝', 'text' => '"' . $topProc . '" is the most frequently recorded procedure in this period (' . $topProcCount . ' cases).', 'type' => 'info'];
        }

        // 6. Average surgical load
        if ($avgPerMonth > 0) {
            $insights[] = ['icon' => '📊', 'text' => 'Average surgical workload is ' . $avgPerMonth . ' cases per month over the selected period.', 'type' => 'info'];
        }

        // 7. Forecast direction & rate
        if ($forecastReady && $forecastSlope !== null) {
            if ($forecastSlope > 0.5) {
                $insights[] = ['icon' => '📈', 'text' => 'Statistical forecast (least squares) projects an upward trend of approximately +' . $forecastSlope . ' cases per month over the next ' . self::FORECAST_MONTHS . ' months.', 'type' => 'info'];
            } elseif ($forecastSlope < -0.5) {
                $insights[] = ['icon' => '📉', 'text' => 'Statistical forecast (least squares) projects a downward trend of approximately ' . $forecastSlope . ' cases per month over the next ' . self::FORECAST_MONTHS . ' months.', 'type' => 'info'];
            } else {
                $insights[] = ['icon' => '➡️', 'text' => 'Statistical forecast suggests surgical volume will remain approximately steady over the next ' . self::FORECAST_MONTHS . ' months (trend: ' . ($forecastSlope >= 0 ? '+' : '') . $forecastSlope . ' cases/mo).', 'type' => 'info'];
            }
        }

        // 8. Elective vs Emergency balance note
        if ($caseType === 'all' && $electiveCount > 0 && $emergencyCount > 0) {
            $ratio = round($electiveCount / max(1, $emergencyCount), 1);
            if ($ratio >= 3) {
                $insights[] = ['icon' => '✅', 'text' => 'Elective cases outnumber emergency cases by a ratio of ' . $ratio . ':1, indicating a predominantly planned surgical programme.', 'type' => 'good'];
            }
        }

        return $insights;
    }
}
