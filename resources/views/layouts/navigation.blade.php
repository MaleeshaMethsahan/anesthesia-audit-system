<!-- Sidebar Backdrop for Mobile -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-gray-900/80 md:hidden" 
     style="display: none;"></div>

<!-- Sidebar Navigation -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 w-64 md:w-72 bg-slate-900 text-slate-300 flex flex-col justify-between shadow-2xl transition-transform duration-300 ease-in-out md:translate-x-0 border-r border-slate-800">

    <div>
        <!-- App Logo & Title -->
        <div class="h-16 flex items-center justify-between px-6 bg-slate-950/50 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="p-2 bg-indigo-600 rounded-lg text-white group-hover:bg-indigo-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-white text-base leading-tight tracking-wide">Anaesthesia Audit</h1>
                    <span class="text-[0.65rem] font-semibold uppercase tracking-wider text-indigo-400">DOA System</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Role Badge Header -->
        <div class="px-6 py-3 bg-slate-950/30 border-b border-slate-800/60 flex items-center justify-between text-xs">
            <span class="text-slate-400 uppercase font-semibold tracking-wider text-[0.65rem]">Logged in as</span>
            <span class="px-2 py-0.5 rounded-full font-bold uppercase text-[0.65rem]
                {{ auth()->user()->role === 'system_admin' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 
                   (auth()->user()->role === 'doctor' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30') }}">
                {{ str_replace('_', ' ', auth()->user()->role) }}
            </span>
        </div>

        <!-- Navigation Links Menu -->
        <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-14rem)]">
            
            @if(auth()->user()->role === 'doctor')
                <!-- DOCTOR SPECIFIC MENU -->
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-slate-500 px-3 pt-2 pb-1">Clinical Navigation</div>
                
                <a href="{{ route('doctor.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('doctor.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <a href="{{ route('doctor.audit-form.create') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('doctor.audit-form.*') ? 'bg-amber-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Add Patient Record
                </a>

                <a href="{{ route('doctor.records.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('doctor.records.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                    View Database
                </a>

                <a href="{{ route('doctor.records.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition text-slate-300 hover:bg-slate-800 hover:text-white">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Patients Directory
                </a>

                <a href="{{ route('doctor.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition text-slate-300 hover:bg-slate-800 hover:text-white">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Reports & Analytics
                </a>

                <a href="{{ route('doctor.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition text-slate-300 hover:bg-slate-800 hover:text-white">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                    Surgical Statistics
                </a>

                <a href="{{ route('doctor.records.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition text-slate-300 hover:bg-slate-800 hover:text-white">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Global Search
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('profile.edit') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    System Settings
                </a>

            @elseif(auth()->user()->role === 'data_entry')
                <!-- DATA ENTRY MENU -->
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-slate-500 px-3 pt-2 pb-1">Data Entry Menu</div>

                <a href="{{ route('data_entry.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('data_entry.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <a href="{{ route('data_entry.audit-form.create') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('data_entry.audit-form.*') ? 'bg-amber-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Add Patient Record
                </a>

                <a href="{{ route('data_entry.records.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('data_entry.records.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                    View Database
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('profile.edit') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Profile & Settings
                </a>

            @elseif(auth()->user()->role === 'system_admin')
                <!-- SYSTEM ADMIN MENU -->
                <div class="text-[0.65rem] font-bold uppercase tracking-wider text-slate-500 px-3 pt-2 pb-1">System Administration</div>

                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Admin Dashboard
                </a>

                <a href="{{ route('admin.reference-data.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.reference-data.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Manage Master Data
                </a>

                <a href="{{ route('admin.users.create') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Register User
                </a>

                <a href="{{ route('admin.logs.index') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('admin.logs.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    View Login Logs
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition {{ request()->routeIs('profile.edit') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Profile & Settings
                </a>
            @endif

        </nav>
    </div>

    <!-- Sidebar Footer: Profile & Logout Options -->
    <div x-data="{ profileOpen: false }" class="relative p-4 border-t border-slate-800 bg-slate-950/60">
        
        <!-- Upward Profile Popover Menu -->
        <div x-show="profileOpen" 
             @click.outside="profileOpen = false" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="absolute bottom-full left-3 right-3 mb-2 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl overflow-hidden z-50 p-2"
             style="display: none;">
            
            <div class="px-3 py-2 border-b border-slate-700/60 mb-1">
                <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-[0.7rem] text-slate-400 truncate">{{ Auth::user()->email }}</div>
            </div>

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-indigo-600 hover:text-white rounded-lg transition">
                <svg class="w-4 h-4 text-indigo-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                {{ __('Profile Details') }}
            </a>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg transition text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>

        <button @click="profileOpen = !profileOpen" type="button" class="w-full flex items-center justify-between p-1.5 rounded-lg hover:bg-slate-800 transition text-left group">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm flex-shrink-0 group-hover:bg-indigo-500 transition">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="truncate">
                    <div class="text-sm font-bold text-white truncate group-hover:text-indigo-300 transition">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="p-1.5 text-slate-400 group-hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
            </div>
        </button>

    </div>

</aside>
