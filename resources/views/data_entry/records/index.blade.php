<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Anesthesia Records') }}
            </h2>
            <a href="{{ route('data_entry.records.create') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                + Add New Record
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-300 rounded-md text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                @if ($records->isEmpty())
                    <div class="p-10 text-center text-gray-400">
                        <p class="text-lg">No records found.</p>
                        <p class="text-sm mt-1">Start by adding your first anesthesia record.</p>
                        <a href="{{ route('data_entry.records.create') }}"
                           class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                            + Add Record
                        </a>
                    </div>
                @else
                    <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                        <span class="text-sm text-gray-600">
                            Total records: <strong>{{ $records->count() }}</strong>
                        </span>
                        <span class="text-sm text-gray-500">Showing records entered by you</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">IP No.</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Patient Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Age</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Surgery Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Diagnosis</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Procedure</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Anaesthesia</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Surgeon</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Anesthetist</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Post-Op</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Adverse Events</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Entered On</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($records as $i => $record)
                                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-green-50 transition-colors">
                                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $record->ip_number }}</td>
                                        <td class="px-4 py-3 text-gray-800 whitespace-nowrap">{{ $record->patient_name }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->age }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->gender }}</td>
                                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $record->surgery_date->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->diagnosis }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->procedure }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full
                                                {{ $record->anesthesia_type === 'GA' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $record->anesthesia_type === 'Spinal' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $record->anesthesia_type === 'Epidural' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $record->anesthesia_type === 'Regional' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $record->anesthesia_type === 'Local' ? 'bg-green-100 text-green-800' : '' }}
                                            ">
                                                {{ $record->anesthesia_type }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->surgeon ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $record->anesthetist ?? '—' }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $record->duration_minutes ? $record->duration_minutes . ' min' : '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($record->post_op_destination)
                                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full
                                                    {{ $record->post_op_destination === 'ICU' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}
                                                ">
                                                    {{ $record->post_op_destination }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 max-w-xs truncate" title="{{ $record->adverse_events }}">
                                            {{ $record->adverse_events ? \Illuminate\Support\Str::limit($record->adverse_events, 40) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $record->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('data_entry.dashboard') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>
