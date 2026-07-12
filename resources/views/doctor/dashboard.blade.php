<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border border-gray-100">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Welcome back, Dr. {{ auth()->user()->name ?? 'Doctor' }}!</h3>
                    <p class="text-sm text-gray-500 mt-1">Select an action below to get started.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 items-center">
                
                <a href="{{ route('doctor.audit-form.create') ?? '#' }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="text-sm font-bold tracking-wide">Add Patient Record</span>
                </a>

                <a href="{{ route('doctor.records.index') ?? '#' }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                    <span class="text-sm font-bold tracking-wide">View Database</span>
                </a>

                <a href="#" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-rose-400 to-rose-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm font-bold tracking-wide">Patients Directory</span>
                </a>

                <a href="#" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-sm font-bold tracking-wide">Reports & Analytics</span>
                </a>

                <a href="#" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    <span class="text-sm font-bold tracking-wide">Surgical Statistics</span>
                </a>

                <a href="#" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="text-sm font-bold tracking-wide">Global Search</span>
                </a>

                <a href="#" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-teal-400 to-teal-600 rounded-full shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200 text-white group">
                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm font-bold tracking-wide">System Settings</span>
                </a>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500 relative">
                    <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Surgeries Today</div>
                    <div class="text-4xl font-extrabold text-gray-900 mt-2">{{ $surgeriesToday ?? 0 }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500 relative">
                    <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Surgeries This Week</div>
                    <div class="text-4xl font-extrabold text-gray-900 mt-2">{{ $surgeriesThisWeek ?? 0 }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 relative">
                    <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Surgeries</div>
                    <div class="text-4xl font-extrabold text-gray-900 mt-2">{{ $totalSurgeries ?? 0 }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Surgical Caseload (Last 6 Months)</h3>
                <div class="relative h-80 w-full">
                    <canvas id="surgeriesChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('surgeriesChart').getContext('2d');
            
            const labelsData = {!! isset($chartLabels) ? json_encode($chartLabels) : '[]' !!};
            const chartDataArray = {!! isset($chartData) ? json_encode($chartData) : '[]' !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsData,
                    datasets: [{
                        label: 'Completed Surgeries',
                        data: chartDataArray,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleFont: { size: 13 },
                            bodyFont: { size: 14, weight: 'bold' },
                            padding: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(243, 244, 246, 1)'
                            },
                            border: { dash: [4, 4] }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>