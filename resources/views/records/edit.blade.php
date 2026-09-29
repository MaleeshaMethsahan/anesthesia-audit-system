<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Edit Anaesthesia Audit Record') }} #{{ $surgery->surgery_id }}
                </h2>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs">
                    <!-- Last Modified Date & Time -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Last Modified: 
                        <strong class="font-bold">
                            {{ $surgery->updated_at ? date('d-M-Y \a\t h:i A', strtotime($surgery->updated_at)) : 'Never modified' }}
                        </strong>
                    </span>

                    <!-- Update Count -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-700 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Times Updated: 
                        <strong class="font-bold">{{ (int)($surgery->update_count ?? 0) }} {{ ($surgery->update_count ?? 0) == 1 ? 'time' : 'times' }}</strong>
                    </span>

                    <!-- Created At Date -->
                    @if($surgery->created_at)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 shadow-sm">
                        Created: {{ date('d-M-Y h:i A', strtotime($surgery->created_at)) }}
                    </span>
                    @endif
                </div>
            </div>
            <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.records.index') : route('data_entry.records.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-sky-500/20 transition flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Database
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-8 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-8 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <p class="font-bold">Error updating record:</p>
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-8 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @php
            $routePrefix = auth()->user()->role === 'data_entry' ? 'data_entry.' : 'doctor.';
            @endphp
            <form action="{{ route($routePrefix . 'records.update', $surgery->surgery_id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- 1. Patient Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Patient Details
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patient Name <span class="text-red-500">*</span></label>
                            <input type="text" name="patient[name]" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.name', $surgery->name) }}" placeholder="Full Name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PHN <span class="text-red-500">*</span></label>
                            <input type="text" name="patient[phn]" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.phn', $surgery->phn) }}" placeholder="e.g. 12345">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Age</label>
                            <input type="number" name="patient[age]"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.age', $surgery->age) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sex <span class="text-red-500">*</span></label>
                            <select name="patient[sex]" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select</option>
                                <option value="M" {{ old('patient.sex', $surgery->sex)=='M' ? 'selected' : '' }}>Male</option>
                                <option value="F" {{ old('patient.sex', $surgery->sex)=='F' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">BHT (Bed Head Ticket)</label>
                            <input type="text" name="patient[bht]"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.bht', $surgery->bht) }}" placeholder="BHT Number">
                        </div>
                    </div>
                </div>

                <!-- 2. Surgery Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.121 15.536c-1.171 1.952-3.07-1.567-4.242-2.739-.952-.953-1.184-3.52 0-4.704 1.184-1.185 3.751-.952 4.704 0 1.172 1.172 4.691 3.071 2.739 4.242z">
                                </path>
                            </svg>
                            Surgery Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operation Name <span class="text-red-500">*</span></label>
                                <input type="text" name="surgery[operation_name]" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    value="{{ old('surgery.operation_name', $surgery->operation_name) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Surgical Specialty</label>
                                <select name="surgery[surgical_specialty]"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Specialty</option>
                                    @foreach(['General Surgery', 'Orthopaedics', 'OBGYN', 'ENT', 'Ophthalmology', 'Urology', 'Neuro surgery', 'Plastic surgery', 'Vascular surgery', 'Maxillofacial', 'Dental'] as $spec)
                                        <option value="{{ $spec }}" {{ old('surgery.surgical_specialty', $surgery->surgical_specialty) == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Surgery</label>
                                <input type="date" name="surgery[date_of_surgery]"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    value="{{ old('surgery.date_of_surgery', $surgery->date_of_surgery) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Surgery Type <span class="text-red-500">*</span></label>
                                <div class="flex gap-4 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="surgery[surgery_type]" value="Em" required
                                            class="text-indigo-600 focus:ring-indigo-500" {{ old('surgery.surgery_type', $surgery->surgery_type)=='Em' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Emergency (Em)</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="surgery[surgery_type]" value="Elect" required
                                            class="text-indigo-600 focus:ring-indigo-500" {{ old('surgery.surgery_type', $surgery->surgery_type)=='Elect' ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Elective (Elect)</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ASA Grade</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach(['I', 'II', 'III', 'IV', 'V', 'VI', 'E'] as $asa)
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="surgery[asa_grade]" value="{{ $asa }}"
                                            class="text-indigo-600 focus:ring-indigo-500" {{ old('surgery.asa_grade', $surgery->asa_grade)==$asa ? 'checked' : '' }}>
                                        <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">{{ $asa }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Surgeon Details -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Surgeon Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name of Con. Surgeon</label>
                                    <select name="surgeon_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Surgeon</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->staff_id }}" {{ old('surgeon_id', $surgeonId) == $staff->staff_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name of Con. Anaesthetist</label>
                                    <select name="anaesthetist_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Anaesthetist</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->staff_id }}" {{ old('anaesthetist_id', $anaesthetistId) == $staff->staff_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name of MOA</label>
                                    <select name="moa_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select MOA</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->staff_id }}" {{ old('moa_id', $moaId) == $staff->staff_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Pre-Op Medical Conditions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">Pre-Op Medical Conditions</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($medicalSystems as $system)
                            <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <h4 class="font-bold text-indigo-600 dark:text-indigo-400 mb-2 border-b border-gray-200 dark:border-gray-600 pb-1">
                                    {{ $system->system_name }} ({{ $system->system_code }})
                                </h4>
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                    @foreach($system->conditions as $condition)
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="patient_conditions[]" value="{{ $condition->condition_id }}"
                                            class="rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            {{ in_array($condition->condition_id, old('patient_conditions', $selectedConditionIds)) ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">
                                            {{ $condition->condition_name }}
                                            <span class="text-xs text-gray-400">({{ $condition->patient_type }})</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 4. Anaesthesia Management -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-teal-600 to-cyan-700 px-6 py-4">
                        <h3 class="text-xl font-bold text-white">Anaesthesia Management</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mode of Anaesthesia <span class="text-red-500">*</span></label>
                            <div class="flex gap-6">
                                @foreach(['GA', 'Regional', 'GA + Regional'] as $mode)
                                <label class="inline-flex items-center">
                                    <input type="radio" name="anaesthesia[mode]" value="{{ $mode }}" required
                                        class="text-indigo-600 focus:ring-indigo-500"
                                        {{ old('anaesthesia.mode', optional($anaesthesia)->mode) == $mode ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $mode }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <!-- Induction Agents -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Induction Agents</label>
                                <div class="space-y-1">
                                    @foreach($inductionAgents as $agent)
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="anaesthesia_induction_agents[]" value="{{ $agent->induction_agent_id }}"
                                            class="rounded text-indigo-600"
                                            {{ in_array($agent->induction_agent_id, old('anaesthesia_induction_agents', $selectedInductionIds)) ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $agent->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Maintenance Agents -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Maintenance Agents</label>
                                <div class="space-y-1">
                                    @foreach($maintenanceAgents as $agent)
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="anaesthesia_maintenance_agents[]" value="{{ $agent->maintenance_agent_id }}"
                                            class="rounded text-indigo-600"
                                            {{ in_array($agent->maintenance_agent_id, old('anaesthesia_maintenance_agents', $selectedMaintenanceIds)) ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $agent->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Regional Types -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Regional Techniques</label>
                                <div class="space-y-1">
                                    @foreach($regionalTypes as $type)
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="anaesthesia_regional_types[]" value="{{ $type->regional_type_id }}"
                                            class="rounded text-indigo-600"
                                            {{ in_array($type->regional_type_id, old('anaesthesia_regional_types', $selectedRegionalIds)) ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $type->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <!-- Intubation & Relaxants -->
                            <div>
                                <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <input type="checkbox" name="anaesthesia[difficult_intubation]" value="1"
                                        class="rounded text-indigo-600"
                                        {{ old('anaesthesia.difficult_intubation', optional($anaesthesia)->difficult_intubation) ? 'checked' : '' }}>
                                    <span class="ml-2">Difficult Intubation</span>
                                </label>

                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mt-3 mb-1">Intubation Methods</label>
                                <div class="grid grid-cols-2 gap-1">
                                    @foreach($intubationMethods as $method)
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" name="anaesthesia_intubation_methods[]" value="{{ $method->method_id }}"
                                            class="rounded text-indigo-600"
                                            {{ in_array($method->method_id, old('anaesthesia_intubation_methods', $selectedIntubationIds)) ? 'checked' : '' }}>
                                        <span class="ml-1 text-gray-700 dark:text-gray-300">{{ $method->method_name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <input type="checkbox" name="anaesthesia[relaxant_used]" value="1"
                                        class="rounded text-indigo-600"
                                        {{ old('anaesthesia.relaxant_used', optional($anaesthesia)->relaxant_used) ? 'checked' : '' }}>
                                    <span class="ml-2">Relaxant Used</span>
                                </label>

                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mt-3 mb-1">Relaxants</label>
                                <div class="grid grid-cols-2 gap-1">
                                    @foreach($relaxants as $relaxant)
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" name="anaesthesia_relaxants[]" value="{{ $relaxant->relaxant_id }}"
                                            class="rounded text-indigo-600"
                                            {{ in_array($relaxant->relaxant_id, old('anaesthesia_relaxants', $selectedRelaxantIds)) ? 'checked' : '' }}>
                                        <span class="ml-1 text-gray-700 dark:text-gray-300">{{ $relaxant->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Monitoring & Analgesia & Adverse Events -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Monitoring -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                        <h4 class="font-bold text-lg text-indigo-600 dark:text-indigo-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Monitoring Used</h4>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto">
                            @foreach($monitoringTypes as $monitor)
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="surgery_monitoring[]" value="{{ $monitor->monitoring_id }}"
                                    class="rounded text-indigo-600"
                                    {{ in_array($monitor->monitoring_id, old('surgery_monitoring', $selectedMonitoringIds)) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $monitor->monitoring_name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Analgesics -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                        <h4 class="font-bold text-lg text-indigo-600 dark:text-indigo-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Analgesia Administered</h4>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto">
                            @foreach($analgesics as $analgesic)
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="surgery_analgesics[]" value="{{ $analgesic->analgesic_id }}"
                                    class="rounded text-indigo-600"
                                    {{ in_array($analgesic->analgesic_id, old('surgery_analgesics', $selectedAnalgesicIds)) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $analgesic->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Analgesia Other Details</label>
                            <input type="text" name="surgery[analgesia_other_details]"
                                class="w-full text-xs rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('surgery.analgesia_other_details', $surgery->analgesia_other_details) }}" placeholder="Details if other">
                        </div>
                    </div>

                    <!-- Adverse Events -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                        <h4 class="font-bold text-lg text-red-600 dark:text-red-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Adverse Events</h4>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto">
                            @foreach($adverseEvents as $event)
                            <label class="flex items-center text-sm">
                                <input type="checkbox" name="surgery_adverse_events[]" value="{{ $event->event_id }}"
                                    class="rounded text-red-600"
                                    {{ in_array($event->event_id, old('surgery_adverse_events', $selectedAdverseEventIds)) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $event->event_name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Adverse Event Details</label>
                            <input type="text" name="surgery[adverse_event_other_details]"
                                class="w-full text-xs rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('surgery.adverse_event_other_details', $surgery->adverse_event_other_details) }}" placeholder="Details if any">
                        </div>
                    </div>
                </div>

                <!-- Post-Op Admission -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Post-Op Disposition / Admission</label>
                    <div class="flex gap-6">
                        @foreach(['Ward', 'HDU', 'ICU'] as $adm)
                        <label class="inline-flex items-center">
                            <input type="radio" name="surgery[post_op_admission]" value="{{ $adm }}"
                                class="text-indigo-600 focus:ring-indigo-500"
                                {{ old('surgery.post_op_admission', $surgery->post_op_admission) == $adm ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $adm }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit & Cancel Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.records.index') : route('data_entry.records.index') }}"
                        class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold rounded-lg transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold rounded-lg shadow-lg transition transform hover:-translate-y-0.5">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
