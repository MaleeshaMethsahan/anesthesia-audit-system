<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of unique patients with search, audit counts, and pagination.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $totalPatientsCount = Patient::count();

        if ($search !== '') {
            $searchLower = strtolower($search);

            // Fetch all patients and filter by decrypted name or plain-text PHN
            $matchingPatientIds = Patient::all()->filter(function (Patient $patient) use ($searchLower) {
                $nameMatch = !empty($patient->name) && str_contains(strtolower($patient->name), $searchLower);
                $phnMatch = !empty($patient->phn) && str_contains(strtolower($patient->phn), $searchLower);
                return $nameMatch || $phnMatch;
            })->pluck('patient_id');

            $patients = Patient::whereIn('patient_id', $matchingPatientIds)
                ->withCount('surgeries')
                ->with(['surgeries' => function ($query) {
                    $query->orderBy('date_of_surgery', 'desc')->orderBy('surgery_id', 'desc');
                }])
                ->orderBy('patient_id', 'desc')
                ->paginate(15)
                ->withQueryString();
        } else {
            $patients = Patient::withCount('surgeries')
                ->with(['surgeries' => function ($query) {
                    $query->orderBy('date_of_surgery', 'desc')->orderBy('surgery_id', 'desc');
                }])
                ->orderBy('patient_id', 'desc')
                ->paginate(15)
                ->withQueryString();
        }

        return view('patients.index', compact('patients', 'search', 'totalPatientsCount'));
    }

    /**
     * Display the specified patient profile with summary statistics and surgery history.
     */
    public function show(int $patientId)
    {
        $patient = Patient::with([
            'conditions',
            'surgeries' => function ($query) {
                $query->with('anaesthesia')
                    ->orderBy('date_of_surgery', 'desc')
                    ->orderBy('surgery_id', 'desc');
            }
        ])->findOrFail($patientId);

        $surgeriesWithDate = $patient->surgeries->filter(fn($s) => !empty($s->date_of_surgery));
        
        $totalAudits = $patient->surgeries->count();
        $firstSurgery = $surgeriesWithDate->sortBy('date_of_surgery')->first() ?? $patient->surgeries->last();
        $latestSurgery = $surgeriesWithDate->sortByDesc('date_of_surgery')->first() ?? $patient->surgeries->first();

        $firstSurgeryDate = $firstSurgery && $firstSurgery->date_of_surgery
            ? Carbon::parse($firstSurgery->date_of_surgery)->format('d-M-Y')
            : ($firstSurgery ? Carbon::parse($firstSurgery->created_at)->format('d-M-Y') : 'N/A');

        $latestSurgeryDate = $latestSurgery && $latestSurgery->date_of_surgery
            ? Carbon::parse($latestSurgery->date_of_surgery)->format('d-M-Y')
            : ($latestSurgery ? Carbon::parse($latestSurgery->created_at)->format('d-M-Y') : 'N/A');

        return view('patients.show', compact(
            'patient',
            'totalAudits',
            'firstSurgeryDate',
            'latestSurgeryDate'
        ));
    }
}
