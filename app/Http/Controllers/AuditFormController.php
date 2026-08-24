<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\Anaesthesia;
use App\Models\MedicalSystem;
use App\Models\Staff;
use App\Models\AdverseEvent;
use App\Models\Analgesic;
use App\Models\InductionAgent;
use App\Models\MaintenanceAgent;
use App\Models\MonitoringType;
use App\Models\RegionalType;
use App\Models\Relaxant;
use App\Models\IntubationMethod; // Added model import
use Exception;

class AuditFormController extends Controller
{
    /**
     * Step 1: Show the patient lookup page (PHN entry before the main form).
     */
    public function startCreate(Request $request)
    {
        $routePrefix = auth()->user()->role === 'data_entry' ? 'data_entry.' : 'doctor.';
        $lookupUrl   = route($routePrefix . 'audit-form.patient-lookup');
        $formUrl     = route($routePrefix . 'audit-form.create');

        return view('audit-form-start', compact('lookupUrl', 'formUrl'));
    }

    /**
     * Step 2: Show the main audit form, optionally pre-filled with patient data
     * when a valid ?phn= query parameter is passed from Step 1.
     */
    public function create(Request $request)
    {
        // Pre-fill patient data if a PHN was passed from the lookup step
        $prefillPatient = null;
        $phn = trim((string) $request->query('phn', ''));
        if ($phn) {
            $prefillPatient = Patient::where('phn', $phn)->first();
        }

        $medicalSystems = MedicalSystem::with('conditions')->get();
        $staffMembers = Staff::all();
        $adverseEvents = AdverseEvent::all();
        $analgesics = Analgesic::all();
        $inductionAgents = InductionAgent::all();
        $maintenanceAgents = MaintenanceAgent::whereIn('name', ['Isoflurane', 'Sevoflurane'])->get();
        $monitoringTypes = MonitoringType::all();
        $regionalTypes = RegionalType::all();
        $relaxants = Relaxant::all();
        $intubationMethods = IntubationMethod::all();

        return view('audit-form', compact(
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
            'prefillPatient'  // null for new, Patient model for returning
        ));
    }

    public function phnSuggestions(Request $request)
    {
        $query = trim((string) $request->query('query', ''));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        // Use Eloquent so the name encryption cast decrypts automatically
        $patients = Patient::query()
            ->where('phn', 'like', $query . '%')
            ->orderBy('phn')
            ->limit(10)
            ->get(['phn', 'name', 'age', 'sex']);

        return response()->json(
            $patients->map(function (Patient $patient) {
                return [
                    'phn'  => $patient->phn,
                    'name' => $patient->name,   // decrypted via cast
                    'age'  => $patient->age,
                    'sex'  => $patient->sex,
                ];
            })
        );
    }

    /**
     * Look up a patient by exact PHN and return their basic info for auto-fill.
     * Returns null JSON if no patient found.
     */
    public function lookupPatient(Request $request)
    {
        $phn = trim((string) $request->query('phn', ''));

        if (!$phn) {
            return response()->json(null);
        }

        $patient = Patient::where('phn', $phn)->first();

        if (!$patient) {
            return response()->json(null);
        }

        return response()->json([
            'patient_id' => $patient->patient_id,
            'name'       => $patient->name, // decrypted automatically via cast
            'age'        => $patient->age,
            'sex'        => $patient->sex,
            'bht'        => $patient->bht,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient.name' => 'required',
            'patient.phn' => 'required',
            'patient.sex' => 'required|in:M,F',
            'surgery.operation_name' => 'required',
            'surgery.surgery_type' => 'required|in:Em,Elect',
            'anaesthesia.mode' => 'required|in:GA,Regional,GA + Regional',
        ]);

        try {
            DB::beginTransaction();

            // 1. Patient
            $patientData = $request->input('patient');
            $phn = $patientData['phn'] ?? null;
            
            $patient = Patient::where('phn', $phn)->first();
            if (!$patient) {
                $patient = Patient::create($patientData);
                
                if ($request->has('patient_conditions')) {
                    $conditions = [];
                    foreach ((array)$request->input('patient_conditions') as $condition_id) {
                        $conditions[$condition_id] = ['recorded_at' => now()];
                    }
                    $patient->conditions()->attach($conditions);
                }
            }

            // 2. Surgery
            $surgeryData = $request->input('surgery');
            $surgeryData['patient_id'] = $patient->patient_id;
            $surgery = Surgery::create($surgeryData);

            $surgeryStaff = [];

if ($request->filled('surgeon_id')) {
    $surgeryStaff[] = [
        'surgery_id' => $surgery->surgery_id,
        'staff_id' => $request->input('surgeon_id'),
        'role_in_case' => 'Consultant Surgeon',
    ];
}

if ($request->filled('anaesthetist_id')) {
    $surgeryStaff[] = [
        'surgery_id' => $surgery->surgery_id,
        'staff_id' => $request->input('anaesthetist_id'),
        'role_in_case' => 'Consultant Anaesthetist',
    ];
}

if ($request->filled('moa_id')) {
    $surgeryStaff[] = [
        'surgery_id' => $surgery->surgery_id,
        'staff_id' => $request->input('moa_id'),
        'role_in_case' => 'MOA',
    ];
}

if (!empty($surgeryStaff)) {
    DB::table('surgery_staff')->insert($surgeryStaff);
}
            if ($request->has('surgery_adverse_events')) {
                $surgery->adverseEvents()->attach($request->input('surgery_adverse_events'));
            }
            if ($request->has('surgery_analgesics')) {
                $surgery->analgesics()->attach($request->input('surgery_analgesics'));
            }
            if ($request->has('surgery_monitoring')) {
                $surgery->monitoringTypes()->attach($request->input('surgery_monitoring'));
            }

            // 3. Anaesthesia
            $anaesthesiaData = $request->input('anaesthesia');
            $anaesthesiaData['surgery_id'] = $surgery->surgery_id;
            
            $anaesthesiaData['difficult_intubation'] = clone_has($request, 'anaesthesia.difficult_intubation') ? 1 : 0;
            $anaesthesiaData['relaxant_used'] = clone_has($request, 'anaesthesia.relaxant_used') ? 1 : 0;
            
            if (isset($anaesthesiaData['difficult_intubation']) && $anaesthesiaData['difficult_intubation'] === 'on') {
                $anaesthesiaData['difficult_intubation'] = 1;
            } else {
                $anaesthesiaData['difficult_intubation'] = (int)($anaesthesiaData['difficult_intubation'] ?? 0);
            }

            if (isset($anaesthesiaData['relaxant_used']) && $anaesthesiaData['relaxant_used'] === 'on') {
                $anaesthesiaData['relaxant_used'] = 1;
            } else {
                $anaesthesiaData['relaxant_used'] = (int)($anaesthesiaData['relaxant_used'] ?? 0);
            }

            $anaesthesia = Anaesthesia::create($anaesthesiaData);

            // Sync Anaesthesia Arrays
            if ($request->has('anaesthesia_induction_agents')) {
                $anaesthesia->inductionAgents()->attach($request->input('anaesthesia_induction_agents'));
            }
            if ($request->has('anaesthesia_maintenance_agents')) {
                $anaesthesia->maintenanceAgents()->attach($request->input('anaesthesia_maintenance_agents'));
            }
            if ($request->has('anaesthesia_regional_types')) {
                $anaesthesia->regionalTypes()->attach($request->input('anaesthesia_regional_types'));
            }
            if ($request->has('anaesthesia_relaxants')) {
                $anaesthesia->relaxants()->attach($request->input('anaesthesia_relaxants'));
            }
            // Sync Intubation Methods
            if ($request->has('anaesthesia_intubation_methods')) {
                $anaesthesia->intubationMethods()->attach($request->input('anaesthesia_intubation_methods'));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Audit Form submitted successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to save audit form: ' . $e->getMessage());
        }
    }
}

function clone_has($request, $key) {
    return $request->has($key) || !is_null($request->input($key));
}