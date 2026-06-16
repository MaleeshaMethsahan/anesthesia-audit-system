<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome & Quick Actions Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Welcome back, Dr. {{ auth()->user()->name }}!</h3>
                    <p class="text-sm text-gray-500 mt-1">Here is your surgical caseload overview.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('doctor.audit-form.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 transition">
                        + Add Patient Record
                    </a>
                    <a href="{{ route('doctor.records.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        View Database
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Today -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500 relative">
                    <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Surgeries Today</div>
                    <div class="text-4xl font-extrabold text-gray-900 mt-2">{{ $surgeriesToday }}</div>
                </div>
                
                <!-- This Week -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500 relative">
                    <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Surgeries This Week</div>
                    <div class="text-4xl font-extrabold text-gray-900 mt-2">{{ $surgeriesThisWeek }}</div>
                </div>
                
                <!-- Total -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 relative">
                    <div class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Surgeries</div>
                    <div class="text-4xl font-extrabold text-gray-900 mt-2">{{ $totalSurgeries }}</div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Surgical Caseload (Last 6 Months)</h3>
                <div class="relative h-80 w-full">
                    <canvas id="surgeriesChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('surgeriesChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Completed Surgeries',
                        data: {!! json_encode($chartData) !!},
                        borderColor: 'rgb(59, 130, 246)', // Tailwind blue-500
                        backgroundColor: 'rgba(59, 130, 246, 0.1)', // Light blue fill
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4, // Smooth curves
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
                            display: false // Hides the legend for a cleaner look
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)', // Tailwind gray-900
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
                                stepSize: 1, // Ensures the Y-axis only shows whole numbers
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(243, 244, 246, 1)' // Tailwind gray-100
                            },
                            border: { dash: [4, 4] } // Dashed grid lines
                        },
                        x: {
                            grid: {
                                display: false // Hides vertical grid lines
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>