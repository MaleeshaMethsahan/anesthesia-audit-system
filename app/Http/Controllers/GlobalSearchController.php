<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use App\Models\Patient;
use App\Models\MedicalSystem;
use App\Models\Staff;
use App\Models\AdverseEvent;
use App\Models\Analgesic;
use App\Models\InductionAgent;
use App\Models\MaintenanceAgent;
use App\Models\MonitoringType;
use App\Models\RegionalType;
use App\Models\Relaxant;
use App\Models\IntubationMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    /**
     * Display the Global Search page with comprehensive filtering
     * across all Anaesthesia Audit Form attributes.
     */
    public function index(Request $request)
    {
        // 1. Reference Data for all Audit Form Selectors and Filters
        $medicalSystems    = MedicalSystem::with('conditions')->get();
        $staffMembers      = Staff::orderBy('name')->get();
        $adverseEvents     = AdverseEvent::orderBy('event_name')->get();
        $analgesics        = Analgesic::orderBy('name')->get();
        $inductionAgents   = InductionAgent::orderBy('name')->get();
        $maintenanceAgents = MaintenanceAgent::orderBy('name')->get();
        $monitoringTypes   = MonitoringType::orderBy('monitoring_name')->get();
        $regionalTypes     = RegionalType::orderBy('name')->get();
        $relaxants         = Relaxant::orderBy('name')->get();
        $intubationMethods = IntubationMethod::orderBy('method_name')->get();

        $specialties = [
            'Gen. Surg', 'Ortho', 'Plastic', 'Vascular', 'Neuro', 'Obs', 'Gynae',
            'Uro', 'OMF', 'Paed', 'ENT', 'Onco', 'GI', 'GE-ERCP', 'Transplant', 'Cardiac'
        ];

        $asaGrades       = ['I', 'II', 'III', 'IV', 'V', 'VI', 'E'];
        $surgeryTypes    = ['Em' => 'Emergency (Em)', 'Elect' => 'Elective (Elect)'];
        $admissionTypes  = ['Ward', 'HDU', 'ICU'];
        $anaesthesiaModes= ['GA', 'Regional', 'GA + Regional'];

        // 2. Base Query with full relationships eager-loaded
        $query = Surgery::query()->with([
            'patient.conditions.system',
            'anaesthesia.inductionAgents',
            'anaesthesia.maintenanceAgents',
            'anaesthesia.regionalTypes',
            'anaesthesia.relaxants',
            'anaesthesia.intubationMethods',
            'staff',
            'adverseEvents',
            'analgesics',
            'monitoringTypes',
        ]);

        $activeFilters = [];

        // 3. Universal Free-Text / Keyword Search
        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $activeFilters['q'] = [
                'label' => 'Keyword: "' . $q . '"',
                'param' => 'q',
            ];

            // Resolve patient IDs where decrypted name matches keyword
            $matchingPatientIdsByName = Patient::all()
                ->filter(function ($patient) use ($q) {
                    return !empty($patient->name) && str_contains(strtolower($patient->name), strtolower($q));
                })
                ->pluck('patient_id')
                ->toArray();

            // Resolve staff IDs where name matches
            $matchingStaffSurgeryIds = DB::table('surgery_staff as ss')
                ->join('staff as st', 'ss.staff_id', '=', 'st.staff_id')
                ->where('st.name', 'like', "%{$q}%")
                ->pluck('ss.surgery_id')
                ->toArray();

            // Resolve patient IDs where condition name matches
            $matchingConditionPatientIds = DB::table('patient_conditions as pc')
                ->join('medical_conditions as mc', 'pc.condition_id', '=', 'mc.condition_id')
                ->where('mc.condition_name', 'like', "%{$q}%")
                ->pluck('pc.patient_id')
                ->toArray();

            // Resolve surgery IDs where analgesic matches
            $matchingAnalgesicSurgeryIds = DB::table('surgery_analgesics as sa')
                ->join('analgesics as a', 'sa.analgesic_id', '=', 'a.analgesic_id')
                ->where('a.name', 'like', "%{$q}%")
                ->pluck('sa.surgery_id')
                ->toArray();

            // Resolve surgery IDs where adverse event matches
            $matchingEventSurgeryIds = DB::table('surgery_adverse_events as se')
                ->join('adverse_events as ae', 'se.event_id', '=', 'ae.event_id')
                ->where('ae.event_name', 'like', "%{$q}%")
                ->pluck('se.surgery_id')
                ->toArray();

            $query->where(function ($sub) use (
                $q, 
                $matchingPatientIdsByName, 
                $matchingStaffSurgeryIds, 
                $matchingConditionPatientIds,
                $matchingAnalgesicSurgeryIds,
                $matchingEventSurgeryIds
            ) {
                $sub->where('operation_name', 'like', "%{$q}%")
                    ->orWhere('surgical_specialty', 'like', "%{$q}%")
                    ->orWhere('adverse_event_other_details', 'like', "%{$q}%")
                    ->orWhere('analgesia_other_details', 'like', "%{$q}%")
                    ->orWhereHas('patient', function ($pSub) use ($q, $matchingPatientIdsByName, $matchingConditionPatientIds) {
                        $pSub->where('phn', 'like', "%{$q}%")
                             ->orWhere('bht', 'like', "%{$q}%");
                        if (!empty($matchingPatientIdsByName)) {
                            $pSub->orWhereIn('patient_id', $matchingPatientIdsByName);
                        }
                        if (!empty($matchingConditionPatientIds)) {
                            $pSub->orWhereIn('patient_id', $matchingConditionPatientIds);
                        }
                    })
                    ->orWhereHas('anaesthesia', function ($aSub) use ($q) {
                        $aSub->where('notes', 'like', "%{$q}%")
                             ->orWhere('induction_other_details', 'like', "%{$q}%")
                             ->orWhere('regional_other_details', 'like', "%{$q}%")
                             ->orWhere('mode', 'like', "%{$q}%");
                    });

                if (!empty($matchingStaffSurgeryIds)) {
                    $sub->orWhereIn('surgery_id', $matchingStaffSurgeryIds);
                }
                if (!empty($matchingAnalgesicSurgeryIds)) {
                    $sub->orWhereIn('surgery_id', $matchingAnalgesicSurgeryIds);
                }
                if (!empty($matchingEventSurgeryIds)) {
                    $sub->orWhereIn('surgery_id', $matchingEventSurgeryIds);
                }
            });
        }

        // 4. Patient Demographics Filters
        if ($request->filled('sex')) {
            $sex = $request->query('sex');
            $query->whereHas('patient', fn($p) => $p->where('sex', $sex));
            $activeFilters['sex'] = [
                'label' => 'Sex: ' . ($sex === 'M' ? 'Male' : 'Female'),
                'param' => 'sex',
            ];
        }

        if ($request->filled('age_min')) {
            $ageMin = (int) $request->query('age_min');
            $query->whereHas('patient', fn($p) => $p->where('age', '>=', $ageMin));
            $activeFilters['age_min'] = [
                'label' => 'Min Age: ' . $ageMin,
                'param' => 'age_min',
            ];
        }

        if ($request->filled('age_max')) {
            $ageMax = (int) $request->query('age_max');
            $query->whereHas('patient', fn($p) => $p->where('age', '<=', $ageMax));
            $activeFilters['age_max'] = [
                'label' => 'Max Age: ' . $ageMax,
                'param' => 'age_max',
            ];
        }

        if ($request->filled('phn')) {
            $phn = trim((string) $request->query('phn'));
            $query->whereHas('patient', fn($p) => $p->where('phn', 'like', "%{$phn}%"));
            $activeFilters['phn'] = [
                'label' => 'PHN: ' . $phn,
                'param' => 'phn',
            ];
        }

        if ($request->filled('bht')) {
            $bht = trim((string) $request->query('bht'));
            $query->whereHas('patient', fn($p) => $p->where('bht', 'like', "%{$bht}%"));
            $activeFilters['bht'] = [
                'label' => 'BHT: ' . $bht,
                'param' => 'bht',
            ];
        }

        // 5. Surgery Specifics Filters
        if ($request->filled('specialties')) {
            $specs = (array) $request->query('specialties');
            $query->whereIn('surgical_specialty', $specs);
            $activeFilters['specialties'] = [
                'label' => 'Specialty: ' . implode(', ', $specs),
                'param' => 'specialties',
            ];
        }

        if ($request->filled('surgery_type')) {
            $sType = $request->query('surgery_type');
            $query->where('surgery_type', $sType);
            $activeFilters['surgery_type'] = [
                'label' => 'Type: ' . ($sType === 'Em' ? 'Emergency' : 'Elective'),
                'param' => 'surgery_type',
            ];
        }

        if ($request->filled('asa_grades')) {
            $grades = (array) $request->query('asa_grades');
            $query->whereIn('asa_grade', $grades);
            $activeFilters['asa_grades'] = [
                'label' => 'ASA: ' . implode(', ', $grades),
                'param' => 'asa_grades',
            ];
        }

        if ($request->filled('date_from')) {
            $dateFrom = $request->query('date_from');
            $query->where('date_of_surgery', '>=', $dateFrom);
            $activeFilters['date_from'] = [
                'label' => 'From: ' . $dateFrom,
                'param' => 'date_from',
            ];
        }

        if ($request->filled('date_to')) {
            $dateTo = $request->query('date_to');
            $query->where('date_of_surgery', '<=', $dateTo);
            $activeFilters['date_to'] = [
                'label' => 'To: ' . $dateTo,
                'param' => 'date_to',
            ];
        }

        if ($request->filled('admissions')) {
            $admissions = (array) $request->query('admissions');
            $query->whereIn('post_op_admission', $admissions);
            $activeFilters['admissions'] = [
                'label' => 'Admission: ' . implode(', ', $admissions),
                'param' => 'admissions',
            ];
        }

        // 6. Clinical Team Filters
        if ($request->filled('surgeon_id')) {
            $sId = (int) $request->query('surgeon_id');
            $query->whereHas('staff', fn($s) => $s->where('staff.staff_id', $sId)->where('role_in_case', 'Consultant Surgeon'));
            $surgeon = $staffMembers->firstWhere('staff_id', $sId);
            $activeFilters['surgeon_id'] = [
                'label' => 'Surgeon: ' . ($surgeon ? $surgeon->name : '#' . $sId),
                'param' => 'surgeon_id',
            ];
        }

        if ($request->filled('anaesthetist_id')) {
            $aId = (int) $request->query('anaesthetist_id');
            $query->whereHas('staff', fn($s) => $s->where('staff.staff_id', $aId)->where('role_in_case', 'Consultant Anaesthetist'));
            $anaes = $staffMembers->firstWhere('staff_id', $aId);
            $activeFilters['anaesthetist_id'] = [
                'label' => 'Anaesthetist: ' . ($anaes ? $anaes->name : '#' . $aId),
                'param' => 'anaesthetist_id',
            ];
        }

        if ($request->filled('moa_id')) {
            $moaId = (int) $request->query('moa_id');
            $query->whereHas('staff', fn($s) => $s->where('staff.staff_id', $moaId)->where('role_in_case', 'MOA'));
            $moa = $staffMembers->firstWhere('staff_id', $moaId);
            $activeFilters['moa_id'] = [
                'label' => 'MOA: ' . ($moa ? $moa->name : '#' . $moaId),
                'param' => 'moa_id',
            ];
        }

        // 7. Medical Disease & Conditions Filters
        if ($request->filled('patient_type')) {
            $pType = $request->query('patient_type');
            $query->whereHas('patient.conditions', fn($c) => $c->where('patient_type', $pType));
            $activeFilters['patient_type'] = [
                'label' => 'Condition Type: ' . $pType,
                'param' => 'patient_type',
            ];
        }

        if ($request->filled('conditions')) {
            $conditionIds = (array) $request->query('conditions');
            $query->whereHas('patient.conditions', fn($c) => $c->whereIn('medical_conditions.condition_id', $conditionIds));
            
            $conditionNames = [];
            foreach ($medicalSystems as $sys) {
                foreach ($sys->conditions as $cond) {
                    if (in_array($cond->condition_id, $conditionIds)) {
                        $conditionNames[] = $cond->condition_name;
                    }
                }
            }
            $activeFilters['conditions'] = [
                'label' => 'Conditions: ' . (count($conditionNames) > 2 ? count($conditionNames) . ' selected' : implode(', ', $conditionNames)),
                'param' => 'conditions',
            ];
        }

        // 8. Anaesthesia Mode & Technique Filters
        if ($request->filled('anaesthesia_mode')) {
            $mode = $request->query('anaesthesia_mode');
            $query->whereHas('anaesthesia', fn($a) => $a->where('mode', $mode));
            $activeFilters['anaesthesia_mode'] = [
                'label' => 'Mode: ' . $mode,
                'param' => 'anaesthesia_mode',
            ];
        }

        if ($request->filled('monitoring_types')) {
            $mIds = (array) $request->query('monitoring_types');
            $query->whereHas('monitoringTypes', fn($m) => $m->whereIn('monitoring_types.monitoring_id', $mIds));
            $mNames = $monitoringTypes->whereIn('monitoring_id', $mIds)->pluck('monitoring_name')->toArray();
            $activeFilters['monitoring_types'] = [
                'label' => 'Monitoring: ' . implode(', ', $mNames),
                'param' => 'monitoring_types',
            ];
        }

        if ($request->filled('induction_type')) {
            $indType = $request->query('induction_type');
            $query->whereHas('anaesthesia', fn($a) => $a->where('induction_type', $indType));
            $activeFilters['induction_type'] = [
                'label' => 'Induction: ' . $indType,
                'param' => 'induction_type',
            ];
        }

        if ($request->filled('induction_agents')) {
            $iaIds = (array) $request->query('induction_agents');
            $query->whereHas('anaesthesia.inductionAgents', fn($ia) => $ia->whereIn('induction_agents.induction_agent_id', $iaIds));
            $iaNames = $inductionAgents->whereIn('induction_agent_id', $iaIds)->pluck('name')->toArray();
            $activeFilters['induction_agents'] = [
                'label' => 'Induction Agent: ' . implode(', ', $iaNames),
                'param' => 'induction_agents',
            ];
        }

        if ($request->filled('difficult_intubation')) {
            $diffVal = $request->query('difficult_intubation');
            $isDiff = in_array($diffVal, ['1', 'yes', 'true']);
            $query->whereHas('anaesthesia', fn($a) => $a->where('difficult_intubation', $isDiff ? 1 : 0));
            $activeFilters['difficult_intubation'] = [
                'label' => 'Difficult Intubation: ' . ($isDiff ? 'Yes' : 'No'),
                'param' => 'difficult_intubation',
            ];
        }

        if ($request->filled('intubation_methods')) {
            $imIds = (array) $request->query('intubation_methods');
            $query->whereHas('anaesthesia.intubationMethods', fn($im) => $im->whereIn('intubation_methods.method_id', $imIds));
            $imNames = $intubationMethods->whereIn('method_id', $imIds)->pluck('method_name')->toArray();
            $activeFilters['intubation_methods'] = [
                'label' => 'Intubation Method: ' . implode(', ', $imNames),
                'param' => 'intubation_methods',
            ];
        }

        if ($request->filled('maintenance_route')) {
            $mRoute = $request->query('maintenance_route');
            $query->whereHas('anaesthesia', fn($a) => $a->where('maintenance_route', $mRoute));
            $activeFilters['maintenance_route'] = [
                'label' => 'Maintenance Route: ' . $mRoute,
                'param' => 'maintenance_route',
            ];
        }

        if ($request->filled('maintenance_agents')) {
            $maIds = (array) $request->query('maintenance_agents');
            $query->whereHas('anaesthesia.maintenanceAgents', fn($ma) => $ma->whereIn('maintenance_agents.maintenance_agent_id', $maIds));
            $maNames = $maintenanceAgents->whereIn('maintenance_agent_id', $maIds)->pluck('name')->toArray();
            $activeFilters['maintenance_agents'] = [
                'label' => 'Maintenance Agent: ' . implode(', ', $maNames),
                'param' => 'maintenance_agents',
            ];
        }

        if ($request->filled('relaxant_used')) {
            $relVal = $request->query('relaxant_used');
            $isRel = in_array($relVal, ['1', 'yes', 'true']);
            $query->whereHas('anaesthesia', fn($a) => $a->where('relaxant_used', $isRel ? 1 : 0));
            $activeFilters['relaxant_used'] = [
                'label' => 'Relaxant Used: ' . ($isRel ? 'Yes' : 'No'),
                'param' => 'relaxant_used',
            ];
        }

        if ($request->filled('relaxants')) {
            $rIds = (array) $request->query('relaxants');
            $query->whereHas('anaesthesia.relaxants', fn($r) => $r->whereIn('relaxants.relaxant_id', $rIds));
            $rNames = $relaxants->whereIn('relaxant_id', $rIds)->pluck('name')->toArray();
            $activeFilters['relaxants'] = [
                'label' => 'Relaxant: ' . implode(', ', $rNames),
                'param' => 'relaxants',
            ];
        }

        if ($request->filled('regional_types')) {
            $rtIds = (array) $request->query('regional_types');
            $query->whereHas('anaesthesia.regionalTypes', fn($rt) => $rt->whereIn('regional_types.regional_type_id', $rtIds));
            $rtNames = $regionalTypes->whereIn('regional_type_id', $rtIds)->pluck('name')->toArray();
            $activeFilters['regional_types'] = [
                'label' => 'Regional: ' . implode(', ', $rtNames),
                'param' => 'regional_types',
            ];
        }

        // 9. Analgesia Filters
        if ($request->filled('analgesics')) {
            $anIds = (array) $request->query('analgesics');
            $query->whereHas('analgesics', fn($an) => $an->whereIn('analgesics.analgesic_id', $anIds));
            $anNames = $analgesics->whereIn('analgesic_id', $anIds)->pluck('name')->toArray();
            $activeFilters['analgesics'] = [
                'label' => 'Analgesia: ' . implode(', ', $anNames),
                'param' => 'analgesics',
            ];
        }

        // 10. Adverse Events Filters
        if ($request->filled('has_adverse_events')) {
            $hasAdv = $request->query('has_adverse_events');
            if ($hasAdv === 'yes') {
                $query->where(function ($sub) {
                    $sub->has('adverseEvents')
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('adverse_event_other_details')
                               ->where('adverse_event_other_details', '!=', '');
                        });
                });
                $activeFilters['has_adverse_events'] = [
                    'label' => 'Adverse Events: Yes',
                    'param' => 'has_adverse_events',
                ];
            } elseif ($hasAdv === 'no') {
                $query->whereDoesntHave('adverseEvents')
                      ->where(function ($sub) {
                          $sub->whereNull('adverse_event_other_details')
                              ->orWhere('adverse_event_other_details', '');
                      });
                $activeFilters['has_adverse_events'] = [
                    'label' => 'Adverse Events: None',
                    'param' => 'has_adverse_events',
                ];
            }
        }

        if ($request->filled('adverse_events')) {
            $aeIds = (array) $request->query('adverse_events');
            $query->whereHas('adverseEvents', fn($ae) => $ae->whereIn('adverse_events.event_id', $aeIds));
            $aeNames = $adverseEvents->whereIn('event_id', $aeIds)->pluck('event_name')->toArray();
            $activeFilters['adverse_events'] = [
                'label' => 'Events: ' . implode(', ', $aeNames),
                'param' => 'adverse_events',
            ];
        }

        // 11. Pagination & Sorting
        $surgeries = $query->orderBy('date_of_surgery', 'desc')
                           ->orderBy('surgery_id', 'desc')
                           ->paginate(15)
                           ->withQueryString();

        $totalAuditsCount = Surgery::count();

        return view('global-search.index', compact(
            'surgeries',
            'medicalSystems',
            'staffMembers',
            'adverseEvents',
            'analgesics',
            'inductionAgents',
            'maintenanceAgents',
            'monitoringTypes',
            'regionalTypes',
            'relaxants',
            'intubationMethods',
            'specialties',
            'asaGrades',
            'surgeryTypes',
            'admissionTypes',
            'anaesthesiaModes',
            'activeFilters',
            'totalAuditsCount'
        ));
    }
}
