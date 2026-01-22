<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Message Notification -->
            @if (session('status'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <!-- Top Row: Status & Savings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Daily Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Today's Status</h3>
                    @if ($smokedToday)
                        <div class="flex flex-col items-center text-center text-amber-600 bg-amber-50 p-4 rounded-lg">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <strong class="block text-lg">You smoked today.</strong>
                            <span class="text-sm mt-1">Don't give up!</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center text-center text-emerald-600 bg-emerald-50 p-4 rounded-lg">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <strong class="block text-lg">Clean Streak!</strong>
                            <span class="text-sm mt-1">Keep it up!</span>
                        </div>
                    @endif
                </div>

                <!-- Total Savings Display -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Total Savings</h3>
                    <div class="flex flex-col items-center justify-center h-full pb-6">
                        <span class="text-4xl font-bold text-emerald-600 font-mono" id="savingsDisplay">$0.00</span>
                        <span class="text-sm text-gray-400 mt-2">Real-time Est.</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Monthly Progress Calendar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ now()->format('F Y') }} Progress</h3>

                <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-2">
                    <div>Su</div>
                    <div>Mo</div>
                    <div>Tu</div>
                    <div>We</div>
                    <div>Th</div>
                    <div>Fr</div>
                    <div>Sa</div>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                    @php
                        $startDay = \Carbon\Carbon::parse($calendar[0]['date'])->dayOfWeek;
                    @endphp

                    <!-- Empty cells for days before start of month -->
                    @for ($i = 0; $i < $startDay; $i++)
                        <div class="p-2"></div>
                    @endfor

                    @foreach ($calendar as $day)
                        <div class="p-2 rounded-lg flex items-center justify-center relative
                                {{ $day['status'] === 'smoked' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $day['status'] === 'clean' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $day['status'] === 'future' ? 'text-gray-300' : '' }}
                                {{ $day['is_today'] ? 'ring-2 ring-indigo-500 font-bold' : '' }}
                            ">
                            {{ $day['day'] }}
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>


    <script>
        const userSettings = {
            quitDate: "{{ Auth::user()->quit_date }}",
            cigsPerDay: {{ Auth::user()->cigarettes_per_day ?? 20 }},
            packPrice: {{ Auth::user()->pack_price ?? 10 }},
            totalPenalty: {{ $totalPenalty ?? 0 }}
        };

        // Savings Logic
        let savingsInterval;

        function startSavingsCounter() {
            if (!userSettings.quitDate) return;

            const quitTime = new Date(userSettings.quitDate).getTime();
            const pricePerCig = userSettings.packPrice / 20;
            const cigsPerSec = userSettings.cigsPerDay / 86400;
            const costPerSec = cigsPerSec * userSettings.packPrice / 20 * 20; // Simplified
            // The formula: Cost per sec = ((cigarrette per day / 20) x price per pack) / 86400 seconds.
            const costPerSecond = ((userSettings.cigsPerDay / 20) * userSettings.packPrice) / 86400;

            function update() {
                const now = new Date().getTime();
                const diffSeconds = (now - quitTime) / 1000;

                if (diffSeconds > 0) {
                    const grossSavings = diffSeconds * costPerSecond;
                    // Net savings = Gross - Penalty (but not less than 0)
                    const netSavings = Math.max(0, grossSavings - userSettings.totalPenalty);

                    const el = document.getElementById('savingsDisplay');
                    if (el) el.innerText = 'RM' + netSavings.toFixed(4); // RM currency
                }
                requestAnimationFrame(update);
            }
            update();
        }

        startSavingsCounter();
    </script>
</x-app-layout>