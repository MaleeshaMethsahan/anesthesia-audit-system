<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                    Surgical Statistics
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                    Surgical activity · trends · period comparison · short-term forecasts &mdash; based on historical audit data
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>{{ \Carbon\Carbon::parse($filterFrom)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($filterTo)->format('d M Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen text-slate-800 dark:text-slate-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 1. FILTER SECTION                                               --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"></path></svg>
                        <h4 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Filter Statistics</h4>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mr-1 hidden sm:inline">Presets:</span>
                        <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except(['date_from', 'date_to']), ['period' => '30d'])) }}"
                           class="px-2.5 py-1 rounded-lg font-bold transition {{ request('period') === '30d' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">30D</a>
                        <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except(['date_from', 'date_to']), ['period' => '90d'])) }}"
                           class="px-2.5 py-1 rounded-lg font-bold transition {{ request('period') === '90d' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">90D</a>
                        <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except(['date_from', 'date_to']), ['period' => '6m'])) }}"
                           class="px-2.5 py-1 rounded-lg font-bold transition {{ request('period') === '6m' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">6M</a>
                        <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except(['date_from', 'date_to']), ['period' => '1y'])) }}"
                           class="px-2.5 py-1 rounded-lg font-bold transition {{ (request('period') === '1y' || (!request('period') && !request('date_from'))) ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">1Y</a>
                    </div>
                </div>
                <form method="GET" action="{{ route('doctor.surgical-statistics.index') }}" class="px-6 py-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                        {{-- Date From --}}
                        <div class="lg:col-span-1">
                            <label for="date_from" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Date From</label>
                            <input type="date"
                                   id="date_from"
                                   name="date_from"
                                   value="{{ $filterFrom }}"
                                   class="w-full bg-slate-100 dark:bg-slate-700 border-none rounded-xl text-sm py-2.5 px-3 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-slate-900 transition">
                        </div>

                        {{-- Date To --}}
                        <div class="lg:col-span-1">
                            <label for="date_to" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Date To</label>
                            <input type="date"
                                   id="date_to"
                                   name="date_to"
                                   value="{{ $filterTo }}"
                                   class="w-full bg-slate-100 dark:bg-slate-700 border-none rounded-xl text-sm py-2.5 px-3 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-slate-900 transition">
                        </div>

                        {{-- Surgical Specialty --}}
                        <div class="lg:col-span-1">
                            <label for="specialty" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Specialty</label>
                            <select id="specialty"
                                    name="specialty"
                                    class="w-full bg-slate-100 dark:bg-slate-700 border-none rounded-xl text-sm py-2.5 px-3 dark:text-white focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-slate-900 transition">
                                <option value="all" {{ $specialty === 'all' ? 'selected' : '' }}>All Specialties</option>
                                @foreach($allSpecialties as $spec)
                                    <option value="{{ $spec }}" {{ $specialty === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Case Type --}}
                        <div class="lg:col-span-1">
                            <label for="case_type" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Case Type</label>
                            <select id="case_type"
                                    name="case_type"
                                    class="w-full bg-slate-100 dark:bg-slate-700 border-none rounded-xl text-sm py-2.5 px-3 dark:text-white focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-slate-900 transition">
                                <option value="all"   {{ $caseType === 'all'   ? 'selected' : '' }}>All Cases</option>
                                <option value="Em"    {{ $caseType === 'Em'    ? 'selected' : '' }}>Emergency</option>
                                <option value="Elect" {{ $caseType === 'Elect' ? 'selected' : '' }}>Elective</option>
                            </select>
                        </div>

                        {{-- ASA Grade --}}
                        <div class="lg:col-span-1">
                            <label for="asa_grade" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">ASA Grade</label>
                            <div class="flex items-center gap-2">
                                <select id="asa_grade"
                                        name="asa_grade"
                                        class="flex-1 bg-slate-100 dark:bg-slate-700 border-none rounded-xl text-sm py-2.5 px-3 dark:text-white focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-slate-900 transition">
                                    <option value="all" {{ $asaFilter === 'all' ? 'selected' : '' }}>All ASA Grades</option>
                                    @foreach(['I','II','III','IV','V','VI','E'] as $grade)
                                        <option value="{{ $grade }}" {{ $asaFilter === $grade ? 'selected' : '' }}>ASA {{ $grade }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    Apply
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- Active filter badges --}}
                    @if($specialty !== 'all' || $caseType !== 'all' || $asaFilter !== 'all')
                        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-slate-100 dark:border-gray-700/60">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Active Filters:</span>
                            @if($specialty !== 'all')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                    {{ $specialty }}
                                    <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except('specialty'), ['specialty' => 'all'])) }}" class="ml-1 hover:text-purple-900 dark:hover:text-white">×</a>
                                </span>
                            @endif
                            @if($caseType !== 'all')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    {{ $caseType === 'Em' ? 'Emergency' : 'Elective' }}
                                    <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except('case_type'), ['case_type' => 'all'])) }}" class="ml-1 hover:text-blue-900 dark:hover:text-white">×</a>
                                </span>
                            @endif
                            @if($asaFilter !== 'all')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    ASA {{ $asaFilter }}
                                    <a href="{{ route('doctor.surgical-statistics.index', array_merge(request()->except('asa_grade'), ['asa_grade' => 'all'])) }}" class="ml-1 hover:text-amber-900 dark:hover:text-white">×</a>
                                </span>
                            @endif
                            <a href="{{ route('doctor.surgical-statistics.index') }}" class="text-xs font-bold text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-white underline ml-2">Clear all</a>
                        </div>
                    @endif
                </form>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 2. OVERVIEW STATISTIC CARDS                                    --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

                {{-- Total Surgeries --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between col-span-2 md:col-span-1 lg:col-span-1">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total</span>
                            <div class="p-1.5 bg-indigo-100 dark:bg-indigo-950/60 rounded-lg">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalSurgeries }}</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Surgeries</div>
                    </div>
                    <div class="mt-3 text-xs text-slate-400 dark:text-slate-500">{{ $monthsSpan }} month{{ $monthsSpan !== 1 ? 's' : '' }} period</div>
                </div>

                {{-- Emergency --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Emergency</span>
                            <div class="p-1.5 bg-red-100 dark:bg-red-950/60 rounded-lg">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                        </div>
                        @if($caseType === 'Elect')
                            {{-- User filtered to Elective-only: emergency count is always 0 inside that filter. Show greyed out. --}}
                            <div class="text-3xl font-black text-slate-300 dark:text-slate-600">—</div>
                            <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">Not in filter</div>
                        @else
                            <div class="text-3xl font-black text-red-600 dark:text-red-400">{{ $emergencyCount }}</div>
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Cases</div>
                        @endif
                    </div>
                    <div class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                        @if($caseType === 'Elect')
                            Elective-only filter active
                        @elseif($caseType === 'Em')
                            All {{ $emergencyCount }} cases are emergency
                        @else
                            {{ $totalSurgeries > 0 ? round(($emergencyCount / $totalSurgeries) * 100, 1) : 0 }}% of total
                        @endif
                    </div>
                </div>

                {{-- Elective --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Elective</span>
                            <div class="p-1.5 bg-blue-100 dark:bg-blue-950/60 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        @if($caseType === 'Em')
                            {{-- User filtered to Emergency-only: elective count is always 0 inside that filter. Show greyed out. --}}
                            <div class="text-3xl font-black text-slate-300 dark:text-slate-600">—</div>
                            <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 mt-1">Not in filter</div>
                        @else
                            <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $electiveCount }}</div>
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Cases</div>
                        @endif
                    </div>
                    <div class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                        @if($caseType === 'Em')
                            Emergency-only filter active
                        @elseif($caseType === 'Elect')
                            All {{ $electiveCount }} cases are elective
                        @else
                            {{ $totalSurgeries > 0 ? round(($electiveCount / $totalSurgeries) * 100, 1) : 0 }}% of total
                        @endif
                    </div>
                </div>

                {{-- High-Risk --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">High-Risk</span>
                            <div class="p-1.5 bg-amber-100 dark:bg-amber-950/60 rounded-lg">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $highRiskCount }}</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">ASA III–VI/E</div>
                    </div>
                    <div class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                        {{ $highRiskRate }}% of total
                    </div>
                </div>

                {{-- Avg/Month --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Avg / Month</span>
                            <div class="p-1.5 bg-emerald-100 dark:bg-emerald-950/60 rounded-lg">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $avgPerMonth }}</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Cases / Month</div>
                    </div>
                    <div class="mt-3 text-xs text-slate-400 dark:text-slate-500">Over {{ $monthsSpan }} months</div>
                </div>

                {{-- Emergency Rate --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Em. Rate</span>
                            <div class="p-1.5 bg-purple-100 dark:bg-purple-950/60 rounded-lg">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-black text-purple-600 dark:text-purple-400">{{ $emergencyRate }}%</div>
                        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1">Emergency Rate</div>
                    </div>
                    <div class="mt-3 text-xs @if($caseType === 'all' && $emergencyRate > 35) text-red-500 dark:text-red-400 font-bold @else text-slate-400 dark:text-slate-500 @endif">
                        @if($caseType === 'Em')
                            Emergency-only filter active
                        @elseif($caseType === 'Elect')
                            Elective-only filter active
                        @elseif($emergencyRate > 35)
                            Above 35% threshold
                        @elseif($emergencyRate > 0)
                            Within normal range
                        @else
                            No emergency data
                        @endif
                    </div>
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 3. SURGICAL VOLUME TREND + EMERGENCY vs ELECTIVE               --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Volume Trend (line chart: actual + forecast) --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-600 rounded-xl text-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Surgical Volume Trend</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">Monthly cases — actual &amp; forecast</p>
                            </div>
                        </div>
                        @if($forecastReady)
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 flex-shrink-0">
                                Forecast included
                            </span>
                        @endif
                    </div>

                    {{-- Legend --}}
                    <div class="flex items-center gap-4 mt-3 mb-4">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                            <span class="inline-block w-6 h-0.5 bg-indigo-500 rounded"></span> Actual
                        </div>
                        @if($forecastReady)
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                                <span class="inline-block w-6 h-0.5 border-t-2 border-dashed border-purple-400 rounded"></span> Estimated (Forecast)
                            </div>
                        @endif
                    </div>

                    @if($totalSurgeries === 0)
                        <div class="flex items-center justify-center h-56 text-slate-400 dark:text-slate-500 text-sm italic">
                            No surgical records found for this period.
                        </div>
                    @else
                        <div class="relative h-56 w-full overflow-hidden">
                            <canvas id="volumeTrendChart"></canvas>
                        </div>
                        @if(!$forecastReady && !empty($forecastMessage))
                            <p class="mt-3 text-xs text-slate-400 dark:text-slate-500 italic text-center">{{ $forecastMessage }}</p>
                        @endif
                    @endif
                </div>

                {{-- Emergency vs Elective Stacked Bar --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-600 rounded-xl text-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Emergency vs Elective Trend</h3>
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    @if($caseTypeFilterActive)
                                        Filtered to {{ $caseType === 'Em' ? 'Emergency only' : 'Elective only' }} · breakdown not applicable
                                    @else
                                        Monthly breakdown by case type
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex items-center gap-4 mt-3 mb-4">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                            <span class="w-3 h-3 rounded-sm bg-red-500 inline-block"></span> Emergency
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                            <span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span> Elective
                        </div>
                    </div>

                    @if($totalSurgeries === 0)
                        <div class="flex items-center justify-center h-56 text-slate-400 dark:text-slate-500 text-sm italic">
                            No surgical records found for this period.
                        </div>
                    @else
                        <div class="relative h-56 w-full overflow-hidden">
                            <canvas id="emergencyElectiveChart"></canvas>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 4. SPECIALTY DISTRIBUTION + ASA DISTRIBUTION                   --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Specialty Distribution (Horizontal Bar) --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-600 rounded-xl text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">Specialty Distribution</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Cases by surgical specialty</p>
                        </div>
                    </div>
                    @if(empty($specialtyDistribution))
                        <div class="flex items-center justify-center h-48 text-slate-400 dark:text-slate-500 text-sm italic">
                            No specialty data available.
                        </div>
                    @else
                        <div class="relative h-48 w-full overflow-hidden">
                            <canvas id="specialtyBarChart"></canvas>
                        </div>
                    @endif
                </div>

                {{-- ASA Classification & High-Risk Trend --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-amber-500 rounded-xl text-white shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">ASA Classification &amp; Risk Profile</h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Physical status stratification &amp; high-risk volume</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $highRiskCount > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' }}">
                                {{ $highRiskCount }} High-Risk
                            </span>
                        </div>

                        @if(empty($asaDistribution))
                            <div class="flex items-center justify-center h-48 text-slate-400 dark:text-slate-500 text-sm italic">
                                No ASA data available.
                            </div>
                        @else
                            {{-- Low-Risk vs High-Risk Summary --}}
                            @php
                                $totalAsaGraded = array_sum($asaDistribution);
                                $lowRiskCount   = max(0, $totalAsaGraded - $highRiskCount);
                                $lowRiskPct     = $totalAsaGraded > 0 ? round(($lowRiskCount / $totalAsaGraded) * 100) : 0;
                                $highRiskPct    = $totalAsaGraded > 0 ? round(($highRiskCount / $totalAsaGraded) * 100) : 0;
                            @endphp
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700">
                                    <div class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">Low-Risk (ASA I–II)</div>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-lg font-black text-slate-800 dark:text-white">{{ $lowRiskCount }}</span>
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $lowRiskPct }}%</span>
                                    </div>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-100 dark:border-gray-700">
                                    <div class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider">High-Risk (ASA III–VI/E)</div>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-lg font-black text-slate-800 dark:text-white">{{ $highRiskCount }}</span>
                                        <span class="text-xs font-bold {{ $highRiskCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $highRiskPct }}%</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ASA Grade Badges --}}
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($asaDistribution as $grade => $count)
                                    @php
                                        $isHigh = in_array($grade, ['III','IV','V','VI','E']);
                                        $badgeClass = $isHigh
                                            ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800'
                                            : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-xs font-bold border {{ $badgeClass }}">
                                        ASA {{ $grade }}: <span class="font-black">{{ $count }}</span>
                                    </span>
                                @endforeach
                            </div>

                            {{-- High-Risk Monthly Trend Mini Chart --}}
                            <div>
                                <div class="text-[0.7rem] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                    @if($asaFilter !== 'all')
                                        ASA {{ $asaFilter }} Monthly Trend
                                    @else
                                        High-Risk Monthly Volume Trend
                                    @endif
                                </div>
                                <div class="relative h-28 w-full overflow-hidden">
                                    <canvas id="highRiskTrendChart"></canvas>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 5. PERIOD COMPARISON                                            --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-600 rounded-xl text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Period Comparison</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Current period versus the immediately preceding equivalent period</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex flex-col items-end text-xs text-slate-400 dark:text-slate-500 font-semibold">
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold">Current: {{ $periodComparison['current_period_label'] }}</span>
                        <span class="text-slate-400">Previous: {{ $periodComparison['previous_period_label'] }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-gray-700 bg-slate-100/70 dark:bg-gray-700/80 text-slate-600 dark:text-slate-200 text-xs font-bold">
                            <tr>
                                <th class="px-6 py-3 text-left">Metric</th>
                                <th class="px-6 py-3 text-center">Current Period</th>
                                <th class="px-6 py-3 text-center">Previous Period</th>
                                <th class="px-6 py-3 text-center">Absolute Change</th>
                                <th class="px-6 py-3 text-center">% Change</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                            @php
                                // Dynamically build comparison rows, skipping those made meaningless by active case-type filter.
                                // e.g. when case_type=Em, 'Elective Surgeries' would always show 0 vs 0.
                                $compRows = [
                                    ['label' => 'Total Surgeries',     'cur' => $periodComparison['current_total'],  'prev' => $periodComparison['previous_total'],  'diff' => $periodComparison['diff_total'],  'pct' => $periodComparison['pct_total'],  'skip' => false],
                                    ['label' => 'Emergency Surgeries', 'cur' => $periodComparison['current_emerg'],  'prev' => $periodComparison['previous_emerg'],  'diff' => $periodComparison['diff_emerg'],  'pct' => $periodComparison['pct_emerg'],  'skip' => $caseType === 'Elect'],
                                    ['label' => 'Elective Surgeries',  'cur' => $periodComparison['current_elect'],  'prev' => $periodComparison['previous_elect'],  'diff' => $periodComparison['diff_elect'],  'pct' => $periodComparison['pct_elect'],  'skip' => $caseType === 'Em'],
                                    ['label' => 'High-Risk Cases',     'cur' => $periodComparison['current_hr'],     'prev' => $periodComparison['previous_hr'],     'diff' => $periodComparison['diff_hr'],     'pct' => $periodComparison['pct_hr'],     'skip' => ($asaFilter !== 'all' && !in_array($asaFilter, ['III','IV','V','VI','E']))],
                                ];
                            @endphp
                            @foreach($compRows as $row)
                                @if($row['skip'])
                                    @continue
                                @endif
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white text-sm">{{ $row['label'] }}</td>
                                    <td class="px-6 py-4 text-center text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $row['cur'] }}</td>
                                    <td class="px-6 py-4 text-center font-semibold text-slate-500 dark:text-slate-400">{{ $row['prev'] }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @php $diff = $row['diff']; @endphp
                                        <span class="font-bold text-sm {{ $diff > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($diff < 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-400 dark:text-slate-500') }}">
                                            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                            @if($diff > 0) <span>↑</span> @elseif($diff < 0) <span>↓</span> @else <span>→</span> @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($row['pct'] !== null)
                                            @php $pct = $row['pct']; @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-black {{ $pct > 0 ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : ($pct < 0 ? 'bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400') }}">
                                                {{ $pct > 0 ? '+' : '' }}{{ $pct }}%
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 text-xs italic">No prior data</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-3 bg-slate-50/50 dark:bg-gray-800/50 border-t border-slate-100 dark:border-gray-700/60 text-xs text-slate-400 dark:text-slate-500 italic">
                    Previous period: {{ $periodComparison['previous_period_label'] }}. Percentages are rounded to one decimal place.
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 6. FORECAST SECTION                                             --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-600 rounded-xl text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Surgical Activity Forecast</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Statistical estimates based on historical audit data only · not clinical predictions</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($forecastReady && $forecastSlope !== null)
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $forecastSlope > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : ($forecastSlope < 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700') }}">
                                Trend: {{ $forecastSlope > 0 ? '+' : '' }}{{ $forecastSlope }} cases/mo
                            </span>
                        @endif
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 flex-shrink-0">
                            Least Squares (OLS)
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    @if(!$forecastReady)
                        <div class="flex flex-col items-center justify-center py-8 text-center gap-3">
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-semibold">{{ $forecastMessage ?: 'Not enough historical data to generate a forecast.' }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 max-w-sm">Extend the date range to include at least 3 months of historical records with non-zero surgical counts.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            @foreach($forecastData as $idx => $val)
                                <div class="bg-purple-50 dark:bg-purple-950/30 rounded-xl p-5 border border-purple-200 dark:border-purple-800/50 text-center">
                                    <div class="text-xs font-bold uppercase tracking-wider text-purple-500 dark:text-purple-400 mb-1">{{ $forecastLabels[$idx] ?? 'Forecast +'.($idx+1) }}</div>
                                    <div class="text-4xl font-black text-purple-700 dark:text-purple-300">{{ $val }}</div>
                                    <div class="text-xs text-purple-500 dark:text-purple-400 font-semibold mt-1">Estimated cases</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="space-y-1">
                                <p class="text-xs text-amber-800 dark:text-amber-200 font-semibold">
                                    {{ $forecastConfidence ?? 'Ordinary Least Squares projection' }}
                                </p>
                                <p class="text-xs text-amber-700 dark:text-amber-300 font-medium leading-relaxed">
                                    These are <strong>statistical estimates</strong> calculated by ordinary least squares linear regression based on the {{ $monthsSpan }} monthly observations in the selected period.
                                    They are intended for audit and operational planning reference only and are <strong>not clinical predictions or guarantees</strong>.
                                    Actual future volumes may differ due to clinical scheduling, staffing, or unobserved factors.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 7. AUTOMATED INSIGHTS                                           --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            @if(!empty($insights))
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex items-center gap-3">
                        <div class="p-2 bg-indigo-600 rounded-xl text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Automated Insights</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Rule-based analysis from real database statistics — not AI-generated</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($insights as $insight)
                            @php
                                $bgClass = match($insight['type']) {
                                    'warn' => 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/50',
                                    'good' => 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/50',
                                    'bad'  => 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-800/50',
                                    default => 'bg-slate-50 dark:bg-gray-700/40 border-slate-200 dark:border-gray-600/50',
                                };
                                $textClass = match($insight['type']) {
                                    'warn' => 'text-amber-800 dark:text-amber-200',
                                    'good' => 'text-emerald-800 dark:text-emerald-200',
                                    'bad'  => 'text-red-800 dark:text-red-200',
                                    default => 'text-slate-700 dark:text-slate-300',
                                };
                            @endphp
                            <div class="flex items-start gap-3 p-4 rounded-xl border {{ $bgClass }}">
                                <span class="text-xl flex-shrink-0 leading-none">{{ $insight['icon'] }}</span>
                                <p class="text-sm font-medium {{ $textClass }} leading-relaxed">{{ $insight['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════════════ --}}
            {{-- 8. TOP PROCEDURES + COMORBIDITY (Side by Side)                 --}}
            {{-- ═══════════════════════════════════════════════════════════════ --}}
            @if(!empty($topProcedures) || !empty($comorbidityData))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Top Procedures --}}
                    @if(!empty($topProcedures))
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex items-center gap-3">
                                <div class="p-2 bg-blue-600 rounded-xl text-white shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Most Common Procedures</h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Top {{ count($topProcedures) }} operations by frequency in selected period</p>
                                </div>
                            </div>
                            <div class="divide-y divide-slate-100 dark:divide-gray-700">
                                @php $maxCount = $topProcedures[0]['count'] ?? 1; @endphp
                                @foreach($topProcedures as $idx => $proc)
                                    <div class="px-6 py-3.5 flex items-center gap-4">
                                        <span class="text-xs font-black text-slate-400 dark:text-slate-500 w-5 text-right flex-shrink-0">{{ $idx + 1 }}</span>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $proc['operation_name'] }}">
                                                {{ $proc['operation_name'] }}
                                            </div>
                                            <div class="mt-1 h-1.5 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ round(($proc['count'] / $maxCount) * 100) }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-black text-blue-600 dark:text-blue-400 flex-shrink-0 tabular-nums">{{ $proc['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Comorbidity Chart --}}
                    @if(!empty($comorbidityData))
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex items-center gap-3">
                                <div class="p-2 bg-rose-600 rounded-xl text-white shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Comorbidity Profile</h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Medical system frequency among surgical patients</p>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="relative h-52 w-full overflow-hidden">
                                    <canvas id="comorbidityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    @elseif(!empty($topProcedures))
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden flex flex-col justify-between">
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 bg-slate-50/50 dark:bg-gray-800/90 flex items-center gap-3">
                                <div class="p-2 bg-rose-600 rounded-xl text-white shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Comorbidity Profile</h3>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Medical system frequency among surgical patients</p>
                                </div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col items-center justify-center text-center gap-2">
                                <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No comorbidities recorded</p>
                                <p class="text-[0.7rem] text-slate-400 dark:text-slate-500 max-w-xs">No medical system conditions are associated with the patients in this filtered cohort.</p>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

            {{-- 9. AUDIT FOOTER --}}
            <div class="pt-6 pb-2 text-center text-xs font-medium text-slate-400 dark:text-slate-500 border-t border-slate-200/60 dark:border-gray-800">
                Department of Anaesthesia &bull; Teaching Hospital Jaffna &bull; Surgical Statistics Audit
            </div>

        </div>
    </div>

    {{-- Chart.js — placed inside x-app-layout so it is part of $slot, not orphaned outside --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isDark    = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#9ca3af' : '#64748b';
            const gridColor = isDark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(226, 232, 240, 0.7)';

            // ── Raw data from PHP ────────────────────────────────────────────
            const trendLabels    = {!! json_encode($trendLabels)    !!};
            const trendData      = {!! json_encode($trendData)      !!};
            const trendEmergency = {!! json_encode($trendEmergency) !!};
            const trendElective  = {!! json_encode($trendElective)  !!};
            const forecastLabels = {!! json_encode($forecastLabels) !!};
            const forecastData   = {!! json_encode($forecastData)   !!};
            const forecastReady  = {!! json_encode($forecastReady)  !!};

            const specialtyDist   = {!! json_encode($specialtyDistribution) !!};
            const asaDist         = {!! json_encode($asaDistribution)       !!};
            const highRiskTrend   = {!! json_encode($highRiskTrend)         !!};
            const comorbidityData = {!! json_encode($comorbidityData)       !!};

            // ── Colour palette ───────────────────────────────────────────────
            const palette = ['#4f46e5','#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];

            // Safe canvas getter — returns null when canvas is conditionally absent from DOM
            function gc(id) { return document.getElementById(id); }

            // ── 1. SURGICAL VOLUME TREND ──────────────────────────────────────
            const volumeEl = gc('volumeTrendChart');
            if (volumeEl && trendLabels.length > 0) {
                const ctx  = volumeEl.getContext('2d');
                const grad = ctx.createLinearGradient(0, 0, 0, 220);
                grad.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
                grad.addColorStop(1, 'rgba(99, 102, 241, 0.03)');

                const allLabels   = [...trendLabels, ...forecastLabels];
                const actualPad   = forecastReady ? new Array(forecastLabels.length).fill(null) : [];
                // Anchor forecast at the last actual data point so the line connects seamlessly
                const forecastPad = (forecastReady && trendData.length > 0)
                    ? [...new Array(trendData.length - 1).fill(null), trendData[trendData.length - 1]]
                    : [];

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: allLabels,
                        datasets: [
                            {
                                label: 'Actual Surgeries',
                                data: [...trendData, ...actualPad],
                                borderColor: '#6366f1',
                                backgroundColor: grad,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#6366f1',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                spanGaps: false
                            },
                            ...(forecastReady ? [{
                                label: 'Estimated (Forecast)',
                                data: [...forecastPad, ...forecastData],
                                borderColor: '#a855f7',
                                backgroundColor: 'rgba(168, 85, 247, 0.08)',
                                borderWidth: 2.5,
                                borderDash: [8, 4],
                                fill: false,
                                tension: 0.3,
                                pointBackgroundColor: '#a855f7',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                spanGaps: false
                            }] : [])
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 150,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } },
                            x: { ticks: { color: textColor, maxRotation: 45 }, grid: { display: false } }
                        },
                        plugins: {
                            legend: {
                                display: forecastReady,
                                labels: { color: textColor, boxWidth: 14, font: { size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(c) {
                                        if (c.raw === null) return '';
                                        if (c.dataset.label === 'Estimated (Forecast)' && c.dataIndex === trendData.length - 1) {
                                            return ' ' + c.dataset.label + ': ' + c.raw + ' (Baseline)';
                                        }
                                        return ' ' + c.dataset.label + ': ' + c.raw;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ── 2. EMERGENCY vs ELECTIVE STACKED BAR ────────────────────────
            const emElectEl = gc('emergencyElectiveChart');
            if (emElectEl && trendLabels.length > 0) {
                new Chart(emElectEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: trendLabels,
                        datasets: [
                            { label: 'Emergency', data: trendEmergency, backgroundColor: 'rgba(239, 68, 68, 0.85)', borderRadius: { topLeft: 4, topRight: 4 } },
                            { label: 'Elective',  data: trendElective,  backgroundColor: 'rgba(59, 130, 246, 0.85)', borderRadius: { topLeft: 4, topRight: 4 } }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 150,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            x: { stacked: true, ticks: { color: textColor, maxRotation: 45 }, grid: { display: false } },
                            y: { stacked: true, beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }
                        },
                        plugins: { legend: { labels: { color: textColor, boxWidth: 14, font: { size: 11 } } } }
                    }
                });
            }

            // ── 3. SPECIALTY HORIZONTAL BAR ──────────────────────────────────
            const specEl = gc('specialtyBarChart');
            if (specEl && Object.keys(specialtyDist).length > 0) {
                new Chart(specEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(specialtyDist),
                        datasets: [{ data: Object.values(specialtyDist), backgroundColor: palette.slice(0, Object.keys(specialtyDist).length), borderRadius: 6 }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 150,
                        scales: {
                            x: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } },
                            y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } }
                        },
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.raw + ' cases' } } }
                    }
                });
            }

            // ── 4. ASA / HIGH-RISK TREND MINI LINE ───────────────────────────
            const hrTrendEl = gc('highRiskTrendChart');
            const asaFilter = "{{ $asaFilter }}";
            const isHighRisk = asaFilter === 'all' || ['III','IV','V','VI','E'].includes(asaFilter);
            const lineColor  = isHighRisk ? '#f59e0b' : '#10b981';
            const areaColor  = isHighRisk ? 'rgba(245,158,11,0.12)' : 'rgba(16,185,129,0.12)';
            const seriesName = asaFilter === 'all' ? 'High-Risk Cases' : 'ASA ' + asaFilter + ' Cases';

            if (hrTrendEl && highRiskTrend.length > 0) {
                new Chart(hrTrendEl.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: seriesName,
                            data: highRiskTrend,
                            borderColor: lineColor,
                            backgroundColor: areaColor,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: lineColor
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 150,
                        scales: {
                            x: { display: false },
                            y: { beginAtZero: true, ticks: { color: textColor, precision: 0, maxTicksLimit: 4 }, grid: { color: gridColor } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: c => ' ' + seriesName + ': ' + (c.raw !== null ? c.raw : '—') } }
                        }
                    }
                });
            }

            // ── 5. COMORBIDITY HORIZONTAL BAR ────────────────────────────────
            const comorbEl = gc('comorbidityChart');
            if (comorbEl && Object.keys(comorbidityData).length > 0) {
                new Chart(comorbEl.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(comorbidityData),
                        datasets: [{ label: 'Patients', data: Object.values(comorbidityData), backgroundColor: 'rgba(244,63,94,0.75)', borderRadius: 6 }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 150,
                        scales: {
                            x: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } },
                            y: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } }
                        },
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.raw + ' patients' } } }
                    }
                });
            }
        });
    </script>

</x-app-layout>

