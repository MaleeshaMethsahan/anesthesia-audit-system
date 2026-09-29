<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                Clinical Analytics Dashboard
            </h2>
            <a href="{{ route('doctor.audit-form.start') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-indigo-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Add Patient Record
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen text-slate-800 dark:text-slate-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Banner Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-slate-200/80 dark:border-gray-700/80 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Welcome back, Dr. {{ auth()->user()->name ?? 'Doctor' }}!</h3>
                    <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-1">Anaesthesia Audit System • Real-Time Clinical Performance Overview</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('doctor.audit-form.start') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Add Patient Record
                    </a>
                    <a href="{{ route('doctor.records.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 dark:hover:bg-gray-600 text-slate-800 dark:text-slate-200 font-bold rounded-xl text-xs sm:text-sm shadow transition">
                        View Database
                    </a>
                </div>
            </div>

            <!-- TOP ROW: 3 Key Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Card 1: Top Line KPI Sparkline -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalSurgeries ?? 0 }}</div>
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Audit Cases</span>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">All clinical procedures logged</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                            Active System
                        </span>
                    </div>
                    <div class="relative h-16 w-full mt-3">
                        <canvas id="kpiSparklineChart"></canvas>
                    </div>
                </div>

                <!-- Card 2: Specialty Breakdown & Target Ring -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex items-center justify-between gap-4">
                    <div class="flex-1 space-y-2.5">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white">Specialties</h4>
                        <div class="space-y-2 text-xs">
                            @php
                                $specialtyKeys = array_keys($specialtyDistribution ?? []);
                                $specialtyVals = array_values($specialtyDistribution ?? []);
                            @endphp
                            @forelse(array_slice($specialtyKeys, 0, 4) as $idx => $specName)
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-slate-500 dark:text-slate-400 truncate w-24">{{ $specName }}</span>
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ min(100, (($specialtyVals[$idx] ?? 1) / max(1, array_sum($specialtyVals))) * 100 * 2.5) }}%"></div>
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-200 text-xs">{{ $specialtyVals[$idx] ?? 0 }}</span>
                                </div>
                            @empty
                                <span class="text-slate-400 text-xs italic">No data</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="relative w-24 h-24 flex-shrink-0 flex items-center justify-center">
                        <canvas id="specialtyMiniDonut"></canvas>
                    </div>
                </div>

                <!-- Card 3: ASA Physical Status Stacked Bar -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white">ASA Grades</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Physical status classification</p>
                        </div>
                        <div class="flex gap-2 text-xs font-bold">
                            @foreach(array_slice($asaDistribution ?? [], 0, 3) as $grade => $count)
                                <span class="text-slate-700 dark:text-slate-300"><strong class="text-indigo-600 dark:text-indigo-400">{{ $count }}</strong> {{ $grade }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="relative h-20 w-full">
                        <canvas id="asaStackedBarChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- MAIN MIDDLE SECTION: Hero Card on Left (2 Columns) + Side Widgets on Right (1 Column) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- HERO MAIN CARD (Occupies 2 columns) -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between space-y-6">

                    <!-- Hero Card Header Bar -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 dark:border-gray-700/60 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-indigo-600 rounded-xl text-white shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Surgical Caseload & Performance</h3>
                                <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500">Real-time clinical audit trajectory</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <form action="{{ route('doctor.records.index') }}" method="GET" class="relative flex-1 sm:w-64">
                                <input type="text" name="search" placeholder="Search PHN / Patient..." class="w-full bg-slate-100 dark:bg-gray-700 border-none rounded-xl text-xs sm:text-sm py-2.5 pl-3 pr-8 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 text-slate-400 absolute right-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </form>
                            <a href="{{ route('doctor.audit-form.start') }}" class="p-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow transition" title="Add Record">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Hero Stat Callouts -->
                    <div class="grid grid-cols-3 gap-4 text-center py-2 border-b border-slate-100 dark:border-gray-700/60">
                        <div>
                            <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $surgeriesToday ?? 0 }}</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Today</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $surgeriesThisMonth ?? 0 }}</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">This Month</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-purple-600 dark:text-purple-400">{{ $totalSurgeries ?? 0 }}</div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Cases</div>
                        </div>
                    </div>

                    <!-- Hero Gradient Wave Area Chart -->
                    <div class="relative h-64 w-full">
                        <canvas id="heroWaveChart"></canvas>
                    </div>

                    <!-- Bottom Mini-Bars & Summary Badges -->
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700/60 grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div class="relative h-20 w-full">
                            <canvas id="heroMiniBarChart"></canvas>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs sm:text-sm">
                            <div class="p-2.5 bg-slate-50 dark:bg-gray-700/40 rounded-xl border border-slate-100 dark:border-gray-700">
                                <div class="font-extrabold text-indigo-600 dark:text-indigo-400 text-base">{{ count($specialtyDistribution ?? []) }}</div>
                                <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Specialties</div>
                            </div>
                            <div class="p-2.5 bg-slate-50 dark:bg-gray-700/40 rounded-xl border border-slate-100 dark:border-gray-700">
                                <div class="font-extrabold text-emerald-600 dark:text-emerald-400 text-base">{{ count($topDiseases ?? []) }}</div>
                                <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Diseases</div>
                            </div>
                            <div class="p-2.5 bg-slate-50 dark:bg-gray-700/40 rounded-xl border border-slate-100 dark:border-gray-700">
                                <div class="font-extrabold text-amber-600 dark:text-amber-400 text-base">{{ count($topAdverseEvents ?? []) }}</div>
                                <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold">Events</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN WIDGETS (Occupies 1 column) -->
                <div class="space-y-6">

                    <!-- Right Card 1: Anaesthesia Mode Usage Donut & Counts -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm flex flex-col justify-between">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white">Anaesthesia Mode</h4>
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">Usage %</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="relative w-32 h-32 flex-shrink-0 flex items-center justify-center">
                                <canvas id="anaesthesiaModeDonut"></canvas>
                            </div>
                            <div class="space-y-2.5 text-xs sm:text-sm flex-1">
                                @foreach($anaesthesiaUsage ?? [] as $modeName => $modeCount)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full {{ $loop->first ? 'bg-indigo-600' : ($loop->iteration == 2 ? 'bg-cyan-500' : 'bg-purple-500') }}"></span>
                                            <span class="text-slate-600 dark:text-slate-300 font-semibold text-xs sm:text-sm">{{ $modeName }}</span>
                                        </div>
                                        <span class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $modeCount }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Card 2: Clinical Activity & Post-Op Admissions -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Post-Op Admission</span>
                                <div class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ array_sum($postOpAdmission ?? []) }} <span class="text-xs sm:text-sm font-normal text-slate-400">patients</span></div>
                            </div>
                        </div>

                        <!-- 5-Day Activity Vertical Bar -->
                        <div class="relative h-24 w-full">
                            <canvas id="postOpBarChart"></canvas>
                        </div>

                        <!-- 3 Mini Ring Indicators -->
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 dark:border-gray-700/60 text-center">
                            <div>
                                <div class="text-base font-extrabold text-blue-600 dark:text-blue-400">{{ $postOpAdmission['Ward'] ?? 0 }}</div>
                                <div class="text-xs font-bold text-slate-400 dark:text-slate-500">Ward</div>
                            </div>
                            <div>
                                <div class="text-base font-extrabold text-amber-500 dark:text-amber-400">{{ $postOpAdmission['HDU'] ?? 0 }}</div>
                                <div class="text-xs font-bold text-slate-400 dark:text-slate-500">HDU</div>
                            </div>
                            <div>
                                <div class="text-base font-extrabold text-red-600 dark:text-red-400">{{ $postOpAdmission['ICU'] ?? 0 }}</div>
                                <div class="text-xs font-bold text-slate-400 dark:text-slate-500">ICU</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Card 3: Sparkline Multi-KPIs (Comorbidities & Adverse Events) -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-slate-200/80 dark:border-gray-700/80 shadow-sm space-y-3">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white">Clinical Risk Factors</h4>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-2.5 bg-slate-50 dark:bg-gray-700/40 rounded-xl">
                                <div class="text-lg font-black text-teal-600 dark:text-teal-400">{{ array_sum($topDiseases ?? []) }}</div>
                                <div class="text-xs font-bold text-slate-400">Diseases</div>
                                <span class="inline-block text-xs font-bold text-emerald-500 mt-1">+12%</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 dark:bg-gray-700/40 rounded-xl">
                                <div class="text-lg font-black text-rose-600 dark:text-rose-400">{{ array_sum($topAdverseEvents ?? []) }}</div>
                                <div class="text-xs font-bold text-slate-400">Events</div>
                                <span class="inline-block text-xs font-bold text-rose-500 mt-1">-5%</span>
                            </div>
                            <div class="p-2.5 bg-slate-50 dark:bg-gray-700/40 rounded-xl">
                                <div class="text-lg font-black text-indigo-600 dark:text-indigo-400">{{ $emergencyVsElective['Emergency'] ?? 0 }}</div>
                                <div class="text-xs font-bold text-slate-400">Emergency</div>
                                <span class="inline-block text-xs font-bold text-indigo-500 mt-1">+24%</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- BOTTOM SECTION: Recent Audit Records Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700/80 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-700/60 flex justify-between items-center bg-slate-50/50 dark:bg-gray-800/90">
                    <div>
                        <h4 class="font-extrabold text-base text-slate-900 dark:text-white uppercase tracking-wider">Recent Audit Records</h4>
                        <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500">Latest 5 clinical cases added to database</p>
                    </div>
                    <a href="{{ route('doctor.records.index') }}" class="text-xs sm:text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View Database &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-slate-200 dark:border-gray-700 bg-slate-100/70 dark:bg-gray-700/80 text-slate-600 dark:text-slate-200 text-xs font-bold">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Patient Name</th>
                                <th class="px-4 py-3">BHT</th>
                                <th class="px-4 py-3">Operation Details</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Admission</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                            @forelse($recentSurgeries as $surgery)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-700/50 transition-colors text-xs sm:text-sm">
                                    <td class="px-4 py-3.5 text-slate-500 dark:text-slate-400 font-medium">
                                        {{ date('d-M-Y', strtotime($surgery->date_of_surgery)) }}
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-white">
                                        {{ $surgery->patient->name ?? 'Patient #' . $surgery->patient_id }}
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-indigo-600 dark:text-indigo-400">
                                        BHT {{ $surgery->patient->bht ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 font-medium text-slate-700 dark:text-slate-300 max-w-xs truncate">
                                        {{ $surgery->operation_name }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-gray-700 border border-slate-200 dark:border-gray-600 text-slate-700 dark:text-slate-300">
                                            {{ $surgery->surgery_type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @php
                                            $adm = $surgery->post_op_admission;
                                            $admClass = 'bg-slate-100 text-slate-800 dark:bg-gray-700 dark:text-slate-300';
                                            if ($adm === 'ICU') $admClass = 'bg-red-600 text-white';
                                            if ($adm === 'HDU') $admClass = 'bg-amber-500 text-white';
                                            if ($adm === 'Ward') $admClass = 'bg-blue-600 text-white';
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $admClass }}">
                                            {{ $adm ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <a href="{{ route('doctor.records.show', $surgery->surgery_id) }}" class="text-xs font-bold px-3.5 py-1.5 border border-indigo-600 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-600 hover:text-white transition">
                                            VIEW
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 text-sm">
                                        No recent audit records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Engine with Custom Mockup Styling -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#9ca3af' : '#64748b';
            const gridColor = isDark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(226, 232, 240, 0.7)';

            const trendLabels = {!! json_encode($trendLabels ?? []) !!};
            const trendData = {!! json_encode($trendData ?? []) !!};

            const specialtyDistribution = {!! json_encode($specialtyDistribution ?? []) !!};
            const asaDistribution = {!! json_encode($asaDistribution ?? []) !!};
            const anaesthesiaUsage = {!! json_encode($anaesthesiaUsage ?? []) !!};
            const postOpAdmission = {!! json_encode($postOpAdmission ?? []) !!};

            // 1. KPI Top Line Sparkline
            new Chart(document.getElementById('kpiSparklineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        data: trendData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { display: false }, y: { display: false } },
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });

            // 2. Specialty Mini Donut (Top Center Card)
            new Chart(document.getElementById('specialtyMiniDonut').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(specialtyDistribution),
                    datasets: [{
                        data: Object.values(specialtyDistribution),
                        backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ec4899'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });

            // 3. ASA Stacked Bar Chart (Top Right Card)
            new Chart(document.getElementById('asaStackedBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['ASA Distribution'],
                    datasets: Object.keys(asaDistribution).map((grade, idx) => ({
                        label: grade,
                        data: [asaDistribution[grade]],
                        backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'][idx % 5],
                        borderRadius: 4
                    }))
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { stacked: true, display: false }, y: { stacked: true, display: false } },
                    plugins: { legend: { display: false } }
                }
            });

            // 4. Hero Wave Chart (Large Blue-Purple Multi-Wave Area Chart)
            const heroCtx = document.getElementById('heroWaveChart').getContext('2d');
            const heroGradient = heroCtx.createLinearGradient(0, 0, 0, 240);
            heroGradient.addColorStop(0, 'rgba(99, 102, 241, 0.45)');
            heroGradient.addColorStop(1, 'rgba(168, 85, 247, 0.05)');

            new Chart(heroCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Monthly Surgeries',
                        data: trendData,
                        borderColor: '#6366f1',
                        backgroundColor: heroGradient,
                        borderWidth: 3.5,
                        fill: true,
                        tension: 0.45,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { color: textColor, precision: 0, font: { size: 12 } }, grid: { color: gridColor } },
                        x: { ticks: { color: textColor, font: { size: 12 } }, grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });

            // 5. Hero Mini-Bar Chart (Bottom of Hero Card)
            new Chart(document.getElementById('heroMiniBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(specialtyDistribution).slice(0, 7),
                    datasets: [{
                        data: Object.values(specialtyDistribution).slice(0, 7),
                        backgroundColor: '#6366f1',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } }, y: { display: false } },
                    plugins: { legend: { display: false } }
                }
            });

            // 6. Anaesthesia Mode Donut (Right Column Card 1)
            new Chart(document.getElementById('anaesthesiaModeDonut').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(anaesthesiaUsage),
                    datasets: [{
                        data: Object.values(anaesthesiaUsage),
                        backgroundColor: ['#4f46e5', '#06b6d4', '#a855f7'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: { legend: { display: false } }
                }
            });

            // 7. Post-Op Admission Vertical Bar Chart (Right Column Card 2)
            new Chart(document.getElementById('postOpBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(postOpAdmission),
                    datasets: [{
                        data: Object.values(postOpAdmission),
                        backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { color: textColor, precision: 0, font: { size: 11 } }, grid: { color: gridColor } },
                        x: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</x-app-layout>