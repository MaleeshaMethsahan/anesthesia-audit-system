<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="p-1.5 bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
                        Global Clinical Audit Search
                    </h2>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Multi-parameter filtering & search across all fields of the Anaesthesia Audit Form
                </p>
            </div>

            <!-- Stats Header Badge -->
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    {{ $surgeries->total() }} {{ Str::plural('Record', $surgeries->total()) }} Found
                </span>
                <span class="text-xs text-gray-400 dark:text-gray-500 hidden sm:inline">
                    (Total database: {{ $totalAuditsCount }})
                </span>
            </div>
        </div>
    </x-slot>

    @php
        $routePrefix = auth()->user()->role === 'data_entry' ? 'data_entry.' : 'doctor.';
        $hasActiveFilters = count($activeFilters) > 0;
    @endphp

    <div class="py-8 bg-gray-50/50 dark:bg-gray-900/60 min-h-screen" 
         x-data="{ 
            filtersOpen: {{ $hasActiveFilters ? 'true' : 'false' }},
            activeTab: 'patient',
            diseaseTypeFilter: '{{ request('patient_type', 'all') }}'
         }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- MAIN SEARCH & TOGGLE BAR -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-4 transition-all">
                <form action="{{ route('global-search.index') }}" method="GET" id="globalSearchForm">
                    
                    <!-- Search Input Row -->
                    <div class="flex flex-col sm:flex-row gap-3 items-stretch">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" 
                                   name="q" 
                                   id="mainSearchInput"
                                   value="{{ request('q') }}"
                                   placeholder="Search by Patient Name, PHN, BHT, Operation, Surgeon, Anesthetic, Complication..."
                                   class="w-full pl-11 pr-24 py-3 bg-gray-50 dark:bg-gray-900/70 border border-gray-300 dark:border-gray-600 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-inner">
                            
                            @if(request('q'))
                                <a href="{{ route('global-search.index', array_filter(request()->except('q'))) }}" 
                                   class="absolute right-12 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1"
                                   title="Clear query">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif

                            <span class="hidden md:inline-flex absolute right-3 top-1/2 -translate-y-1/2 text-[0.65rem] font-bold text-gray-400 dark:text-gray-500 px-1.5 py-0.5 rounded bg-gray-200/80 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600">
                                ⌘K / Ctrl+K
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button type="submit" 
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-indigo-500/20 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <span>Search</span>
                            </button>

                            <button type="button" 
                                    @click="filtersOpen = !filtersOpen"
                                    :class="filtersOpen ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 border-indigo-300 dark:border-indigo-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-600'"
                                    class="inline-flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold border transition hover:bg-gray-200 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span>Filters</span>
                                @if(count($activeFilters) > 0)
                                    <span class="ml-0.5 px-1.5 py-0.5 text-[0.65rem] font-black rounded-full bg-indigo-600 text-white">
                                        {{ count($activeFilters) }}
                                    </span>
                                @endif
                                <svg class="w-3.5 h-3.5 transition-transform" :class="filtersOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            @if($hasActiveFilters)
                                <a href="{{ route('global-search.index') }}" 
                                   class="p-3 text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-900/50 transition"
                                   title="Reset all filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- ACTIVE FILTERS BADGES -->
                    @if($hasActiveFilters)
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mr-1">Active Filters:</span>
                            @foreach($activeFilters as $key => $filter)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    <span>{{ $filter['label'] }}</span>
                                    <a href="{{ route('global-search.index', array_filter(request()->except($filter['param']))) }}" 
                                       class="text-indigo-400 hover:text-indigo-800 dark:hover:text-white"
                                       title="Remove filter">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                </span>
                            @endforeach

                            <a href="{{ route('global-search.index') }}" 
                               class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline ml-2">
                                Clear All
                            </a>
                        </div>
                    @endif

                    <!-- EXPANDABLE ADVANCED AUDIT FORM FILTERS PANEL -->
                    <div x-show="filtersOpen" 
                         x-collapse
                         class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-700" 
                         style="display: none;">
                        
                        <!-- Filter Tabs Header -->
                        <div class="flex flex-wrap gap-1 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                            <button type="button" @click="activeTab = 'patient'" 
                                    :class="activeTab === 'patient' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" 
                                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Patient Demographics
                            </button>

                            <button type="button" @click="activeTab = 'surgery'" 
                                    :class="activeTab === 'surgery' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" 
                                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 15.536c-1.171 1.952-3.07-1.567-4.242-2.739-.952-.953-1.184-3.52 0-4.704 1.184-1.185 3.751-.952 4.704 0 1.172 1.172 4.691 3.071 2.739 4.242z"></path></svg>
                                Surgery & Team
                            </button>

                            <button type="button" @click="activeTab = 'diseases'" 
                                    :class="activeTab === 'diseases' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" 
                                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Medical Diseases
                            </button>

                            <button type="button" @click="activeTab = 'anaesthesia'" 
                                    :class="activeTab === 'anaesthesia' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" 
                                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                Anaesthesia & Technique
                            </button>

                            <button type="button" @click="activeTab = 'outcomes'" 
                                    :class="activeTab === 'outcomes' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" 
                                    class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Analgesia & Adverse Events
                            </button>
                        </div>

                        <!-- TAB 1: PATIENT DEMOGRAPHICS -->
                        <div x-show="activeTab === 'patient'" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Sex</label>
                                    <select name="sex" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500">
                                        <option value="">All Sexes</option>
                                        <option value="M" {{ request('sex') == 'M' ? 'selected' : '' }}>Male</option>
                                        <option value="F" {{ request('sex') == 'F' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Age (Min)</label>
                                    <input type="number" name="age_min" value="{{ request('age_min') }}" placeholder="e.g. 18" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Age (Max)</label>
                                    <input type="number" name="age_max" value="{{ request('age_max') }}" placeholder="e.g. 75" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">PHN Number</label>
                                    <input type="text" name="phn" value="{{ request('phn') }}" placeholder="Exact or partial PHN" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">BHT (Bed Head Ticket)</label>
                                    <input type="text" name="bht" value="{{ request('bht') }}" placeholder="BHT code" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: SURGERY & TEAM -->
                        <div x-show="activeTab === 'surgery'" class="space-y-4" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Surgery Type -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Surgery Type</label>
                                    <select name="surgery_type" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">All Types (Em & Elect)</option>
                                        <option value="Em" {{ request('surgery_type') == 'Em' ? 'selected' : '' }}>Emergency (Em)</option>
                                        <option value="Elect" {{ request('surgery_type') == 'Elect' ? 'selected' : '' }}>Elective (Elect)</option>
                                    </select>
                                </div>

                                <!-- Date From -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Surgery Date (From)</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <!-- Date To -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Surgery Date (To)</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </div>

                                <!-- Post-Op Admission -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Post-Op Admission</label>
                                    <div class="flex gap-2">
                                        @foreach($admissionTypes as $adm)
                                            <label class="inline-flex items-center gap-1 text-xs cursor-pointer text-gray-700 dark:text-gray-300">
                                                <input type="checkbox" name="admissions[]" value="{{ $adm }}" {{ in_array($adm, (array)request('admissions', [])) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                                                <span>{{ $adm }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- ASA Grades Selection -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">ASA Physical Status Grade</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($asaGrades as $grade)
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="asa_grades[]" value="{{ $grade }}" {{ in_array($grade, (array)request('asa_grades', [])) ? 'checked' : '' }} class="peer sr-only">
                                            <span class="px-3 py-1 text-xs font-bold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 transition">
                                                ASA {{ $grade }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Clinical Team -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Consultant Surgeon</label>
                                    <select name="surgeon_id" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any Surgeon</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->staff_id }}" {{ request('surgeon_id') == $staff->staff_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Consultant Anaesthetist</label>
                                    <select name="anaesthetist_id" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any Anaesthetist</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->staff_id }}" {{ request('anaesthetist_id') == $staff->staff_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">MOA</label>
                                    <select name="moa_id" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any MOA</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->staff_id }}" {{ request('moa_id') == $staff->staff_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Surgical Specialties (16 options) -->
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Surgical Specialty</label>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($specialties as $spec)
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="specialties[]" value="{{ $spec }}" {{ in_array($spec, (array)request('specialties', [])) ? 'checked' : '' }} class="peer sr-only">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 transition">
                                                {{ $spec }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: MEDICAL DISEASES & CONDITIONS -->
                        <div x-show="activeTab === 'diseases'" class="space-y-4" style="display: none;">
                            <!-- Adult vs Paediatric Filter Toggle -->
                            <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-gray-100 dark:border-gray-700/60">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Filter Conditions By Patient Category:</span>
                                <div class="flex bg-gray-100 dark:bg-gray-700 p-0.5 rounded-lg text-xs">
                                    <button type="button" @click="diseaseTypeFilter = 'all'" :class="diseaseTypeFilter === 'all' ? 'bg-white dark:bg-gray-800 text-indigo-600 font-bold shadow-sm' : 'text-gray-500'" class="px-3 py-1 rounded-md transition">All</button>
                                    <button type="button" @click="diseaseTypeFilter = 'Adult'" :class="diseaseTypeFilter === 'Adult' ? 'bg-white dark:bg-gray-800 text-indigo-600 font-bold shadow-sm' : 'text-gray-500'" class="px-3 py-1 rounded-md transition">Adult Only</button>
                                    <button type="button" @click="diseaseTypeFilter = 'Paediatric'" :class="diseaseTypeFilter === 'Paediatric' ? 'bg-white dark:bg-gray-800 text-indigo-600 font-bold shadow-sm' : 'text-gray-500'" class="px-3 py-1 rounded-md transition">Paediatric Only</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-72 overflow-y-auto pr-1">
                                @foreach($medicalSystems as $system)
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200/80 dark:border-gray-600">
                                        <h5 class="text-xs font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-1 mb-2">
                                            {{ $system->system_name }} ({{ $system->system_code }})
                                        </h5>
                                        <div class="space-y-1.5">
                                            @foreach($system->conditions as $cond)
                                                <label x-show="diseaseTypeFilter === 'all' || diseaseTypeFilter === '{{ $cond->patient_type }}'" class="flex items-start gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                    <input type="checkbox" name="conditions[]" value="{{ $cond->condition_id }}" {{ in_array($cond->condition_id, (array)request('conditions', [])) ? 'checked' : '' }} class="mt-0.5 rounded text-teal-600 focus:ring-teal-500 dark:bg-gray-700">
                                                    <span>{{ $cond->condition_name }} <span class="text-[0.65rem] text-gray-400">({{ substr($cond->patient_type, 0, 1) }})</span></span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- TAB 4: ANAESTHESIA & TECHNIQUE -->
                        <div x-show="activeTab === 'anaesthesia'" class="space-y-4" style="display: none;">
                            <!-- Mode & Route -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Mode of Anaesthesia</label>
                                    <select name="anaesthesia_mode" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any Mode (GA / Regional / Combined)</option>
                                        @foreach($anaesthesiaModes as $m)
                                            <option value="{{ $m }}" {{ request('anaesthesia_mode') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Induction Route</label>
                                    <select name="induction_type" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any (Gas / IV)</option>
                                        <option value="Gas" {{ request('induction_type') == 'Gas' ? 'selected' : '' }}>Gas</option>
                                        <option value="IV" {{ request('induction_type') == 'IV' ? 'selected' : '' }}>IV</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Difficult Intubation</label>
                                    <select name="difficult_intubation" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any</option>
                                        <option value="yes" {{ request('difficult_intubation') === 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ request('difficult_intubation') === 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Relaxant Used</label>
                                    <select name="relaxant_used" class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Any</option>
                                        <option value="yes" {{ request('relaxant_used') === 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ request('relaxant_used') === 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Monitoring Types -->
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Monitoring Employed</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($monitoringTypes as $mon)
                                        <label class="inline-flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer bg-gray-50 dark:bg-gray-700/50 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <input type="checkbox" name="monitoring_types[]" value="{{ $mon->monitoring_id }}" {{ in_array($mon->monitoring_id, (array)request('monitoring_types', [])) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                                            <span>{{ $mon->monitoring_name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Induction Agents, Intubation, Relaxants, Regional Types -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Induction Agents</label>
                                    <div class="space-y-1">
                                        @foreach($inductionAgents as $ia)
                                            <label class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="induction_agents[]" value="{{ $ia->induction_agent_id }}" {{ in_array($ia->induction_agent_id, (array)request('induction_agents', [])) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span>{{ $ia->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Intubation Method</label>
                                    <div class="space-y-1">
                                        @foreach($intubationMethods as $im)
                                            <label class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="intubation_methods[]" value="{{ $im->method_id }}" {{ in_array($im->method_id, (array)request('intubation_methods', [])) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span>{{ $im->method_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Relaxants</label>
                                    <div class="space-y-1">
                                        @foreach($relaxants as $rel)
                                            <label class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="relaxants[]" value="{{ $rel->relaxant_id }}" {{ in_array($rel->relaxant_id, (array)request('relaxants', [])) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span>{{ $rel->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Regional Types</label>
                                    <div class="space-y-1">
                                        @foreach($regionalTypes as $rt)
                                            <label class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="regional_types[]" value="{{ $rt->regional_type_id }}" {{ in_array($rt->regional_type_id, (array)request('regional_types', [])) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700">
                                                <span>{{ $rt->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: ANALGESIA & ADVERSE EVENTS -->
                        <div x-show="activeTab === 'outcomes'" class="space-y-4" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Analgesics -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Analgesic Agents Used</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($analgesics as $alg)
                                            <label class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="analgesics[]" value="{{ $alg->analgesic_id }}" {{ in_array($alg->analgesic_id, (array)request('analgesics', [])) ? 'checked' : '' }} class="rounded text-teal-600 focus:ring-teal-500 dark:bg-gray-700">
                                                <span>{{ $alg->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Adverse Events -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Adverse Events</label>
                                        <div class="flex items-center gap-2">
                                            <label class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400">
                                                <input type="radio" name="has_adverse_events" value="" {{ !request('has_adverse_events') ? 'checked' : '' }} class="text-indigo-600">
                                                <span>Any</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 text-xs text-red-600 dark:text-red-400 font-bold">
                                                <input type="radio" name="has_adverse_events" value="yes" {{ request('has_adverse_events') === 'yes' ? 'checked' : '' }} class="text-red-600">
                                                <span>With Events</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                                                <input type="radio" name="has_adverse_events" value="no" {{ request('has_adverse_events') === 'no' ? 'checked' : '' }} class="text-emerald-600">
                                                <span>No Events</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        @foreach($adverseEvents as $ae)
                                            <label class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="adverse_events[]" value="{{ $ae->event_id }}" {{ in_array($ae->event_id, (array)request('adverse_events', [])) ? 'checked' : '' }} class="rounded text-red-600 focus:ring-red-500 dark:bg-gray-700">
                                                <span>{{ $ae->event_name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Submission Buttons Inside Drawer -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <button type="button" @click="filtersOpen = false" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium">
                                Collapse Filters
                            </button>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('global-search.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 rounded-lg transition">
                                    Reset All
                                </a>
                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow transition">
                                    Apply Filters & Search
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- RESULTS SECTION -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/80 overflow-hidden">
                
                <!-- Table Header Bar -->
                <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Search Results</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                Page {{ $surgeries->currentPage() }} of {{ max(1, $surgeries->lastPage()) }}
                            </span>
                        </h3>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Showing {{ $surgeries->firstItem() ?? 0 }} - {{ $surgeries->lastItem() ?? 0 }} of {{ $surgeries->total() }} matching audits
                    </div>
                </div>

                <!-- SURGERIES RESULTS TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 bg-gray-100/70 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 text-[0.7rem] font-bold">
                            <tr>
                                <th class="px-4 py-3">Patient Details</th>
                                <th class="px-4 py-3">Operation Details</th>
                                <th class="px-4 py-3">Anaesthesia & Technique</th>
                                <th class="px-4 py-3">Medical Conditions</th>
                                <th class="px-4 py-3">Analgesia & Events</th>
                                <th class="px-4 py-3">Clinical Team</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse($surgeries as $surgery)
                                @php
                                    $patient = $surgery->patient;
                                    $anaesthesia = $surgery->anaesthesia;
                                    $hasEvents = $surgery->adverseEvents->isNotEmpty() || !empty($surgery->adverse_event_other_details);

                                    // Color badges helper
                                    $asaColors = [
                                        'I'   => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300 dark:border-emerald-800',
                                        'II'  => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border-blue-300 dark:border-blue-800',
                                        'III' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-300 dark:border-amber-800',
                                        'IV'  => 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-300 dark:border-rose-800',
                                        'V'   => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border-red-300 dark:border-red-800',
                                        'VI'  => 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border-purple-300 dark:border-purple-800',
                                        'E'   => 'bg-red-600 text-white border-red-600',
                                    ];
                                    $asaClass = $asaColors[$surgery->asa_grade] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-300';
                                @endphp

                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40 align-top transition-colors">
                                    <!-- Patient Details -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                                PHN {{ $patient?->phn ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-sm text-gray-900 dark:text-white mt-1 uppercase">
                                            {{ $patient?->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $patient?->age ? $patient->age . 'y' : 'Age -' }} | 
                                            {{ $patient?->sex === 'M' ? 'Male' : ($patient?->sex === 'F' ? 'Female' : '-') }} | 
                                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">BHT {{ $patient?->bht ?? '-' }}</span>
                                        </div>
                                    </td>

                                    <!-- Operation Details -->
                                    <td class="px-4 py-3.5 max-w-xs whitespace-normal">
                                        <div class="font-bold text-sm text-indigo-600 dark:text-indigo-400 leading-snug">
                                            {{ $surgery->operation_name }}
                                        </div>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                            @if($surgery->surgical_specialty)
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                                    {{ $surgery->surgical_specialty }}
                                                </span>
                                            @endif

                                            @if($surgery->surgery_type)
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded {{ $surgery->surgery_type === 'Em' ? 'bg-red-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                                    {{ $surgery->surgery_type === 'Em' ? 'Emergency' : 'Elective' }}
                                                </span>
                                            @endif

                                            @if($surgery->asa_grade)
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded border {{ $asaClass }}">
                                                    ASA {{ $surgery->asa_grade }}
                                                </span>
                                            @endif

                                            @if($surgery->post_op_admission)
                                                <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded {{ $surgery->post_op_admission === 'ICU' ? 'bg-red-600 text-white' : ($surgery->post_op_admission === 'HDU' ? 'bg-amber-500 text-white' : 'bg-blue-600 text-white') }}">
                                                    {{ $surgery->post_op_admission }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[0.7rem] text-gray-400 dark:text-gray-500 mt-1.5 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>{{ $surgery->date_of_surgery ? date('d-M-Y', strtotime($surgery->date_of_surgery)) : 'Date not set' }}</span>
                                        </div>
                                    </td>

                                    <!-- Anaesthesia Technique -->
                                    <td class="px-4 py-3.5 max-w-[200px] whitespace-normal">
                                        @if($anaesthesia)
                                            <div class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1 mb-1">
                                                <span class="px-2 py-0.5 rounded text-[0.7rem] {{ str_contains($anaesthesia->mode, 'GA') ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-500/30' }}">
                                                    {{ $anaesthesia->mode }}
                                                </span>
                                            </div>

                                            <div class="text-[0.7rem] text-gray-500 dark:text-gray-400 space-y-0.5">
                                                @if($anaesthesia->induction_type)
                                                    <div>Induction: <strong class="text-gray-700 dark:text-gray-300">{{ $anaesthesia->induction_type }}</strong></div>
                                                @endif
                                                @if($anaesthesia->maintenance_route)
                                                    <div>Maint: <strong class="text-gray-700 dark:text-gray-300">{{ $anaesthesia->maintenance_route }}</strong></div>
                                                @endif
                                                @if($anaesthesia->difficult_intubation)
                                                    <div class="text-red-600 dark:text-red-400 font-bold">⚠️ Difficult Intubation</div>
                                                @endif
                                                @if($anaesthesia->relaxant_used)
                                                    <div class="text-indigo-600 dark:text-indigo-400">✓ Relaxant Used</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-xs">No technique recorded</span>
                                        @endif

                                        <!-- Monitoring Types -->
                                        @if($surgery->monitoringTypes->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                @foreach($surgery->monitoringTypes as $m)
                                                    <span class="text-[0.6rem] px-1 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 rounded border border-blue-200 dark:border-blue-900/50">
                                                        {{ $m->monitoring_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Medical Conditions -->
                                    <td class="px-4 py-3.5 max-w-[180px] whitespace-normal">
                                        @if($patient && $patient->conditions->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($patient->conditions as $cond)
                                                    <span class="text-[0.65rem] px-1.5 py-0.5 bg-teal-50 dark:bg-teal-950/40 text-teal-800 dark:text-teal-300 rounded border border-teal-200 dark:border-teal-900/50">
                                                        {{ $cond->condition_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">- None recorded -</span>
                                        @endif
                                    </td>

                                    <!-- Analgesia & Adverse Events -->
                                    <td class="px-4 py-3.5 max-w-[180px] whitespace-normal">
                                        <!-- Adverse events -->
                                        <div class="mb-1.5">
                                            @if($hasEvents)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($surgery->adverseEvents as $ae)
                                                        <span class="text-[0.65rem] px-1.5 py-0.5 bg-red-600 text-white font-bold rounded shadow-sm">
                                                            {{ $ae->event_name }}
                                                        </span>
                                                    @endforeach
                                                    @if($surgery->adverse_event_other_details)
                                                        <span class="text-[0.65rem] px-1.5 py-0.5 bg-red-100 text-red-800 rounded border border-red-300">
                                                            {{ Str::limit($surgery->adverse_event_other_details, 20) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-[0.7rem] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    No Adverse Events
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Analgesia -->
                                        @if($surgery->analgesics->isNotEmpty())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($surgery->analgesics as $alg)
                                                    <span class="text-[0.6rem] px-1 py-0.5 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-300 rounded border border-green-200 dark:border-green-800">
                                                        {{ $alg->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Clinical Team -->
                                    <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-400 max-w-[170px] whitespace-normal">
                                        @forelse($surgery->staff as $staff)
                                            <div class="text-[0.7rem] leading-tight mb-1">
                                                <strong class="text-gray-800 dark:text-gray-200">
                                                    {{ $staff->pivot->role_in_case == 'Consultant Surgeon' ? 'Surg' : ($staff->pivot->role_in_case == 'Consultant Anaesthetist' ? 'Anaes' : 'MOA') }}:
                                                </strong>
                                                {{ $staff->name }}
                                            </div>
                                        @empty
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endforelse
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3.5 text-center">
                                        <div class="flex flex-col gap-1.5 items-center w-28 mx-auto">
                                            <a href="{{ route($routePrefix . 'records.show', $surgery->surgery_id) }}" 
                                               class="w-full text-center px-2.5 py-1 text-[0.7rem] font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-500 rounded hover:bg-indigo-600 hover:text-white transition">
                                                View Audit
                                            </a>

                                            <a href="{{ route($routePrefix . 'records.edit', $surgery->surgery_id) }}" 
                                               class="w-full text-center px-2.5 py-1 text-[0.7rem] font-bold text-amber-600 dark:text-amber-400 border border-amber-500 rounded hover:bg-amber-600 hover:text-white transition">
                                                Edit Record
                                            </a>

                                            @if($patient)
                                                <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.patients.show', $patient->patient_id) : '#' }}" 
                                                   class="w-full text-center px-2.5 py-1 text-[0.7rem] font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:underline transition">
                                                    Patient Profile
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                                        <div class="max-w-md mx-auto">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4 text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-base text-gray-800 dark:text-white mb-1">No Matching Audit Records Found</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                                We couldn't find any surgical records matching your selected query or filters.
                                            </p>
                                            @if($hasActiveFilters)
                                                <a href="{{ route('global-search.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 rounded-lg hover:bg-indigo-100 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    Reset All Filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION FOOTER -->
                @if($surgeries->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                        {{ $surgeries->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
