<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\AuditFormMeta;
use App\Models\Patient;
use App\Models\Surgery;
use App\Services\ReportAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct(private ReportAnalyticsService $analytics) {}

    // =========================================================================
    // 0. Reports Hub
    // =========================================================================

    public function index()
    {
        $meta = AuditFormMeta::first();
        return view('doctor.reports.index', compact('meta'));
    }

    // =========================================================================
    // 1. Case Audit Report — single surgery full detail
    // =========================================================================

    public function caseAudit(int $surgeryId)
    {
        $surgery  = $this->loadFullSurgery($surgeryId);
        $meta     = AuditFormMeta::first();

        return view('doctor.reports.case-audit', compact('surgery', 'meta'));
    }

    public function caseAuditPdf(int $surgeryId)
    {
        $surgery = $this->loadFullSurgery($surgeryId);
        $meta    = AuditFormMeta::first();

        $pdf = Pdf::loadView('doctor.reports.pdf.case-audit', compact('surgery', 'meta'))
            ->setPaper('a4', 'portrait');

        $filename = 'case-audit-' . $surgeryId . '.pdf';
        return $pdf->download($filename);
    }

    // =========================================================================
    // 2. Patient Anaesthesia History — by PHN
    // =========================================================================

    public function patientHistory(Request $request)
    {
        $phn     = trim((string) $request->query('phn', ''));
        $patient = null;
        $surgeries = collect();
        $hasDifficultAirway = false;
        $hasAdverseEvents   = false;
        $meta    = AuditFormMeta::first();

        if ($phn) {
            $patient = Patient::where('phn', $phn)->first();

            if ($patient) {
                $surgeries = Surgery::where('patient_id', $patient->patient_id)
                    ->with([
                        'anaesthesia.inductionAgents',
                        'anaesthesia.maintenanceAgents',
                        'anaesthesia.relaxants',
                        'anaesthesia.intubationMethods',
                        'anaesthesia.regionalTypes',
                        'adverseEvents',
                        'staff',
                        'monitoringTypes',
                    ])
                    ->orderBy('date_of_surgery', 'desc')
                    ->orderBy('surgery_id', 'desc')
                    ->get();

                $hasDifficultAirway = $surgeries->contains(
                    fn($s) => optional($s->anaesthesia)->difficult_intubation == 1
                );
                $hasAdverseEvents = $surgeries->contains(
                    fn($s) => $s->adverseEvents->isNotEmpty()
                );
            }
        }

        // PHN suggestions: reuse the same lookup for autocomplete JS
        $specialties = $this->analytics->getSpecialties();

        return view('doctor.reports.patient-history', compact(
            'phn', 'patient', 'surgeries', 'hasDifficultAirway', 'hasAdverseEvents', 'meta', 'specialties'
        ));
    }

    public function patientHistoryPdf(Request $request)
    {
        $phn     = trim((string) $request->query('phn', ''));
        $patient = null;
        $surgeries = collect();
        $hasDifficultAirway = false;
        $hasAdverseEvents   = false;
        $meta    = AuditFormMeta::first();

        if ($phn) {
            $patient = Patient::where('phn', $phn)->first();

            if ($patient) {
                $surgeries = Surgery::where('patient_id', $patient->patient_id)
                    ->with([
                        'anaesthesia.inductionAgents',
                        'anaesthesia.maintenanceAgents',
                        'anaesthesia.relaxants',
                        'anaesthesia.intubationMethods',
                        'anaesthesia.regionalTypes',
                        'adverseEvents',
                        'staff',
                        'monitoringTypes',
                    ])
                    ->orderBy('date_of_surgery', 'desc')
                    ->orderBy('surgery_id', 'desc')
                    ->get();

                $hasDifficultAirway = $surgeries->contains(
                    fn($s) => optional($s->anaesthesia)->difficult_intubation == 1
                );
                $hasAdverseEvents = $surgeries->contains(
                    fn($s) => $s->adverseEvents->isNotEmpty()
                );
            }
        }

        $pdf = Pdf::loadView('doctor.reports.pdf.patient-history', compact(
            'phn', 'patient', 'surgeries', 'hasDifficultAirway', 'hasAdverseEvents', 'meta'
        ))->setPaper('a4', 'portrait');

        $filename = 'patient-history-' . ($phn ?: 'unknown') . '.pdf';
        return $pdf->download($filename);
    }

    // =========================================================================
    // 3. Difficult Airway Registry
    // =========================================================================

    public function difficultAirway(Request $request)
    {
        $filters   = $this->extractFilters($request);
        $meta      = AuditFormMeta::first();
        $specialties = $this->analytics->getSpecialties();

        $query = Surgery::whereHas('anaesthesia', fn($q) => $q->where('difficult_intubation', 1));

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_of_surgery', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date_of_surgery', '<=', $filters['date_to']);
        }
        if (!empty($filters['specialty'])) {
            $query->where('surgical_specialty', $filters['specialty']);
        }

        $surgeries = $query->with([
                'patient',
                'anaesthesia.inductionAgents',
                'anaesthesia.intubationMethods',
                'anaesthesia.relaxants',
                'staff',
            ])
            ->orderBy('date_of_surgery', 'desc')
            ->get();

        return view('doctor.reports.difficult-airway', compact('surgeries', 'filters', 'specialties', 'meta'));
    }

    public function difficultAirwayPdf(Request $request)
    {
        $filters = $this->extractFilters($request);
        $meta    = AuditFormMeta::first();

        $query = Surgery::whereHas('anaesthesia', fn($q) => $q->where('difficult_intubation', 1));

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_of_surgery', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date_of_surgery', '<=', $filters['date_to']);
        }
        if (!empty($filters['specialty'])) {
            $query->where('surgical_specialty', $filters['specialty']);
        }

        $surgeries = $query->with([
                'patient',
                'anaesthesia.inductionAgents',
                'anaesthesia.intubationMethods',
                'anaesthesia.relaxants',
                'staff',
            ])
            ->orderBy('date_of_surgery', 'desc')
            ->get();

        $pdf = Pdf::loadView('doctor.reports.pdf.difficult-airway', compact('surgeries', 'filters', 'meta'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('difficult-airway-registry.pdf');
    }

    // =========================================================================
    // 4. Adverse Event / M&M Report
    // =========================================================================

    public function adverseEvents(Request $request)
    {
        $filters     = $this->extractFilters($request);
        $meta        = AuditFormMeta::first();
        $specialties = $this->analytics->getSpecialties();

        $query = Surgery::whereHas('adverseEvents');

        $this->applyStandardFilters($query, $filters);

        $surgeries = $query->with([
                'patient',
                'anaesthesia',
                'adverseEvents',
                'staff',
            ])
            ->orderBy('date_of_surgery', 'desc')
            ->get();

        // Group by event type for M&M view
        $groupedByEvent = [];
        foreach ($surgeries as $surgery) {
            foreach ($surgery->adverseEvents as $event) {
                $groupedByEvent[$event->event_name][] = $surgery;
            }
        }
        ksort($groupedByEvent);

        return view('doctor.reports.adverse-events', compact(
            'surgeries', 'groupedByEvent', 'filters', 'specialties', 'meta'
        ));
    }

    public function adverseEventsPdf(Request $request)
    {
        $filters = $this->extractFilters($request);
        $meta    = AuditFormMeta::first();

        $query = Surgery::whereHas('adverseEvents');
        $this->applyStandardFilters($query, $filters);

        $surgeries = $query->with([
                'patient',
                'anaesthesia',
                'adverseEvents',
                'staff',
            ])
            ->orderBy('date_of_surgery', 'desc')
            ->get();

        $groupedByEvent = [];
        foreach ($surgeries as $surgery) {
            foreach ($surgery->adverseEvents as $event) {
                $groupedByEvent[$event->event_name][] = $surgery;
            }
        }
        ksort($groupedByEvent);

        $pdf = Pdf::loadView('doctor.reports.pdf.adverse-events', compact(
            'surgeries', 'groupedByEvent', 'filters', 'meta'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('adverse-events-mm-report.pdf');
    }

    // =========================================================================
    // 5. My Case Log
    // =========================================================================

    public function myCases(Request $request)
    {
        $filters     = $this->extractFilters($request);
        $meta        = AuditFormMeta::first();
        $doctorName  = Auth::user()->name;

        $query = Surgery::whereHas('staff', fn($q) => $q->where('name', $doctorName));

        // Do NOT silently fall back to all cases — that would expose other doctors' data.
        $noAssignments = (clone $query)->count() === 0;

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_of_surgery', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date_of_surgery', '<=', $filters['date_to']);
        }

        $surgeries = $query->with([
                'patient',
                'anaesthesia',
                'adverseEvents',
                'staff',
            ])
            ->orderBy('date_of_surgery', 'desc')
            ->get();

        $totalCases   = $surgeries->count();
        $emCases      = $surgeries->where('surgery_type', 'Em')->count();
        $electiveCases = $surgeries->where('surgery_type', 'Elect')->count();

        return view('doctor.reports.my-cases', compact(
            'surgeries', 'filters', 'meta', 'doctorName', 'totalCases', 'emCases', 'electiveCases', 'noAssignments'
        ));
    }

    public function myCasesPdf(Request $request)
    {
        $filters    = $this->extractFilters($request);
        $meta       = AuditFormMeta::first();
        $doctorName = Auth::user()->name;

        // Only load cases belonging to this doctor — do NOT fall back to all cases.
        $query = Surgery::whereHas('staff', fn($q) => $q->where('name', $doctorName));

        if (!empty($filters['date_from'])) {
            $query->whereDate('date_of_surgery', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date_of_surgery', '<=', $filters['date_to']);
        }

        $surgeries = $query->with([
                'patient',
                'anaesthesia',
                'adverseEvents',
                'staff',
            ])
            ->orderBy('date_of_surgery', 'desc')
            ->get();

        $totalCases    = $surgeries->count();
        $emCases       = $surgeries->where('surgery_type', 'Em')->count();
        $electiveCases = $surgeries->where('surgery_type', 'Elect')->count();

        $pdf = Pdf::loadView('doctor.reports.pdf.my-cases', compact(
            'surgeries', 'filters', 'meta', 'doctorName', 'totalCases', 'emCases', 'electiveCases'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('my-case-log.pdf');
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function loadFullSurgery(int $surgeryId): Surgery
    {
        return Surgery::with([
            'patient.conditions.system',
            'anaesthesia.inductionAgents',
            'anaesthesia.maintenanceAgents',
            'anaesthesia.relaxants',
            'anaesthesia.intubationMethods',
            'anaesthesia.regionalTypes',
            'adverseEvents',
            'analgesics',
            'monitoringTypes',
            'staff',
        ])->findOrFail($surgeryId);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'date_from'    => $request->query('date_from', ''),
            'date_to'      => $request->query('date_to', ''),
            'specialty'    => $request->query('specialty', ''),
            'asa_grade'    => $request->query('asa_grade', ''),
            'surgery_type' => $request->query('surgery_type', ''),
        ];
    }

    private function applyStandardFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
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
    }
}
