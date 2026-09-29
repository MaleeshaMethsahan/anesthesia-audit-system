<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                    Anaesthesia Audit Record
                </p>

                <h2 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    Record #{{ $surgery->surgery_id }}
                </h2>

                <div class="mt-4 flex flex-wrap items-center text-sm" style="column-gap: 40px; row-gap: 10px;">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Patient:</span>
                        <span class="ml-2 font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->name }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">PHN:</span>
                        <span class="ml-2 font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->phn ?? 'Not recorded' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Surgery Date:</span>
                        <span class="ml-2 font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->date_of_surgery
                                ? \Carbon\Carbon::parse($surgery->date_of_surgery)->format('d M Y')
                                : 'Not recorded'
                            }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ auth()->user()->role === 'doctor'
                        ? route('doctor.records.index')
                        : route('data_entry.records.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-sky-500/20 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Records
                </a>

                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>

                <a
                    href="{{ auth()->user()->role === 'doctor'
                        ? route('doctor.records.edit', $surgery->surgery_id)
                        : route('data_entry.records.edit', $surgery->surgery_id) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-amber-500/20 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Record
                </a>
            </div>

        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/90 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Patient Details
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Patient Name
                        </p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->name ?? 'Not recorded' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            PHN
                        </p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->phn ?? 'Not recorded' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Age / Sex
                        </p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->age ?? 'Not recorded' }} / {{ $surgery->sex ?? 'Not recorded' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            BHT
                        </p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $surgery->bht ?? 'Not recorded' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/90 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Surgery Details
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Operation:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 ml-1">{{ $surgery->operation_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Specialty:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 ml-1">{{ $surgery->surgical_specialty }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Surgery Type:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 ml-1">{{ $surgery->surgery_type }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Date:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 ml-1">{{ date('d-M-Y', strtotime($surgery->date_of_surgery)) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">ASA Grade:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 ml-1">{{ $surgery->asa_grade ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Post-op Admission:</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 ml-1">{{ $surgery->post_op_admission ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($surgery->anaesthesia))
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/90 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Anaesthesia Details
                    </h3>
                </div>

                <div class="p-6">
                    <div class="mb-4 flex items-center text-sm">
                        <span class="font-medium text-gray-500 dark:text-gray-400">Mode:</span>
                        <span class="ml-2 inline-flex items-center rounded-full
                            {{ $surgery->anaesthesia->mode == 'GA'
                                ? 'bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300'
                                : ($surgery->anaesthesia->mode == 'Regional' ? 'bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-300' : 'bg-purple-100 dark:bg-purple-900/60 text-purple-800 dark:text-purple-300')
                            }}
                            px-3 py-1 font-semibold">
                            {{ $surgery->anaesthesia->mode == 'GA'
                                ? 'General Anaesthesia'
                                : ($surgery->anaesthesia->mode == 'Regional' ? 'Regional Anaesthesia' : 'GA + Regional Anaesthesia')
                            }}
                        </span>
                    </div>

                    @if(in_array($surgery->anaesthesia->mode, ['GA', 'GA + Regional']))
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 mb-4">
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-4">
                            <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">Induction</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Type:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->induction_type ?? 'Not recorded' }}</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Agents:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->inductionAgents->pluck('name')->implode(', ') ?: 'Not recorded' }}</span>
                                </div>
                                @if($surgery->anaesthesia->induction_other_details)
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Other:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->induction_other_details }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-4">
                            <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">Maintenance</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Route:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->maintenance_route ?? 'Not recorded' }}</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Agents:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->maintenanceAgents->pluck('name')->implode(', ') ?: 'Not recorded' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-4">
                            <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">Relaxants</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Used:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->relaxant_used ? 'Yes' : 'No' }}</span>
                                </div>
                                @if($surgery->anaesthesia->relaxant_used)
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Agents:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->relaxants->pluck('name')->implode(', ') ?: 'Not recorded' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-4">
                            <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">Difficult Intubation</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 65px;">Status:</span>
                                    <span class="font-semibold {{ $surgery->anaesthesia->difficult_intubation ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }}">{{ $surgery->anaesthesia->difficult_intubation ? 'Yes' : 'No' }}</span>
                                </div>
                                @if($surgery->anaesthesia->difficult_intubation)
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 65px;">Methods:</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->intubationMethods->pluck('method_name')->implode(', ') ?: 'Not recorded' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(in_array($surgery->anaesthesia->mode, ['Regional', 'GA + Regional']))
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-4 mb-4">
                        <h4 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">Regional Details</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Types:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->regionalTypes->pluck('name')->implode(', ') ?: 'Not recorded' }}</span>
                            </div>
                            @if($surgery->anaesthesia->regional_other_details)
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 55px;">Other:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $surgery->anaesthesia->regional_other_details }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($surgery->anaesthesia->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 block mb-1">Additional Notes</span>
                        <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $surgery->anaesthesia->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-3 text-base font-bold text-gray-800 dark:text-white">Medical Conditions</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($surgery->diseases as $condition)
                            <span class="inline-flex items-center rounded-md border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/40 px-3 py-1 text-sm font-medium text-indigo-800 dark:text-indigo-300">
                                {{ $condition }}
                            </span>
                        @empty
                            <span class="text-sm italic text-gray-400 dark:text-gray-500">
                                None recorded
                            </span>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-3 text-base font-bold text-gray-800 dark:text-white">Monitoring</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($surgery->monitor as $monitor)
                            <span class="inline-flex items-center rounded-md border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/40 px-3 py-1 text-sm font-medium text-indigo-800 dark:text-indigo-300">
                                {{ $monitor }}
                            </span>
                        @empty
                            <span class="text-sm italic text-gray-400 dark:text-gray-500">
                                No monitoring data recorded
                            </span>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-3 text-base font-bold text-gray-800 dark:text-white">Analgesia</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($surgery->analgesia as $analgesic)
                            <span class="inline-flex items-center rounded-md border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/40 px-3 py-1 text-sm font-medium text-indigo-800 dark:text-indigo-300">
                                {{ $analgesic }}
                            </span>
                        @empty
                            <span class="text-sm italic text-gray-400 dark:text-gray-500">
                                No analgesia recorded
                            </span>
                        @endforelse

                        @if($surgery->analgesia_other_details)
                            <span class="inline-flex items-center rounded-md border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/40 px-3 py-1 text-sm font-medium text-amber-800 dark:text-amber-300">
                                Other details: {{ $surgery->analgesia_other_details }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h4 class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-3 text-base font-bold text-gray-800 dark:text-white">Adverse Events</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($surgery->events as $event)
                            <span class="inline-flex items-center rounded-md border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/40 px-3 py-1 text-sm font-medium text-indigo-800 dark:text-indigo-300">
                                {{ $event }}
                            </span>
                        @empty
                            <span class="text-sm italic text-gray-400 dark:text-gray-500">
                                No adverse events recorded
                            </span>
                        @endforelse

                        @if($surgery->adverse_event_other_details)
                            <span class="inline-flex items-center rounded-md border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/40 px-3 py-1 text-sm font-medium text-red-700 dark:text-red-300">
                                Other details: {{ $surgery->adverse_event_other_details }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h4 class="text-base font-bold text-gray-800 dark:text-white mb-3">Clinical Team</h4>
                <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                    @forelse($surgery->staff as $staffMember)
                        <li class="flex items-start">
                            <span class="font-semibold text-gray-800 dark:text-gray-200" style="width: 190px;">
                                {{ $staffMember->role_in_case }}:
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $staffMember->name }}
                            </span>
                        </li>
                    @empty
                        <li class="text-gray-400 dark:text-gray-500">
                            No staff data recorded.
                        </li>
                    @endforelse
                </ul>
            </div>
            
        </div>
    </div>
</x-app-layout>