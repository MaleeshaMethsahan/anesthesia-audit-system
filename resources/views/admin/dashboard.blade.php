<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-red-600 mb-4">Welcome, Admin!</h3>
                    
                    <!-- NEW BUTTON -->
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        + Register New User
                    </a>

                    <div class="mt-8 bg-white shadow rounded-lg p-6">
    <h3 class="text-lg font-bold mb-4">
        Active Users (Last 5 Minutes)
    </h3>

    @if($activeUsers->count())
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Role</th>
                    <th class="px-4 py-2 border">Last Seen</th>
                </tr>
            </thead>

            <tbody>
                @foreach($activeUsers as $user)
                    <tr>
                        <td class="px-4 py-2 border">
                            {{ $user->name }}
                        </td>

                        <td class="px-4 py-2 border">
                            {{ ucfirst($user->role) }}
                        </td>

                        <td class="px-4 py-2 border">
                            {{ $user->last_seen_at?->diffForHumans() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-500">
            No active users.
        </p>
    @endif
</div>

                </div>
                <div class="p-6 text-gray-900">
                    
                    <!-- NEW BUTTON -->
                    <a href="{{ route('admin.logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        View Login Logs
                    </a>

                    

                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>