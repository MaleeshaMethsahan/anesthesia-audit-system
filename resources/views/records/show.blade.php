<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-wider">
                Record #{{ $surgery->surgery_id }}
            </h2>
            <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.records.index') : route('data_entry.records.index') }}" class="text-sm font-semibold px-3 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">
                Back to Records
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Patient Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Name:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Patient ID:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->patient_id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Age / Sex:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->age }} / {{ $surgery->sex }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">BHT:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->bht }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Surgery Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Operation:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->operation_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Specialty:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->surgical_specialty }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Surgery Type:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->surgery_type }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Date:</span>
                        <span class="font-semibold text-gray-800">{{ date('d-M-Y', strtotime($surgery->date_of_surgery)) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">ASA Grade:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->asa_grade ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Post-op Admission:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->post_op_admission ?? '-' }}</span>
                    </div>
                </div>
            </div>

            @if(isset($surgery->anaesthesia))
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Anaesthesia Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    
                    <div>
                        <span class="text-gray-500">Mode:</span>
                        <span class="font-semibold text-gray-800">{{ $surgery->anaesthesia->mode == 'GA' ? 'General (GA)' : 'Regional' }}</span>
                    </div>

                    @if($surgery->anaesthesia->mode == 'GA')
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Induction ({{ $surgery->anaesthesia->induction_type }}):</span>
                            {{ $surgery->anaesthesia->inductionAgents->pluck('name')->implode(', ') ?: 'N/A' }}
                            @if($surgery->anaesthesia->induction_other_details)
                                <span class="text-sm italic text-gray-500 block">- Specifically: {{ $surgery->anaesthesia->induction_other_details }}</span>
                            @endif
                        </div>
                        
                        <div>
                            <span class="text-gray-500">Maintenance:</span>
                            <span class="font-semibold text-gray-800">
                                {{ $surgery->anaesthesia->maintenance_route ?? 'N/A' }}
                                @if($surgery->anaesthesia->maintenanceAgents && $surgery->anaesthesia->maintenanceAgents->count() > 0)
                                    ({{ $surgery->anaesthesia->maintenanceAgents->pluck('name')->implode(', ') }})
                                @endif
                            </span>
                        </div>

                        <div>
                            <span class="text-gray-500">Relaxant Used:</span>
                            <span class="font-semibold text-gray-800">
                                @if($surgery->anaesthesia->relaxant_used)
                                    Yes 
                                    @if($surgery->anaesthesia->relaxants && $surgery->anaesthesia->relaxants->count() > 0)
                                        ({{ $surgery->anaesthesia->relaxants->pluck('name')->implode(', ') }})
                                    @endif
                                @else
                                    No
                                @endif
                            </span>
                        </div>

                        <div>
                            <span class="text-gray-500">Difficult Intubation:</span>
                            <span class="font-semibold {{ $surgery->anaesthesia->difficult_intubation ? 'text-amber-600' : 'text-gray-800' }}">
                                @if($surgery->anaesthesia->difficult_intubation)
                                    Yes
                                    @if($surgery->anaesthesia->intubationMethods && $surgery->anaesthesia->intubationMethods->count() > 0)
                                        ({{ $surgery->anaesthesia->intubationMethods->pluck('method_name')->implode(', ') }})
                                    @endif
                                @else
                                    No
                                @endif
                            </span>
                        </div>
                    @else
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white block mb-1">Regional Types:</span>
                            <span class="text-gray-800 dark:text-gray-200">
                                @if($surgery->anaesthesia->regionalTypes && $surgery->anaesthesia->regionalTypes->count() > 0)
                                    {{ $surgery->anaesthesia->regionalTypes->pluck('name')->implode(', ') }}
                                @else
                                    N/A
                                @endif
                            </span>
                            @if($surgery->anaesthesia->regional_other_details)
                                <span class="text-sm italic text-gray-500 block mt-1">- Specifically: {{ $surgery->anaesthesia->regional_other_details }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                @if($surgery->anaesthesia->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <span class="text-gray-500 text-sm block mb-1">Additional Notes:</span>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $surgery->anaesthesia->notes }}</p>
                </div>
                @endif
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                    <h4 class="text-base font-bold text-gray-800 mb-3">Medical Conditions</h4>
                    <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                        @forelse($surgery->diseases as $disease)
                            <li>{{ $disease }}</li>
                        @empty
                            <li class="list-none text-gray-400">No conditions recorded.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                    <h4 class="text-base font-bold text-gray-800 mb-3">Monitoring</h4>
                    <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                        @forelse($surgery->monitor as $monitor)
                            <li>{{ $monitor }}</li>
                        @empty
                            <li class="list-none text-gray-400">No monitoring data recorded.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                    <h4 class="text-base font-bold text-gray-800 mb-3">Analgesia</h4>
                    <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                        @forelse($surgery->analgesia as $analgesic)
                            <li>{{ $analgesic }}</li>
                        @empty
                            <li class="list-none text-gray-400">No analgesia recorded.</li>
                        @endforelse

                        @if($surgery->analgesia_other_details)
                            <li class="font-semibold text-teal-600 mt-2">Other: {{ $surgery->analgesia_other_details }}</li>
                        @endif
                    </ul>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                    <h4 class="text-base font-bold text-gray-800 mb-3">Adverse Events</h4>
                    <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
                        @forelse($surgery->events as $event)
                            <li>{{ $event }}</li>
                        @empty
                            <li class="list-none text-green-600">None reported.</li>
                        @endforelse

                        @if($surgery->adverse_event_other_details)
                            <li class="font-semibold text-red-600 mt-2">Other: {{ $surgery->adverse_event_other_details }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 p-6">
                <h4 class="text-base font-bold text-gray-800 mb-3">Clinical Team</h4>
                <ul class="text-sm text-gray-700 space-y-2">
                    @forelse($surgery->staff as $staffMember)
                        <li>
                            <span class="font-semibold text-gray-800">{{ $staffMember->role_in_case }}:</span>
                            {{ $staffMember->name }}
                        </li>
                    @empty
                        <li class="text-gray-400">No staff data recorded.</li>
                    @endforelse
                </ul>
            </div>
            
        </div>
    </div>
</x-app-layout>