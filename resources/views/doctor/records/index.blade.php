<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-wider">
                {{ __('Anaesthesia Audit Database') }}
            </h2>
            <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                + ADD NEW RECORD
            </a>
        </div>
    </x-slot>

    <!-- PHP Helper Functions Translated -->
    @php
        function getASABadge($grade) {
            $class = "bg-gray-500 text-white";
            if ($grade == 'I') $class = "bg-green-500 text-white";
            if ($grade == 'II') $class = "bg-blue-300 text-gray-900";
            if ($grade == 'III') $class = "bg-yellow-400 text-gray-900";
            if (in_array($grade, ['IV', 'V', 'VI', 'E'])) $class = "bg-red-500 text-white";
            return '<span class="px-2 py-0.5 rounded text-xs font-bold ' . $class . '">ASA ' . htmlspecialchars($grade) . '</span>';
        }

        function getAdmissionBadge($type) {
            if (!$type) return "";
            $class = "bg-gray-100 text-gray-800 border border-gray-300";
            if ($type == 'ICU') $class = "bg-red-600 text-white border-red-600";
            if ($type == 'HDU') $class = "bg-yellow-400 text-gray-900 border-yellow-400";
            if ($type == 'Ward') $class = "bg-blue-600 text-white border-blue-600";
            return '<span class="px-2 py-0.5 rounded text-xs font-bold ' . $class . '">' . htmlspecialchars($type) . '</span>';
        }
    @endphp

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h5 class="font-bold text-lg text-gray-800">Case Audit Log</h5>
                        <p class="text-xs text-gray-500">Viewing all clinical records from Teaching Hospital Jaffna</p>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                        {{ count($surgeries) }} Records
                    </span>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b-2 border-gray-200 bg-gray-100 text-gray-600 text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Patient</th>
                                <th class="px-4 py-3">Operation Details</th>
                                <th class="px-4 py-3">Conditions</th>
                                <th class="px-4 py-3">Monitoring</th>
                                <th class="px-4 py-3">Analgesia</th>
                                <th class="px-4 py-3">Adverse</th>
                                <th class="px-4 py-3">Clinical Team</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($surgeries as $row)
                                <tr class="hover:bg-gray-50 align-top">
                                    <td class="px-4 py-4 font-bold text-gray-400">#{{ $row->surgery_id }}</td>
                                    
                                    <!-- Patient -->
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-800 uppercase">{{ $row->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $row->age }}y | {{ $row->sex }} | <span class="text-blue-600 font-bold">BHT {{ $row->bht }}</span>
                                        </div>
                                    </td>

                                    <!-- Surgery -->
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-blue-600 text-xs mb-1 whitespace-normal max-w-xs">{{ $row->operation_name }}</div>
                                        <div class="flex flex-wrap gap-1 items-center mt-2">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 border text-gray-800">{{ $row->surgery_type }}</span>
                                            {!! getASABadge($row->asa_grade) !!}
                                            {!! getAdmissionBadge($row->post_op_admission) !!}
                                        </div>
                                        <div class="text-gray-400 text-xs mt-2">{{ date('d-M-Y', strtotime($row->date_of_surgery)) }}</div>
                                    </td>

                                    <!-- Conditions -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[150px]">
                                        <div class="flex flex-wrap gap-1">
                                            @if($row->diseases->isEmpty()) <span class="text-gray-400">-</span> @endif
                                            @foreach($row->diseases as $d)
                                                <span class="text-[0.65rem] px-1.5 py-0.5 bg-white border border-gray-300 rounded text-gray-600">{{ $d }}</span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Monitoring -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[150px]">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($row->monitor as $m)
                                                <span class="text-[0.65rem] px-1.5 py-0.5 bg-white border border-gray-300 rounded font-bold text-blue-700">{{ $m }}</span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Analgesia -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[150px]">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($row->analgesia as $a)
                                                <span class="text-[0.65rem] px-1.5 py-0.5 bg-green-50 border border-green-200 rounded text-green-800">{{ $a }}</span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Adverse Events -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[120px]">
                                        @if($row->events->isEmpty())
                                            <span class="text-green-600 text-xs font-bold">None</span>
                                        @else
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($row->events as $e)
                                                    <span class="text-[0.65rem] px-1.5 py-0.5 bg-red-600 text-white rounded shadow-sm">{{ $e }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Staff -->
                                    <td class="px-4 py-4 text-xs text-gray-600">
                                        @foreach($row->staff as $s)
                                            <div class="mb-1 border-b border-gray-100 pb-1">
                                                <span class="font-bold text-gray-800">
                                                    {{ $s->role_in_case == 'Consultant Surgeon' ? 'Surg' : ($s->role_in_case == 'Consultant Anaesthetist' ? 'Anaes' : 'MOA') }}:
                                                </span> 
                                                {{ $s->name }}
                                            </div>
                                        @endforeach
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col gap-2 items-center">
                                            <a href="{{ route('data_entry.records.show', $row->surgery_id) }}" class="text-[0.7rem] font-bold px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition w-full text-center">
                                                VIEW
                                            </a>
                                            <a href="#" onclick="return confirm('ARE YOU SURE? This will permanently delete this record.');" class="text-[0.7rem] font-bold px-3 py-1 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition w-full text-center">
                                                DELETE
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-8 w-8 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>No clinical records found in the database.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-400">
                    Data automatically synchronized with Anaesthesia Information System.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>