<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Entry Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-green-600">Data Entry Portal</h3>
                    <p>Start inputting hospital records here.</p>
                </div>
                
            </div>
            <div>
                <!-- Link to access the form for adding a new anesthesia patient record -->
                <a href="{{ route('data_entry.audit-form.start') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Add Patient Records
                </a>
                <!-- Link to view the table of existing clinical records -->
                <a href="{{ route('data_entry.records.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    View Clinical Records
                </a>
            </div>
        </div>
    </div>
    
</x-app-layout>