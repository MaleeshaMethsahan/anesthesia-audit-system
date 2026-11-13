<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('Data Entry Dashboard') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Data Entry Mode
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Hospital clinical record entry and patient audit log management.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('data_entry.audit-form.start') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Patient Record
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome / Portal Overview Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-700 flex items-center justify-center font-black text-2xl text-white shadow-lg flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 text-center sm:text-left space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                                    Data Entry Portal
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Start inputting hospital records, manage patient anaesthesia logs, and review clinical database records.
                                </p>
                            </div>
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ auth()->user()->name ?? 'Data Entry Officer' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Action Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Card 1: Add Patient Records -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col justify-between hover:shadow-md hover:border-emerald-400 dark:hover:border-emerald-500 transition group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                New Record
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                            Add Patient Records
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Access the form for adding a new anesthesia patient record. Record patient demographics, intraoperative anesthesia data, and surgical details.
                        </p>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                        <a href="{{ route('data_entry.audit-form.start') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-emerald-500/20 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Patient Records
                        </a>
                    </div>
                </div>

                <!-- Card 2: View Clinical Records -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8 flex flex-col justify-between hover:shadow-md hover:border-blue-400 dark:hover:border-blue-500 transition group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                Database Table
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                            View Clinical Records
                       </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Access the table of existing clinical records to search, inspect, and review previously submitted hospital surgical audit cases.
                        </p>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                        <a href="{{ route('data_entry.records.index') }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md hover:shadow-blue-500/20 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            View Clinical Records
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
