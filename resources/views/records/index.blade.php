<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                Anaesthesia Audit Database
            </h2>
            <!-- Dynamically route the "ADD NEW RECORD" button based on the logged-in user's role (doctor vs data_entry) -->
            <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.audit-form.start') : route('data_entry.audit-form.start') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition">
                + ADD NEW RECORD
            </a>
        </div>
    </x-slot>

    <!-- PHP Helper Functions Translated -->
    @php
        if (!function_exists('getASABadge')) {
            function getASABadge($grade) {
                $class = "bg-gray-500 text-white";
                if ($grade == 'I') $class = "bg-green-500 text-white";
                if ($grade == 'II') $class = "bg-blue-500 text-white";
                if ($grade == 'III') $class = "bg-amber-500 text-white";
                if (in_array($grade, ['IV', 'V', 'VI', 'E'])) $class = "bg-red-500 text-white";
                return '<span class="px-2 py-0.5 rounded text-xs font-bold ' . $class . '">ASA ' . htmlspecialchars($grade) . '</span>';
            }
        }

        if (!function_exists('getAdmissionBadge')) {
            function getAdmissionBadge($type) {
                if (!$type) return "";
                $class = "bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600";
                if ($type == 'ICU') $class = "bg-red-600 text-white border-red-600";
                if ($type == 'HDU') $class = "bg-amber-500 text-white border-amber-500";
                if ($type == 'Ward') $class = "bg-blue-600 text-white border-blue-600";
                return '<span class="px-2 py-0.5 rounded text-xs font-bold ' . $class . '">' . htmlspecialchars($type) . '</span>';
            }
        }
    @endphp

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                
                <!-- Card Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/90">
                    <div>
                        <h5 class="font-bold text-lg text-gray-800 dark:text-white">Case Audit Log</h5>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Viewing all clinical records from Teaching Hospital Jaffna</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="recordCountBadge" class="bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300 text-xs font-bold px-3 py-1 rounded-full border border-blue-200 dark:border-blue-800">
                            {{ count($surgeries) }} Records
                        </span>
                        <div class="relative">
                            <input
                                id="recordSearch"
                                type="text"
                                placeholder="Search PHN / Name"
                                aria-label="Search by PHN or patient name"
                                class="w-80 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5 pr-8 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                style="width: 20rem;"
                            >
                            <span class="pointer-events-none text-gray-400 dark:text-gray-500" style="position: absolute; right: 0.6rem; top: 50%; transform: translateY(-50%); display: flex; align-items: center;" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-200 text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">PHN</th>
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
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="recordsTableBody">
                            @forelse($surgeries as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 align-top record-row transition-colors" data-phn="{{ strtolower($row->phn ?? '') }}" data-name="{{ strtolower($row->name ?? '') }}">
                                    <td class="px-4 py-4 font-bold text-gray-700 dark:text-gray-300">{{ $row->phn ?? '-' }}</td>
                                    
                                    <!-- Patient -->
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-gray-800 dark:text-white uppercase">{{ $row->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $row->age }}y | {{ $row->sex }} | <span class="text-blue-600 dark:text-blue-400 font-bold">BHT {{ $row->bht }}</span>
                                        </div>
                                    </td>

                                    <!-- Surgery -->
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-blue-600 dark:text-blue-400 text-xs mb-1 whitespace-normal max-w-xs">{{ $row->operation_name }}</div>
                                        <div class="flex flex-wrap gap-1 items-center mt-2">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $row->surgery_type }}</span>
                                            {!! getASABadge($row->asa_grade) !!}
                                            {!! getAdmissionBadge($row->post_op_admission) !!}
                                        </div>
                                        <div class="text-gray-400 dark:text-gray-400 text-xs mt-2">{{ date('d-M-Y', strtotime($row->date_of_surgery)) }}</div>
                                    </td>

                                    <!-- Conditions -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[170px]" x-data="{ expanded: false }">
                                        @if($row->diseases->isEmpty()) 
                                            <span class="text-gray-400 dark:text-gray-500">-</span> 
                                        @else
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($row->diseases as $index => $d)
                                                    <span x-show="expanded || {{ $index }} < 5" 
                                                          class="text-[0.65rem] px-1.5 py-0.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-600 dark:text-gray-300">
                                                        {{ $d }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            @if($row->diseases->count() > 5)
                                                <button @click="expanded = !expanded" 
                                                        type="button" 
                                                        class="mt-1 text-[0.65rem] font-bold text-indigo-600 dark:text-indigo-400 hover:underline focus:outline-none flex items-center gap-0.5">
                                                    <span x-text="expanded ? 'Show Less' : '+ {{ $row->diseases->count() - 5 }} More'"></span>
                                                </button>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Monitoring -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[150px]">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($row->monitor as $m)
                                                <span class="text-[0.65rem] px-1.5 py-0.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded font-bold text-blue-700 dark:text-blue-300">{{ $m }}</span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Analgesia -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[150px]">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($row->analgesia as $a)
                                                <span class="text-[0.65rem] px-1.5 py-0.5 bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800 rounded text-green-800 dark:text-green-300">{{ $a }}</span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Adverse Events -->
                                    <td class="px-4 py-4 whitespace-normal max-w-[120px]">
                                        @if($row->events->isEmpty())
                                            <span class="text-green-600 dark:text-green-400 text-xs font-bold">None</span>
                                        @else
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($row->events as $e)
                                                    <span class="text-[0.65rem] px-1.5 py-0.5 bg-red-600 text-white rounded shadow-sm">{{ $e }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Staff -->
                                    <td class="px-4 py-4 text-xs text-gray-600 dark:text-gray-400">
                                        @foreach($row->staff as $s)
                                            <div class="mb-1 border-b border-gray-100 dark:border-gray-700 pb-1">
                                                <span class="font-bold text-gray-800 dark:text-gray-200">
                                                    {{ $s->role_in_case == 'Consultant Surgeon' ? 'Surg' : ($s->role_in_case == 'Consultant Anaesthetist' ? 'Anaes' : 'MOA') }}:
                                                </span> 
                                                {{ $s->name }}
                                            </div>
                                        @endforeach
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col gap-2 items-center">
                                            <!-- Dynamically resolve the view route using the user's role -->
                                            <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.records.show', $row->surgery_id) : route('data_entry.records.show', $row->surgery_id) }}" class="text-[0.7rem] font-bold px-3 py-1 border border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 transition w-full text-center">
                                                VIEW
                                            </a>
                                            <a href="{{ auth()->user()->role === 'doctor' ? route('doctor.records.edit', $row->surgery_id) : route('data_entry.records.edit', $row->surgery_id) }}" class="text-[0.7rem] font-bold px-3 py-1 border border-amber-600 dark:border-amber-500 text-amber-600 dark:text-amber-400 rounded hover:bg-amber-600 hover:text-white dark:hover:bg-amber-500 transition w-full text-center">
                                                EDIT
                                            </a>
                                            <form method="POST" action="{{ auth()->user()->role === 'doctor' ? route('doctor.records.destroy', $row->surgery_id) : route('data_entry.records.destroy', $row->surgery_id) }}" class="w-full" onsubmit="return confirm('ARE YOU SURE? This will permanently delete this record.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[0.7rem] font-bold px-3 py-1 border border-red-600 dark:border-red-500 text-red-600 dark:text-red-400 rounded hover:bg-red-600 hover:text-white dark:hover:bg-red-500 transition w-full text-center">
                                                    DELETE
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>No clinical records found in the database.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="noSearchResults" class="hidden px-6 py-8 text-center text-sm text-gray-500 border-t border-gray-100">
                    No matching records found for your search.
                </div>

                <!-- Footer -->
                <!-- <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-400">
                    Data automatically synchronized with Anaesthesia Information System.
                </div> -->

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('recordSearch');
            const rows = document.querySelectorAll('.record-row');
            const noResults = document.getElementById('noSearchResults');
            const recordCountBadge = document.getElementById('recordCountBadge');
            const totalCount = rows.length;

            if (!searchInput || rows.length === 0) {
                return;
            }

            const updateRecordCount = function (visibleCount) {
                if (!recordCountBadge) {
                    return;
                }

                recordCountBadge.textContent = visibleCount + ' Records';
            };

            updateRecordCount(totalCount);

            searchInput.addEventListener('input', function (event) {
                const query = event.target.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const phn = row.dataset.phn || '';
                    const name = row.dataset.name || '';
                    const matches = phn.includes(query) || name.includes(query);

                    row.style.display = matches ? '' : 'none';
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount !== 0 || query === '');
                }

                updateRecordCount(visibleCount);
            });
        });
    </script>
</x-app-layout>
