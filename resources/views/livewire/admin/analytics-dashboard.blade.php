<div class="py-6 lg:py-12" x-data='initAnalyticsCharts({
    trend: @json($trend),
    metrics: @json($metrics),
    division: @json($divisionStats),
    late: @json($lateBuckets),
    absent: @json($absentStats)
})'>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Filters -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300">
                    {{ __('Analytics Dashboard') }}
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Comprehensive overview of workforce performance.') }}
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="w-full sm:w-40">
                    <x-tom-select wire:model.live="month" placeholder="{{ __('Select Month') }}" class="w-full">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
                <div class="w-full sm:w-32">
                     <x-tom-select wire:model.live="year" placeholder="{{ __('Select Year') }}" class="w-full">
                        @foreach(range(date('Y')-1, date('Y')) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </x-tom-select>
                </div>
                <!-- Loading Indicator -->
                <div wire:loading class="flex items-center px-3 text-primary-600">
                     <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Employees -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl p-6 rounded-3xl shadow-lg border border-gray-100/50 dark:border-gray-700/50 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Workforce') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $summary['total_employees'] }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Employee Active
                    </p>
                </div>
            </div>

            <!-- Attendance Rate -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl p-6 rounded-3xl shadow-lg border border-gray-100/50 dark:border-gray-700/50 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Attendance Rate') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $summary['attendance_rate'] }}%</h3>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-3">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $summary['attendance_rate'] }}%"></div>
                    </div>
                </div>
            </div>

             <!-- Late Rate -->
             <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl p-6 rounded-3xl shadow-lg border border-gray-100/50 dark:border-gray-700/50 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Late Occurrence') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $summary['late_rate'] }}%</h3>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-2 font-medium">Of total present</p>
                </div>
            </div>

             <!-- Avg Daily Attendance -->
             <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl p-6 rounded-3xl shadow-lg border border-gray-100/50 dark:border-gray-700/50 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Avg Daily Presence') }}</p>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $summary['avg_daily_attendance'] }}</h3>
                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-2 font-medium">People / Day</p>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Line Chart (Trend) -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Attendance Trend') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Dynamic flow over selected month') }}</p>
                    </div>
                </div>
                <div class="relative h-72 w-full">
                    <canvas x-ref="trendChart"></canvas>
                </div>
            </div>

            <!-- Division Performance -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Division Performance') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Contribution by department') }}</p>
                    </div>
                </div>
                <div class="relative h-72 w-full">
                    <canvas x-ref="divisionChart"></canvas>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
               <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Status Distribution') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Overall metrics breakdown') }}</p>
                    </div>
                </div>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas x-ref="statusChart"></canvas>
                </div>
            </div>

             <!-- Late Severity -->
             <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                     <div>
                         <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Late Analysis') }}</h3>
                         <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Severity of tardiness') }}</p>
                     </div>
                 </div>
                 <div class="relative h-64 w-full flex justify-center">
                     <canvas x-ref="lateChart"></canvas>
                 </div>
             </div>
        </div>

        <!-- Leaderboards Grid -->
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 px-1 flex items-center gap-2">
            <span>🏆</span> {{ __('Wall of Fame (Top 5)') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Top Diligent -->
            <div class="bg-gradient-to-br from-white to-green-50/50 dark:from-gray-800 dark:to-green-900/10 p-6 rounded-3xl shadow-xl border border-green-100 dark:border-green-900/30">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Early Birds') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Most consistent arrival') }}</p>
                    </div>
                </div>
                <div class="flow-root">
                    <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($topDiligent as $index => $employee)
                            <li class="py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 relative">
                                        <img class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm" src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}">
                                        @if($index < 3)
                                            <span class="absolute -top-1 -right-1 text-sm filter drop-shadow">
                                                {{ $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate dark:text-white">
                                            {{ $employee->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                                            {{ $employee->jobTitle?->name ?? 'Employee' }}
                                        </p>
                                    </div>
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100/80 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                        {{ gmdate('H:i', $employee->avg_check_in) }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-6 italic">{{ __('No data available') }}</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Top Late -->
            <div class="bg-gradient-to-br from-white to-red-50/50 dark:from-gray-800 dark:to-red-900/10 p-6 rounded-3xl shadow-xl border border-red-100 dark:border-red-900/30">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400 rounded-2xl">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Frequent Late') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Needs improvement') }}</p>
                    </div>
                </div>
                <div class="flow-root">
                    <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($topLate as $index => $employee)
                            <li class="py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 relative">
                                        <img class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm" src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate dark:text-white">
                                            {{ $employee->name }}
                                        </p>
                                    </div>
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100/80 text-red-800 dark:bg-red-900/30 dark:text-red-200">
                                        {{ $employee->late_count }}x
                                    </div>
                                </div>
                            </li>
                        @empty
                             <div class="text-center py-6">
                                <span class="text-4xl">🎉</span>
                                <p class="text-sm text-gray-500 mt-2">{{ __('Everyone is on time!') }}</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Top Early Leavers -->
            <div class="bg-gradient-to-br from-white to-orange-50/50 dark:from-gray-800 dark:to-orange-900/10 p-6 rounded-3xl shadow-xl border border-orange-100 dark:border-orange-900/30">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400 rounded-2xl">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ __('Early Runners') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Check-out too soon') }}</p>
                    </div>
                </div>
                <div class="flow-root">
                    <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($topEarlyLeavers as $index => $employee)
                            <li class="py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 relative">
                                        <img class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm" src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate dark:text-white">
                                            {{ $employee->name }}
                                        </p>
                                    </div>
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100/80 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200">
                                        {{ $employee->early_leave_count }}x
                                    </div>
                                </div>
                            </li>
                        @empty
                            <div class="text-center py-6">
                                <span class="text-4xl">👏</span>
                                <p class="text-sm text-gray-500 mt-2">{{ __('Full attendance!') }}</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Use Global Window function instead of Alpine.data to ensure it works across SPA navigations
        window.initAnalyticsCharts = (initialData) => ({
             data: initialData,
             charts: {},
             
             translate(key) {
                const dict = {
                    'present': '{{ __("Present") }}',
                    'late': '{{ __("Late") }}',
                    'sick': '{{ __("Sick") }}',
                    'excused': '{{ __("Excused") }}',
                    'absent': '{{ __("Absent") }}',
                    'alpha': '{{ __("Alpha") }}'
                };
                return dict[key.toLowerCase()] || key;
             },

             init() {
                 // Wait for next tick to ensure DOM is ready (refs)
                 this.$nextTick(() => {
                     this.renderCharts();
                 });

                 // Listener triggers re-render
                 Livewire.on('chart-update', (newData) => {
                     this.data.trend = newData.trend;
                     this.data.metrics = newData.metrics;
                     this.data.division = newData.divisionStats;
                     this.data.late = newData.lateBuckets;
                     this.data.absent = newData.absentStats; // Ensure key matches
                     
                     this.renderCharts();
                 });
             },

             renderCharts() {
                // Safety check for Chart.js
                if (typeof Chart === 'undefined') {
                    setTimeout(() => this.renderCharts(), 200);
                    return;
                }

                // We now destroy in individual render functions for safety using Chart.getChart
                this.renderTrendChart();
                this.renderDivisionChart();
                this.renderStatusChart();
                this.renderLateChart();
             },

             renderTrendChart() {
                const ctx = this.$refs.trendChart;
                if (!ctx) return;
                
                // Destroy existing chart on this canvas if any
                if (Chart.getChart(ctx)) {
                    Chart.getChart(ctx).destroy();
                }

                const gradient = ctx.getContext('2d').createLinearGradient(0,0,0,300);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                this.charts.trend = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.data.trend.labels || [],
                        datasets: [
                            {
                                label: this.translate('present'),
                                data: this.data.trend.present || [],
                                borderColor: '#10B981',
                                backgroundColor: gradient,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 2
                            },
                            {
                                label: this.translate('late'),
                                data: this.data.trend.late || [],
                                borderColor: '#EF4444',
                                borderDash: [5,5],
                                fill: false,
                                tension: 0.4,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                         plugins: {
                            legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6 } },
                            tooltip: { mode: 'index', intersect: false }
                        },
                         scales: {
                            x: { grid: { display: false } },
                            y: { grid: { borderDash: [2, 4], color: '#f3f4f6' }, beginAtZero: true }
                        }
                    }
                });
             },

             renderDivisionChart() {
                 const ctx = this.$refs.divisionChart;
                 if (!ctx) return;
                 
                 if (Chart.getChart(ctx)) {
                    Chart.getChart(ctx).destroy();
                 }

                 this.charts.division = new Chart(ctx, {
                     type: 'bar',
                     data: {
                         labels: this.data.division.labels || [],
                         datasets: [{
                             label: '{{ __("Present") }}',
                             data: this.data.division.data || [],
                             backgroundColor: '#3B82F6',
                             borderRadius: 4
                         }]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         plugins: { legend: { display: false } },
                         scales: { x: { grid: { display:false } }, y: { beginAtZero: true, grid: { borderDash: [2, 4] } } }
                     }
                 });
             },

             renderStatusChart() {
                 const ctx = this.$refs.statusChart;
                 if (!ctx) return;

                 if (Chart.getChart(ctx)) {
                    Chart.getChart(ctx).destroy();
                 }

                 const labels = Object.keys(this.data.metrics || {});
                 const data = Object.values(this.data.metrics || {});

                 this.charts.status = new Chart(ctx, {
                     type: 'doughnut',
                     data: {
                         labels: labels.map(l => this.translate(l)),
                         datasets: [{
                             data: data,
                             backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#3B82F6', '#6B7280'],
                             borderWidth: 0
                         }]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         cutout: '75%',
                         plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } }
                     }
                 });
             },

             renderLateChart() {
                 const ctx = this.$refs.lateChart;
                 if (!ctx) return;

                 if (Chart.getChart(ctx)) {
                    Chart.getChart(ctx).destroy();
                 }

                 const labels = Object.keys(this.data.late || {});
                 const data = Object.values(this.data.late || {});

                 this.charts.late = new Chart(ctx, {
                     type: 'pie',
                     data: {
                         labels: labels,
                         datasets: [{
                             data: data,
                             backgroundColor: ['#FECACA', '#FCA5A5', '#EF4444', '#B91C1C'],
                             borderWidth: 0
                         }]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } }
                     }
                 });
             }
        });
    </script>
    @endpush
</div>
