<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of unique patients with search, sorting, audit counts, and pagination.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $sort = trim((string) $request->query('sort', 'newest_registered'));
        $totalPatientsCount = Patient::count();

        // 1. Fetch all patients with surgery counts and relationships
        $patients = Patient::withCount('surgeries')
            ->with(['surgeries' => function ($query) {
                $query->orderBy('date_of_surgery', 'desc')->orderBy('surgery_id', 'desc');
            }])
            ->get();

        // 2. Filter by search term (matching PHN or decrypted Name)
        if ($search !== '') {
            $searchLower = strtolower($search);

            $patients = $patients->filter(function (Patient $patient) use ($searchLower) {
                $nameMatch = !empty($patient->name) && str_contains(strtolower($patient->name), $searchLower);
                $phnMatch = !empty($patient->phn) && str_contains(strtolower($patient->phn), $searchLower);
                return $nameMatch || $phnMatch;
            });
        }

        // 3. Apply sorting
        $patients = $patients->sort(function (Patient $a, Patient $b) use ($sort) {
            switch ($sort) {
                case 'oldest_registered':
                    return $a->patient_id <=> $b->patient_id;

                case 'name_asc':
                    return strcasecmp($a->name ?? '', $b->name ?? '') ?: ($b->patient_id <=> $a->patient_id);

                case 'name_desc':
                    return strcasecmp($b->name ?? '', $a->name ?? '') ?: ($b->patient_id <=> $a->patient_id);

                case 'phn_asc':
                    return strnatcasecmp($a->phn ?? '', $b->phn ?? '') ?: ($b->patient_id <=> $a->patient_id);

                case 'phn_desc':
                    return strnatcasecmp($b->phn ?? '', $a->phn ?? '') ?: ($b->patient_id <=> $a->patient_id);

                case 'audits_desc':
                    $cmp = $b->surgeries_count <=> $a->surgeries_count;
                    return $cmp !== 0 ? $cmp : ($b->patient_id <=> $a->patient_id);

                case 'audits_asc':
                    $cmp = $a->surgeries_count <=> $b->surgeries_count;
                    return $cmp !== 0 ? $cmp : ($b->patient_id <=> $a->patient_id);

                case 'surgery_desc':
                    $dateA = $a->surgeries->first()?->date_of_surgery;
                    $dateB = $b->surgeries->first()?->date_of_surgery;
                    if ($dateA && $dateB) {
                        $cmp = strcmp($dateB, $dateA);
                        return $cmp !== 0 ? $cmp : ($b->patient_id <=> $a->patient_id);
                    }
                    if ($dateA && !$dateB) return -1; // A has surgery, comes before B
                    if (!$dateA && $dateB) return 1;  // B has surgery, comes before A
                    return $b->patient_id <=> $a->patient_id;

                case 'surgery_asc':
                    $dateA = $a->surgeries->whereNotNull('date_of_surgery')->sortBy('date_of_surgery')->first()?->date_of_surgery;
                    $dateB = $b->surgeries->whereNotNull('date_of_surgery')->sortBy('date_of_surgery')->first()?->date_of_surgery;
                    if ($dateA && $dateB) {
                        $cmp = strcmp($dateA, $dateB);
                        return $cmp !== 0 ? $cmp : ($b->patient_id <=> $a->patient_id);
                    }
                    if ($dateA && !$dateB) return -1;
                    if (!$dateA && $dateB) return 1;
                    return $b->patient_id <=> $a->patient_id;

                case 'newest_registered':
                default:
                    return $b->patient_id <=> $a->patient_id;
            }
        })->values();

        // 4. Server-side pagination with query preservation
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $currentItems = $patients->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedPatients = new LengthAwarePaginator(
            $currentItems,
            $patients->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('patients.index', [
            'patients' => $paginatedPatients,
            'search' => $search,
            'sort' => $sort,
            'totalPatientsCount' => $totalPatientsCount,
        ]);
    }

    /**
     * Autocomplete suggestions endpoint for PHN and Patient Name.
     */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->query('query', $request->query('search', $request->query('q', ''))));

        if ($query === '') {
            return response()->json([]);
        }

        $queryLower = strtolower($query);

        $matches = Patient::withCount('surgeries')
            ->get()
            ->filter(function (Patient $patient) use ($queryLower) {
                $nameMatch = !empty($patient->name) && str_contains(strtolower($patient->name), $queryLower);
                $phnMatch = !empty($patient->phn) && str_contains(strtolower($patient->phn), $queryLower);
                return $nameMatch || $phnMatch;
            })
            ->take(10)
            ->values();

        return response()->json(
            $matches->map(function (Patient $patient) {
                return [
                    'patient_id' => $patient->patient_id,
                    'phn' => $patient->phn ?? 'N/A',
                    'name' => $patient->name ?? 'Unknown',
                    'age' => $patient->age ? $patient->age . 'Y' : 'N/A',
                    'sex' => $patient->sex === 'M' ? 'M' : ($patient->sex === 'F' ? 'F' : ($patient->sex ?? 'N/A')),
                    'bht' => $patient->bht ?? 'N/A',
                    'audits_count' => $patient->surgeries_count,
                    'url' => route('doctor.patients.show', $patient->patient_id),
                ];
            })
        );
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
