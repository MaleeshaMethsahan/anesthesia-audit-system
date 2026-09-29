<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    System administration, user access management, and audit form settings.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    System Admin Mode
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Success Message -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-800 dark:text-emerald-300 shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-semibold">{{ session('status') }}</span>
                </div>
            @endif

            <!-- NEW SECTION: AUDIT FORM SETTINGS -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-slate-100 dark:border-slate-700/60">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                Audit Form Settings
                            </h3>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Configure doctor availability in the audit form and maintain clinical master reference data.
                        </p>
                    </div>
                    <a href="{{ route('admin.audit-form-settings.index') }}" 
                       class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Open Settings Overview →
                    </a>
                </div>

                <!-- 2 Cards Inside Audit Form Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">
                    
                    <!-- 1. Doctors in Audit Form -->
                    <div class="p-5 rounded-xl border border-slate-200/80 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900/40 flex flex-col justify-between hover:border-cyan-400 dark:hover:border-cyan-600 transition">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-100 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                                    {{ $activeDoctorsCount ?? 0 }} of {{ $totalDoctorsCount ?? 0 }} Active
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-base">
                                Doctors in Audit Form
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Select which surgeons, anaesthetists, and MOAs appear in the clinical audit form dropdowns. Easily toggle visibility on or off without affecting historical records.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-800">
                            <a href="{{ route('admin.audit-form-settings.doctors') }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                                Manage Doctor Visibility →
                            </a>
                        </div>
                    </div>

                    <!-- 2. Manage Master Data -->
                    <div class="p-5 rounded-xl border border-slate-200/80 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900/40 flex flex-col justify-between hover:border-indigo-400 dark:hover:border-indigo-600 transition">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    Master Reference
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-base">
                                Manage Master Data
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Maintain clinical master options for medical conditions, monitoring techniques, anaesthesia induction and maintenance agents, analgesics, and adverse events.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-800">
                            <a href="{{ route('admin.reference-data.index') }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                                Manage Master Data →
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SYSTEM & USER ADMINISTRATION SHORTCUTS -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">
                    User Accounts & Security Logs
                </h3>
                
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Manage Users
                    </a>
                    <a href="{{ route('admin.users.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Register New User
                    </a>
                    <a href="{{ route('admin.logs.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        View Login Logs
                    </a>
                </div>
            </div>

            <!-- ACTIVE USERS (LAST 5 MINUTES) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Users (Last 5 Minutes)
                    </h3>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                        {{ $activeUsers->count() }} active
                    </span>
                </div>

                @if($activeUsers->count())
                    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-900/60 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th class="px-5 py-3 text-left">Name</th>
                                    <th class="px-5 py-3 text-left">Role</th>
                                    <th class="px-5 py-3 text-left">Last Seen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                @foreach($activeUsers as $user)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold
                                                {{ $user->role === 'system_admin' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300' : ($user->role === 'doctor' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300') }}">
                                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-xs text-slate-500 dark:text-slate-400">
                                            {{ $user->last_seen_at?->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        No active users in the last 5 minutes.
                    </p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>