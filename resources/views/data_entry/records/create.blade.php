<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Anesthesia Record') }}
            </h2>
            <a href="{{ route('data_entry.records.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                &larr; Back to Records
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded-md">
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('data_entry.records.store') }}" class="space-y-6">
                        @csrf

                        {{-- Patient Info --}}
                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-3 border-b pb-1">Patient Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">IP Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="ip_number" value="{{ old('ip_number') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Patient Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="patient_name" value="{{ old('patient_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Age <span class="text-red-500">*</span></label>
                                    <input type="number" name="age" value="{{ old('age') }}" min="0" max="150"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Gender <span class="text-red-500">*</span></label>
                                    <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                        <option value="">-- Select --</option>
                                        @foreach(['Male','Female','Other'] as $g)
                                            <option value="{{ $g }}" {{ old('gender') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Surgery Info --}}
                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-3 border-b pb-1">Surgery Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Surgery Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="surgery_date" value="{{ old('surgery_date') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="0"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Diagnosis <span class="text-red-500">*</span></label>
                                    <input type="text" name="diagnosis" value="{{ old('diagnosis') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Procedure <span class="text-red-500">*</span></label>
                                    <input type="text" name="procedure" value="{{ old('procedure') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Surgeon</label>
                                    <input type="text" name="surgeon" value="{{ old('surgeon') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Anesthetist</label>
                                    <input type="text" name="anesthetist" value="{{ old('anesthetist') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Anesthesia Info --}}
                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-3 border-b pb-1">Anesthesia</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Anesthesia Type <span class="text-red-500">*</span></label>
                                    <select name="anesthesia_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" required>
                                        <option value="">-- Select --</option>
                                        @foreach(['GA','Spinal','Epidural','Regional','Local'] as $type)
                                            <option value="{{ $type }}" {{ old('anesthesia_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Post-Op Destination</label>
                                    <select name="post_op_destination" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                        <option value="">-- Select --</option>
                                        @foreach(['Ward','ICU','HDU','Recovery','Home'] as $dest)
                                            <option value="{{ $dest }}" {{ old('post_op_destination') == $dest ? 'selected' : '' }}>{{ $dest }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Adverse Events & Notes --}}
                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-3 border-b pb-1">Additional Details</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Adverse Events / Complications</label>
                                    <textarea name="adverse_events" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">{{ old('adverse_events') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Save Record
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
