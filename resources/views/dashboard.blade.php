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
            </form>

            <div class="grid grid-cols-2 gap-2">
                <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Revenue</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="border-r-2 pr-2">
                            <span class="block text-gray-500 text-sm">This Week</span>
                            <span class="text-md font-semibold text-gray-800 block"> {{ format_rupiah($thisWeekRevenue) }} </span>
                            <span class="text-sm font-medium text-gray-600"> Unearned:
                                <span class="text-red-600"> {{ format_rupiah($thisWeekUnearnedRevenue) }} </span>
                            </span>
                        </div>
                        <div class="border-r-2 pr-2">
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
                                        <svg class="w-[16px] h-[16px] shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
                                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M5.575 13.729C4.501 15.033 5.43 17 7.12 17h9.762c1.69 0 2.618-1.967 1.544-3.271l-4.881-5.927a2 2 0 0 0-3.088 0l-4.88 5.927Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="md:text-sm sm:text-xs font-medium">+{{ $percentage }}% from last month</span>
                                    @else
                                        <svg class="w-[16px] h-[16px] shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" 
                                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M18.425 10.271C19.499 8.967 18.57 7 16.88 7H7.12c-1.69 0-2.618 1.967-1.544 3.271l4.881 5.927a2 2 0 0 0 3.088 0l4.88-5.927Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="md:text-sm sm:text-xs font-medium">-{{ abs($percentage) }}% from last month</span>
                                    @endif
                                </span>
                            </span>
                            <span class="text-sm font-medium text-gray-600"> Unearned:
                                <span class="text-red-600"> {{ format_rupiah($thisMonthUnearnedRevenue) }} </span>
                            </span>
                        </div>
                        <div class="pr-2">
                            <h4 class="block text-gray-500 text-sm">Last Month</h4>
                            <span class="text-md font-semibold text-gray-800 block">
                                {{ format_rupiah($lastMonthRevenue) }}
                            </span>
                            <span class="text-sm font-medium text-gray-600"> Unearned:
                                <span class="text-red-600"> {{ format_rupiah($lastMonthUnearnedRevenue) }} </span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Top Items</h4>
                    <canvas id="topItemsChart" height="100"></canvas>
                </div>
                <div class="col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Paid vs Unpaid Revenue</h4>
                    <canvas class="mx-auto" id="revenueDonutChart" height="200"></canvas>
                </div>
                <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Sales Comparison</h4>
                    <canvas id="salesChart" height="100"></canvas>
                </div>
                <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-md text-gray-700 uppercase font-semibold mb-4">Last 10 Sales</h4>
                    <div class="relative overflow-x-auto sm:rounded-lg">
                        <table class="w-full text-sm text-center rtl:text-right text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">Invoice Number</th>
                                    <th scope="col" class="px-6 py-3">Customer</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Total</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSales as $sale)
                                <tr class="bg-white border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4 text-left font-semibold"> {{ $sale->invoice_number }} </th>
                                    <td class="px-6 py-4"> {{ $sale->customer_name }} </th>
                                    <td class="px-6 py-4 font-semibold"> {{ format_date_with_time($sale->created_at) }} </th>
                                    <td class="px-6 py-4"> {{ format_rupiah($sale->total_amount) }} </td>
                                    <td class="px-6 py-4">
                                        @if ($sale->is_paid)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">Paid</span>
                                        @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">Not Yet Paid</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No sales found</td>
                                </tr>
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
    const salesChart = new Chart(ctxSalesChart, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'This Month',
                    data: thisMonthData,
                    fill: false,
                    borderColor: 'rgb(103, 192, 144)',
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 3,
                },
                {
                    label: 'Last Month',
                    data: lastMonthData,
                    fill: false,
                    borderColor: 'rgb(255, 159, 64)',
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
                }
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

    const ctxTopItems = document.getElementById('topItemsChart').getContext('2d');
    const topItemsLabels = @json($topItems->pluck('item.name'));
    const topItemsData = @json($topItems->pluck('total_qty'));
    const topItemsChart = new Chart(ctxTopItems, {
        type: 'bar',
        data: {
            labels: topItemsLabels,
            datasets: [{
                label: 'Quantity Sold',
                data: topItemsData,
                backgroundColor: [
                    'rgba(103, 192, 144, 0.7)',
                    'rgba(228, 0, 75, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgb(103, 192, 144)',
                    'rgb(228, 0, 75)',
                    'rgb(255, 159, 64)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
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
                    title: {
                        display: true,
                        text: 'Item'
                    },
                    grid: { display: false }
                }
            }
        }
    });

    const ctxRevenue = document.getElementById('revenueDonutChart').getContext('2d');
    const revenueDonutChart = new Chart(ctxRevenue, {
        type: 'doughnut',
        data: {
            labels: [
                'Paid ({{ $paidCount }} invoice)',
                'Unpaid ({{ $unpaidCount }} invoice)'
            ],
            datasets: [{
                data: [{{ $paidRevenue }}, {{ $unpaidRevenue }}],
                backgroundColor: ['rgb(103, 192, 144)', 'rgb(228, 0, 75)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true, 
                        padding: 20,
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
</script>
