@props(['id' => 'feature-lock-modal'])

<div x-data="{ show: false, title: '', message: '' }"
     x-on:feature-lock.window="show = true; title = $event.detail.title || 'Enterprise Feature 🔒'; message = $event.detail.message || 'This feature is available in the Enterprise Edition. Please upgrade to unlock.';"
     x-on:close-modal.window="show = false"
     x-show="show"
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="fixed inset-0 transform transition-all" x-on:click="show = false">
        <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"></div>
    </div>

    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-full">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <span x-text="title"></span>
            </div>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                <p x-text="message"></p>
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                    <p class="font-semibold text-gray-800 dark:text-gray-200 mb-1">Unlocks:</p>
                    <ul class="list-disc list-inside space-y-1 ml-1">
                        <li>Payroll Generation & Automation</li>
                        <li>Advanced Reporting (Excel/PDF)</li>
                        <li>Audit Trails & Security Logs</li>
                        <li>Secure Storage & Face ID Enforcement</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-right">
            <button x-on:click="show = false" type="button" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Close') }}
            </button>
            <a href="https://wa.me/6282324774380?text=I%20want%20to%20upgrade%20to%20Enterprise%20Edition" target="_blank" class="ml-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Upgrade Now') }}
            </a>
        </div>
    </div>
</div>
