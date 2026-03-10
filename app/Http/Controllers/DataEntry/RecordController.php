<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\AnesthesiaRecord;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index()
    {
        $records = AnesthesiaRecord::where('created_by', auth()->id())
            ->orderByDesc('surgery_date')
            ->get();

        return view('data_entry.records.index', compact('records'));
    }

    public function create()
    {
        return view('data_entry.records.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ip_number'          => 'required|string|max:50',
            'patient_name'       => 'required|string|max:255',
            'age'                => 'required|integer|min:0|max:150',
            'gender'             => 'required|in:Male,Female,Other',
            'surgery_date'       => 'required|date',
            'diagnosis'          => 'required|string|max:255',
            'procedure'          => 'required|string|max:255',
            'anesthesia_type'    => 'required|in:GA,Spinal,Epidural,Regional,Local',
            'surgeon'            => 'nullable|string|max:255',
            'anesthetist'        => 'nullable|string|max:255',
            'duration_minutes'   => 'nullable|integer|min:0',
            'adverse_events'     => 'nullable|string',
            'post_op_destination'=> 'nullable|in:Ward,ICU,HDU,Recovery,Home',
            'notes'              => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        AnesthesiaRecord::create($validated);

        return redirect()->route('data_entry.records.index')
            ->with('success', 'Record added successfully.');
    }
}
