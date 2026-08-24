<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Anaesthesia Audit Form') }}
        </h2>
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
                <p class="font-bold">Error saving record:</p>
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
            <form action="{{ route($routePrefix . 'audit-form.store') }}" method="POST" class="space-y-8">
                @csrf

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
                    <div class="p-6 space-y-4">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patient Name
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="patient[name]" id="patient-name-input" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.name', $prefillPatient?->name) }}" placeholder="Full Name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">PHN <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="patient[phn]" id="patient-phn-input"
                                data-suggestions-url="{{ route($routePrefix . 'audit-form.phn-suggestions') }}"
                                list="phn-suggestions-list" autocomplete="off" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.phn', $prefillPatient?->phn) }}" placeholder="e.g. 12345" {{ $prefillPatient ? 'readonly' : '' }}>
                            <datalist id="phn-suggestions-list"></datalist>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Age</label>
                            <input type="number" name="patient[age]" id="patient-age-input"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.age', $prefillPatient?->age) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sex <span
                                    class="text-red-500">*</span></label>
                            <select name="patient[sex]" id="patient-sex-select" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select</option>
                                <option value="M" {{ old('patient.sex', $prefillPatient?->sex)=='M' ? 'selected' : '' }}>Male</option>
                                <option value="F" {{ old('patient.sex', $prefillPatient?->sex)=='F' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">BHT (Bed Head
                                Ticket)</label>
                            <input type="text" name="patient[bht]" id="patient-bht-input"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ old('patient.bht', $prefillPatient?->bht) }}" placeholder="BHT Number">
                        </div>
                        </div>
                    </div>
                </div>

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
                    <div class="p-6 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Operation
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="surgery[operation_name]" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    value="{{ old('surgery.operation_name') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Surgical
                                    Specialty</label>
                                <select name="surgery[surgical_specialty]"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Specialty</option>
                                    @foreach(['Gen. Surg', 'Ortho', 'Plastic', 'Vascular', 'Neuro', 'Obs', 'Gynae', 'Uro', 'OMF', 'Paed', 'ENT', 'Onco', 'GI', 'GE-ERCP', 'Transplant', 'Cardiac'] as $specialty)
                                    <option value="{{ $specialty }}" {{ old('surgery.surgical_specialty')==$specialty
                                        ? 'selected' : '' }}>{{ $specialty }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of
                                    Surgery</label>
                                <input type="date" name="surgery[date_of_surgery]"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    value="{{ old('surgery.date_of_surgery') ?? date('Y-m-d') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Surgery Type <span class="text-red-500">*</span></label>
                                <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg border border-gray-200 dark:border-gray-600 gap-1 w-full sm:w-auto">
                                    <label class="cursor-pointer flex-1 sm:flex-initial">
                                        <input type="radio" name="surgery[surgery_type]" value="Em" class="peer sr-only" required {{ old('surgery.surgery_type')=='Em' ? 'checked' : '' }}>
                                        <span class="px-4 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-red-600 peer-checked:text-white peer-checked:shadow-sm">
                                            Emergency (Em)
                                        </span>
                                    </label>
                                    <label class="cursor-pointer flex-1 sm:flex-initial">
                                        <input type="radio" name="surgery[surgery_type]" value="Elect" class="peer sr-only" required {{ old('surgery.surgery_type')=='Elect' ? 'checked' : '' }}>
                                        <span class="px-4 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:shadow-sm">
                                            Elective (Elect)
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ASA Grade</label>
                                <div class="flex flex-wrap p-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg border border-gray-200 dark:border-gray-600 gap-1">
                                    @foreach(['I','II','III','IV','V','VI','E'] as $asa)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="surgery[asa_grade]" value="{{ $asa }}" class="peer sr-only" {{ old('surgery.asa_grade')==$asa ? 'checked' : '' }}>
                                        <span class="px-3.5 py-1.5 text-sm font-bold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:shadow-sm">
                                            {{ $asa }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Surgeon Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name
                                        of Con. Surgeon</label>
                                    <select name="surgeon_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Surgeon</option>
                                        @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->staff_id }}" {{ old('surgeon_id')==$staff->staff_id ?
                                            'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name
                                        of Con. Anaesthetist</label>
                                    <select name="anaesthetist_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Anaesthetist</option>
                                        @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->staff_id }}" {{ old('anaesthetist_id')==$staff->
                                            staff_id ? 'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name
                                        of MOA</label>
                                    <select name="moa_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select MOA</option>
                                        @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->staff_id }}" {{ old('moa_id')==$staff->staff_id ?
                                            'selected' : '' }}>
                                            {{ $staff->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden"
                    id="medical-disease-section">
                    <div class="bg-gradient-to-r from-teal-500 to-emerald-600 px-6 py-4 flex justify-between items-center flex-wrap gap-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Medical disease
                        </h3>
                        <div class="flex bg-black/20 rounded-lg p-1">
                            <label class="cursor-pointer px-4 py-1 text-sm font-semibold text-white rounded-md transition-colors" id="label-adult">
                                <input type="radio" name="medical_disease_type" value="Adult" class="hidden" checked>
                                Adult
                            </label>
                            <label class="cursor-pointer px-4 py-1 text-sm font-semibold text-white rounded-md transition-colors" id="label-paediatric">
                                <input type="radio" name="medical_disease_type" value="Paediatric" class="hidden">
                                Paediatric
                            </label>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($medicalSystems as $system)
                            @php
                            $hasAdult = $system->conditions->where('patient_type', 'Adult')->count() > 0;
                            $hasPaed = $system->conditions->where('patient_type', 'Paediatric')->count() > 0;
                            @endphp
                            <div class="system-card bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600"
                                data-has-adult="{{ $hasAdult ? 'true' : 'false' }}"
                                data-has-paed="{{ $hasPaed ? 'true' : 'false' }}">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-600 pb-2 mb-3">
                                    {{ $system->system_name }} ({{ $system->system_code }})
                                </h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($system->conditions as $condition)
                                    <label class="condition-label flex items-start cursor-pointer group"
                                        data-type="{{ $condition->patient_type }}">
                                        <input type="checkbox" name="patient_conditions[]"
                                            value="{{ $condition->condition_id }}" {{ in_array($condition->condition_id,
                                        old('patient_conditions', [])) ? 'checked' : '' }} class="mt-1 rounded
                                        border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-800
                                        dark:border-gray-600">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $condition->condition_name }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-amber-600 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg>
                            Anaesthesia Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Monitoring</h4>
                            <div class="flex flex-wrap gap-4">
                                @foreach($monitoringTypes as $monitor)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="surgery_monitoring[]"
                                        value="{{ $monitor->monitoring_id }}" {{ in_array($monitor->monitoring_id,
                                    old('surgery_monitoring', [])) ? 'checked' : '' }} class="rounded text-indigo-600
                                    focus:ring-indigo-500 dark:bg-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $monitor->monitoring_name
                                        }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                                Mode of Anaesthesia</h4>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mode <span class="text-red-500">*</span></label>
                                <div class="inline-flex flex-wrap p-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg border border-gray-200 dark:border-gray-600 gap-1">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="anaesthesia[mode]" value="GA" class="peer sr-only anaesthesia-mode-radio" required {{ old('anaesthesia.mode')=='GA' ? 'checked' : '' }}>
                                        <span class="px-4 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:shadow-sm">
                                            General (GA)
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="anaesthesia[mode]" value="Regional" class="peer sr-only anaesthesia-mode-radio" required {{ old('anaesthesia.mode')=='Regional' ? 'checked' : '' }}>
                                        <span class="px-4 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:shadow-sm">
                                            Regional
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="anaesthesia[mode]" value="GA + Regional" class="peer sr-only anaesthesia-mode-radio" required {{ old('anaesthesia.mode')=='GA + Regional' ? 'checked' : '' }}>
                                        <span class="px-4 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-purple-600 peer-checked:text-white peer-checked:shadow-sm">
                                            General + Regional (GA + Regional)
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="bg-amber-50 dark:bg-gray-700/30 p-5 rounded-lg border border-amber-100 dark:border-gray-600 ga-section hidden"
                                id="ga-section-container">
                                <h5 class="font-bold text-amber-900 dark:text-amber-300 mb-4">GA Details</h5>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Induction</label>
                                        <div class="flex gap-4 mb-2">
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="anaesthesia[induction_type]" value="Gas" {{
                                                    old('anaesthesia.induction_type')=='Gas' ? 'checked' : '' }}
                                                    class="induction-type-radio text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Gas</span>
                                            </label>
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="anaesthesia[induction_type]" value="IV" {{
                                                    old('anaesthesia.induction_type')=='IV' ? 'checked' : '' }}
                                                    class="induction-type-radio text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">IV</span>
                                            </label>
                                        </div>
                                        <div id="induction-agents-container" class="flex flex-wrap gap-3 mt-2 pl-2 border-l-2 border-amber-200 {{ old('anaesthesia.induction_type') == 'IV' ? '' : 'hidden' }}">
                                            @foreach($inductionAgents as $agent)
                                            <label class="flex items-center space-x-2">
                                                <input type="checkbox" name="anaesthesia_induction_agents[]"
                                                    value="{{ $agent->induction_agent_id }}" 
                                                    data-name="{{ strtolower($agent->name) }}"
                                                    {{ in_array($agent->induction_agent_id, old('anaesthesia_induction_agents', [])) ? 'checked' : '' }}
                                                    class="induction-agent-cb rounded border-amber-300 text-amber-600 focus:ring-amber-500 dark:bg-gray-800 dark:border-gray-600">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $agent->name }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                        
                                        <div id="induction-other-container" class="mt-3 pl-2 border-l-2 border-amber-200 {{ old('anaesthesia.induction_type') == 'IV' && old('anaesthesia.induction_other_details') ? '' : 'hidden' }}">
                                            <input type="text" name="anaesthesia[induction_other_details]" 
                                                value="{{ old('anaesthesia.induction_other_details') }}" 
                                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                                placeholder="Please specify other induction agent(s)...">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center space-x-3 cursor-pointer mb-2">
                                            <input type="checkbox" id="difficult-intubation-checkbox" name="anaesthesia[difficult_intubation]" {{ old('anaesthesia.difficult_intubation') ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500 w-5 h-5 dark:bg-gray-700">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Difficult Intubation</span>
                                        </label>
                                        
                                        <div id="intubation-methods-container" class="flex flex-wrap gap-3 mt-2 pl-8 {{ old('anaesthesia.difficult_intubation') ? '' : 'hidden' }}">
                                            @if(isset($intubationMethods))
                                                @foreach($intubationMethods as $method)
                                                <label class="flex items-center space-x-2">
                                                    <input type="checkbox" name="anaesthesia_intubation_methods[]" value="{{ $method->method_id }}" {{ in_array($method->method_id, old('anaesthesia_intubation_methods', [])) ? 'checked' : '' }} class="rounded border-amber-300 text-amber-600 focus:ring-amber-500 dark:bg-gray-800 dark:border-gray-600">
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $method->method_name }}</span>
                                                </label>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maintenance</label>
                                        <div class="flex gap-4 mb-2">
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="anaesthesia[maintenance_route]" value="TIVA" {{
                                                    old('anaesthesia.maintenance_route')=='TIVA' ? 'checked' : '' }}
                                                    class="maintenance-route-radio text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">TIVA</span>
                                            </label>
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" name="anaesthesia[maintenance_route]" value="Gas" {{
                                                    old('anaesthesia.maintenance_route')=='Gas' ? 'checked' : '' }}
                                                    class="maintenance-route-radio text-amber-600 focus:ring-amber-500 dark:bg-gray-700">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">Gas</span>
                                            </label>
                                        </div>
                                        <div id="maintenance-agents-container" class="flex flex-wrap gap-3 mt-2 pl-2 border-l-2 border-amber-200 {{ old('anaesthesia.maintenance_route')=='Gas' ? '' : 'hidden' }}">
                                            @foreach($maintenanceAgents as $agent)
                                            <label class="flex items-center space-x-2">
                                                <input type="checkbox" name="anaesthesia_maintenance_agents[]"
                                                    value="{{ $agent->maintenance_agent_id }}" {{
                                                    in_array($agent->maintenance_agent_id,
                                                old('anaesthesia_maintenance_agents', [])) ? 'checked' : '' }}
                                                class="rounded border-amber-300 text-amber-600 focus:ring-amber-500
                                                dark:bg-gray-800 dark:border-gray-600">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $agent->name }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center space-x-3 cursor-pointer mb-2">
                                            <input type="checkbox" id="relaxant-used-checkbox" name="anaesthesia[relaxant_used]" {{
                                                old('anaesthesia.relaxant_used') ? 'checked' : '' }}
                                                class="rounded text-amber-600 focus:ring-amber-500 w-5 h-5 dark:bg-gray-700">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Relaxant
                                                Used</span>
                                        </label>
                                        <div id="relaxant-options-container" class="flex flex-wrap gap-3 mt-2 pl-8 {{ old('anaesthesia.relaxant_used') ? '' : 'hidden' }}">
                                            @foreach($relaxants as $relaxant)
                                            <label class="flex items-center space-x-2">
                                                <input type="checkbox" name="anaesthesia_relaxants[]"
                                                    value="{{ $relaxant->relaxant_id }}" {{
                                                    in_array($relaxant->relaxant_id, old('anaesthesia_relaxants', [])) ?
                                                'checked' : '' }} class="rounded border-amber-300 text-amber-600
                                                focus:ring-amber-500 dark:bg-gray-800 dark:border-gray-600">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $relaxant->name }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-lg border border-blue-100 dark:border-blue-800/50 regional-section hidden mt-4"
                                id="regional-section-container">
                                <h5 class="font-bold text-blue-900 dark:text-blue-300 mb-4">Regional Details</h5>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($regionalTypes as $type)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="anaesthesia_regional_types[]"
                                            value="{{ $type->regional_type_id }}"
                                            data-name="{{ strtolower($type->name) }}"
                                            {{ in_array($type->regional_type_id, old('anaesthesia_regional_types', [])) ? 'checked' : '' }}
                                            class="regional-type-cb rounded border-blue-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $type->name }}</span>
                                    </label>
                                    @endforeach
                                </div>

                                <div id="regional-other-container" class="mt-4 pl-3 border-l-2 border-blue-300 {{ old('anaesthesia.regional_other_details') ? '' : 'hidden' }}">
                                    <input type="text" name="anaesthesia[regional_other_details]"
                                        value="{{ old('anaesthesia.regional_other_details') }}"
                                        class="w-full md:w-1/2 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Please specify other regional type(s)...">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Analgesia</h4>
                            <div class="flex flex-wrap gap-4">
                                @foreach($analgesics as $analgesic)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="surgery_analgesics[]"
                                            value="{{ $analgesic->analgesic_id }}"
                                            data-name="{{ strtolower($analgesic->name) }}"
                                            {{ in_array($analgesic->analgesic_id, old('surgery_analgesics', [])) ? 'checked' : '' }}
                                            class="analgesia-cb rounded text-teal-600 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $analgesic->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div id="analgesia-other-container" class="mt-4 pl-3 border-l-2 border-teal-300 {{ old('surgery.analgesia_other_details') ? '' : 'hidden' }}">
                                <input type="text" name="surgery[analgesia_other_details]"
                                    value="{{ old('surgery.analgesia_other_details') }}"
                                    class="w-full md:w-1/2 text-sm rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Please specify other analgesia...">
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 border-b border-red-200 dark:border-red-900/50 pb-2 text-red-700 dark:text-red-400">Adverse Events</h4>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adverse Events Occurred?</label>
                                <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg border border-gray-200 dark:border-gray-600 gap-1">
                                    @php
                                        $hasAdverse = old('has_adverse_events') == 'yes' || count(old('surgery_adverse_events', [])) > 0 || old('surgery.adverse_event_other_details');
                                    @endphp
                                    <label class="cursor-pointer">
                                        <input type="radio" name="has_adverse_events" value="no" class="peer sr-only adverse-events-toggle-radio" {{ !$hasAdverse ? 'checked' : '' }}>
                                        <span class="px-5 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-green-600 peer-checked:text-white peer-checked:shadow-sm">
                                            No
                                        </span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="has_adverse_events" value="yes" class="peer sr-only adverse-events-toggle-radio" {{ $hasAdverse ? 'checked' : '' }}>
                                        <span class="px-5 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-red-600 peer-checked:text-white peer-checked:shadow-sm">
                                            Yes
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div id="adverse-events-panel" class="{{ $hasAdverse ? '' : 'hidden' }}">
                                <div class="flex flex-wrap gap-4">
                                    @foreach($adverseEvents as $event)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="surgery_adverse_events[]"
                                                value="{{ $event->event_id }}"
                                                data-name="{{ strtolower($event->event_name) }}"
                                                {{ in_array($event->event_id, old('surgery_adverse_events', [])) ? 'checked' : '' }}
                                                class="adverse-event-cb rounded text-red-600 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $event->event_name }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div id="adverse-other-container" class="mt-4 pl-3 border-l-2 border-red-300 {{ old('surgery.adverse_event_other_details') ? '' : 'hidden' }}">
                                    <input type="text" name="surgery[adverse_event_other_details]"
                                        value="{{ old('surgery.adverse_event_other_details') }}"
                                        class="w-full md:w-1/2 text-sm rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Please specify other adverse event(s)...">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">Post Op Admission</h4>
                            <div class="inline-flex flex-wrap p-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg border border-gray-200 dark:border-gray-600 gap-1">
                                @foreach(['Ward', 'HDU', 'ICU'] as $admission)
                                <label class="cursor-pointer">
                                    <input type="radio" name="surgery[post_op_admission]" value="{{ $admission }}" class="peer sr-only" {{ old('surgery.post_op_admission') == $admission ? 'checked' : '' }}>
                                    <span class="px-5 py-2 text-sm font-semibold rounded-md flex items-center justify-center transition-all text-gray-600 dark:text-gray-300 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:shadow-sm">
                                        {{ $admission }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Notes</label>
                            <textarea name="anaesthesia[notes]" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Any complications, special notes, etc.">{{ old('anaesthesia.notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-4">
                    <button type="reset" class="px-6 py-3 text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 transition-colors">
                        Reset Form
                    </button>
                    <pre> </pre>
                    <button type="submit" class="px-6 py-3 text-base font-medium text-white bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 rounded-lg focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-800 shadow-md hover:shadow-lg transition-all">
                        Save Audit Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Patient Type Toggle Logic
            const ageInput = document.getElementById('patient-age-input');
            const phnInput = document.getElementById('patient-phn-input');
            const phnSuggestionsList = document.getElementById('phn-suggestions-list');
            const patientNameInput = document.getElementById('patient-name-input');
            const patientSexSelect = document.getElementById('patient-sex-select');
            const adultLabel = document.getElementById('label-adult');
            const paedLabel = document.getElementById('label-paediatric');
            const systemCards = document.querySelectorAll('.system-card');
            const medicalDiseaseRadios = document.querySelectorAll('input[name="medical_disease_type"]');
            let phnSuggestionTimer = null;
            let phnSuggestionController = null;
            let phnSuggestionsCache = [];

            function selectMedicalDiseaseType(type) {
                const targetRadio = document.querySelector('input[name="medical_disease_type"][value="' + type + '"]');

                if (targetRadio && !targetRadio.checked) {
                    targetRadio.click();
                } else if (targetRadio) {
                    updateDisplay(type);
                }
            }

            function syncMedicalDiseaseByAge() {
                const age = parseInt(ageInput ? ageInput.value : '', 10);

                if (Number.isNaN(age)) {
                    return;
                }

                if (age > 18) {
                    selectMedicalDiseaseType('Adult');
                } else {
                    selectMedicalDiseaseType('Paediatric');
                }
            }

            async function loadPhnSuggestions() {
                if (!phnInput || !phnSuggestionsList) {
                    return;
                }

                const query = phnInput.value.trim();

                if (query.length < 2) {
                    phnSuggestionsList.innerHTML = '';
                    return;
                }

                const suggestionsUrl = phnInput.dataset.suggestionsUrl;

                if (!suggestionsUrl) {
                    return;
                }

                if (phnSuggestionController) {
                    phnSuggestionController.abort();
                }

                phnSuggestionController = new AbortController();

                try {
                    const response = await fetch(`${suggestionsUrl}?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'Accept': 'application/json',
                        },
                        signal: phnSuggestionController.signal,
                    });

                    if (!response.ok) {
                        return;
                    }

                    const suggestions = await response.json();
                    phnSuggestionsCache = Array.isArray(suggestions) ? suggestions : [];
                    phnSuggestionsList.innerHTML = '';

                    phnSuggestionsCache.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.phn;
                        option.label = item.name ? `${item.phn} - ${item.name}` : item.phn;
                        phnSuggestionsList.appendChild(option);
                    });

                    applyPatientDetailsFromPhn();
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        phnSuggestionsList.innerHTML = '';
                        phnSuggestionsCache = [];
                    }
                }
            }

            function applyPatientDetailsFromPhn() {
                if (!phnInput) {
                    return;
                }

                const matchedPatient = phnSuggestionsCache.find(item => String(item.phn) === phnInput.value.trim());

                if (!matchedPatient) {
                    return;
                }

                if (patientNameInput && matchedPatient.name) {
                    patientNameInput.value = matchedPatient.name;
                }

                if (patientSexSelect && matchedPatient.sex) {
                    patientSexSelect.value = matchedPatient.sex;
                }
            }
            
            function updateDisplay(type) {
                // Update toggle styling
                if (type === 'Adult') {
                    adultLabel.classList.add('bg-white', 'text-teal-600', 'shadow-sm');
                    adultLabel.classList.remove('text-white');
                    paedLabel.classList.remove('bg-white', 'text-teal-600', 'shadow-sm');
                    paedLabel.classList.add('text-white');
                } else {
                    paedLabel.classList.add('bg-white', 'text-teal-600', 'shadow-sm');
                    paedLabel.classList.remove('text-white');
                    adultLabel.classList.remove('bg-white', 'text-teal-600', 'shadow-sm');
                    adultLabel.classList.add('text-white');
                }

                // Show/hide systems and conditions
                systemCards.forEach(card => {
                    const hasAdult = card.dataset.hasAdult === 'true';
                    const hasPaed = card.dataset.hasPaed === 'true';
                    
                    if ((type === 'Adult' && !hasAdult) || (type === 'Paediatric' && !hasPaed)) {
                        card.style.display = 'none';
                    } else {
                        card.style.display = 'block';
                        const conditions = card.querySelectorAll('.condition-label');
                        conditions.forEach(cond => {
                            if (cond.dataset.type === type) {
                                cond.style.display = 'flex';
                            } else {
                                cond.style.display = 'none';
                                cond.querySelector('input').checked = false; // Uncheck hidden items
                            }
                        });
                    }
                });
            }

            // Radio change event
            medicalDiseaseRadios.forEach(radio => {
                radio.addEventListener('change', (e) => updateDisplay(e.target.value));
            });

            if (ageInput) {
                ageInput.addEventListener('input', syncMedicalDiseaseByAge);
                ageInput.addEventListener('change', syncMedicalDiseaseByAge);
            }

            if (phnInput) {
                phnInput.addEventListener('input', function() {
                    window.clearTimeout(phnSuggestionTimer);
                    phnSuggestionTimer = window.setTimeout(loadPhnSuggestions, 250);
                    applyPatientDetailsFromPhn();
                });

                phnInput.addEventListener('focus', loadPhnSuggestions);
                phnInput.addEventListener('change', applyPatientDetailsFromPhn);
            }

            // Init
            const checkedRadio = document.querySelector('input[name="medical_disease_type"]:checked');
            if(checkedRadio) updateDisplay(checkedRadio.value);

            if (ageInput && ageInput.value !== '') {
                syncMedicalDiseaseByAge();
            }

            // Anaesthesia Mode visibility toggling
            const modeRadios = document.querySelectorAll('.anaesthesia-mode-radio');
            const gaSection = document.getElementById('ga-section-container');
            const regionalSection = document.getElementById('regional-section-container');

            function updateAnaesthesiaMode() {
                let mode = '';
                modeRadios.forEach(r => { if (r.checked) mode = r.value; });

                if (mode === 'GA') {
                    gaSection.classList.remove('hidden');
                    regionalSection.classList.add('hidden');
                } else if (mode === 'Regional') {
                    regionalSection.classList.remove('hidden');
                    gaSection.classList.add('hidden');
                } else if (mode === 'GA + Regional') {
                    gaSection.classList.remove('hidden');
                    regionalSection.classList.remove('hidden');
                } else {
                    gaSection.classList.add('hidden');
                    regionalSection.classList.add('hidden');
                }
            }

            modeRadios.forEach(r => r.addEventListener('change', updateAnaesthesiaMode));
            updateAnaesthesiaMode(); // Run on init

            // Adverse Events Yes/No Toggle Logic
            const adverseRadios = document.querySelectorAll('.adverse-events-toggle-radio');
            const adversePanel = document.getElementById('adverse-events-panel');

            function updateAdverseEventsToggle() {
                let hasAdverse = false;
                adverseRadios.forEach(r => { if (r.checked && r.value === 'yes') hasAdverse = true; });
                if (hasAdverse) {
                    adversePanel.classList.remove('hidden');
                } else {
                    adversePanel.classList.add('hidden');
                    document.querySelectorAll('.adverse-event-cb').forEach(cb => cb.checked = false);
                }
            }

            adverseRadios.forEach(r => r.addEventListener('change', updateAdverseEventsToggle));
            updateAdverseEventsToggle();
            
            // Difficult Intubation Toggle Logic
            const diffIntCheckbox = document.getElementById('difficult-intubation-checkbox');
            const diffIntContainer = document.getElementById('intubation-methods-container');

            if (diffIntCheckbox && diffIntContainer) {
                diffIntCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        diffIntContainer.classList.remove('hidden');
                    } else {
                        diffIntContainer.classList.add('hidden');
                        // Uncheck all sub-boxes when hiding
                        const checkboxes = diffIntContainer.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(cb => cb.checked = false);
                    }
                });
            }

            // Relaxant Used Toggle Logic
            const relaxantCheckbox = document.getElementById('relaxant-used-checkbox');
            const relaxantContainer = document.getElementById('relaxant-options-container');

            if (relaxantCheckbox && relaxantContainer) {
                relaxantCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        relaxantContainer.classList.remove('hidden');
                    } else {
                        relaxantContainer.classList.add('hidden');
                        // Uncheck all sub-boxes when hiding
                        const checkboxes = relaxantContainer.querySelectorAll('input[type="checkbox"]');
                        checkboxes.forEach(cb => cb.checked = false);
                    }
                });
            }

            // Maintenance Route Toggle Logic
            const maintenanceRadios = document.querySelectorAll('.maintenance-route-radio');
            const maintenanceAgentsContainer = document.getElementById('maintenance-agents-container');

            if (maintenanceRadios && maintenanceAgentsContainer) {
                maintenanceRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'Gas') {
                            maintenanceAgentsContainer.classList.remove('hidden');
                        } else {
                            maintenanceAgentsContainer.classList.add('hidden');
                            // Uncheck all checkboxes when hiding
                            const checkboxes = maintenanceAgentsContainer.querySelectorAll('input[type="checkbox"]');
                            checkboxes.forEach(cb => cb.checked = false);
                        }
                    });
                });
            }

            // Induction Type Toggle Logic
            const inductionTypeRadios = document.querySelectorAll('input[name="anaesthesia[induction_type]"]');
            const inductionAgentsContainer = document.getElementById('induction-agents-container');
            const inductionOtherContainer = document.getElementById('induction-other-container');
            const inductionCheckboxes = document.querySelectorAll('.induction-agent-cb');

            function resetInductionSelections() {
                inductionCheckboxes.forEach(cb => {
                    cb.checked = false;
                });

                if (inductionOtherContainer) {
                    const input = inductionOtherContainer.querySelector('input');
                    if (input) input.value = '';
                }
            }

            function checkInductionVisibility() {
                const selectedRadio = document.querySelector('input[name="anaesthesia[induction_type]"]:checked');
                const isIVSelected = selectedRadio && selectedRadio.value === 'IV';

                if (inductionAgentsContainer) {
                    inductionAgentsContainer.classList.toggle('hidden', !isIVSelected);
                }

                if (!isIVSelected) {
                    if (inductionOtherContainer) {
                        inductionOtherContainer.classList.add('hidden');
                    }
                    resetInductionSelections();
                    return;
                }

                checkInductionOthers();
            }

            function checkInductionOthers() {
                let isOthersChecked = false;
                inductionCheckboxes.forEach(cb => {
                    // Check if checked AND the name is 'others' or 'other'
                    if (cb.checked && (cb.dataset.name === 'others' || cb.dataset.name === 'other')) {
                        isOthersChecked = true;
                    }
                });
                
                if (isOthersChecked) {
                    if (inductionOtherContainer) {
                        inductionOtherContainer.classList.remove('hidden');
                    }
                } else {
                    if (inductionOtherContainer) {
                        inductionOtherContainer.classList.add('hidden');
                    }
                    // Clear the text box when hidden so we don't save accidental data
                    const input = inductionOtherContainer ? inductionOtherContainer.querySelector('input') : null;
                    if(input) input.value = '';
                }
            }

            if (inductionTypeRadios.length > 0) {
                inductionTypeRadios.forEach(radio => {
                    radio.addEventListener('change', checkInductionVisibility);
                });
            }

            if (inductionCheckboxes.length > 0 && inductionOtherContainer) {
                inductionCheckboxes.forEach(cb => {
                    cb.addEventListener('change', checkInductionOthers);
                });
            }

            checkInductionVisibility();

            // "Others" Regional Type Text Box Toggle Logic
            const regionalCheckboxes = document.querySelectorAll('.regional-type-cb');
            const regionalOtherContainer = document.getElementById('regional-other-container');

            function checkRegionalOthers() {
                let isOthersChecked = false;
                regionalCheckboxes.forEach(cb => {
                    // Check if checked AND the name contains 'other'
                    if (cb.checked && (cb.dataset.name === 'others' || cb.dataset.name === 'other')) {
                        isOthersChecked = true;
                    }
                });
                
                if (isOthersChecked) {
                    regionalOtherContainer.classList.remove('hidden');
                } else {
                    regionalOtherContainer.classList.add('hidden');
                    // Clear the text box when hidden so we don't save accidental data
                    const input = regionalOtherContainer.querySelector('input');
                    if(input) input.value = '';
                }
            }

            if (regionalCheckboxes.length > 0 && regionalOtherContainer) {
                regionalCheckboxes.forEach(cb => {
                    cb.addEventListener('change', checkRegionalOthers);
                });
                checkRegionalOthers(); // Run on page load
            }

            // "Others" Adverse Event Text Box Toggle Logic
            const adverseCheckboxes = document.querySelectorAll('.adverse-event-cb');
            const adverseOtherContainer = document.getElementById('adverse-other-container');

            function checkAdverseOthers() {
                let isOthersChecked = false;
                adverseCheckboxes.forEach(cb => {
                    // Check if checked AND the name contains 'other'
                    if (cb.checked && (cb.dataset.name === 'others' || cb.dataset.name === 'other')) {
                        isOthersChecked = true;
                    }
                });
                
                if (isOthersChecked) {
                    adverseOtherContainer.classList.remove('hidden');
                } else {
                    adverseOtherContainer.classList.add('hidden');
                    // Clear the text box when hidden so we don't save accidental data
                    const input = adverseOtherContainer.querySelector('input');
                    if(input) input.value = '';
                }
            }

            if (adverseCheckboxes.length > 0 && adverseOtherContainer) {
                adverseCheckboxes.forEach(cb => {
                    cb.addEventListener('change', checkAdverseOthers);
                });
                checkAdverseOthers(); // Run on page load
            }

            // "Others" Analgesia Text Box Toggle Logic
            const analgesiaCheckboxes = document.querySelectorAll('.analgesia-cb');
            const analgesiaOtherContainer = document.getElementById('analgesia-other-container');

            function checkAnalgesiaOthers() {
                let isOthersChecked = false;
                analgesiaCheckboxes.forEach(cb => {
                    // Check if checked AND the name contains 'other'
                    if (cb.checked && (cb.dataset.name === 'others' || cb.dataset.name === 'other')) {
                        isOthersChecked = true;
                    }
                });
                
                if (isOthersChecked) {
                    analgesiaOtherContainer.classList.remove('hidden');
                } else {
                    analgesiaOtherContainer.classList.add('hidden');
                    // Clear the text box when hidden so we don't save accidental data
                    const input = analgesiaOtherContainer.querySelector('input');
                    if(input) input.value = '';
                }
            }

            if (analgesiaCheckboxes.length > 0 && analgesiaOtherContainer) {
                analgesiaCheckboxes.forEach(cb => {
                    cb.addEventListener('change', checkAnalgesiaOthers);
                });
            });
    </script>
</x-app-layout>