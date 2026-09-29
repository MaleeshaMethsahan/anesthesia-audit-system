<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                    Patients Directory
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Registered patients and their clinical audit history
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('doctor.audit-form.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Add New Record</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            <!-- Compact Toolbar: Stats, Search with Autocomplete & Sort Controls -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
                <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
                    
                    <!-- Left: Dynamic Patient Counters -->
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-md text-xs font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span>{{ $totalPatientsCount }} Registered Patients</span>
                        </span>

                        @if(!empty($search))
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                <span>Search Results:</span>
                                <strong class="text-gray-900 dark:text-white font-bold">{{ $patients->total() }}</strong>
                            </span>
                        @endif
                    </div>

                    <!-- Right: Search Form with Autocomplete & Sort Selector -->
                    <form method="GET" action="{{ route('doctor.patients.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        
                        <!-- Search Box with Live Autocomplete Suggestions -->
                        <div 
                            x-data="{
                                query: '{{ addslashes($search ?? '') }}',
                                suggestions: [],
                                isOpen: false,
                                isLoading: false,
                                debounceTimer: null,
                                selectedIndex: -1,
                                fetchSuggestions() {
                                    clearTimeout(this.debounceTimer);
                                    if (!this.query || this.query.trim().length < 1) {
                                        this.suggestions = [];
                                        this.isOpen = false;
                                        return;
                                    }
                                    this.debounceTimer = setTimeout(() => {
                                        this.isLoading = true;
                                        fetch('{{ route('doctor.patients.suggestions') }}?query=' + encodeURIComponent(this.query.trim()))
                                            .then(res => res.json())
                                            .then(data => {
                                                this.suggestions = data || [];
                                                this.isOpen = this.suggestions.length > 0;
                                                this.selectedIndex = -1;
                                                this.isLoading = false;
                                            })
                                            .catch(() => {
                                                this.suggestions = [];
                                                this.isOpen = false;
                                                this.isLoading = false;
                                            });
                                    }, 200);
                                },
                                onArrowDown() {
                                    if (!this.isOpen || this.suggestions.length === 0) return;
                                    this.selectedIndex = (this.selectedIndex + 1) % this.suggestions.length;
                                },
                                onArrowUp() {
                                    if (!this.isOpen || this.suggestions.length === 0) return;
                                    this.selectedIndex = (this.selectedIndex - 1 + this.suggestions.length) % this.suggestions.length;
                                },
                                onEnter(e) {
                                    if (this.isOpen && this.selectedIndex >= 0 && this.suggestions[this.selectedIndex]) {
                                        e.preventDefault();
                                        window.location.href = this.suggestions[this.selectedIndex].url;
                                    }
                                }
                            }" 
                            @click.outside="isOpen = false"
                            class="relative min-w-[18rem] sm:min-w-[22rem]"
                        >
                            <div class="relative flex items-center">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                
                                <input 
                                    type="text" 
                                    name="search" 
                                    x-model="query"
                                    @input="fetchSuggestions()"
                                    @focus="if (suggestions.length > 0) isOpen = true"
                                    @keydown.arrow-down.prevent="onArrowDown()"
                                    @keydown.arrow-up.prevent="onArrowUp()"
                                    @keydown.enter="onEnter($event)"
                                    @keydown.escape="isOpen = false"
                                    placeholder="Search PHN / Patient Name" 
                                    aria-label="Search PHN or Patient Name"
                                    autocomplete="off"
                                    class="w-full pl-9 pr-10 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/80 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm"
                                >

                                @if(!empty($search))
                                    <a href="{{ route('doctor.patients.index', ['sort' => $sort]) }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="Clear search">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>

                            <!-- Autocomplete Suggestions Dropdown -->
                            <div 
                                x-show="isOpen && suggestions.length > 0" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-2xl overflow-hidden z-50 max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60"
                                style="display: none;"
                            >
                                <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900/60 text-[0.65rem] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Matching Patients
                                </div>
                                <template x-for="(item, idx) in suggestions" :key="item.patient_id">
                                    <a 
                                        :href="item.url" 
                                        :class="selectedIndex === idx ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-800 dark:text-gray-200'"
                                        class="flex items-center justify-between px-3.5 py-2.5 text-xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center gap-2.5 overflow-hidden">
                                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[0.7rem] bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-600 flex-shrink-0" x-text="item.phn"></span>
                                            <span class="font-bold uppercase tracking-wide truncate" x-text="item.name"></span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 font-medium flex-shrink-0 ml-2">
                                            <span x-text="item.age + ' | ' + item.sex"></span>
                                            <span class="px-1.5 py-0.5 rounded text-[0.65rem] font-bold bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800" x-text="item.audits_count + (item.audits_count === 1 ? ' Audit' : ' Audits')"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- Sort By Dropdown -->
                        <div class="relative flex items-center min-w-[12rem]">
                            <select 
                                name="sort" 
                                onchange="this.form.submit()" 
                                aria-label="Sort patients"
                                class="w-full text-xs font-semibold py-2 pl-3 pr-8 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/80 text-gray-800 dark:text-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-sm"
                            >
                                <option value="newest_registered" {{ $sort === 'newest_registered' ? 'selected' : '' }}>Newest Registered</option>
                                <option value="oldest_registered" {{ $sort === 'oldest_registered' ? 'selected' : '' }}>Oldest Registered</option>
                                <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Patient Name (A–Z)</option>
                                <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Patient Name (Z–A)</option>
                                <option value="phn_asc" {{ $sort === 'phn_asc' ? 'selected' : '' }}>PHN (Low–High)</option>
                                <option value="phn_desc" {{ $sort === 'phn_desc' ? 'selected' : '' }}>PHN (High–Low)</option>
                                <option value="audits_desc" {{ $sort === 'audits_desc' ? 'selected' : '' }}>Most Audits</option>
                                <option value="audits_asc" {{ $sort === 'audits_asc' ? 'selected' : '' }}>Least Audits</option>
                                <option value="surgery_desc" {{ $sort === 'surgery_desc' ? 'selected' : '' }}>Latest Surgery</option>
                                <option value="surgery_asc" {{ $sort === 'surgery_asc' ? 'selected' : '' }}>Oldest Surgery</option>
                            </select>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Patient Table Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-200 text-xs font-bold">
                            <tr>
                                <th class="px-5 py-3.5">PHN</th>
                                <th class="px-5 py-3.5">Patient</th>
                                <th class="px-5 py-3.5">Age / Sex</th>
                                <th class="px-5 py-3.5">BHT</th>
                                <th class="px-5 py-3.5">Audits</th>
                                <th class="px-5 py-3.5">Last Surgery</th>
                                <th class="px-5 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($patients as $patient)
                                @php
                                    $latestSurgery = $patient->surgeries->first();
                                    $lastSurgeryDate = $latestSurgery && $latestSurgery->date_of_surgery
                                        ? \Carbon\Carbon::parse($latestSurgery->date_of_surgery)->format('d-M-Y')
                                        : 'N/A';
                                    $bhtNumber = !empty($patient->bht) ? $patient->bht : (!empty($latestSurgery?->bht) ? $latestSurgery->bht : 'N/A');
                                    $auditCount = $patient->surgeries_count ?? $patient->surgeries->count();
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 align-middle transition-colors">
                                    
                                    <!-- PHN -->
                                    <td class="px-5 py-4 font-bold text-gray-900 dark:text-gray-100">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-600">
                                            {{ $patient->phn ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <!-- Patient Name -->
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white uppercase tracking-wide">
                                            {{ $patient->name ?? 'Unknown' }}
                                        </div>
                                    </td>

                                    <!-- Age / Sex -->
                                    <td class="px-5 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                        {{ $patient->age ? $patient->age . 'Y' : 'N/A' }} | 
                                        <span class="{{ $patient->sex === 'M' ? 'text-blue-600 dark:text-blue-400 font-bold' : ($patient->sex === 'F' ? 'text-pink-600 dark:text-pink-400 font-bold' : '') }}">
                                            {{ $patient->sex === 'M' ? 'Male' : ($patient->sex === 'F' ? 'Female' : ($patient->sex ?? 'N/A')) }}
                                        </span>
                                    </td>

                                    <!-- BHT -->
                                    <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-300">
                                        <span class="font-medium {{ $bhtNumber !== 'N/A' ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-gray-400' }}">
                                            {{ $bhtNumber }}
                                        </span>
                                    </td>

                                    <!-- Audits Count Badge -->
                                    <td class="px-5 py-4">
                                        @if($auditCount === 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                                0 Audits
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ $auditCount }} {{ $auditCount === 1 ? 'Audit' : 'Audits' }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Last Surgery Date -->
                                    <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-300 font-medium">
                                        <span class="{{ $lastSurgeryDate !== 'N/A' ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400' }}">
                                            {{ $lastSurgeryDate }}
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('doctor.patients.show', $patient->patient_id) }}" class="inline-flex items-center justify-center text-xs font-bold px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <span>VIEW</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            @if(!empty($search))
                                                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No patients match your search.</p>
                                                <p class="text-xs text-gray-500">Try searching with a different PHN or patient name.</p>
                                                <a href="{{ route('doctor.patients.index', ['sort' => $sort]) }}" class="mt-2 text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 underline">
                                                    Clear Search Filter
                                                </a>
                                            @else
                                                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No patients registered</p>
                                                <p class="text-xs text-gray-500">There are currently no registered patient records in the system.</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                @if($patients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/80">
                        {{ $patients->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
