<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-wider">
                Clinical Reports
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/70 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Page Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 border border-slate-200/80 dark:border-gray-700/80 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Reports Centre</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Generate, view and download clinical audit reports</p>
                </div>
                <a href="{{ route('doctor.analytics') }}" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
                    Analytics Dashboard
                </a>
            </div>

            <!-- Report Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                <!-- 1. Case Audit Report -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6 flex flex-col justify-between gap-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">Case Audit Report</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Full clinical detail of a single surgery: patient demographics, anaesthesia, agents, staff, adverse events.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <p class="text-xs text-slate-400 mb-3">Enter a surgery ID to view / download the full case audit PDF.</p>
                        <form method="GET" action="" id="caseAuditForm" class="flex gap-2">
                            <input type="number" id="caseAuditSurgeryId" placeholder="Surgery ID…" min="1"
                                class="flex-1 rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            <button type="button" onclick="openCaseAudit()"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-sm transition">
                                View
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 2. Patient Anaesthesia History -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6 flex flex-col justify-between gap-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-rose-100 dark:bg-rose-900/40 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">Patient Anaesthesia History</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Complete anaesthesia history for a patient by PHN. Flags difficult airway &amp; adverse events prominently.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <form method="GET" action="{{ route('doctor.reports.patient-history') }}" class="flex gap-2">
                            <input type="text" name="phn" placeholder="Patient PHN…"
                                class="flex-1 rounded-lg border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-sm px-3 py-2 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none">
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-sm transition">
                                View
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 3. Difficult Airway Registry -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6 flex flex-col justify-between gap-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/40 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">Difficult Airway Registry</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">All cases with documented difficult intubation. Filterable by date range and specialty.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <a href="{{ route('doctor.reports.difficult-airway') }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            Open Registry
                        </a>
                    </div>
                </div>

                <!-- 4. Adverse Event / M&M Report -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6 flex flex-col justify-between gap-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900/40 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">Adverse Event / M&amp;M Report</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">All cases with adverse events, grouped by event type. Printable for Morbidity &amp; Mortality meetings.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <a href="{{ route('doctor.reports.adverse-events') }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            Open M&amp;M Report
                        </a>
                    </div>
                </div>

                <!-- 5. My Case Log -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200/80 dark:border-gray-700/80 shadow-sm p-6 flex flex-col justify-between gap-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base">My Case Log</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">All cases you were involved in. Export as PDF for personal records and revalidation portfolios.</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-gray-700">
                        <a href="{{ route('doctor.reports.my-cases') }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            View My Cases
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        function openCaseAudit() {
            const id = document.getElementById('caseAuditSurgeryId').value;
            if (id && parseInt(id) > 0) {
                window.location.href = '{{ route("doctor.reports.case", ":id") }}'.replace(':id', id);
            }
        }
    </script>
</x-app-layout>
