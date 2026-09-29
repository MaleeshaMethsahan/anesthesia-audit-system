<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('Audit Form Settings') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        Form Configuration
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Configure doctor availability and manage clinical master data for the anesthesia audit forms.
                </p>
            </div>
            <a href="{{ route('admin.dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-sky-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Flash Status Messages -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-800 dark:text-emerald-300 shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-semibold">{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl flex items-center gap-3 text-rose-800 dark:text-rose-300 shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Main Hub Grid: 2 Primary Sections -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- 1. Doctors in Audit Form -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                                {{ $activeDoctors }} of {{ $totalDoctors }} Active
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            Doctors in Audit Form
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Control which consultant surgeons, anaesthetists, and MOAs appear in the audit form dropdowns. Toggle availability, add visiting surgeons, and maintain doctor roster visibility.
                        </p>

                        <div class="grid grid-cols-3 gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60 text-center">
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <span class="block text-xl font-black text-slate-800 dark:text-slate-100">{{ $totalDoctors }}</span>
                                <span class="text-[0.7rem] uppercase font-bold text-slate-400 dark:text-slate-500">Total Roster</span>
                            </div>
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl">
                                <span class="block text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $activeDoctors }}</span>
                                <span class="text-[0.7rem] uppercase font-bold text-emerald-600/70 dark:text-emerald-400/70">Visible</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <span class="block text-xl font-black text-slate-500 dark:text-slate-400">{{ $hiddenDoctors }}</span>
                                <span class="text-[0.7rem] uppercase font-bold text-slate-400 dark:text-slate-500">Hidden</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4">
                        <a href="{{ route('admin.audit-form-settings.doctors') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-cyan-500/20 transition">
                            Manage Doctors in Audit Form →
                        </a>
                    </div>
                </div>

                <!-- 2. Manage Master Data -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                {{ array_sum($masterDataCounts) }} Master Items
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            Manage Clinical Master Data
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Maintain clinical reference parameters including medical conditions, monitoring techniques, anaesthesia induction and maintenance agents, analgesics, and adverse events.
                        </p>

                        <div class="grid grid-cols-3 gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/60 text-center">
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <span class="block text-xl font-black text-indigo-600 dark:text-indigo-400">{{ $masterDataCounts['conditions'] }}</span>
                                <span class="text-[0.7rem] uppercase font-bold text-slate-400 dark:text-slate-500">Conditions</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <span class="block text-xl font-black text-slate-800 dark:text-slate-100">{{ $masterDataCounts['induction_agents'] + $masterDataCounts['maintenance_agents'] }}</span>
                                <span class="text-[0.7rem] uppercase font-bold text-slate-400 dark:text-slate-500">Agents</span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                                <span class="block text-xl font-black text-slate-800 dark:text-slate-100">{{ $masterDataCounts['adverse_events'] }}</span>
                                <span class="text-[0.7rem] uppercase font-bold text-slate-400 dark:text-slate-500">Adverse Events</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4">
                        <a href="{{ route('admin.reference-data.index') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-indigo-500/20 transition">
                            Manage Master Data →
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
