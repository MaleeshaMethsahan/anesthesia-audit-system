<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                Analytics Dashboard
            </h2>
            <a href="{{ route('doctor.reports.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-sm shadow transition">
                Reports Centre
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen text-slate-800 dark:text-slate-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Scope Toggle + Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5 border border-slate-200/80 dark:border-gray-700/80">
                <form method="GET" action="{{ route('doctor.analytics') }}" class="flex flex-wrap gap-4 items-end">

                    <!-- Scope Toggle -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Scope</label>
                        <div class="flex rounded-xl overflow-hidden border border-slate-200 dark:border-gray-700">
                            <a href="{{ route('doctor.analytics', array_merge(request()->query(), ['scope' => 'my'])) }}"
                               class="px-5 py-2 text-sm font-bold transition {{ $scope === 'my' ? 'bg-indigo-600 text-white' : 'bg-slate-50 dark:bg-gray-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-800' }}">
                                My Cases
                            </a>
                            <a href="{{ route('doctor.analytics', array_merge(request()->query(), ['scope' => 'all'])) }}"
                               class="px-5 py-2 text-sm font-bold transition {{ $scope === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-50 dark:bg-gray-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-gray-800' }}">
                                All Cases (Audit View)
                            </a>
                        </div>
                    </div>

                    <input type="hidden" name="scope" value="{{ $scope }}">

                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                            class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                            class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Specialty</label>
                        <select name="specialty" class="rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">All Specialties</option>
                            @foreach($specialties as $spec)
                                <option value="{{ $spec }}" {{ $filters['specialty'] === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition">Apply</button>
                    <a href="{{ route('doctor.analytics', ['scope' => $scope]) }}" class="px-5 py-2 bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-200 font-bold rounded-lg text-sm transition">Reset</a>
                </form>
            </div>

            <!-- Warnings / Empty States -->
            @if(isset($scopeFallback) && $scopeFallback)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-5 text-amber-800 dark:text-amber-300 font-semibold text-sm">
                ⚠ You currently have no assigned cases. No data is available to display in "My Cases" scope.
            </div>
            @endif

            @if(!empty($filters['date_from']) && !empty($filters['date_to']) && $filters['date_from'] > $filters['date_to'])
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-5 text-red-800 dark:text-red-300 font-semibold text-sm">
                ⚠ The "From" date is after the "To" date. Please adjust your date filters.
            </div>
            @elseif($caseCounts['total'] === 0 && (!isset($scopeFallback) || !$scopeFallback))
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-5 text-blue-800 dark:text-blue-300 font-semibold text-sm">
                ℹ No cases found for the selected filters.
            </div>
            @endif

            <!-- KPI Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $caseCounts['total'] }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Total Cases</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-800 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-red-600 dark:text-red-400">{{ $caseCounts['em'] }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Emergency</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-blue-200 dark:border-blue-800 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $caseCounts['elect'] }}</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Elective</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-amber-200 dark:border-amber-800 shadow-sm p-5 text-center">
                    <div class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $adverseEventRate['overall_rate'] }}%</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Adverse Event Rate</div>
                </div>
            </div>

            <!-- Row: Trend + Em vs Elect + ASA -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Case Volume Trend -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-4">Case Volume Trend (6 Months)</h4>
                    <div class="relative h-52"><canvas id="trendChart"></canvas></div>
                </div>
                <!-- Em vs Elect + ASA stacked -->
                <div class="space-y-5">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-3">Em vs Elective</h4>
                        <div class="relative h-24"><canvas id="emVsElectChart"></canvas></div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-3">ASA Distribution</h4>
                        <div class="relative h-24"><canvas id="asaChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Row: Anaesthesia Mode + Post-Op + Difficult/Relaxant KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Anaesthesia Mode -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-3">Anaesthesia Mode</h4>
                    <div class="relative h-36 mx-auto w-36"><canvas id="anaestModeChart"></canvas></div>
                    <div class="mt-3 space-y-1.5">
                        @foreach($anaesthesiaUsage as $mode => $cnt)
                        <div class="flex justify-between text-xs font-semibold">
                            <span class="text-slate-500 dark:text-slate-400">{{ $mode }}</span>
                            <span class="text-slate-900 dark:text-white font-bold">{{ $cnt }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Post-Op Destination -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-3">Post-Op Destination</h4>
                    <div class="relative h-36"><canvas id="postOpChart"></canvas></div>
                    <div class="grid grid-cols-3 gap-2 text-center mt-3">
                        <div><div class="text-lg font-black text-blue-600 dark:text-blue-400">{{ $postOpBreakdown['Ward'] ?? 0 }}</div><div class="text-xs font-bold text-slate-400">Ward</div></div>
                        <div><div class="text-lg font-black text-amber-500 dark:text-amber-400">{{ $postOpBreakdown['HDU'] ?? 0 }}</div><div class="text-xs font-bold text-slate-400">HDU</div></div>
                        <div><div class="text-lg font-black text-red-600 dark:text-red-400">{{ $postOpBreakdown['ICU'] ?? 0 }}</div><div class="text-xs font-bold text-slate-400">ICU</div></div>
                    </div>
                </div>
                <!-- Safety KPIs -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6 flex flex-col gap-5 justify-center">
                    <div class="text-center p-4 rounded-xl {{ $difficultIntubRate > 10 ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-slate-50 dark:bg-gray-700/40' }}">
                        <div class="text-4xl font-black {{ $difficultIntubRate > 10 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">{{ $difficultIntubRate }}%</div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Difficult Intubation Rate</div>
                        @if($difficultIntubRate > 10)
                            <span class="inline-block mt-1 text-xs font-bold text-red-600">⚠ Elevated</span>
                        @endif
                    </div>
                    <div class="text-center p-4 rounded-xl bg-slate-50 dark:bg-gray-700/40">
                        <div class="text-4xl font-black text-slate-900 dark:text-white">{{ $relaxantUseRate }}%</div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mt-1">Relaxant Use Rate</div>
                    </div>
                </div>
            </div>

            <!-- ===== ADVERSE EVENT RATE BREAKDOWN — Clinically Most Important ===== -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-slate-900 dark:text-white">Adverse Event Rate Breakdown</h4>
                        <p class="text-xs text-slate-400">By ASA grade, surgery type, and anaesthesia mode</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">By ASA Grade</p>
                        <div class="relative h-48"><canvas id="aeByAsaChart"></canvas></div>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">By Surgery Type</p>
                        <div class="relative h-48"><canvas id="aeByTypeChart"></canvas></div>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">By Anaesthesia Mode</p>
                        <div class="relative h-48"><canvas id="aeByModeChart"></canvas></div>
                    </div>
                </div>
            </div>

            <!-- Row: Top Adverse Events + Top Comorbidities -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-4">Top Adverse Events</h4>
                    <div class="relative h-56"><canvas id="topAeChart"></canvas></div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6">
                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 dark:text-white mb-4">Top Comorbidities</h4>
                    <div class="relative h-56"><canvas id="topComorChart"></canvas></div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#9ca3af' : '#64748b';
            const gridColor = isDark ? 'rgba(75,85,99,0.25)' : 'rgba(226,232,240,0.7)';

            const trend = {!! json_encode($trend) !!};
            const emVsElect = {!! json_encode($emVsElect) !!};
            const asaDist = {!! json_encode($asaDist) !!};
            const anaestMode = {!! json_encode($anaesthesiaUsage) !!};
            const postOp = {!! json_encode($postOpBreakdown) !!};
            const aeRate = {!! json_encode($adverseEventRate) !!};
            const topAe = {!! json_encode($topAdverseEvents) !!};
            const topComor = {!! json_encode($topComorbidities) !!};

            // 1. Trend Line
            const heroCtx = document.getElementById('trendChart').getContext('2d');
            const heroGrad = heroCtx.createLinearGradient(0, 0, 0, 200);
            heroGrad.addColorStop(0, 'rgba(99,102,241,0.45)');
            heroGrad.addColorStop(1, 'rgba(168,85,247,0.05)');
            new Chart(heroCtx, {
                type: 'line',
                data: { labels: trend.labels, datasets: [{ label: 'Cases', data: trend.data, borderColor: '#6366f1', backgroundColor: heroGrad, borderWidth: 3, fill: true, tension: 0.45, pointBackgroundColor: '#8b5cf6', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }, x: { ticks: { color: textColor }, grid: { display: false } } }, plugins: { legend: { display: false } } }
            });

            // 2. Em vs Elect
            new Chart(document.getElementById('emVsElectChart'), {
                type: 'bar',
                data: { labels: ['Emergency', 'Elective'], datasets: [{ data: [emVsElect['Em'] ?? 0, emVsElect['Elect'] ?? 0], backgroundColor: ['#ef4444', '#3b82f6'], borderRadius: 6 }] },
                options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, display: false }, y: { ticks: { color: textColor }, grid: { display: false } } }, plugins: { legend: { display: false } } }
            });

            // 3. ASA Distribution
            new Chart(document.getElementById('asaChart'), {
                type: 'bar',
                data: { labels: Object.keys(asaDist), datasets: [{ data: Object.values(asaDist), backgroundColor: ['#4f46e5','#06b6d4','#10b981','#f59e0b','#ef4444','#a855f7','#f97316'], borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { x: { ticks: { color: textColor, font: { size: 11 } }, grid: { display: false } }, y: { display: false } }, plugins: { legend: { display: false } } }
            });

            // 4. Anaesthesia Mode Donut
            new Chart(document.getElementById('anaestModeChart'), {
                type: 'doughnut',
                data: { labels: Object.keys(anaestMode), datasets: [{ data: Object.values(anaestMode), backgroundColor: ['#4f46e5','#06b6d4','#a855f7'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } }
            });

            // 5. Post-Op Bar
            new Chart(document.getElementById('postOpChart'), {
                type: 'bar',
                data: { labels: Object.keys(postOp), datasets: [{ data: Object.values(postOp), backgroundColor: ['#3b82f6','#f59e0b','#ef4444'], borderRadius: 6 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }, x: { ticks: { color: textColor }, grid: { display: false } } }, plugins: { legend: { display: false } } }
            });

            // 6a. AE Rate by ASA
            const asaLabels = Object.keys(aeRate.by_asa);
            const asaRates = asaLabels.map(k => aeRate.by_asa[k].rate);
            new Chart(document.getElementById('aeByAsaChart'), {
                type: 'bar',
                data: { labels: asaLabels.map(g => 'ASA ' + g), datasets: [{ label: 'AE Rate %', data: asaRates, backgroundColor: '#ef4444', borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, ticks: { color: textColor, callback: v => v + '%' }, grid: { color: gridColor } }, x: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } } }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } } } }
            });

            // 6b. AE Rate by Surgery Type
            const typeLabels = Object.keys(aeRate.by_type);
            const typeRates = typeLabels.map(k => aeRate.by_type[k].rate);
            new Chart(document.getElementById('aeByTypeChart'), {
                type: 'bar',
                data: { labels: typeLabels.map(t => t === 'Em' ? 'Emergency' : 'Elective'), datasets: [{ label: 'AE Rate %', data: typeRates, backgroundColor: ['#ef4444','#3b82f6'], borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, ticks: { color: textColor, callback: v => v + '%' }, grid: { color: gridColor } }, x: { ticks: { color: textColor }, grid: { display: false } } }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } } } }
            });

            // 6c. AE Rate by Anaesthesia Mode
            const modeLabels = Object.keys(aeRate.by_mode);
            const modeRates = modeLabels.map(k => aeRate.by_mode[k].rate);
            new Chart(document.getElementById('aeByModeChart'), {
                type: 'bar',
                data: { labels: modeLabels, datasets: [{ label: 'AE Rate %', data: modeRates, backgroundColor: ['#4f46e5','#06b6d4','#a855f7'], borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100, ticks: { color: textColor, callback: v => v + '%' }, grid: { color: gridColor } }, x: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } } }, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } } } }
            });

            // 7. Top Adverse Events
            new Chart(document.getElementById('topAeChart'), {
                type: 'bar',
                data: { labels: Object.keys(topAe), datasets: [{ data: Object.values(topAe), backgroundColor: '#ef4444', borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }, y: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } } }, plugins: { legend: { display: false } } }
            });

            // 8. Top Comorbidities
            new Chart(document.getElementById('topComorChart'), {
                type: 'bar',
                data: { labels: Object.keys(topComor), datasets: [{ data: Object.values(topComor), backgroundColor: '#06b6d4', borderRadius: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }, y: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } } }, plugins: { legend: { display: false } } }
            });
        });
    </script>
</x-app-layout>
