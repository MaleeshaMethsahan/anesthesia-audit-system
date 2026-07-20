<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index()
    {
        // 1. Fetch main surgeries with patient data
        $surgeries = DB::table('surgeries as s')
            ->join('patients as p', 's.patient_id', '=', 'p.patient_id')
            ->select(
                's.surgery_id', 's.operation_name', 's.surgical_specialty', 
                's.surgery_type', 's.date_of_surgery', 's.asa_grade', 's.post_op_admission',
                's.adverse_event_other_details', 's.analgesia_other_details', // <-- ADDED HERE
                'p.patient_id', 'p.phn', 'p.name', 'p.age', 'p.sex', 'p.bht'
            )
            ->orderBy('s.surgery_id', 'desc')
            ->get();

        // 2. Fetch sub-data for each surgery
        foreach ($surgeries as $row) {
            $this->hydrateSurgeryDetails($row);
        }

        return view('records.index', compact('surgeries'));
    }

    public function show(int $surgeryId)
    {
        $surgery = DB::table('surgeries as s')
            ->join('patients as p', 's.patient_id', '=', 'p.patient_id')
            ->select(
                's.surgery_id', 's.operation_name', 's.surgical_specialty',
                's.surgery_type', 's.date_of_surgery', 's.asa_grade', 's.post_op_admission',
                's.adverse_event_other_details', 's.analgesia_other_details', // <-- ADDED HERE
                'p.patient_id','p.phn', 'p.name', 'p.age', 'p.sex', 'p.bht'
            )
            ->where('s.surgery_id', $surgeryId)
            ->first();

        abort_unless($surgery, 404);

        $this->hydrateSurgeryDetails($surgery);

        return view('records.show', compact('surgery'));
    }

    public function edit(int $surgeryId)
    {
        $surgery = DB::table('surgeries as s')
            ->join('patients as p', 's.patient_id', '=', 'p.patient_id')
            ->select(
                's.surgery_id', 's.operation_name', 's.surgical_specialty',
                's.surgery_type', 's.date_of_surgery', 's.asa_grade', 's.post_op_admission',
                's.adverse_event_other_details', 's.analgesia_other_details',
                's.created_at', 's.updated_at', 's.update_count',
                'p.patient_id','p.phn', 'p.name', 'p.age', 'p.sex', 'p.bht'
            )
            ->where('s.surgery_id', $surgeryId)
            ->first();

        abort_unless($surgery, 404);

        if (isset($surgery->name) && !empty($surgery->name)) {
            try {
                $surgery->name = Crypt::decryptString($surgery->name);
            } catch (\Throwable $e) {
                // leave unencrypted
            }
        }

        // Selected pivot IDs
        $selectedConditionIds = DB::table('patient_conditions')->where('patient_id', $surgery->patient_id)->pluck('condition_id')->toArray();
        $selectedMonitoringIds = DB::table('surgery_monitoring')->where('surgery_id', $surgeryId)->pluck('monitoring_id')->toArray();
        $selectedAnalgesicIds = DB::table('surgery_analgesics')->where('surgery_id', $surgeryId)->pluck('analgesic_id')->toArray();
        $selectedAdverseEventIds = DB::table('surgery_adverse_events')->where('surgery_id', $surgeryId)->pluck('event_id')->toArray();

        // Selected Staff IDs
        $surgeonId = DB::table('surgery_staff')->where('surgery_id', $surgeryId)->where('role_in_case', 'Consultant Surgeon')->value('staff_id');
        $anaesthetistId = DB::table('surgery_staff')->where('surgery_id', $surgeryId)->where('role_in_case', 'Consultant Anaesthetist')->value('staff_id');
        $moaId = DB::table('surgery_staff')->where('surgery_id', $surgeryId)->where('role_in_case', 'MOA')->value('staff_id');

        // Anaesthesia details
        $anaesthesia = DB::table('anaesthesia')->where('surgery_id', $surgeryId)->first();
        $selectedInductionIds = [];
        $selectedMaintenanceIds = [];
        $selectedRegionalIds = [];
        $selectedRelaxantIds = [];
        $selectedIntubationIds = [];

        if ($anaesthesia) {
            $aId = $anaesthesia->anaesthesia_id;
            $selectedInductionIds = DB::table('anaesthesia_induction_agents')->where('anaesthesia_id', $aId)->pluck('induction_agent_id')->toArray();
            $selectedMaintenanceIds = DB::table('anaesthesia_maintenance_agents')->where('anaesthesia_id', $aId)->pluck('maintenance_agent_id')->toArray();
            $selectedRegionalIds = DB::table('anaesthesia_regional_types')->where('anaesthesia_id', $aId)->pluck('regional_type_id')->toArray();
            $selectedRelaxantIds = DB::table('anaesthesia_relaxants')->where('anaesthesia_id', $aId)->pluck('relaxant_id')->toArray();
            $selectedIntubationIds = DB::table('anaesthesia_intubation_methods')->where('anaesthesia_id', $aId)->pluck('method_id')->toArray();
        }

        // Master lists
        $medicalSystems = \App\Models\MedicalSystem::with('conditions')->get();
        $staffMembers = \App\Models\Staff::all();
        $adverseEvents = \App\Models\AdverseEvent::all();
        $analgesics = \App\Models\Analgesic::all();
        $inductionAgents = \App\Models\InductionAgent::all();
        $maintenanceAgents = \App\Models\MaintenanceAgent::whereIn('name', ['Isoflurane', 'Sevoflurane'])->get();
        $monitoringTypes = \App\Models\MonitoringType::all();
        $regionalTypes = \App\Models\RegionalType::all();
        $relaxants = \App\Models\Relaxant::all();
        $intubationMethods = \App\Models\IntubationMethod::all();

        return view('records.edit', compact(
            'surgery',
            'selectedConditionIds',
            'selectedMonitoringIds',
            'selectedAnalgesicIds',
            'selectedAdverseEventIds',
            'surgeonId',
            'anaesthetistId',
            'moaId',
            'anaesthesia',
            'selectedInductionIds',
            'selectedMaintenanceIds',
            'selectedRegionalIds',
            'selectedRelaxantIds',
            'selectedIntubationIds',
            'medicalSystems',
            'staffMembers',
            'adverseEvents',
            'analgesics',
            'inductionAgents',
            'maintenanceAgents',
            'monitoringTypes',
            'regionalTypes',
            'relaxants',
            'intubationMethods'
        ));
    }

    public function update(Request $request, int $surgeryId)
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

            $surgery = DB::table('surgeries')->where('surgery_id', $surgeryId)->first();
            abort_unless($surgery, 404);

            $patientId = $surgery->patient_id;

            // 1. Patient
            $patientData = $request->input('patient');
            $encryptedName = Crypt::encryptString($patientData['name']);

            DB::table('patients')->where('patient_id', $patientId)->update([
                'name' => $encryptedName,
                'phn' => $patientData['phn'] ?? null,
                'age' => $patientData['age'] ?? null,
                'sex' => $patientData['sex'] ?? null,
                'bht' => $patientData['bht'] ?? null,
            ]);

            // Sync conditions
            DB::table('patient_conditions')->where('patient_id', $patientId)->delete();
            if ($request->has('patient_conditions')) {
                $conditions = [];
                foreach ((array)$request->input('patient_conditions') as $condition_id) {
                    $conditions[] = [
                        'patient_id' => $patientId,
                        'condition_id' => $condition_id,
                        'recorded_at' => now(),
                    ];
                }
                DB::table('patient_conditions')->insert($conditions);
            }

            // 2. Surgery
            $surgeryData = $request->input('surgery');
            DB::table('surgeries')->where('surgery_id', $surgeryId)->update([
                'operation_name' => $surgeryData['operation_name'] ?? null,
                'surgical_specialty' => $surgeryData['surgical_specialty'] ?? null,
                'surgery_type' => $surgeryData['surgery_type'] ?? null,
                'date_of_surgery' => $surgeryData['date_of_surgery'] ?? null,
                'asa_grade' => $surgeryData['asa_grade'] ?? null,
                'post_op_admission' => $surgeryData['post_op_admission'] ?? null,
                'adverse_event_other_details' => $surgeryData['adverse_event_other_details'] ?? null,
                'analgesia_other_details' => $surgeryData['analgesia_other_details'] ?? null,
                'updated_at' => now(),
                'update_count' => DB::raw('update_count + 1'),
            ]);

            // Staff
            DB::table('surgery_staff')->where('surgery_id', $surgeryId)->delete();
            $surgeryStaff = [];
            if ($request->filled('surgeon_id')) {
                $surgeryStaff[] = ['surgery_id' => $surgeryId, 'staff_id' => $request->input('surgeon_id'), 'role_in_case' => 'Consultant Surgeon'];
            }
            if ($request->filled('anaesthetist_id')) {
                $surgeryStaff[] = ['surgery_id' => $surgeryId, 'staff_id' => $request->input('anaesthetist_id'), 'role_in_case' => 'Consultant Anaesthetist'];
            }
            if ($request->filled('moa_id')) {
                $surgeryStaff[] = ['surgery_id' => $surgeryId, 'staff_id' => $request->input('moa_id'), 'role_in_case' => 'MOA'];
            }
            if (!empty($surgeryStaff)) {
                DB::table('surgery_staff')->insert($surgeryStaff);
            }

            // Adverse Events
            DB::table('surgery_adverse_events')->where('surgery_id', $surgeryId)->delete();
            if ($request->has('surgery_adverse_events')) {
                $events = [];
                foreach ((array)$request->input('surgery_adverse_events') as $eventId) {
                    $events[] = ['surgery_id' => $surgeryId, 'event_id' => $eventId];
                }
                DB::table('surgery_adverse_events')->insert($events);
            }

            // Analgesics
            DB::table('surgery_analgesics')->where('surgery_id', $surgeryId)->delete();
            if ($request->has('surgery_analgesics')) {
                $analgesics = [];
                foreach ((array)$request->input('surgery_analgesics') as $analgesicId) {
                    $analgesics[] = ['surgery_id' => $surgeryId, 'analgesic_id' => $analgesicId];
                }
                DB::table('surgery_analgesics')->insert($analgesics);
            }

            // Monitoring
            DB::table('surgery_monitoring')->where('surgery_id', $surgeryId)->delete();
            if ($request->has('surgery_monitoring')) {
                $monitoring = [];
                foreach ((array)$request->input('surgery_monitoring') as $monitoringId) {
                    $monitoring[] = ['surgery_id' => $surgeryId, 'monitoring_id' => $monitoringId];
                }
                DB::table('surgery_monitoring')->insert($monitoring);
            }

            // 3. Anaesthesia
            $anaesthesiaData = $request->input('anaesthesia');
            $difficultIntubation = isset($anaesthesiaData['difficult_intubation']) && ($anaesthesiaData['difficult_intubation'] === 'on' || $anaesthesiaData['difficult_intubation'] == 1) ? 1 : 0;
            $relaxantUsed = isset($anaesthesiaData['relaxant_used']) && ($anaesthesiaData['relaxant_used'] === 'on' || $anaesthesiaData['relaxant_used'] == 1) ? 1 : 0;

            $anaesthesia = DB::table('anaesthesia')->where('surgery_id', $surgeryId)->first();
            if ($anaesthesia) {
                DB::table('anaesthesia')->where('anaesthesia_id', $anaesthesia->anaesthesia_id)->update([
                    'mode' => $anaesthesiaData['mode'] ?? null,
                    'difficult_intubation' => $difficultIntubation,
                    'relaxant_used' => $relaxantUsed,
                ]);
                $anaesthesiaId = $anaesthesia->anaesthesia_id;
            } else {
                $anaesthesiaId = DB::table('anaesthesia')->insertGetId([
                    'surgery_id' => $surgeryId,
                    'mode' => $anaesthesiaData['mode'] ?? null,
                    'difficult_intubation' => $difficultIntubation,
                    'relaxant_used' => $relaxantUsed,
                ]);
            }

            // Sync Anaesthesia Pivot Tables
            DB::table('anaesthesia_induction_agents')->where('anaesthesia_id', $anaesthesiaId)->delete();
            if ($request->has('anaesthesia_induction_agents')) {
                $items = [];
                foreach ((array)$request->input('anaesthesia_induction_agents') as $id) {
                    $items[] = ['anaesthesia_id' => $anaesthesiaId, 'induction_agent_id' => $id];
                }
                DB::table('anaesthesia_induction_agents')->insert($items);
            }

            DB::table('anaesthesia_maintenance_agents')->where('anaesthesia_id', $anaesthesiaId)->delete();
            if ($request->has('anaesthesia_maintenance_agents')) {
                $items = [];
                foreach ((array)$request->input('anaesthesia_maintenance_agents') as $id) {
                    $items[] = ['anaesthesia_id' => $anaesthesiaId, 'maintenance_agent_id' => $id];
                }
                DB::table('anaesthesia_maintenance_agents')->insert($items);
            }

            DB::table('anaesthesia_regional_types')->where('anaesthesia_id', $anaesthesiaId)->delete();
            if ($request->has('anaesthesia_regional_types')) {
                $items = [];
                foreach ((array)$request->input('anaesthesia_regional_types') as $id) {
                    $items[] = ['anaesthesia_id' => $anaesthesiaId, 'regional_type_id' => $id];
                }
                DB::table('anaesthesia_regional_types')->insert($items);
            }

            DB::table('anaesthesia_relaxants')->where('anaesthesia_id', $anaesthesiaId)->delete();
            if ($request->has('anaesthesia_relaxants')) {
                $items = [];
                foreach ((array)$request->input('anaesthesia_relaxants') as $id) {
                    $items[] = ['anaesthesia_id' => $anaesthesiaId, 'relaxant_id' => $id];
                }
                DB::table('anaesthesia_relaxants')->insert($items);
            }

            DB::table('anaesthesia_intubation_methods')->where('anaesthesia_id', $anaesthesiaId)->delete();
            if ($request->has('anaesthesia_intubation_methods')) {
                $items = [];
                foreach ((array)$request->input('anaesthesia_intubation_methods') as $id) {
                    $items[] = ['anaesthesia_id' => $anaesthesiaId, 'method_id' => $id];
                }
                DB::table('anaesthesia_intubation_methods')->insert($items);
            }

            DB::commit();

            $routePrefix = auth()->user()->role === 'data_entry' ? 'data_entry.' : 'doctor.';
            return redirect()->route($routePrefix . 'records.index')->with('success', 'Patient record updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Failed to update record: ' . $e->getMessage());
        }
    }

    public function destroy(int $surgeryId)
    {
        $surgeryExists = DB::table('surgeries')->where('surgery_id', $surgeryId)->exists();
        abort_unless($surgeryExists, 404);

        DB::transaction(function () use ($surgeryId) {
            $anaesthesiaId = DB::table('anaesthesia')
                ->where('surgery_id', $surgeryId)
                ->value('anaesthesia_id');

            if ($anaesthesiaId) {
                DB::table('anaesthesia_induction_agents')->where('anaesthesia_id', $anaesthesiaId)->delete();
                DB::table('anaesthesia_maintenance_agents')->where('anaesthesia_id', $anaesthesiaId)->delete();
                DB::table('anaesthesia_regional_types')->where('anaesthesia_id', $anaesthesiaId)->delete();
                DB::table('anaesthesia_relaxants')->where('anaesthesia_id', $anaesthesiaId)->delete();
                
                // Delete intubation methods
                DB::table('anaesthesia_intubation_methods')->where('anaesthesia_id', $anaesthesiaId)->delete();
                
                DB::table('anaesthesia')->where('anaesthesia_id', $anaesthesiaId)->delete();
            }

            DB::table('surgery_staff')->where('surgery_id', $surgeryId)->delete();
            DB::table('surgery_adverse_events')->where('surgery_id', $surgeryId)->delete();
            DB::table('surgery_analgesics')->where('surgery_id', $surgeryId)->delete();
            DB::table('surgery_monitoring')->where('surgery_id', $surgeryId)->delete();
            DB::table('surgeries')->where('surgery_id', $surgeryId)->delete();
        });

        return redirect()->back()->with('success', 'Record deleted successfully.');
    }

    private function hydrateSurgeryDetails(object $row): void
    {
        $id = $row->surgery_id;
        $patientId = $row->patient_id;

        if (isset($row->name) && !empty($row->name)) {
            try {
                $row->name = Crypt::decryptString($row->name);
            } catch (\Throwable $e) {
                // If not encrypted or decryption fails, leave original string
            }
        }

        $row->diseases = DB::table('patient_conditions as pc')
            ->join('medical_conditions as mc', 'pc.condition_id', '=', 'mc.condition_id')
            ->where('pc.patient_id', $patientId)
            ->pluck('mc.condition_name');

        $row->monitor = DB::table('surgery_monitoring as sm')
            ->join('monitoring_types as mt', 'sm.monitoring_id', '=', 'mt.monitoring_id')
            ->where('sm.surgery_id', $id)
            ->pluck('mt.monitoring_name');

        $row->analgesia = DB::table('surgery_analgesics as sa')
            ->join('analgesics as a', 'sa.analgesic_id', '=', 'a.analgesic_id')
            ->where('sa.surgery_id', $id)
            ->pluck('a.name');

        $row->events = DB::table('surgery_adverse_events as se')
            ->join('adverse_events as ae', 'se.event_id', '=', 'ae.event_id')
            ->where('se.surgery_id', $id)
            ->pluck('ae.event_name');

        $row->staff = DB::table('surgery_staff as ss')
            ->join('staff as st', 'ss.staff_id', '=', 'st.staff_id')
            ->where('ss.surgery_id', $id)
            ->select('ss.role_in_case', 'st.name')
            ->get();

        // --- NEW: FETCH ANAESTHESIA DATA ---
        $anaesthesia = DB::table('anaesthesia')->where('surgery_id', $id)->first();

        if ($anaesthesia) {
            $aId = $anaesthesia->anaesthesia_id;

            $anaesthesia->inductionAgents = DB::table('anaesthesia_induction_agents as aia')
                ->join('induction_agents as ia', 'aia.induction_agent_id', '=', 'ia.induction_agent_id')
                ->where('aia.anaesthesia_id', $aId)
                ->select('ia.name')
                ->get();

            $anaesthesia->maintenanceAgents = DB::table('anaesthesia_maintenance_agents as ama')
                ->join('maintenance_agents as ma', 'ama.maintenance_agent_id', '=', 'ma.maintenance_agent_id')
                ->where('ama.anaesthesia_id', $aId)
                ->select('ma.name')
                ->get();

            $anaesthesia->relaxants = DB::table('anaesthesia_relaxants as ar')
                ->join('relaxants as r', 'ar.relaxant_id', '=', 'r.relaxant_id')
                ->where('ar.anaesthesia_id', $aId)
                ->select('r.name')
                ->get();

            $anaesthesia->regionalTypes = DB::table('anaesthesia_regional_types as art')
                ->join('regional_types as rt', 'art.regional_type_id', '=', 'rt.regional_type_id')
                ->where('art.anaesthesia_id', $aId)
                ->select('rt.name')
                ->get();
                
            // Fetch our newly added Intubation Methods
            $anaesthesia->intubationMethods = DB::table('anaesthesia_intubation_methods as aim')
                ->join('intubation_methods as im', 'aim.method_id', '=', 'im.method_id')
                ->where('aim.anaesthesia_id', $aId)
                ->select('im.method_name')
                ->get();

            $row->anaesthesia = $anaesthesia;
        } else {
            $row->anaesthesia = null;
        }
    }
}