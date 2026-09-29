<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('Profile & Settings') }}
                    </h2>
                    @php
                        $role = auth()->user()->role ?? 'doctor';
                        $roleBadgeColor = match($role) {
                            'system_admin' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                            'doctor'       => 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
                            'data_entry'   => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                            default        => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $roleBadgeColor }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ ucwords(str_replace('_', ' ', $role)) }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Manage your personal account credentials, update authentication details, and secure your session.
                </p>
            </div>
            
            @php
                $dashboardRoute = match($role) {
                    'system_admin' => route('admin.dashboard'),
                    'doctor'       => route('doctor.dashboard'),
                    'data_entry'   => route('data_entry.dashboard'),
                    default        => url('/dashboard'),
                };
            @endphp
            <a href="{{ $dashboardRoute }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-sky-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Hero Profile Overview Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    
                    <!-- Avatar circle with role gradient -->
                    <div class="relative flex-shrink-0">
                        @php
                            $avatarGradient = match($role) {
                                'system_admin' => 'from-indigo-600 to-indigo-800 shadow-indigo-600/30',
                                'doctor'       => 'from-cyan-600 to-blue-700 shadow-cyan-600/30',
                                'data_entry'   => 'from-emerald-600 to-teal-700 shadow-emerald-600/30',
                                default        => 'from-slate-600 to-slate-800 shadow-slate-600/30',
                            };
                        @endphp
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr {{ $avatarGradient }} flex items-center justify-center font-black text-2xl text-white shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-800" title="Account Active"></span>
                    </div>

                    <div class="flex-1 text-center sm:text-left space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                                    {{ $user->name }}
                                </h1>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                                    {{ $user->email }}
                                </p>
                            </div>

                            <div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $roleBadgeColor }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 pt-3 text-xs text-slate-500 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700/60">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Member since {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span>Account Status: Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Section Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password Section Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Danger Zone Section Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-rose-200/80 dark:border-rose-900/40 p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
