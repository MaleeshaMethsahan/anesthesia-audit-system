<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                Difficult Airway Registry
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('doctor.reports.difficult-airway.pdf', request()->query()) }}"
                   class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center gap-2">
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-5">
                <form method="GET" action="{{ route('doctor.reports.difficult-airway') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                            class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                            class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Specialty</label>
                        <select name="specialty" class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none">
                            <option value="">All Specialties</option>
                            @foreach($specialties as $spec)
                                <option value="{{ $spec }}" {{ $filters['specialty'] === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-sm transition">
                        Filter
                    </button>
                    <a href="{{ route('doctor.reports.difficult-airway') }}" class="px-5 py-2 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-lg text-sm transition">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Results -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 flex justify-between items-center bg-slate-50/50 dark:bg-gray-800/90">
                    <div>
                        <h4 class="font-extrabold text-base text-slate-900 dark:text-white uppercase tracking-wider">Difficult Airway Cases</h4>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $surgeries->count() }} case(s) found</p>
                    </div>
                    <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-xs font-bold rounded-full border border-amber-200 dark:border-amber-800">
                        ⚠ Airway Registry
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-gray-700 bg-slate-100/70 dark:bg-gray-700/80 text-slate-600 dark:text-slate-200 text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Patient</th>
                                <th class="px-4 py-3">Operation</th>
                                <th class="px-4 py-3">Specialty</th>
                                <th class="px-4 py-3">ASA</th>
                                <th class="px-4 py-3">Intubation Method</th>
                                <th class="px-4 py-3">Induction Agents</th>
                                <th class="px-4 py-3">Relaxants</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                            @forelse($surgeries as $surgery)
                            <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors">
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400 font-medium">{{ $surgery->date_of_surgery ? date('d M Y', strtotime($surgery->date_of_surgery)) : 'N/A' }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-white">
                                    <div>{{ $surgery->patient->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400 font-normal">PHN: {{ $surgery->patient->phn ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300 max-w-xs truncate">{{ $surgery->operation_name }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $surgery->surgical_specialty ?? 'N/A' }}</td>
                                <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-bold">{{ $surgery->asa_grade ?? 'N/A' }}</span></td>
                                <td class="px-4 py-3">
                                    @foreach(optional($surgery->anaesthesia)->intubationMethods ?? [] as $m)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-semibold mr-1">{{ $m->method_name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">
                                    @foreach(optional($surgery->anaesthesia)->inductionAgents ?? [] as $a)
                                        <span class="text-xs text-slate-600 dark:text-slate-300 mr-1">{{ $a->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3">
                                    @foreach(optional($surgery->anaesthesia)->relaxants ?? [] as $r)
                                        <span class="text-xs text-slate-600 dark:text-slate-300 mr-1">{{ $r->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-center space-x-1">
                                    <a href="{{ route('doctor.reports.case', $surgery->surgery_id) }}" class="text-xs font-bold px-3 py-1.5 border border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400 rounded-lg hover:bg-indigo-600 hover:text-white transition">View</a>
                                    <a href="{{ route('doctor.reports.case.pdf', $surgery->surgery_id) }}" class="text-xs font-bold px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">PDF</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center text-slate-400 dark:text-slate-500 text-sm">
                                    No difficult airway cases found for the selected filters.
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
