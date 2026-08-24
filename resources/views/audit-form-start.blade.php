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
                        Search Patient by PHN
                    </h3>
                    <p class="text-blue-100 text-sm mt-1">Type the Patient Hospital Number — matching records appear instantly below.</p>
                </div>

                <div class="p-6 space-y-5">

                    {{-- PHN Input with live suggestions --}}
                    <div>
                        <label for="phn-search-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Patient Hospital Number (PHN) <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            {{-- Search icon inside input --}}
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg id="search-icon" class="w-5 h-5 text-gray-400 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                {{-- Spinner (hidden by default) --}}
                                <svg id="search-spinner" class="w-5 h-5 text-indigo-500 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>

                            <input
                                type="text"
                                id="phn-search-input"
                                placeholder="Start typing PHN…"
                                autocomplete="off"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-base transition"
                            >

                            {{-- Dropdown suggestion list --}}
                            <ul
                                id="suggestions-list"
                                class="hidden absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg overflow-hidden"
                                role="listbox"
                                aria-label="Patient suggestions"
                            ></ul>
                        </div>

                        <p id="min-chars-hint" class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Type at least 1 character to search.</p>
                    </div>

                    {{-- Result Panel (shown after a lookup completes) --}}
                    <div id="result-panel" class="hidden">

                        {{-- Not Found: New Patient (existing patient goes directly to form) --}}
                        <div id="result-not-found" class="hidden rounded-xl border-2 border-amber-400 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-600 p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-bold text-amber-800 dark:text-amber-300 text-sm">Patient Not Found</span>
                            </div>
                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                No record found for this PHN. A <strong>new patient</strong> will be created when you submit the form.
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
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold rounded-lg shadow cursor-not-allowed pointer-events-none transition-all duration-300"
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
        const suggestionsUrl = @json(route(auth()->user()->role === 'data_entry' ? 'data_entry.audit-form.phn-suggestions' : 'doctor.audit-form.phn-suggestions'));
        const lookupUrl      = @json($lookupUrl);
        const formUrl        = @json($formUrl);

        const phnInput        = document.getElementById('phn-search-input');
        const suggestionsList = document.getElementById('suggestions-list');
        const resultPanel     = document.getElementById('result-panel');
        const resultNotFound  = document.getElementById('result-not-found');
        const nextBtn         = document.getElementById('next-btn');
        const searchIcon      = document.getElementById('search-icon');
        const searchSpinner   = document.getElementById('search-spinner');
        const minCharsHint    = document.getElementById('min-chars-hint');

        let debounceTimer  = null;
        let lookupTimer    = null;
        let lastQuery      = '';

        // ── Enable / Disable the Next button ─────────────────────────────────
        function enableNext(phn) {
            nextBtn.href = formUrl + '?phn=' + encodeURIComponent(phn);
            nextBtn.classList.remove(
                'bg-gray-300','dark:bg-gray-600',
                'text-gray-500','dark:text-gray-400',
                'cursor-not-allowed','pointer-events-none'
            );
            nextBtn.classList.add(
                'bg-indigo-600','hover:bg-indigo-700',
                'text-white','scale-105'
            );
            nextBtn.removeAttribute('aria-disabled');
        }

        function disableNext() {
            nextBtn.href = '#';
            nextBtn.classList.remove(
                'bg-indigo-600','hover:bg-indigo-700',
                'text-white','scale-105'
            );
            nextBtn.classList.add(
                'bg-gray-300','dark:bg-gray-600',
                'text-gray-500','dark:text-gray-400',
                'cursor-not-allowed','pointer-events-none'
            );
            nextBtn.setAttribute('aria-disabled', 'true');
        }

        // ── Show / hide spinner ───────────────────────────────────────────────
        function showSpinner() {
            searchIcon.classList.add('hidden');
            searchSpinner.classList.remove('hidden');
        }
        function hideSpinner() {
            searchIcon.classList.remove('hidden');
            searchSpinner.classList.add('hidden');
        }

        // ── Hide the result panel & clear dropdowns ───────────────────────────
        function resetResults() {
            resultPanel.classList.add('hidden');
            resultNotFound.classList.add('hidden');
        }

        // ── Build suggestion dropdown ─────────────────────────────────────────
        function renderSuggestions(patients) {
            suggestionsList.innerHTML = '';

            if (!patients.length) {
                suggestionsList.classList.add('hidden');
                return;
            }

            patients.forEach(p => {
                const li = document.createElement('li');
                li.setAttribute('role', 'option');
                li.className = [
                    'flex items-center justify-between gap-4',
                    'px-4 py-3 cursor-pointer',
                    'hover:bg-indigo-50 dark:hover:bg-indigo-900/30',
                    'border-b border-gray-100 dark:border-gray-700 last:border-0',
                    'transition-colors duration-150'
                ].join(' ');

                const sexLabel = p.sex === 'M' ? 'Male' : (p.sex === 'F' ? 'Female' : '—');
                const decryptedName = p.name ?? '—';

                li.innerHTML = `
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                            ${p.sex === 'M' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300' : 'bg-pink-100 text-pink-600 dark:bg-pink-900 dark:text-pink-300'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">${escHtml(decryptedName)}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PHN: <span class="font-mono font-medium text-indigo-600 dark:text-indigo-400">${escHtml(p.phn)}</span></p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                        ${p.sex === 'M' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300'}">
                        ${escHtml(sexLabel)}
                    </span>
                `;

                li.addEventListener('click', () => selectPatient(p));
                suggestionsList.appendChild(li);
            });

            suggestionsList.classList.remove('hidden');
        }

        // ── Select a patient → go directly to the form ───────────────────────
        function selectPatient(p) {
            // Navigate immediately — form will auto-fill from the PHN
            window.location.href = formUrl + '?phn=' + encodeURIComponent(p.phn);
        }

        // ── Exact PHN lookup for Enter-key / manual input ─────────────────────
        // Only used when user types a PHN manually and presses Enter.
        // If patient exists → go to form. If not → show not-found state.
        function doExactLookup(phn) {
            showSpinner();
            resetResults();

            fetch(lookupUrl + '?phn=' + encodeURIComponent(phn), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data) {
                    // Patient found → go directly to form
                    window.location.href = formUrl + '?phn=' + encodeURIComponent(phn);
                } else {
                    // Not found → show message + enable Next for new patient
                    resultPanel.classList.remove('hidden');
                    resultNotFound.classList.remove('hidden');
                    enableNext(phn);
                }
            })
            .catch(() => {
                resetResults();
                disableNext();
            })
            .finally(() => hideSpinner());
        }

        // ── Partial PHN suggestions (auto-search as you type) ─────────────────
        function fetchSuggestions(query) {
            if (query.length < 1) {
                suggestionsList.classList.add('hidden');
                resetResults();
                disableNext();
                minCharsHint.classList.remove('hidden');
                return;
            }

            minCharsHint.classList.add('hidden');
            showSpinner();

            fetch(suggestionsUrl + '?query=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(patients => {
                renderSuggestions(patients);

                // If input exactly matches one record's PHN, auto-trigger the full lookup
                if (patients.length === 0) {
                    // No suggestions → show not-found state + enable Next for new patient
                    suggestionsList.classList.add('hidden');
                    resultPanel.classList.remove('hidden');
                    resultNotFound.classList.remove('hidden');
                    enableNext(query);
                }
            })
            .catch(() => {
                suggestionsList.classList.add('hidden');
            })
            .finally(() => hideSpinner());
        }

        // ── Input events ──────────────────────────────────────────────────────
        phnInput.addEventListener('input', () => {
            const val = phnInput.value.trim();

            // Reset everything on each keystroke
            resetResults();
            disableNext();
            clearTimeout(debounceTimer);
            clearTimeout(lookupTimer);

            if (!val) {
                suggestionsList.classList.add('hidden');
                minCharsHint.classList.remove('hidden');
                hideSpinner();
                return;
            }

            // Debounce the suggestion fetch (250 ms)
            debounceTimer = setTimeout(() => fetchSuggestions(val), 250);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', e => {
            if (!phnInput.contains(e.target) && !suggestionsList.contains(e.target)) {
                suggestionsList.classList.add('hidden');
            }
        });

        // Keyboard navigation in suggestions
        phnInput.addEventListener('keydown', e => {
            const items = suggestionsList.querySelectorAll('li');
            let focused = suggestionsList.querySelector('li.bg-indigo-50, li.dark\\:bg-indigo-900\\/30[data-focused]');

            if (e.key === 'Escape') {
                suggestionsList.classList.add('hidden');
                return;
            }

            if (e.key === 'Enter' && phnInput.value.trim()) {
                suggestionsList.classList.add('hidden');
                doExactLookup(phnInput.value.trim());
                return;
            }
        });

        // ── Utility: escape HTML ──────────────────────────────────────────────
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    </script>
</x-app-layout>
