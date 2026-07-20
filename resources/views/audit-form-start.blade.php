<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Anaesthesia Audit — Patient Lookup') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen flex items-start justify-center">
        <div class="w-full max-w-lg mx-auto px-4">

            {{-- Step Indicator --}}
            <div class="flex items-center mb-8 gap-0">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center shadow">1</span>
                    <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Patient Lookup</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-300 dark:bg-gray-600 mx-3"></div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm font-bold flex items-center justify-center">2</span>
                    <span class="text-sm font-medium text-gray-400 dark:text-gray-500">Audit Form</span>
                </div>
            </div>

            {{-- Lookup Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Enter Patient PHN
                    </h3>
                    <p class="text-blue-100 text-sm mt-1">Enter the Patient Hospital Number to check for existing records.</p>
                </div>

                <div class="p-6 space-y-6">
                    {{-- PHN Input --}}
                    <div>
                        <label for="phn-search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Patient Hospital Number (PHN) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-3">
                            <input
                                type="text"
                                id="phn-search-input"
                                placeholder="e.g. 12345"
                                autocomplete="off"
                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-base"
                            >
                            <button
                                type="button"
                                id="phn-search-btn"
                                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </button>
                        </div>
                    </div>

                    {{-- Result Panel (hidden until search) --}}
                    <div id="result-panel" class="hidden">

                        {{-- Found: Returning Patient --}}
                        <div id="result-found" class="hidden rounded-xl border-2 border-green-400 bg-green-50 dark:bg-green-900/20 dark:border-green-600 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-bold text-green-800 dark:text-green-300 text-sm">Returning Patient Found</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                    <p id="result-name" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">PHN</p>
                                    <p id="result-phn" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Age</p>
                                    <p id="result-age" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Sex</p>
                                    <p id="result-sex" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-green-700 dark:text-green-400">
                                Basic info will be auto-filled in the form. You can update if needed.
                            </p>
                        </div>

                        {{-- Not Found: New Patient --}}
                        <div id="result-not-found" class="hidden rounded-xl border-2 border-blue-400 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600 p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-bold text-blue-800 dark:text-blue-300 text-sm">No Existing Record Found</span>
                            </div>
                            <p class="text-sm text-blue-700 dark:text-blue-400">
                                This PHN is not registered. A new patient record will be created when you submit the form.
                            </p>
                        </div>

                    </div>

                    {{-- Next Button --}}
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ url()->previous() }}"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 flex items-center gap-1 transition">
                            ← Back
                        </a>
                        <a
                            id="next-btn"
                            href="#"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold rounded-lg shadow cursor-not-allowed pointer-events-none transition"
                            aria-disabled="true"
                        >
                            Next — Fill Audit Form
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const lookupUrl = @json($lookupUrl);
        const formUrl   = @json($formUrl);

        const phnInput      = document.getElementById('phn-search-input');
        const searchBtn     = document.getElementById('phn-search-btn');
        const resultPanel   = document.getElementById('result-panel');
        const resultFound   = document.getElementById('result-found');
        const resultNotFound= document.getElementById('result-not-found');
        const nextBtn       = document.getElementById('next-btn');

        function enableNext(phn) {
            nextBtn.href = formUrl + '?phn=' + encodeURIComponent(phn);
            nextBtn.classList.remove('bg-gray-300','dark:bg-gray-600','text-gray-500','dark:text-gray-400','cursor-not-allowed','pointer-events-none');
            nextBtn.classList.add('bg-indigo-600','hover:bg-indigo-700','text-white');
            nextBtn.removeAttribute('aria-disabled');
        }

        function doSearch() {
            const phn = phnInput.value.trim();
            if (!phn) return;

            searchBtn.disabled = true;
            searchBtn.textContent = 'Searching...';

            fetch(lookupUrl + '?phn=' + encodeURIComponent(phn), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                resultPanel.classList.remove('hidden');
                if (data) {
                    resultFound.classList.remove('hidden');
                    resultNotFound.classList.add('hidden');
                    document.getElementById('result-name').textContent = data.name ?? '—';
                    document.getElementById('result-phn').textContent  = phn;
                    document.getElementById('result-age').textContent  = data.age  ?? '—';
                    document.getElementById('result-sex').textContent  = data.sex === 'M' ? 'Male' : (data.sex === 'F' ? 'Female' : '—');
                } else {
                    resultNotFound.classList.remove('hidden');
                    resultFound.classList.add('hidden');
                }
                enableNext(phn);
            })
            .catch(() => {
                resultPanel.classList.add('hidden');
            })
            .finally(() => {
                searchBtn.disabled = false;
                searchBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Search`;
            });
        }

        searchBtn.addEventListener('click', doSearch);
        phnInput.addEventListener('keydown', e => { if (e.key === 'Enter') doSearch(); });
    </script>
</x-app-layout>
