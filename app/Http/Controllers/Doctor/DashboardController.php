<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surgery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $doctorName = Auth::user()->name;

        // Base query: surgeries associated with the authenticated doctor
        $baseQuery = Surgery::whereHas('staff', function ($query) use ($doctorName) {
            $query->where('name', $doctorName);
        });

        // Fallback to all surgeries if doctor has no explicit assignments yet
        if ((clone $baseQuery)->count() === 0) {
            $baseQuery = Surgery::query();
        }

        // 1. KPI Metrics
        $totalSurgeries = (clone $baseQuery)->count();
        $surgeriesToday = (clone $baseQuery)->whereDate('date_of_surgery', Carbon::today())->count();
        $surgeriesThisMonth = (clone $baseQuery)
            ->whereYear('date_of_surgery', Carbon::now()->year)
            ->whereMonth('date_of_surgery', Carbon::now()->month)
            ->count();

        // 2. Surgery Trend (Last 6 Months)
        $trendLabels = [];
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $trendLabels[] = $month->format('M Y');
            $trendData[] = (clone $baseQuery)
                ->whereYear('date_of_surgery', $month->year)
                ->whereMonth('date_of_surgery', $month->month)
                ->count();
        }

        // 3. Emergency vs Elective Surgeries
        $emergencyVsElective = (clone $baseQuery)
            ->select('surgery_type', DB::raw('count(*) as count'))
            ->whereNotNull('surgery_type')
            ->groupBy('surgery_type')
            ->pluck('count', 'surgery_type')
            ->toArray();

        // 4. Surgical Specialties Distribution
        $specialtyDistribution = (clone $baseQuery)
            ->select('surgical_specialty', DB::raw('count(*) as count'))
            ->whereNotNull('surgical_specialty')
            ->groupBy('surgical_specialty')
            ->orderBy('count', 'desc')
            ->pluck('count', 'surgical_specialty')
            ->toArray();

        // 5. ASA Grade Distribution
        $asaDistribution = (clone $baseQuery)
            ->select('asa_grade', DB::raw('count(*) as count'))
            ->whereNotNull('asa_grade')
            ->groupBy('asa_grade')
            ->orderBy('asa_grade')
            ->pluck('count', 'asa_grade')
            ->toArray();

        // 6. Anaesthesia Type Usage
        $surgeryIds = (clone $baseQuery)->pluck('surgery_id');
        $anaesthesiaUsage = DB::table('anaesthesia')
            ->whereIn('surgery_id', $surgeryIds)
            ->select('mode', DB::raw('count(*) as count'))
            ->groupBy('mode')
            ->pluck('count', 'mode')
            ->toArray();

        // 7. Most Common Medical Diseases (Top 10)
        $patientIds = (clone $baseQuery)->pluck('patient_id')->unique();
        $topDiseases = DB::table('patient_conditions as pc')
            ->join('medical_conditions as mc', 'pc.condition_id', '=', 'mc.condition_id')
            ->whereIn('pc.patient_id', $patientIds)
            ->select('mc.condition_name', DB::raw('count(*) as count'))
            ->groupBy('mc.condition_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'mc.condition_name')
            ->toArray();

        // 8. Adverse Events Frequency (Top 10)
        $topAdverseEvents = DB::table('surgery_adverse_events as se')
            ->join('adverse_events as ae', 'se.event_id', '=', 'ae.event_id')
            ->whereIn('se.surgery_id', $surgeryIds)
            ->select('ae.event_name', DB::raw('count(*) as count'))
            ->groupBy('ae.event_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'ae.event_name')
            ->toArray();

        // 9. Post-operative Admission (Ward, HDU, ICU)
        $postOpAdmission = (clone $baseQuery)
            ->select('post_op_admission', DB::raw('count(*) as count'))
            ->whereNotNull('post_op_admission')
            ->groupBy('post_op_admission')
            ->pluck('count', 'post_op_admission')
            ->toArray();

        // 10. Recent Audit Records (Last 5 cases)
        $recentSurgeries = (clone $baseQuery)
            ->with(['patient', 'anaesthesia'])
            ->orderBy('date_of_surgery', 'desc')
            ->orderBy('surgery_id', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentSurgeries as $s) {
            if ($s->patient && !empty($s->patient->name)) {
                try {
                    $s->patient->name = Crypt::decryptString($s->patient->name);
                } catch (\Throwable $e) {
                    // Keep unencrypted if decryption fails
                }
            }
        }

        return view('doctor.dashboard', compact(
            'totalSurgeries',
            'surgeriesToday',
            'surgeriesThisMonth',
            'trendLabels',
            'trendData',
            'emergencyVsElective',
            'specialtyDistribution',
            'asaDistribution',
            'anaesthesiaUsage',
            'topDiseases',
            'topAdverseEvents',
            'postOpAdmission',
            'recentSurgeries'
        ));
    }
}