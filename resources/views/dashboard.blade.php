<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('dashboard') }}" class="flex justify-end items-center gap-2 mb-4">
                <select name="month" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 placeholder-gray-400">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                    @endforeach
                </select>
                <select name="year" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 placeholder-gray-400">
                    @foreach(range(date('Y') - 5, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endforeach
                </select>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Filter
                </button>
                @if($selectedMonth != now()->month || $selectedYear != now()->year)
                <a href="{{ route('dashboard') }}"
                    class="text-gray-700 hover:bg-gray-300 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                    Reset
                </a>
                @endif
            </form>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- Revenue -->
                <div class="col-span-2 md:col-span-4 lg:col-span-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Revenue</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="col-span-1 md:col-span-1 lg:col-span-1">
                            <span class="block text-sm text-gray-500">
                                This Week <span class="text-xs text-gray-400">({{ $thisWeekRange }})</span>
                            </span>
                            <span class="text-md font-semibold text-gray-800 block">{{ format_rupiah($thisWeekRevenue) }}</span>
                            <span class="text-sm font-medium text-gray-600">
                                Unearned:
                                <span class="text-red-600">{{ format_rupiah($thisWeekUnearnedRevenue) }}</span>
                            </span>
                        </div>
                        <div class="col-span-1 md:col-span-1 lg:col-span-1">
                            <span class="block text-gray-500 text-sm">This Month</span>
                            @php
                                $isUp = $thisMonthRevenue > $lastMonthRevenue;
                                $percentage = $lastMonthRevenue > 0
                                    ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                                    : 100;
                            @endphp
                            <span class="text-md font-semibold flex items-center {{ $isUp ? 'text-green-600' : 'text-red-600' }}">
                                {{ format_rupiah($thisMonthRevenue) }}
                                <span class="inline-flex items-center ml-2">
                                    @if($isUp)
                                        <svg class="w-[16px] h-[16px] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M5.575 13.729C4.501 15.033 5.43 17 7.12 17h9.762c1.69 0 2.618-1.967 1.544-3.271l-4.881-5.927a2 2 0 0 0-3.088 0l-4.88 5.927Z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium">+{{ $percentage }}% from last month</span>
                                    @else
                                        <svg class="w-[16px] h-[16px] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M18.425 10.271C19.499 8.967 18.57 7 16.88 7H7.12c-1.69 0-2.618 1.967-1.544 3.271l4.881 5.927a2 2 0 0 0 3.088 0l4.88-5.927Z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium">-{{ abs($percentage) }}% from last month</span>
                                    @endif
                                </span>
                            </span>
                            <span class="text-sm font-medium text-gray-600">
                                Unearned:
                                <span class="text-red-600">{{ format_rupiah($thisMonthUnearnedRevenue) }}</span>
                            </span>
                        </div>
                        <div class="col-span-1 md:col-span-1 lg:col-span-1">
                            <span class="block text-gray-500 text-sm">Last Month</span>
                            <span class="text-md font-semibold text-gray-800 block">{{ format_rupiah($lastMonthRevenue) }}</span>
                            <span class="text-sm font-medium text-gray-600">
                                Unearned:
                                <span class="text-red-600">{{ format_rupiah($lastMonthUnearnedRevenue) }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Top Items Chart -->
                <div class="col-span-2 md:col-span-4 lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 max-h-96">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Top Items</h4>
                    <canvas id="topItemsChart"></canvas>
                </div>

                <!-- Paid vs Unpaid Sales Chart -->
                <div class="col-span-1 md:col-span-2 lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 max-h-96">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Paid vs Unpaid Sales</h4>
                    <canvas class="mx-auto" id="paidUnpaidChart"></canvas>
                </div>

                <!-- Payment Channel Chart -->
                <div class=" col-span-1 md:col-span-2 lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 max-h-96">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Payment Channel</h4>
                    <canvas class="mx-auto" id="paymentChannelChart"></canvas>
                </div>

                <!-- Sales Comparison Chart -->
                <div class="col-span-2 md:col-span-4 lg:col-span-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Sales Comparison</h4>
                    <canvas id="salesChart" height="100"></canvas>
                </div>

                <!-- Last 10 Sales -->
                <div class="col-span-2 md:col-span-4 lg:col-span-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Last 10 Sales</h4>
                    <div class="relative overflow-x-auto sm:rounded-lg">
                        <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Invoice Number</th>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Amount</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Handler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSales as $sale)
                                <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4 text-left font-semibold">{{ $sale->invoice_number }}</td>
                                    <td class="px-6 py-4">{{ $sale->customer_name }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ format_date_with_time($sale->created_at) }}</td>
                                    <td class="px-6 py-4">{{ format_rupiah($sale->total_amount) }}</td>
                                    @php
                                    $colors = [
                                    \App\Enums\SaleStatus::PAID->value => 'bg-green-100 text-green-800',
                                    \App\Enums\SaleStatus::PARTIALLY_PAID->value => 'bg-blue-100 text-blue-800',
                                    \App\Enums\SaleStatus::UNPAID->value => 'bg-yellow-100 text-yellow-800',
                                    \App\Enums\SaleStatus::NEED_REVIEW->value => 'bg-orange-100 text-orange-800',
                                    \App\Enums\SaleStatus::CANCELLED->value => 'bg-red-100 text-red-800',
                                    ];
                                    @endphp
                                    <td class="px-6 py-4">
                                        <span class="{{ $colors[$sale->status->value] }} text-xs font-medium px-2.5 py-1 rounded-full">
                                            {{ $sale->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $sale->createdBy->name }}</th>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">No sales found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function showNoData(containerId, message = "No data available") {
        const container = document.getElementById(containerId).parentElement;
        container.innerHTML = `<div class="flex items-center justify-center text-gray-500 italic h-full">${message}</div>`;
    }

    // Top Items Chart
    const ctxTopItems = document.getElementById('topItemsChart').getContext('2d');
    const topItemsLabels = @json($topItems->pluck('item.name'));
    const topItemsData = @json($topItems->pluck('total_qty'));
    if (topItemsData.length === 0) {
        showNoData('topItemsChart', 'No items sold this month');
    }
    else {
        renderTopItemsChart();
    }
    function renderTopItemsChart() {
        const topItemsChart = new Chart(ctxTopItems, {
            type: 'bar',
            data: {
                labels: topItemsLabels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: topItemsData,
                    backgroundColor: [
                        '#67C090B3', 
                        '#E4004BB3', 
                        '#FF9F40B3', 
                        '#36A2EBB3', 
                        '#9966FFB3',
                    ],
                    borderColor: [
                        '#67C090', 
                        '#E4004B', 
                        '#FF9F40', 
                        '#36A2EB', 
                        '#9966FF',
                    ],
                    borderWidth: 2,
                    borderRadius: 6,
                    barThickness: 25,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x + ' pcs';
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        bottom: 35,
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity'
                        },
                        grid: { drawBorder: false },
                        ticks: {
                            stepSize: 1,
                        }
                    },
                    y: {
                        offset: true,
                        title: {
                            display: true,
                            text: 'Item'
                        },
                        grid: { display: false },
                        ticks: {
                            callback: function(value, index) {
                                const label = this.getLabelForValue(value);
                                return label.length > 8 ? label.substring(0, 8) + '…' : label;
                            }
                        },
                    }
                }
            }
        });
    }

    // Paid vs Unpaid Chart
    const ctxPaidUnpaid = document.getElementById('paidUnpaidChart').getContext('2d');
    if ({{ $paidRevenue }} === 0 && {{ $unpaidRevenue }} === 0) {
        showNoData('paidUnpaidChart', 'No sales this month');
    } else {
        renderPaidUnpaidChart();
    }
    function renderPaidUnpaidChart() {
        const paidUnpaidChart = new Chart(ctxPaidUnpaid, {
            type: 'doughnut',
            data: {
                labels: [
                    'Paid ({{ $paidCount }} invoice)',
                    'Unpaid ({{ $unpaidCount }} invoice)'
                ],
                datasets: [{
                    data: [{{ $paidRevenue }}, {{ $unpaidRevenue }}],
                    backgroundColor: ['#67C090', '#E4004B'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true, 
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Channel Chart
    const ctxPayment = document.getElementById('paymentChannelChart').getContext('2d');
    const paymentLabels = @json(array_keys($paymentChannels->toArray()));
    const paymentData = @json(array_values($paymentChannels->toArray()));
    if (paymentData.length === 0) {
        showNoData('paymentChannelChart', 'No payments this month');
    }
    else {
        renderPaymentChannelChart();
    }
    function renderPaymentChannelChart() {
        const paymentChannelChart = new Chart(ctxPayment, {
            type: 'pie',
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentData,
                    backgroundColor: [
                        '#36A2EB',
                        '#FFCE56',
                        '#FF6384',
                        '#4BC0C0',
                        '#9966FF',
                    ],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
            }
        });
    }


    // Sales Comparison Chart
    const ctxSalesChart = document.getElementById('salesChart').getContext('2d');
    const thisMonthData = [
        @foreach($thisMonthSales as $day => $total)
            { x: {{ $day }}, y: {{ $total }} },
        @endforeach
    ];
    const lastMonthData = [
        @foreach($lastMonthSales as $day => $total)
            { x: {{ $day }}, y: {{ $total }} },
        @endforeach
    ];
    const thisMonthLabel = "{{ $thisMonthName }}";
    const lastMonthLabel = "{{ $lastMonthName }}";
    if (thisMonthData.length === 0 && lastMonthData.length === 0) {
        showNoData('salesChart', 'No sales data for comparison');
    } else {
        renderSalesComparisonChart();
    }
    function renderSalesComparisonChart() {
        const salesChart = new Chart(ctxSalesChart, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: 'This Month',
                        data: thisMonthData,
                        fill: false,
                        borderColor: '#67C090',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 3,
                    },
                    {
                        label: 'Last Month',
                        data: lastMonthData,
                        fill: false,
                        borderColor: '#FF9F40',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'nearest',
                    intersect: true,
                },
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'line',
                        }
                    },
                    tooltip: {
                        mode: 'nearest',
                        intersect: true,
                        callbacks: {
                            title: function(context) {
                                const day = context[0].parsed.x;
                                const datasetLabel = context[0].dataset.label;
                                let monthLabel = thisMonthLabel;
                                if (datasetLabel === 'Last Month') {
                                    monthLabel = lastMonthLabel;
                                }

                                return `${day} ${monthLabel}`;
                            },
                            label: function(context) {
                                const value = context.parsed.y;
                                return `${context.dataset.label}: ${value} order(s)`;
                            },
                        },
                    },
                },
                stacked: false,
                scales: {
                    x: {
                        type: 'linear',
                        offset: true,
                        min: 1,
                        max: 31,
                        ticks: {
                            stepSize: 1,
                        },
                        title: {
                            display: true,
                            text: 'Day',
                        },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grace: '10%',
                        title: {
                            display: true,
                            text: 'Number of Sales',
                        },
                        ticks: {
                            stepSize: 1,
                        },
                        grid: { display: true }
                    }
                }
            }
        });
    }
</script>
