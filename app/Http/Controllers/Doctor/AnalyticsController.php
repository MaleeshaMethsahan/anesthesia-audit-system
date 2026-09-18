<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\AuditFormMeta;
use App\Services\ReportAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(private ReportAnalyticsService $analytics) {}

    public function index(Request $request)
    {
        $scope    = $request->query('scope', 'my');   // 'my' | 'all'
        $filters  = [
            'date_from' => $request->query('date_from', ''),
            'date_to'   => $request->query('date_to', ''),
            'specialty' => $request->query('specialty', ''),
        ];

        $doctorName = Auth::user()->name;

        // Build base query according to scope
        $scopeFallback = false;
        if ($scope === 'my') {
            $baseQuery = $this->analytics->buildBaseQuery($filters, $doctorName);
            // If doctor has no staff assignments yet, show empty analytics with a notice
            // rather than silently showing all cases (which could expose other doctors' data).
            if ((clone $baseQuery)->count() === 0) {
                $scopeFallback = true;
                // Leave $baseQuery scoped to this doctor — will return zero results cleanly
            }
        } else {
            $baseQuery = $this->analytics->buildBaseQuery($filters);
        }

        // All aggregates via service
        $caseCounts         = $this->analytics->getCaseCounts($baseQuery);
        $emVsElect          = $this->analytics->getEmVsElect($baseQuery);
        $specialtyDist      = $this->analytics->getSpecialtyDistribution($baseQuery);
        $asaDist            = $this->analytics->getAsaDistribution($baseQuery);
        $anaesthesiaUsage   = $this->analytics->getAnaesthesiaUsage($baseQuery);
        $adverseEventRate   = $this->analytics->getAdverseEventRate($baseQuery);
        $difficultIntubRate = $this->analytics->getDifficultIntubationRate($baseQuery);
        $relaxantUseRate    = $this->analytics->getRelaxantUseRate($baseQuery);
        $postOpBreakdown    = $this->analytics->getPostOpBreakdown($baseQuery);
        $topAdverseEvents   = $this->analytics->getTopAdverseEvents($baseQuery);
        $topComorbidities   = $this->analytics->getTopComorbidities($baseQuery);
        $trend              = $this->analytics->getTrendData($baseQuery);
        $specialties        = $this->analytics->getSpecialties();
        $meta               = AuditFormMeta::first();

        return view('doctor.analytics', compact(
            'scope',
            'filters',
            'caseCounts',
            'emVsElect',
            'specialtyDist',
            'asaDist',
            'anaesthesiaUsage',
            'adverseEventRate',
            'difficultIntubRate',
            'relaxantUseRate',
            'postOpBreakdown',
            'topAdverseEvents',
            'topComorbidities',
            'trend',
            'specialties',
            'meta',
            'doctorName',
            'scopeFallback'
        ));
    }
}
