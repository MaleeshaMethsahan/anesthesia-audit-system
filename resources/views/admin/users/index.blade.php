<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('User Management') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ ($adminCount ?? \App\Models\User::adminCount()) >= 2 ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ ($adminCount ?? \App\Models\User::adminCount()) >= 2 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                        Admins: {{ $adminCount ?? \App\Models\User::adminCount() }}/{{ \App\Models\User::MAX_ADMINS }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Manage system accounts, assign roles, update credentials, and maintain user access.
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-indigo-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Register New User
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Status Messages -->
            @if (session('status'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center gap-3 text-emerald-800 dark:text-emerald-300 shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-semibold">{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl flex items-center gap-3 text-rose-800 dark:text-rose-300 shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-5">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="w-full md:w-1/2 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name or email..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <div class="w-full md:w-auto flex flex-wrap gap-3 items-center justify-end">
                        <select name="role" 
                                onchange="this.form.submit()" 
                                class="text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Roles</option>
                            <option value="doctor" {{ request('role') === 'doctor' ? 'selected' : '' }}>Doctors</option>
                            <option value="data_entry" {{ request('role') === 'data_entry' ? 'selected' : '' }}>Data Entry</option>
                            <option value="system_admin" {{ request('role') === 'system_admin' ? 'selected' : '' }}>System Admins</option>
                        </select>

                        @if(request('search') || request('role'))
                            <a href="{{ route('admin.users.index') }}" 
                               class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white px-3 py-2 bg-slate-100 dark:bg-slate-700 rounded-xl transition">
                                Clear Filters
                            </a>
                        @endif

                        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Data Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900/60">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">User</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Role</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Registered</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Last Seen</th>
                                <th class="px-6 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-sm">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition">
                                    <!-- User Info -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm shadow-sm
                                                {{ $user->role === 'system_admin' ? 'bg-rose-500' : ($user->role === 'doctor' ? 'bg-indigo-600' : 'bg-emerald-600') }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                                    {{ $user->name }}
                                                    @if($user->id === auth()->id())
                                                        <span class="text-[0.65rem] font-bold px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">You</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role Badge -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide
                                            {{ $user->role === 'system_admin' ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800' : 
                                               ($user->role === 'doctor' ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800' : 
                                               'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800') }}">
                                            {{ str_replace('_', ' ', $user->role) }}
                                        </span>
                                    </td>

                                    <!-- Registered Date -->
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                                        {{ $user->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                                    </td>

                                    <!-- Last Seen -->
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                                        {{ $user->last_seen_at?->diffForHumans() ?? 'Never logged in' }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 transition shadow-sm"
                                               title="Edit User">
                                                Edit
                                            </a>

                                            @if($user->id !== auth()->id())
                                                <form method="POST" 
                                                      action="{{ route('admin.users.destroy', $user) }}" 
                                                      onsubmit="return confirm('Are you sure you want to delete the user account for \'{{ $user->name }}\'?\n\nClinical Audit Safety: All anesthesia audit records, surgeries, and historical clinical data created by this user will remain safely preserved in the database.');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 border border-rose-200 dark:border-rose-800 transition shadow-sm"
                                                            title="Delete User Account">
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 dark:text-slate-600 bg-slate-100 dark:bg-slate-800 cursor-not-allowed" title="You cannot delete your own account">
                                                    Protected
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                        No users found matching your search criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

            <!-- Historical Data Preservation Notice Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-slate-800 dark:to-indigo-950/30 border border-blue-200/80 dark:border-indigo-900/60 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="p-2.5 bg-blue-600 text-white rounded-xl shadow flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-blue-900 dark:text-blue-300">
                            Clinical Audit & Historical Record Safety
                        </h4>
                        <p class="text-xs text-blue-800/80 dark:text-slate-300 mt-1 leading-relaxed">
                            Completed anesthesia audits are medical records. Deleting a doctor or data entry account removes only their login access credentials. All existing anesthesia audit records, surgical entries, patient records, and historical staff associations remain permanently preserved in the system.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
