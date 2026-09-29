<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('Doctors in Audit Form') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                        {{ $activeDoctors }} Active in Form
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Select and configure which surgeons, anaesthetists, and MOAs appear in the audit form selection dropdowns.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.audit-form-settings.index') }}" 
                   class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                    ← Form Settings
                </a>
                <button type="button" 
                        @click="$dispatch('open-add-doctor-modal')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-indigo-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Doctor / Surgeon
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 dark:bg-slate-900 min-h-screen" x-data="{ addModalOpen: false, editModalOpen: false, editDoctor: { id: null, name: '', is_active: true } }" @open-add-doctor-modal.window="addModalOpen = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

            <!-- Search and Filter Bar -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 p-5">
                <form method="GET" action="{{ route('admin.audit-form-settings.doctors') }}" class="flex flex-col md:flex-row gap-4 justify-between items-center">
                    <div class="w-full md:w-1/2 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search doctor or surgeon name..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <div class="w-full md:w-auto flex flex-wrap gap-3 items-center justify-end">
                        <select name="status" 
                                onchange="this.form.submit()" 
                                class="text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Visible in Audit Form</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Hidden from Form</option>
                        </select>

                        @if(request('search') || request('status'))
                            <a href="{{ route('admin.audit-form-settings.doctors') }}" 
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

            <!-- Doctor Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                        <thead class="bg-slate-50 dark:bg-slate-900/60">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Doctor / Staff Name</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Audit Form Status</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Historical Audits</th>
                                <th class="px-6 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-sm">
                            @forelse($doctors as $doctor)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition">
                                    
                                    <!-- Name -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm shadow-sm
                                                {{ $doctor->is_active ? 'bg-cyan-600' : 'bg-slate-400 dark:bg-slate-600' }}">
                                                {{ strtoupper(substr($doctor->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">
                                                    {{ $doctor->name }}
                                                </div>
                                                <div class="text-xs text-slate-400">
                                                    ID: #{{ $doctor->staff_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>


                                    <!-- Audit Form Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($doctor->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Appears in Audit Form
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600">
                                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                                Hidden from Form
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Historical Audits -->
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 dark:text-slate-300">
                                        <span class="font-bold">{{ $doctor->surgeries_count }}</span>
                                        <span class="text-xs text-slate-400">surgeries recorded</span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            <!-- 1-Click Visibility Toggle -->
                                            <form method="POST" action="{{ route('admin.audit-form-settings.doctors.toggle', $doctor) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        title="{{ $doctor->is_active ? 'Click to hide this doctor from the audit form' : 'Click to show this doctor in the audit form' }}"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm
                                                            {{ $doctor->is_active ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:bg-amber-100' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                                    @if($doctor->is_active)
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                                        Hide from Form
                                                    @else
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        Show in Form
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Edit Name -->
                                            <button type="button" 
                                                    @click="editDoctor = { id: {{ $doctor->staff_id }}, name: '{{ addslashes($doctor->name) }}', is_active: {{ $doctor->is_active ? 'true' : 'false' }} }; editModalOpen = true"
                                                    class="p-1.5 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition" 
                                                    title="Edit Doctor Name">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <!-- Delete / Has Records Status -->
                                            @php
                                                $hasRecords = ((int) ($doctor->surgeries_count ?? 0)) > 0;
                                            @endphp

                                            @if(!$hasRecords)
                                                <!-- Delete button enabled when doctor has NO records -->
                                                <form method="POST" 
                                                      action="{{ route('admin.audit-form-settings.doctors.destroy', $doctor->staff_id) }}" 
                                                      onsubmit="return confirm('Are you sure you want to permanently delete doctor \'{{ addslashes($doctor->name) }}\' from the audit roster?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800 transition shadow-sm" 
                                                            title="Delete Doctor (No records)">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Delete disabled when doctor HAS records -->
                                                <button type="button" 
                                                        disabled 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800/80 cursor-not-allowed border border-slate-200 dark:border-slate-700/60" 
                                                        title="Cannot delete: Doctor is linked to {{ $doctor->surgeries_count }} clinical audit record(s). Toggle visibility to 'Hidden from Form' instead.">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    Has Records
                                                </button>
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                     <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                         No doctors found matching criteria.
                                     </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($doctors->hasPages())
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $doctors->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Add Doctor Modal -->
        <div x-show="addModalOpen" 
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="addModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                     @click="addModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="addModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700 p-6">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            Add Doctor / Surgeon to Roster
                        </h3>
                        <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            ✕
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.audit-form-settings.doctors.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Doctor Name (e.g. Dr. A. Perera)
                            </label>
                            <input type="text" 
                                   name="name" 
                                   required 
                                   placeholder="Dr. Full Name"
                                   class="w-full text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" 
                                   id="add_is_active" 
                                   name="is_active" 
                                   value="1" 
                                   checked
                                   class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700">
                            <label for="add_is_active" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Make visible in audit form dropdowns immediately
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="button" 
                                    @click="addModalOpen = false"
                                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2 bg-indigo-600 text-white font-bold rounded-xl text-xs hover:bg-indigo-700 shadow-md">
                                Save Doctor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Doctor Modal -->
        <div x-show="editModalOpen" 
             style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                     @click="editModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="editModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700 p-6">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            Edit Doctor Details
                        </h3>
                        <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            ✕
                        </button>
                    </div>

                    <form :action="'{{ url('/admin/audit-form-settings/doctors') }}/' + editDoctor.id" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Doctor Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   x-model="editDoctor.name"
                                   required 
                                   class="w-full text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                Audit Form Visibility
                            </label>
                            <select name="is_active" 
                                    x-model="editDoctor.is_active"
                                    class="w-full text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                                <option :value="1">Visible in Audit Form (Active)</option>
                                <option :value="0">Hidden from Audit Form (Inactive)</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="button" 
                                    @click="editModalOpen = false"
                                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2 bg-indigo-600 text-white font-bold rounded-xl text-xs hover:bg-indigo-700 shadow-md">
                                Update Doctor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
