<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('System Login Logs') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Total: {{ number_format($counts['all']) }} Logins
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Audit trail of authentication attempts, IP addresses, client devices, and timestamps.
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Controls Bar -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-5 space-y-4">
                
                <!-- Timeframe Option Buttons (Pill Bar) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Filter Logins by Timeframe
                    </label>

                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $timeframeOptions = [
                                'all'       => ['label' => 'All Time', 'count' => $counts['all']],
                                'last_hour' => ['label' => 'Last Hour', 'count' => $counts['last_hour']],
                                'last_24h'  => ['label' => 'Last 24 Hours', 'count' => $counts['last_24h']],
                                'week'      => ['label' => 'Last Week', 'count' => $counts['week']],
                                'month'     => ['label' => 'Last Month', 'count' => $counts['month']],
                            ];
                        @endphp

                        @foreach($timeframeOptions as $key => $option)
                            <a href="{{ route('admin.logs.index', array_filter(['timeframe' => $key, 'search' => $search])) }}"
                               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $timeframe === $key ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/70 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200/60 dark:border-slate-600/60' }}">
                                <span>{{ $option['label'] }}</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $timeframe === $key ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300' }}">
                                    {{ number_format($option['count']) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Search and Compact Dropdown Form -->
                <form method="GET" action="{{ route('admin.logs.index') }}" class="pt-3 border-t border-slate-100 dark:border-slate-700/60 flex flex-col md:flex-row gap-3 items-center justify-between">
                    <div class="w-full md:w-80 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Search name, username, email, or IP..." 
                               class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <div class="w-full md:w-auto flex flex-wrap items-center gap-2">
                        <!-- Dropdown Option for Timeframe (convenient on mobile or quick selection) -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <span class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">Timeframe:</span>
                            <select name="timeframe" 
                                    onchange="this.form.submit()" 
                                    class="text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-amber-500">
                                <option value="all" {{ $timeframe === 'all' ? 'selected' : '' }}>All Time ({{ $counts['all'] }})</option>
                                <option value="last_hour" {{ $timeframe === 'last_hour' ? 'selected' : '' }}>Last Hour ({{ $counts['last_hour'] }})</option>
                                <option value="last_24h" {{ $timeframe === 'last_24h' ? 'selected' : '' }}>Last 24 Hours ({{ $counts['last_24h'] }})</option>
                                <option value="week" {{ $timeframe === 'week' ? 'selected' : '' }}>Last Week ({{ $counts['week'] }})</option>
                                <option value="month" {{ $timeframe === 'month' ? 'selected' : '' }}>Last Month ({{ $counts['month'] }})</option>
                            </select>
                        </div>

                        <button type="submit" 
                                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            Apply
                        </button>

                        @if($timeframe !== 'all' || $search !== '')
                            <a href="{{ route('admin.logs.index') }}" 
                               class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

            </div>

            <!-- Active Filter Status Bar -->
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 px-1">
                <div class="flex items-center gap-2">
                    <span>Showing</span>
                    <strong class="text-slate-800 dark:text-slate-200">{{ $logs->total() }}</strong>
                    <span>logins</span>
                    @if($timeframe !== 'all')
                        <span>within</span>
                        <span class="inline-flex items-center gap-1 font-bold text-amber-600 dark:text-amber-400">
                            {{ $timeframeOptions[$timeframe]['label'] ?? $timeframe }}
                        </span>
                    @endif
                    @if($search !== '')
                        <span>matching "<strong class="text-slate-800 dark:text-slate-200">{{ $search }}</strong>"</span>
                    @endif
                </div>
            </div>

            <!-- Logs Table Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/60 uppercase tracking-wider text-xs font-bold text-slate-500 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-3.5">User</th>
                                <th scope="col" class="px-6 py-3.5">Role</th>
                                <th scope="col" class="px-6 py-3.5">IP Address</th>
                                <th scope="col" class="px-6 py-3.5">Login Time</th>
                                <th scope="col" class="px-6 py-3.5">Browser / Device</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition">
                                    
                                    <!-- User -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200">
                                                {{ strtoupper(substr($log->user?->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">
                                                    {{ $log->user?->name ?? 'Deleted User' }}
                                                </div>
                                                <div class="text-xs text-slate-400 flex items-center gap-1.5 flex-wrap">
                                                    <span>{{ $log->user?->email ?? 'N/A' }}</span>
                                                    @if($log->user?->username)
                                                        <span class="text-slate-300 dark:text-slate-600">•</span>
                                                        <span class="font-mono text-amber-600 dark:text-amber-400 font-semibold">{{ '@' . $log->user->username }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $role = $log->user?->role ?? 'unknown';
                                            $badgeClass = match($role) {
                                                'system_admin' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                                'doctor'       => 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
                                                'data_entry'   => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                                default        => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                            {{ ucwords(str_replace('_', ' ', $role)) }}
                                        </span>
                                    </td>

                                    <!-- IP Address -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-xs font-mono text-slate-700 dark:text-slate-300">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                            {{ $log->ip_address ?? '—' }}
                                        </div>
                                    </td>

                                    <!-- Time -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $loginTime = $log->login_at instanceof \Carbon\Carbon ? $log->login_at : \Carbon\Carbon::parse($log->login_at);
                                        @endphp
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $loginTime->format('Y-m-d h:i A') }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            {{ $loginTime->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- Browser / Device -->
                                    <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span class="truncate">{{ Str::limit($log->user_agent, 45) ?: 'Unknown Device' }}</span>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="max-w-sm mx-auto space-y-3">
                                            <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div class="font-bold text-slate-700 dark:text-slate-200">
                                                No Logins Found
                                            </div>
                                            <p class="text-xs text-slate-400">
                                                No authentication records match the selected timeframe ({{ $timeframeOptions[$timeframe]['label'] ?? $timeframe }})
                                                @if($search !== '') or search query "{{ $search }}"@endif.
                                            </p>
                                            @if($timeframe !== 'all' || $search !== '')
                                                <a href="{{ route('admin.logs.index') }}" 
                                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-sm">
                                                    View All Time Logins
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>