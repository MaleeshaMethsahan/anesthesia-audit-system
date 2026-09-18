<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                    My Case Log
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Dr. {{ $doctorName }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('doctor.reports.my-cases.pdf', request()->query()) }}"
                   class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download Case Log PDF
                </a>
                <a href="{{ route('doctor.reports.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow transition">
                    ← Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-5">
                <form method="GET" action="{{ route('doctor.reports.my-cases') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                            class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                            class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-sm transition">Filter</button>
                    <a href="{{ route('doctor.reports.my-cases') }}" class="px-5 py-2 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-lg text-sm transition">Reset</a>
                </form>
            </div>

            @if(isset($noAssignments) && $noAssignments)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 text-amber-800 dark:text-amber-300 font-semibold text-sm">
                ⚠ You currently have no assigned cases.
            </div>
            @endif

            <!-- KPI Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalCases }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Total Cases</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-800 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-red-600 dark:text-red-400">{{ $emCases }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Emergency</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-blue-200 dark:border-blue-800 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $electiveCases }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Elective</div>
                </div>
            </div>

            <!-- Cases Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/90 flex justify-between items-center">
                    <h4 class="font-extrabold text-base text-slate-900 dark:text-white uppercase tracking-wider">Case Log</h4>
                    <span class="text-xs text-slate-400">{{ $surgeries->count() }} case(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-gray-700 bg-slate-100/70 dark:bg-gray-700/80 text-slate-600 dark:text-slate-200 text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Patient</th>
                                <th class="px-4 py-3">Operation</th>
                                <th class="px-4 py-3">Specialty</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">ASA</th>
                                <th class="px-4 py-3">Anaesthesia</th>
                                <th class="px-4 py-3">Post-Op</th>
                                <th class="px-4 py-3">My Role</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                            @forelse($surgeries as $surgery)
                            @php
                                $myRole = $surgery->staff->firstWhere('name', $doctorName)?->pivot->role_in_case ?? 'Staff';
                                $hasAdverse = $surgery->adverseEvents->isNotEmpty();
                                $adm = $surgery->post_op_admission;
                                $admClass = $adm === 'ICU' ? 'bg-red-600 text-white' : ($adm === 'HDU' ? 'bg-amber-500 text-white' : 'bg-blue-600 text-white');
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-700/50 transition-colors {{ $hasAdverse ? 'bg-red-50/30 dark:bg-red-900/5' : '' }}">
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-medium">{{ date('d M Y', strtotime($surgery->date_of_surgery)) }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">
                                    <div>{{ $surgery->patient->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400 font-normal">{{ $surgery->patient->phn ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300 max-w-xs truncate">{{ $surgery->operation_name }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $surgery->surgical_specialty ?? 'N/A' }}</td>
                                <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $surgery->surgery_type === 'Em' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}">{{ $surgery->surgery_type === 'Em' ? 'Em' : 'Elect' }}</span></td>
                                <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-bold">{{ $surgery->asa_grade ?? 'N/A' }}</span></td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ optional($surgery->anaesthesia)->mode ?? 'N/A' }}</td>
                                <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $admClass }}">{{ $adm ?? 'N/A' }}</span></td>
                                <td class="px-4 py-3"><span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $myRole }}</span></td>
                                <td class="px-4 py-3 text-center space-x-1">
                                    <a href="{{ route('doctor.reports.case', $surgery->surgery_id) }}" class="text-xs font-bold px-3 py-1.5 border border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 rounded-lg hover:bg-indigo-600 hover:text-white transition">View</a>
                                    <a href="{{ route('doctor.reports.case.pdf', $surgery->surgery_id) }}" class="text-xs font-bold px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">PDF</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500 text-sm">
                                    No cases found for the selected date range.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
