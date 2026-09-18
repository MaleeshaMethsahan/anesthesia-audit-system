<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                    Patient Anaesthesia History
                </h2>
            </div>
            <div class="flex gap-3">
                @if($patient)
                <a href="{{ route('doctor.reports.patient-history.pdf', ['phn' => $phn]) }}"
                   class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download PDF
                </a>
                @endif
                <a href="{{ route('doctor.reports.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow transition">
                    ← Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            <!-- PHN Search Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                <h3 class="text-sm font-extrabold uppercase tracking-widest text-slate-700 dark:text-white mb-4">Search by Patient PHN</h3>
                <form method="GET" action="{{ route('doctor.reports.patient-history') }}" class="flex gap-3">
                    <input type="text" name="phn" value="{{ $phn }}" placeholder="Enter Patient Hospital Number (PHN)…"
                        id="phnSearchInput"
                        class="flex-1 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-4 py-2.5 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-sm transition">
                        Lookup
                    </button>
                </form>
            </div>

            @if($phn && !$patient)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 text-amber-800 dark:text-amber-300 font-semibold text-sm">
                    No patient found with PHN: <strong>{{ $phn }}</strong>
                </div>
            @endif

            @if($patient)

                {{-- ===== SAFETY BANNERS ===== --}}
                @if($hasDifficultAirway)
                <div class="bg-red-600 text-white rounded-2xl p-5 flex items-center gap-4 shadow-lg">
                    <svg class="w-8 h-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-extrabold text-lg">⚠ DIFFICULT AIRWAY ALERT</p>
                        <p class="text-sm text-red-100 mt-0.5">This patient has a documented history of <strong>difficult intubation</strong>. Review prior airway management details below before proceeding.</p>
                    </div>
                </div>
                @endif

                @if($hasAdverseEvents)
                <div class="bg-orange-500 text-white rounded-2xl p-5 flex items-center gap-4 shadow-lg">
                    <svg class="w-8 h-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-extrabold text-lg">⚠ ADVERSE EVENTS ON RECORD</p>
                        <p class="text-sm text-orange-100 mt-0.5">Previous anaesthetic adverse events are documented for this patient. Review below.</p>
                    </div>
                </div>
                @endif

                {{-- Patient Header --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-2xl flex-shrink-0">
                                {{ strtoupper(substr($patient->name ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $patient->name }}</h3>
                                <div class="flex gap-4 text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    <span>PHN: <strong class="text-slate-700 dark:text-slate-200">{{ $patient->phn }}</strong></span>
                                    <span>BHT: <strong class="text-slate-700 dark:text-slate-200">{{ $patient->bht ?? 'N/A' }}</strong></span>
                                    <span>Age: <strong class="text-slate-700 dark:text-slate-200">{{ $patient->age ?? 'N/A' }}</strong></span>
                                    <span>Sex: <strong class="text-slate-700 dark:text-slate-200">{{ $patient->sex ?? 'N/A' }}</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $surgeries->count() }}</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Procedures</div>
                        </div>
                    </div>

                    @if($patient->conditions->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-gray-700">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Comorbidities</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($patient->conditions as $cond)
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-gray-700 text-xs font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-gray-600">{{ $cond->condition_name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Surgery History --}}
                @forelse($surgeries as $surgery)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ optional($surgery->anaesthesia)->difficult_intubation ? 'border-red-300 dark:border-red-700' : 'border-slate-200/80 dark:border-gray-700/80' }} shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-50/50 dark:bg-gray-800/90">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-500 dark:text-slate-400">{{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</span>
                            <span class="font-extrabold text-slate-900 dark:text-white">{{ $surgery->operation_name }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $surgery->surgery_type === 'Em' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}">{{ $surgery->surgery_type === 'Em' ? 'Emergency' : 'Elective' }}</span>
                            @if(optional($surgery->anaesthesia)->difficult_intubation)
                                <span class="px-2.5 py-0.5 rounded-full bg-red-600 text-white text-xs font-bold">⚠ Difficult Airway</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('doctor.reports.case', $surgery->surgery_id) }}" class="text-xs font-bold px-3 py-1.5 border border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 rounded-lg hover:bg-indigo-600 hover:text-white transition">Full Report</a>
                            <a href="{{ route('doctor.reports.case.pdf', $surgery->surgery_id) }}" class="text-xs font-bold px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">PDF</a>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div><p class="text-xs text-slate-400 font-semibold uppercase">Specialty</p><p class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $surgery->surgical_specialty ?? 'N/A' }}</p></div>
                        <div><p class="text-xs text-slate-400 font-semibold uppercase">ASA Grade</p><p class="font-bold text-slate-800 dark:text-white mt-0.5">{{ $surgery->asa_grade ?? 'N/A' }}</p></div>
                        <div><p class="text-xs text-slate-400 font-semibold uppercase">Anaesthesia Mode</p><p class="font-bold text-slate-800 dark:text-white mt-0.5">{{ optional($surgery->anaesthesia)->mode ?? 'N/A' }}</p></div>
                        <div><p class="text-xs text-slate-400 font-semibold uppercase">Post-Op</p>
                            @php $adm = $surgery->post_op_admission; $admClass = $adm === 'ICU' ? 'text-red-600 dark:text-red-400' : ($adm === 'HDU' ? 'text-amber-500 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400'); @endphp
                            <p class="font-bold {{ $admClass }} mt-0.5">{{ $adm ?? 'N/A' }}</p>
                        </div>
                        @if($surgery->adverseEvents->isNotEmpty())
                        <div class="col-span-2 md:col-span-4">
                            <p class="text-xs text-red-500 font-bold uppercase mb-1.5">Adverse Events</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($surgery->adverseEvents as $ae)
                                    <span class="px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-xs font-bold border border-red-200 dark:border-red-800">{{ $ae->event_name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-10 text-center">
                    <p class="text-slate-400 dark:text-slate-500 text-sm">No surgical/anaesthesia records found for this patient.</p>
                </div>
                @endforelse

            @endif
        </div>
    </div>
</x-app-layout>
