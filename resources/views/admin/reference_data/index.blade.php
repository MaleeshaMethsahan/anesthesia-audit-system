<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
                        {{ __('Clinical Master Data Management') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        Audit Form Settings
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Manage reference parameters, medical conditions, and anesthesia agents used in the audit forms.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.audit-form-settings.doctors') }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    Doctors in Form
                </a>
                <a href="{{ route('admin.audit-form-settings.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition">
                    ← Form Settings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Tab Container -->
            <div x-data="{ tab: 'conditions' }" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                
                <!-- Category Tabs Header -->
                <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <button @click="tab = 'conditions'" :class="{ 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800': tab === 'conditions', 'text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'conditions' }" class="px-6 py-4 border-b-2 text-sm font-medium transition-colors">
                        Medical Diseases / Conditions
                    </button>
                    <button @click="tab = 'monitoring'" :class="{ 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800': tab === 'monitoring', 'text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'monitoring' }" class="px-6 py-4 border-b-2 text-sm font-medium transition-colors">
                        Monitoring Types
                    </button>
                    <button @click="tab = 'anaesthesia'" :class="{ 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800': tab === 'anaesthesia', 'text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'anaesthesia' }" class="px-6 py-4 border-b-2 text-sm font-medium transition-colors">
                        Anaesthesia Agents & Methods
                    </button>
                    <button @click="tab = 'analgesics'" :class="{ 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800': tab === 'analgesics', 'text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'analgesics' }" class="px-6 py-4 border-b-2 text-sm font-medium transition-colors">
                        Analgesics
                    </button>
                    <button @click="tab = 'events'" :class="{ 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold bg-white dark:bg-gray-800': tab === 'events', 'text-gray-500 hover:text-gray-700 dark:text-gray-400': tab !== 'events' }" class="px-6 py-4 border-b-2 text-sm font-medium transition-colors">
                        Adverse Events
                    </button>
                </div>

                <div class="p-6">

                    <!-- TAB 1: MEDICAL DISEASES / CONDITIONS -->
                    <div x-show="tab === 'conditions'" space-y-6>
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Medical Conditions & Diseases</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Manage systemic diseases available in the audit form.</p>
                            </div>
                            <!-- Add Form -->
                            <form action="{{ route('admin.reference-data.store', 'condition') }}" method="POST" class="flex flex-wrap items-center gap-3">
                                @csrf
                                <select name="system_id" required class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select System</option>
                                    @foreach($medicalSystems as $sys)
                                        <option value="{{ $sys->system_id }}">{{ $sys->system_name }} ({{ $sys->system_code }})</option>
                                    @endforeach
                                </select>
                                <input type="text" name="condition_name" placeholder="Condition Name" required class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <select name="patient_type" required class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="Adult">Adult</option>
                                    <option value="Paediatric">Paediatric</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold shadow-sm transition">
                                    + Add Disease
                                </button>
                            </form>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">ID</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">System</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Condition Name</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Patient Type</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($conditions as $cond)
                                        <tr x-data="{ editing: false, name: '{{ addslashes($cond->condition_name) }}', system_id: '{{ $cond->system_id }}', patient_type: '{{ $cond->patient_type }}' }">
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $cond->condition_id }}</td>
                                            <td class="px-4 py-3">
                                                <span x-show="!editing" class="font-medium text-gray-900 dark:text-white">{{ $cond->system->system_name ?? 'N/A' }}</span>
                                                <select x-show="editing" x-model="system_id" form="edit-cond-{{ $cond->condition_id }}" name="system_id" class="text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    @foreach($medicalSystems as $sys)
                                                        <option value="{{ $sys->system_id }}">{{ $sys->system_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span x-show="!editing" class="font-bold text-indigo-600 dark:text-indigo-400">{{ $cond->condition_name }}</span>
                                                <input x-show="editing" x-model="name" form="edit-cond-{{ $cond->condition_id }}" type="text" name="condition_name" class="text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </td>
                                            <td class="px-4 py-3">
                                                <span x-show="!editing" class="px-2 py-0.5 rounded text-xs font-semibold {{ $cond->patient_type == 'Adult' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                                    {{ $cond->patient_type }}
                                                </span>
                                                <select x-show="editing" x-model="patient_type" form="edit-cond-{{ $cond->condition_id }}" name="patient_type" class="text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                    <option value="Adult">Adult</option>
                                                    <option value="Paediatric">Paediatric</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <form id="edit-cond-{{ $cond->condition_id }}" action="{{ route('admin.reference-data.update', ['type' => 'condition', 'id' => $cond->condition_id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <button x-show="!editing" @click="editing = true" type="button" class="text-xs text-blue-600 hover:text-blue-900 font-bold mr-3">Edit</button>
                                                <button x-show="editing" form="edit-cond-{{ $cond->condition_id }}" type="submit" class="text-xs text-green-600 hover:text-green-900 font-bold mr-2">Save</button>
                                                <button x-show="editing" @click="editing = false" type="button" class="text-xs text-gray-500 hover:text-gray-700 font-bold mr-3">Cancel</button>

                                                <form action="{{ route('admin.reference-data.destroy', ['type' => 'condition', 'id' => $cond->condition_id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this disease/condition?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-600 hover:text-red-900 font-bold">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: MONITORING TYPES -->
                    <div x-show="tab === 'monitoring'" space-y-6>
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Monitoring Types</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Manage surgical monitoring options.</p>
                            </div>
                            <form action="{{ route('admin.reference-data.store', 'monitoring') }}" method="POST" class="flex items-center gap-3">
                                @csrf
                                <input type="text" name="monitoring_name" placeholder="Monitoring Name (e.g. ECG, BIS)" required class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold shadow-sm transition">
                                    + Add Monitoring Type
                                </button>
                            </form>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">ID</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Monitoring Name</th>
                                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($monitoringTypes as $m)
                                        <tr x-data="{ editing: false, name: '{{ addslashes($m->monitoring_name) }}' }">
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $m->monitoring_id }}</td>
                                            <td class="px-4 py-3">
                                                <span x-show="!editing" class="font-bold text-gray-900 dark:text-white">{{ $m->monitoring_name }}</span>
                                                <input x-show="editing" x-model="name" form="edit-mon-{{ $m->monitoring_id }}" type="text" name="monitoring_name" class="text-xs rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <form id="edit-mon-{{ $m->monitoring_id }}" action="{{ route('admin.reference-data.update', ['type' => 'monitoring', 'id' => $m->monitoring_id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <button x-show="!editing" @click="editing = true" type="button" class="text-xs text-blue-600 hover:text-blue-900 font-bold mr-3">Edit</button>
                                                <button x-show="editing" form="edit-mon-{{ $m->monitoring_id }}" type="submit" class="text-xs text-green-600 hover:text-green-900 font-bold mr-2">Save</button>
                                                <button x-show="editing" @click="editing = false" type="button" class="text-xs text-gray-500 hover:text-gray-700 font-bold mr-3">Cancel</button>

                                                <form action="{{ route('admin.reference-data.destroy', ['type' => 'monitoring', 'id' => $m->monitoring_id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this monitoring type?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-600 hover:text-red-900 font-bold">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: ANAESTHESIA AGENTS & METHODS -->
                    <div x-show="tab === 'anaesthesia'" x-data="{ subtab: 'induction' }" space-y-6>
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Anaesthesia Agents & Methods</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage Induction, Maintenance, Relaxants, Regional Types, and Intubation Methods.</p>
                        </div>

                        <!-- Subtabs -->
                        <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 mb-6">
                            <button @click="subtab = 'induction'" :class="{ 'border-amber-500 text-amber-600 font-bold': subtab === 'induction', 'text-gray-500': subtab !== 'induction' }" class="pb-2 px-3 border-b-2 text-xs font-semibold">Induction Agents</button>
                            <button @click="subtab = 'maintenance'" :class="{ 'border-amber-500 text-amber-600 font-bold': subtab === 'maintenance', 'text-gray-500': subtab !== 'maintenance' }" class="pb-2 px-3 border-b-2 text-xs font-semibold">Maintenance Agents</button>
                            <button @click="subtab = 'relaxants'" :class="{ 'border-amber-500 text-amber-600 font-bold': subtab === 'relaxants', 'text-gray-500': subtab !== 'relaxants' }" class="pb-2 px-3 border-b-2 text-xs font-semibold">Relaxants</button>
                            <button @click="subtab = 'regional'" :class="{ 'border-amber-500 text-amber-600 font-bold': subtab === 'regional', 'text-gray-500': subtab !== 'regional' }" class="pb-2 px-3 border-b-2 text-xs font-semibold">Regional Types</button>
                            <button @click="subtab = 'intubation'" :class="{ 'border-amber-500 text-amber-600 font-bold': subtab === 'intubation', 'text-gray-500': subtab !== 'intubation' }" class="pb-2 px-3 border-b-2 text-xs font-semibold">Intubation Methods</button>
                        </div>

                        <!-- Induction Agents Subtab -->
                        <div x-show="subtab === 'induction'">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-800 dark:text-gray-200">Induction Agents</h4>
                                <form action="{{ route('admin.reference-data.store', 'induction') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Agent Name (e.g. Propofol)" required class="rounded-md border-gray-300 text-xs dark:bg-gray-700 dark:text-white">
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded text-xs font-bold">+ Add</button>
                                </form>
                            </div>
                            @include('admin.reference_data._simple_table', ['items' => $inductionAgents, 'idCol' => 'induction_agent_id', 'nameCol' => 'name', 'type' => 'induction'])
                        </div>

                        <!-- Maintenance Agents Subtab -->
                        <div x-show="subtab === 'maintenance'">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-800 dark:text-gray-200">Maintenance Agents</h4>
                                <form action="{{ route('admin.reference-data.store', 'maintenance') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Agent Name (e.g. Isoflurane)" required class="rounded-md border-gray-300 text-xs dark:bg-gray-700 dark:text-white">
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded text-xs font-bold">+ Add</button>
                                </form>
                            </div>
                            @include('admin.reference_data._simple_table', ['items' => $maintenanceAgents, 'idCol' => 'maintenance_agent_id', 'nameCol' => 'name', 'type' => 'maintenance'])
                        </div>

                        <!-- Relaxants Subtab -->
                        <div x-show="subtab === 'relaxants'">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-800 dark:text-gray-200">Relaxants</h4>
                                <form action="{{ route('admin.reference-data.store', 'relaxant') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Relaxant Name (e.g. Atracurium)" required class="rounded-md border-gray-300 text-xs dark:bg-gray-700 dark:text-white">
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded text-xs font-bold">+ Add</button>
                                </form>
                            </div>
                            @include('admin.reference_data._simple_table', ['items' => $relaxants, 'idCol' => 'relaxant_id', 'nameCol' => 'name', 'type' => 'relaxant'])
                        </div>

                        <!-- Regional Types Subtab -->
                        <div x-show="subtab === 'regional'">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-800 dark:text-gray-200">Regional Types</h4>
                                <form action="{{ route('admin.reference-data.store', 'regional') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="name" placeholder="Regional Type (e.g. SAB, Epidural)" required class="rounded-md border-gray-300 text-xs dark:bg-gray-700 dark:text-white">
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded text-xs font-bold">+ Add</button>
                                </form>
                            </div>
                            @include('admin.reference_data._simple_table', ['items' => $regionalTypes, 'idCol' => 'regional_type_id', 'nameCol' => 'name', 'type' => 'regional'])
                        </div>

                        <!-- Intubation Methods Subtab -->
                        <div x-show="subtab === 'intubation'">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-800 dark:text-gray-200">Intubation Methods</h4>
                                <form action="{{ route('admin.reference-data.store', 'intubation') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="method_name" placeholder="Method Name (e.g. VL, Bougie)" required class="rounded-md border-gray-300 text-xs dark:bg-gray-700 dark:text-white">
                                    <button type="submit" class="px-3 py-1.5 bg-amber-600 text-white rounded text-xs font-bold">+ Add</button>
                                </form>
                            </div>
                            @include('admin.reference_data._simple_table', ['items' => $intubationMethods, 'idCol' => 'method_id', 'nameCol' => 'method_name', 'type' => 'intubation'])
                        </div>
                    </div>

                    <!-- TAB 4: ANALGESICS -->
                    <div x-show="tab === 'analgesics'" space-y-6>
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Analgesics</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Manage pain relief drugs in the audit form.</p>
                            </div>
                            <form action="{{ route('admin.reference-data.store', 'analgesic') }}" method="POST" class="flex items-center gap-3">
                                @csrf
                                <input type="text" name="name" placeholder="Analgesic Name (e.g. Fentanyl)" required class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold shadow-sm transition">
                                    + Add Analgesic
                                </button>
                            </form>
                        </div>
                        @include('admin.reference_data._simple_table', ['items' => $analgesics, 'idCol' => 'analgesic_id', 'nameCol' => 'name', 'type' => 'analgesic'])
                    </div>

                    <!-- TAB 5: ADVERSE EVENTS -->
                    <div x-show="tab === 'events'" space-y-6>
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Adverse Events</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Manage adverse clinical events selectable in audit records.</p>
                            </div>
                            <form action="{{ route('admin.reference-data.store', 'event') }}" method="POST" class="flex items-center gap-3">
                                @csrf
                                <input type="text" name="event_name" placeholder="Event Name (e.g. Hypoxia)" required class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold shadow-sm transition">
                                    + Add Adverse Event
                                </button>
                            </form>
                        </div>
                        @include('admin.reference_data._simple_table', ['items' => $adverseEvents, 'idCol' => 'event_id', 'nameCol' => 'event_name', 'type' => 'event'])
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
