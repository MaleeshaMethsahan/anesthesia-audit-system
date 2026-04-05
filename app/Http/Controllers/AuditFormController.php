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
    public function create()
    {
        $medicalSystems = MedicalSystem::with('conditions')->get();
        $staffMembers = Staff::all();
        $adverseEvents = AdverseEvent::all();
        $analgesics = Analgesic::all();
        $inductionAgents = InductionAgent::all();
        $maintenanceAgents = MaintenanceAgent::whereIn('name', ['Isoflurane', 'Sevoflurane'])->get();
        $monitoringTypes = MonitoringType::all();
        $regionalTypes = RegionalType::all();
        $relaxants = Relaxant::all();
        $intubationMethods = IntubationMethod::all(); // Fetch intubation methods

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
            'intubationMethods' // Pass to view
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient.name' => 'required',
            'patient.phn' => 'required',
            'patient.sex' => 'required|in:M,F',
            'surgery.operation_name' => 'required',
            'surgery.surgery_type' => 'required|in:Em,Elect',
            'anaesthesia.mode' => 'required|in:GA,Regional',
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
                $surgeryStaff[$request->input('surgeon_id')] = ['role_in_case' => 'Consultant Surgeon'];
            }
            if ($request->filled('anaesthetist_id')) {
                $surgeryStaff[$request->input('anaesthetist_id')] = ['role_in_case' => 'Consultant Anaesthetist'];
            }
            if ($request->filled('moa_id')) {
                $surgeryStaff[$request->input('moa_id')] = ['role_in_case' => 'MOA'];
            }
            if (count($surgeryStaff) > 0) {
                $surgery->staff()->attach($surgeryStaff);
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