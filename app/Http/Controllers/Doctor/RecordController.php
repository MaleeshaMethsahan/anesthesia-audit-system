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
                'p.patient_id', 'p.name', 'p.age', 'p.sex', 'p.bht'
            )
            ->orderBy('s.surgery_id', 'desc')
            ->get();

        // 2. Fetch sub-data for each surgery (Replicating your getList function)
        foreach ($surgeries as $row) {
            if (isset($row->name) && !empty($row->name)) {
                try {
                    $row->name = Crypt::decryptString($row->name);
                } catch (\Throwable $e) {
                    // If not encrypted or decryption fails, leave original string
                }
            }

            $id = $row->surgery_id;
            $patient_id = $row->patient_id;

            $row->diseases = DB::table('patient_conditions as pc')
                ->join('medical_conditions as mc', 'pc.condition_id', '=', 'mc.condition_id')
                ->where('pc.patient_id', $patient_id)
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
        }

        return view('records.index', compact('surgeries'));
    }
}