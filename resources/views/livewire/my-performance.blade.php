<div class="py-6 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if (session()->has('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 dark:border-red-900 dark:bg-red-900/30 p-4">
                <div class="flex">
                    <x-heroicon-m-x-circle class="h-5 w-5 text-red-500 flex-shrink-0" />
                    <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        @if (session()->has('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-900/30 p-4">
                <div class="flex">
                    <x-heroicon-m-check-circle class="h-5 w-5 text-green-500 flex-shrink-0" />
                    <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
            
            {{-- Header --}}
            <div class="px-5 py-4 lg:px-8 lg:py-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-800 relative z-10">
                <div class="flex items-center gap-3">
                    <x-secondary-button href="{{ route('home') }}" class="!rounded-xl !px-3 !py-2 border-gray-200 dark:border-gray-600 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <x-heroicon-o-arrow-left class="h-4 w-4 text-gray-500 dark:text-gray-300" />
                    </x-secondary-button>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-1.5 bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400 rounded-lg">
                            📊
                        </span>
                        {{ __('My Performance Reviews') }}
                    </h3>
                </div>
            </div>

            <div class="p-0">
                @if($appraisals->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center min-h-[400px]">
                        <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-document-text class="w-12 h-12 text-gray-300 dark:text-gray-500" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('No performance reviews found.') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm">{{ __('Your managers have not initiated any appraisals yet.') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($appraisals as $appraisal)
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/30">
                                        <span class="text-indigo-600 dark:text-indigo-400 font-bold text-sm">{{ \Carbon\Carbon::createFromDate($appraisal->period_year, $appraisal->period_month, 1)->format('M') }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white capitalize flex items-center gap-2">
                                            {{ \Carbon\Carbon::createFromDate($appraisal->period_year, $appraisal->period_month, 1)->format('F Y') }}
                                            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-medium ring-1 ring-inset
                                                {{ in_array($appraisal->status, ['draft', 'self_assessment']) ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                {{ in_array($appraisal->status, ['manager_review', '1on1_scheduled']) ? 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                {{ $appraisal->status === 'completed' ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-900/30 dark:text-green-400' : '' }}">
                                                {{ __(ucwords(str_replace('_', ' ', $appraisal->status))) }}
                                            </span>
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('Evaluator') }}: {{ $appraisal->evaluator ? $appraisal->evaluator->name : __('Not assigned yet') }}
                                        </p>
                                        <div class="flex items-center gap-2 text-[10px] text-gray-400 mt-0.5">
                                            @if($appraisal->meeting_date)
                                                <span>{{ __('Meeting') }}: {{ \Carbon\Carbon::parse($appraisal->meeting_date)->format('d M Y') }}</span>
                                                @if($appraisal->meeting_link)
                                                    <span>•</span>
                                                    <a href="{{ $appraisal->meeting_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">{{ __('Join Link') }}</a>
                                                @endif
                                            @else
                                                <span>{{ __('Meeting Not Scheduled') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto mt-2 sm:mt-0 border-t sm:border-0 border-gray-100 dark:border-gray-700 pt-3 sm:pt-0">
                                    <div class="text-left sm:text-right">
                                        <span class="text-[10px] text-gray-500 block uppercase tracking-wider">{{ __('Score') }}</span>
                                        @if($appraisal->status === 'completed')
                                            <span class="font-bold text-gray-900 dark:text-white text-lg">{{ $appraisal->final_score }}</span><span class="text-xs text-gray-400">/100</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        @if($appraisal->status === 'self_assessment')
                                            <button wire:click="openSelfAssessment({{ $appraisal->id }})" class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50 font-bold text-xs uppercase tracking-widest transition flex items-center gap-1">
                                                <x-heroicon-m-pencil-square class="w-4 h-4" />
                                                <span class="hidden sm:inline">{{ __('Assessment') }}</span>
                                            </button>
                                        @elseif($appraisal->status === 'completed' && !$appraisal->employee_acknowledgement)
                                            <button wire:click="acknowledge({{ $appraisal->id }})" class="px-3 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 shadow-lg shadow-green-500/30 font-bold text-xs uppercase tracking-widest transition flex items-center gap-1">
                                                <x-heroicon-m-check class="w-4 h-4" />
                                                <span class="hidden sm:inline">{{ __('Acknowledge') }}</span>
                                            </button>
                                        @endif
                                        @if($appraisal->status === 'completed')
                                            <a href="{{ route('appraisal.export-pdf', $appraisal) }}" class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-red-600 dark:text-red-400 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 font-bold text-xs uppercase tracking-widest transition flex items-center gap-1" title="{{ __('Download PDF') }}">
                                                <x-heroicon-m-arrow-down-tray class="w-4 h-4" />
                                                <span class="hidden sm:inline">PDF</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Self Assessment Form Modal -->
    <x-dialog-modal wire:model.live="showSelfAssessmentModal" maxWidth="3xl">
        <x-slot name="title">
            {{ __('Self Assessment Form') }}
        </x-slot>

        <x-slot name="content">
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Please evaluate your performance for each KPI. Use a scale of 1-100. Provide clear, concise comments to justify your score so your manager can review them during the 1-on-1 meeting.') }}
            </p>
            
            <div class="space-y-6">
                @foreach($evaluations as $index => $evaluation)
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ $evaluation->kpiTemplate->name }}</h4>
                            <span class="text-xs font-mono bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 px-2 py-1 rounded">
                                {{ __('Weight') }}: {{ $evaluation->kpiTemplate->weight }}%
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-1">
                                <x-label for="score_{{ $evaluation->id }}" value="{{ __('Your Score (1-100)') }}" />
                                <x-input id="score_{{ $evaluation->id }}" type="number" min="1" max="100" class="mt-1 block w-full text-center" wire:model="selfScores.{{ $evaluation->id }}" />
                                <x-input-error for="selfScores.{{ $evaluation->id }}" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-label for="comment_{{ $evaluation->id }}" value="{{ __('Self Reflection / Proof of Target') }}" />
                                <textarea id="comment_{{ $evaluation->id }}" wire:model="selfComments.{{ $evaluation->id }}" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300"></textarea>
                                <x-input-error for="selfComments.{{ $evaluation->id }}" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showSelfAssessmentModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ml-2" wire:click="submitSelfAssessment" wire:confirm="{{ __('Are you sure? Once submitted, you cannot change your self-assessment.') }}">
                {{ __('Submit Review to Manager') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
