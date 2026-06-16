<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700">My Surgeries</h3>
                        <p class="text-sm text-gray-500">Total surgeries you have participated in.</p>
                    </div>
                    <div class="text-4xl font-extrabold text-blue-600">
                        {{ $surgeryCount }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-blue-600">Hello, {{ auth()->user()->name }}!</h3>
                    <p>View your patient schedules here.</p>
                    <a href="{{ route('doctor.records.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Clinical Database
                    </a>
                </div>
            </div>
            
            <div class="mt-4 flex gap-4">
                <a href="{{ route('doctor.audit-form.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    Add Patient Records
                </a>
                <a href="{{ route('doctor.records.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    View Clinical Records
                </a>
            </div>
            
        </div>
    </div>
</x-app-layout>