<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                    Case Audit Report
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Surgery #{{ $surgery->surgery_id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('doctor.reports.case.pdf', $surgery->surgery_id) }}"
                   class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download PDF
                </a>
                <a href="{{ route('doctor.reports.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow transition">
                    ← Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @php
                $patient   = $surgery->patient;
                $anaes     = $surgery->anaesthesia;
            @endphp

            <!-- Section 1: Patient & Surgery -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Patient Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-4">Patient Information</h3>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Name</dt><dd class="font-bold text-slate-900 dark:text-white">{{ $patient->name ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">PHN</dt><dd class="font-bold text-slate-900 dark:text-white">{{ $patient->phn ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">BHT</dt><dd class="font-bold text-slate-900 dark:text-white">{{ $patient->bht ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Age</dt><dd class="font-bold text-slate-900 dark:text-white">{{ $patient->age ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Sex</dt><dd class="font-bold text-slate-900 dark:text-white">{{ $patient->sex ?? 'N/A' }}</dd></div>
                    </dl>
                    @if($patient && $patient->conditions->isNotEmpty())
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

                <!-- Surgery Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-4">Surgery Details</h3>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Date</dt><dd class="font-bold text-slate-900 dark:text-white">{{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Operation</dt><dd class="font-bold text-slate-900 dark:text-white text-right max-w-[60%]">{{ $surgery->operation_name }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500 dark:text-slate-400">Specialty</dt><dd class="font-bold text-slate-900 dark:text-white">{{ $surgery->surgical_specialty ?? 'N/A' }}</dd></div>
                        <div class="flex justify-between items-center"><dt class="text-slate-500 dark:text-slate-400">Type</dt>
                            <dd><span class="px-3 py-1 rounded-full text-xs font-bold {{ $surgery->surgery_type === 'Em' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}">{{ $surgery->surgery_type === 'Em' ? 'Emergency' : 'Elective' }}</span></dd>
                        </div>
                        <div class="flex justify-between items-center"><dt class="text-slate-500 dark:text-slate-400">ASA Grade</dt>
                            <dd><span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $surgery->asa_grade ?? 'N/A' }}</span></dd>
                        </div>
                        <div class="flex justify-between items-center"><dt class="text-slate-500 dark:text-slate-400">Post-Op</dt>
                            @php $adm = $surgery->post_op_admission; $admClass = $adm === 'ICU' ? 'bg-red-600 text-white' : ($adm === 'HDU' ? 'bg-amber-500 text-white' : 'bg-blue-600 text-white'); @endphp
                            <dd><span class="px-3 py-1 rounded-full text-xs font-bold {{ $admClass }}">{{ $adm ?? 'N/A' }}</span></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Section 2: Anaesthesia -->
            @if($anaes)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-5">Anaesthesia Details</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-5">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700">
                        <div class="text-xs text-slate-400 font-semibold uppercase mb-1">Mode</div>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $anaes->mode ?? 'N/A' }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700">
                        <div class="text-xs text-slate-400 font-semibold uppercase mb-1">Induction Type</div>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $anaes->induction_type ?? 'N/A' }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700">
                        <div class="text-xs text-slate-400 font-semibold uppercase mb-1">Maintenance Route</div>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $anaes->maintenance_route ?? 'N/A' }}</div>
                    </div>
                    <div class="p-3 rounded-xl {{ $anaes->difficult_intubation ? 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800' : 'bg-slate-50 dark:bg-gray-700/40 border-slate-100 dark:border-gray-700' }} border">
                        <div class="text-xs text-slate-400 font-semibold uppercase mb-1">Difficult Intubation</div>
                        <div class="font-bold {{ $anaes->difficult_intubation ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">
                            {{ $anaes->difficult_intubation ? '⚠ YES' : 'No' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    @if($anaes->inductionAgents->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Induction Agents</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($anaes->inductionAgents as $a)<span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-xs font-semibold text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">{{ $a->name }}</span>@endforeach
                        </div>
                    </div>
                    @endif
                    @if($anaes->maintenanceAgents->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Maintenance Agents</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($anaes->maintenanceAgents as $a)<span class="px-2.5 py-1 rounded-full bg-cyan-50 dark:bg-cyan-900/30 text-xs font-semibold text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">{{ $a->name }}</span>@endforeach
                        </div>
                    </div>
                    @endif
                    @if($anaes->relaxants->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Relaxants</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($anaes->relaxants as $r)<span class="px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 text-xs font-semibold text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">{{ $r->name }}</span>@endforeach
                        </div>
                    </div>
                    @endif
                    @if($anaes->intubationMethods->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Intubation Methods</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($anaes->intubationMethods as $m)<span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-xs font-semibold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">{{ $m->method_name }}</span>@endforeach
                        </div>
                    </div>
                    @endif
                    @if($anaes->regionalTypes->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Regional Types</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($anaes->regionalTypes as $rt)<span class="px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-900/30 text-xs font-semibold text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">{{ $rt->name }}</span>@endforeach
                        </div>
                    </div>
                    @endif
                    @if($anaes->notes)
                    <div class="md:col-span-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Notes</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 italic">{{ $anaes->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Section 3: Monitoring, Analgesics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-4">Monitoring Used</h3>
                    @if($surgery->monitoringTypes->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($surgery->monitoringTypes as $mt)
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-gray-700 text-xs font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-gray-600">{{ $mt->monitoring_name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-400">None recorded.</p>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-4">Analgesics</h3>
                    @if($surgery->analgesics->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($surgery->analgesics as $analgesic)
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-gray-700 text-xs font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-gray-600">{{ $analgesic->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-400">None recorded.</p>
                    @endif
                </div>
            </div>

            <!-- Section 4: Adverse Events -->
            @if($surgery->adverseEvents->isNotEmpty())
            <div class="bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-800 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Adverse Events
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($surgery->adverseEvents as $ae)
                        <span class="px-3 py-1.5 rounded-full bg-red-600 text-white text-xs font-bold shadow">{{ $ae->event_name }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Section 5: Staff -->
            @if($surgery->staff->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-indigo-600 dark:text-indigo-400 mb-4">Clinical Staff</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($surgery->staff as $member)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm flex-shrink-0">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $member->name }}</div>
                            <div class="text-xs text-slate-400">{{ $member->pivot->role_in_case }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
