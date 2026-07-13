<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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