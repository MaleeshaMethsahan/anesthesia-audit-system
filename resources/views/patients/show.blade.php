<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('doctor.patients.index') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Back to Patients Directory</span>
                    </a>
                </div>
                <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight uppercase tracking-wider mt-1">
                    Patient Profile
                </h2>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('doctor.patients.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-sky-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Patients Directory</span>
                </a>
                <a href="{{ route('doctor.audit-form.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Add New Record</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        if (!function_exists('getASABadge')) {
            function getASABadge($grade) {
                if (!$grade) return '<span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-400 text-white">N/A</span>';
                $class = "bg-gray-500 text-white";
                if ($grade == 'I') $class = "bg-green-500 text-white";
                if ($grade == 'II') $class = "bg-blue-500 text-white";
                if ($grade == 'III') $class = "bg-amber-500 text-white";
                if (in_array($grade, ['IV', 'V', 'VI', 'E'])) $class = "bg-red-500 text-white";
                return '<span class="px-2.5 py-0.5 rounded text-xs font-bold ' . $class . '">ASA ' . htmlspecialchars($grade) . '</span>';
            }
        }

        if (!function_exists('getAdmissionBadge')) {
            function getAdmissionBadge($type) {
                if (!$type) return '<span class="text-xs text-gray-400">N/A</span>';
                $class = "bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600";
                if ($type == 'ICU') $class = "bg-red-600 text-white border-red-600";
                if ($type == 'HDU') $class = "bg-amber-500 text-white border-amber-500";
                if ($type == 'Ward') $class = "bg-blue-600 text-white border-blue-600";
                return '<span class="px-2.5 py-0.5 rounded text-xs font-bold ' . $class . '">' . htmlspecialchars($type) . '</span>';
            }
        }
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Patient Demographics & Conditions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/90 border-b border-gray-200 dark:border-gray-700 flex flex-wrap justify-between items-center gap-2">
                    <div>
                        <span class="text-xs uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Clinical Patient Profile</span>
                        <h3 class="text-xl font-extrabold text-gray-900 dark:text-white uppercase tracking-wide">
                            {{ $patient->name ?? 'Unknown Patient' }}
                        </h3>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-200 border border-rose-200 dark:border-rose-800">
                            PHN: {{ $patient->phn ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Age & Gender -->
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Age & Gender</p>
                        <p class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            {{ $patient->age ? $patient->age . ' Years' : 'N/A' }} / 
                            <span class="{{ $patient->sex === 'M' ? 'text-blue-600 dark:text-blue-400 font-bold' : ($patient->sex === 'F' ? 'text-pink-600 dark:text-pink-400 font-bold' : '') }}">
                                {{ $patient->sex === 'M' ? 'Male' : ($patient->sex === 'F' ? 'Female' : ($patient->sex ?? 'N/A')) }}
                            </span>
                        </p>
                    </div>

                    <!-- BHT Number -->
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">BHT Number</p>
                        <p class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            {{ $patient->bht ?? ($patient->surgeries->first()->bht ?? 'Not recorded') }}
                        </p>
                    </div>

                    <!-- First Registered -->
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">First Registered</p>
                        <p class="text-base font-semibold text-gray-800 dark:text-gray-200">
                            {{ $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('d-M-Y') : 'N/A' }}
                        </p>
                    </div>

                    <!-- Total Records -->
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Surgeries</p>
                        <p class="text-base font-bold text-purple-600 dark:text-purple-400">
                            {{ $totalAudits }} {{ $totalAudits === 1 ? 'Record' : 'Records' }}
                        </p>
                    </div>

                </div>

                <!-- Medical Conditions Section -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/40 border-t border-gray-200 dark:border-gray-700/80">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Medical Conditions</p>
                    <div class="flex flex-wrap gap-2 items-center">
                        @forelse($patient->conditions as $cond)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 shadow-sm">
                                {{ $cond->condition_name }}
                            </span>
                        @empty
                            <span class="text-xs italic text-gray-500 dark:text-gray-400">
                                No medical conditions recorded.
                            </span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Patient Summary Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Audits</p>
                        <h4 class="text-2xl font-black text-gray-900 dark:text-white mt-0.5">{{ $totalAudits }}</h4>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">First Surgery</p>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ $firstSurgeryDate }}</h4>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Latest Surgery</p>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ $latestSurgeryDate }}</h4>
                    </div>
                </div>
            </div>

            <!-- Surgery & Audit History Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/90 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Surgery & Audit History</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Chronological history of perioperative audit records (newest first)</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        {{ $patient->surgeries->count() }} Case(s)
                    </span>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($patient->surgeries as $surgery)
                        @php
                            $surgeryDateFormatted = $surgery->date_of_surgery
                                ? \Carbon\Carbon::parse($surgery->date_of_surgery)->format('d-M-Y')
                                : ($surgery->created_at ? \Carbon\Carbon::parse($surgery->created_at)->format('d-M-Y') : 'N/A');
                            $anaesthesiaMode = $surgery->anaesthesia ? $surgery->anaesthesia->mode : 'N/A';
                        @endphp
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            
                            <!-- Surgery Core Info -->
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $surgeryDateFormatted }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200">
                                        {{ $surgery->surgery_type === 'Em' ? 'Emergency' : ($surgery->surgery_type === 'Elect' ? 'Elective' : ($surgery->surgery_type ?? 'N/A')) }}
                                    </span>
                                    {!! getASABadge($surgery->asa_grade) !!}
                                    {!! getAdmissionBadge($surgery->post_op_admission) !!}
                                </div>

                                <div>
                                    <h4 class="text-base font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                                        {{ $surgery->operation_name }}
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span><strong class="text-gray-700 dark:text-gray-300">Specialty:</strong> {{ $surgery->surgical_specialty ?? 'General Surgery' }}</span>
                                        <span>•</span>
                                        <span><strong class="text-gray-700 dark:text-gray-300">Anaesthesia:</strong> {{ $anaesthesiaMode }}</span>
                                        <span>•</span>
                                        <span><strong class="text-gray-700 dark:text-gray-300">Record #:</strong> #{{ $surgery->surgery_id }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3 self-start lg:self-center flex-shrink-0">
                                <a 
                                    href="{{ route('doctor.records.show', $surgery->surgery_id) }}" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>VIEW RECORD</span>
                                </a>
                            </div>

                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="font-medium text-sm">No audit records found for this patient.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
