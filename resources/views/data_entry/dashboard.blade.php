<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Entry Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-green-600">Welcome, {{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Use the options below to add or review anesthesia records.</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Add Record --}}
                <a href="{{ route('data_entry.records.create') }}"
                   class="block bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm p-8 text-center transition-colors">
                    <div class="text-4xl mb-3">&#43;</div>
                    <div class="text-xl font-semibold">Add New Record</div>
                    <div class="text-green-100 text-sm mt-1">Enter a new anesthesia audit record</div>
                </a>

                {{-- View All Records --}}
                <a href="{{ route('data_entry.records.index') }}"
                   class="block bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm p-8 text-center transition-colors">
                    <div class="text-4xl mb-3">&#128203;</div>
                    <div class="text-xl font-semibold">View All Records</div>
                    <div class="text-blue-100 text-sm mt-1">Browse and review records you have entered</div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>