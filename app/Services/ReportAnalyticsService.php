<?php

namespace App\Services;

use App\Models\Surgery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Centralised analytics & reporting query logic for the doctor role.
 * All methods accept a Surgery Eloquent Builder so filters compose naturally.
 */
class ReportAnalyticsService
{
    // -------------------------------------------------------------------------
    // Base Query Builder
    // -------------------------------------------------------------------------

    /**
     * Build a filtered Surgery base query.
     *
     * @param  array       $filters     Keys: date_from, date_to, specialty, asa_grade, surgery_type
     * @param  string|null $doctorName  When set, scopes to surgeries involving this staff member
     * @return Builder
     */
    public function buildBaseQuery(array $filters = [], ?string $doctorName = null): Builder
    {
        $query = Surgery::query();

        if ($doctorName) {
            $query->whereHas('staff', function (Builder $q) use ($doctorName) {
                $q->where('name', $doctorName);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_of_surgery', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date_of_surgery', '<=', $filters['date_to']);
        }

        if (!empty($filters['specialty'])) {
            $query->where('surgical_specialty', $filters['specialty']);
        }

        if (!empty($filters['asa_grade'])) {
            $query->where('asa_grade', $filters['asa_grade']);
        }

        if (!empty($filters['surgery_type'])) {
            $query->where('surgery_type', $filters['surgery_type']);
        }

        return $query;
    }

    // -------------------------------------------------------------------------
    // Aggregate Methods
    // -------------------------------------------------------------------------

    /** Total cases, Em count, Elect count */
    public function getCaseCounts(Builder $query): array
    {
        $total = (clone $query)->count();
        $em    = (clone $query)->where('surgery_type', 'Em')->count();
        $elect = (clone $query)->where('surgery_type', 'Elect')->count();

        return [
            'total' => $total,
            'em'    => $em,
            'elect' => $elect,
        ];
    }

    /** ['Em' => n, 'Elect' => n] */
    public function getEmVsElect(Builder $query): array
    {
        return (clone $query)
            ->select('surgery_type', DB::raw('count(*) as count'))
            ->whereNotNull('surgery_type')
            ->groupBy('surgery_type')
            ->pluck('count', 'surgery_type')
            ->toArray();
    }

    /** ['Specialty Name' => count, ...] ordered desc */
    public function getSpecialtyDistribution(Builder $query): array
    {
        return (clone $query)
            ->select('surgical_specialty', DB::raw('count(*) as count'))
            ->whereNotNull('surgical_specialty')
            ->groupBy('surgical_specialty')
            ->orderBy('count', 'desc')
            ->pluck('count', 'surgical_specialty')
            ->toArray();
    }

    /** ['I' => n, 'II' => n, ...] */
    public function getAsaDistribution(Builder $query): array
    {
        return (clone $query)
            ->select('asa_grade', DB::raw('count(*) as count'))
            ->whereNotNull('asa_grade')
            ->groupBy('asa_grade')
            ->orderBy('asa_grade')
            ->pluck('count', 'asa_grade')
            ->toArray();
    }

    /** ['GA' => n, 'Regional' => n, 'GA + Regional' => n] */
    public function getAnaesthesiaUsage(Builder $query): array
    {
        $surgeryIds = (clone $query)->pluck('surgery_id');

        return DB::table('anaesthesia')
            ->whereIn('surgery_id', $surgeryIds)
            ->select('mode', DB::raw('count(*) as count'))
            ->groupBy('mode')
            ->pluck('count', 'mode')
            ->toArray();
    }

    /**
     * Adverse event breakdown.
     * Returns:
     *   overall_rate (float 0-100),
     *   by_asa  ['ASA grade' => ['total'=>n,'with_event'=>n,'rate'=>f]],
     *   by_type ['Em'|'Elect' => [...same...]],
     *   by_mode ['GA'|... => [...same...]]
     */
    public function getAdverseEventRate(Builder $query): array
    {
        $surgeryIds = (clone $query)->pluck('surgery_id');
        $total      = count($surgeryIds);

        // Surgery IDs that have at least one adverse event
        $withEventIds = DB::table('surgery_adverse_events')
            ->whereIn('surgery_id', $surgeryIds)
            ->distinct()
            ->pluck('surgery_id')
            ->toArray();

        $overallRate = $total > 0 ? round((count($withEventIds) / $total) * 100, 1) : 0;

        // By ASA grade
        $byAsa = [];
        $asaGroupsRaw = (clone $query)
            ->select('asa_grade', 'surgery_id')
            ->whereNotNull('asa_grade')
            ->get();

        $asaTotals = [];
        $asaWithEvent = [];
        foreach ($asaGroupsRaw as $row) {
            $asaTotals[$row->asa_grade] = ($asaTotals[$row->asa_grade] ?? 0) + 1;
            if (in_array($row->surgery_id, $withEventIds)) {
                $asaWithEvent[$row->asa_grade] = ($asaWithEvent[$row->asa_grade] ?? 0) + 1;
            }
        }
        foreach ($asaTotals as $grade => $tot) {
            $we = $asaWithEvent[$grade] ?? 0;
            $byAsa[$grade] = [
                'total'      => $tot,
                'with_event' => $we,
                'rate'       => $tot > 0 ? round(($we / $tot) * 100, 1) : 0,
            ];
        }

        // By surgery type
        $byType = [];
        $typeGroups = (clone $query)
            ->select('surgery_type', 'surgery_id')
            ->whereNotNull('surgery_type')
            ->get();

        $typeTotals = [];
        $typeWithEvent = [];
        foreach ($typeGroups as $row) {
            $typeTotals[$row->surgery_type] = ($typeTotals[$row->surgery_type] ?? 0) + 1;
            if (in_array($row->surgery_id, $withEventIds)) {
                $typeWithEvent[$row->surgery_type] = ($typeWithEvent[$row->surgery_type] ?? 0) + 1;
            }
        }
        foreach ($typeTotals as $type => $tot) {
            $we = $typeWithEvent[$type] ?? 0;
            $byType[$type] = [
                'total'      => $tot,
                'with_event' => $we,
                'rate'       => $tot > 0 ? round(($we / $tot) * 100, 1) : 0,
            ];
        }

        // By anaesthesia mode
        $byMode = [];
        $anaRows = DB::table('anaesthesia')
            ->whereIn('surgery_id', $surgeryIds)
            ->select('surgery_id', 'mode')
            ->whereNotNull('mode')
            ->get();

        $modeTotals = [];
        $modeWithEvent = [];
        foreach ($anaRows as $row) {
            $modeTotals[$row->mode] = ($modeTotals[$row->mode] ?? 0) + 1;
            if (in_array($row->surgery_id, $withEventIds)) {
                $modeWithEvent[$row->mode] = ($modeWithEvent[$row->mode] ?? 0) + 1;
            }
        }
        foreach ($modeTotals as $mode => $tot) {
            $we = $modeWithEvent[$mode] ?? 0;
            $byMode[$mode] = [
                'total'      => $tot,
                'with_event' => $we,
                'rate'       => $tot > 0 ? round(($we / $tot) * 100, 1) : 0,
            ];
        }

        return [
            'overall_rate' => $overallRate,
            'by_asa'       => $byAsa,
            'by_type'      => $byType,
            'by_mode'      => $byMode,
        ];
    }

    /** Percentage of cases with difficult_intubation = 1 */
    public function getDifficultIntubationRate(Builder $query): float
    {
        $surgeryIds = (clone $query)->pluck('surgery_id');
        $total      = count($surgeryIds);
        if ($total === 0) return 0.0;

        $difficult = DB::table('anaesthesia')
            ->whereIn('surgery_id', $surgeryIds)
            ->where('difficult_intubation', 1)
            ->count();

        return round(($difficult / $total) * 100, 1);
    }

    /** Percentage of cases with relaxant_used = 1 */
    public function getRelaxantUseRate(Builder $query): float
    {
        $surgeryIds = (clone $query)->pluck('surgery_id');
        $total      = count($surgeryIds);
        if ($total === 0) return 0.0;

        $relaxant = DB::table('anaesthesia')
            ->whereIn('surgery_id', $surgeryIds)
            ->where('relaxant_used', 1)
            ->count();

        return round(($relaxant / $total) * 100, 1);
    }

    /** ['Ward' => n, 'HDU' => n, 'ICU' => n] */
    public function getPostOpBreakdown(Builder $query): array
    {
        return (clone $query)
            ->select('post_op_admission', DB::raw('count(*) as count'))
            ->whereNotNull('post_op_admission')
            ->groupBy('post_op_admission')
            ->pluck('count', 'post_op_admission')
            ->toArray();
    }

    /** ['Monitoring Name' => count, ...] */
    public function getMonitoringCoverage(Builder $query): array
    {
        $surgeryIds = (clone $query)->pluck('surgery_id');

        return DB::table('surgery_monitoring as sm')
            ->join('monitoring_types as mt', 'sm.monitoring_id', '=', 'mt.monitoring_id')
            ->whereIn('sm.surgery_id', $surgeryIds)
            ->select('mt.monitoring_name', DB::raw('count(*) as count'))
            ->groupBy('mt.monitoring_name')
            ->orderBy('count', 'desc')
            ->pluck('count', 'mt.monitoring_name')
            ->toArray();
    }

    /**
     * Monthly trend data for the last N months.
     * Returns ['labels' => [...], 'data' => [...]]
     */
    public function getTrendData(Builder $query, int $months = 6): array
    {
        $labels = [];
        $data   = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month    = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[]   = (clone $query)
                ->whereYear('date_of_surgery', $month->year)
                ->whereMonth('date_of_surgery', $month->month)
                ->count();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /** Top N adverse events by frequency. Returns ['Event Name' => count, ...] */
    public function getTopAdverseEvents(Builder $query, int $limit = 10): array
    {
        $surgeryIds = (clone $query)->pluck('surgery_id');

        return DB::table('surgery_adverse_events as se')
            ->join('adverse_events as ae', 'se.event_id', '=', 'ae.event_id')
            ->whereIn('se.surgery_id', $surgeryIds)
            ->select('ae.event_name', DB::raw('count(*) as count'))
            ->groupBy('ae.event_name')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->pluck('count', 'ae.event_name')
            ->toArray();
    }

    /** Top N comorbidities by frequency. Returns ['Condition Name' => count, ...] */
    public function getTopComorbidities(Builder $query, int $limit = 10): array
    {
        $patientIds = (clone $query)->pluck('patient_id')->unique();

        return DB::table('patient_conditions as pc')
            ->join('medical_conditions as mc', 'pc.condition_id', '=', 'mc.condition_id')
            ->whereIn('pc.patient_id', $patientIds)
            ->select('mc.condition_name', DB::raw('count(*) as count'))
            ->groupBy('mc.condition_name')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->pluck('count', 'mc.condition_name')
            ->toArray();
    }

    /** All distinct specialties for use in filter dropdowns */
    public function getSpecialties(): array
    {
        return Surgery::select('surgical_specialty')
            ->whereNotNull('surgical_specialty')
            ->distinct()
            ->orderBy('surgical_specialty')
            ->pluck('surgical_specialty')
            ->toArray();
    }
}
