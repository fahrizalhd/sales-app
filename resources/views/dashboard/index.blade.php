<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :menus="[
        ['label' => 'Dashboard'],
        ['label' => 'Overview']
    ]" />
    </x-slot>

    <div class="py-0">
        <div class="max-w-7xl mx-auto p-6 space-y-4">
            <!-- Row 1 -->
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Sales Summary -->
                <div class="md:w-2/3 bg-white p-4 rounded shadow-md h-fit">
                    <h3 class="font-semibold text-md mb-4">Sales Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                        <div class="bg-gray-100 p-3 rounded">
                            <div class="font-medium">Today's Sales</div>
                            <div class="text-lg text-green-600 font-semibold">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-gray-100 p-3 rounded">
                            <div class="font-medium">This Month's Revenue</div>
                            <div class="text-lg text-green-600 font-semibold">Rp {{ number_format($totalThisMonth, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-gray-100 p-3 rounded">
                            <div class="font-medium">Last Month Comparison</div>
                            <div class="text-lg {{ $comparison > 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                {{ $comparison !== null ? ($comparison > 0 ? '+' : '') . $comparison . '%' : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="md:w-1/3 bg-white p-4 rounded shadow-md">
                    <h3 class="font-semibold text-md mb-2">Payment Status</h3>
                    <canvas id="paymentChart" class="w-full h-auto max-h-[200px]"></canvas>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Sales Chart -->
                <div class="md:w-2/3 bg-white p-4 rounded shadow-md -top-30 relative z-10">
                    <h3 class="font-semibold text-md mb-2">30 Days Sales</h3>
                    <canvas id="salesChart" class="w-full h-[300px]"></canvas>
                </div>

                <!-- Top 5 Items -->
                <div class="md:w-1/3 bg-white p-4 rounded shadow-md h-fit">
                    <h3 class="font-semibold text-md mb-2">Most Sold Items</h3>
                    <ol class="pl-5 text-sm text-gray-800 list-decimal">
                        @foreach ($topItems as $item)
                        <li>{{ $item->name }} - {{ $item->total_sold }} pcs</li>
                        @endforeach
                    </ol>

                </div>
            </div>
        </div>
    </div>

    <script>
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesLabels).map(DateStr => new Date(DateStr).toLocaleDateString('en-US', {
                    weekday: "short",
                    month: 'short',
                    day: 'numeric'
                })),
                datasets: [{
                    label: 'Sales Amount',
                    data: @json($salesData),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10000
                        }
                    }
                }
            }
        });

        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: ['Paid', 'Unpaid'],
                datasets: [{
                    data: [@json($paidSalesCount), @json($unpaidSalesCount)],
                    backgroundColor: ['#4ade80', '#f87171'],
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
            }
        });
    </script>
</x-app-layout>