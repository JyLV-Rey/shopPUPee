@extends('common.index')

@section('title', $buyer->first_name . ' ' . $buyer->last_name . ' — Buyer Dashboard')

@push('head')
    @vite('resources/js/charts.js')
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-base-200">
            <div class="avatar placeholder">
                <div class="bg-gradient-to-br from-primary to-primary/70 text-primary-content rounded-2xl w-16 h-16 shadow-sm flex items-center justify-center">
                    <span class="text-2xl font-bold">{{ strtoupper(substr($buyer->first_name, 0, 1)) }}</span>
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $buyer->first_name }} {{ $buyer->last_name }}</h1>
                <p class="text-lg text-base-content/50">Buyer Dashboard</p>
            </div>
        </div>

        {{-- Stat cards row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-sm p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Total Spent</div>
                <div class="stat-value text-lg text-success mt-1">₱{{ number_format($totalSpent, 2) }}</div>
            </div>
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-sm p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Orders Placed
                </div>
                <div class="stat-value text-lg text-primary mt-1">{{ $totalOrdersPlaced }}</div>
            </div>
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-sm p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Cancelled</div>
                <div class="stat-value text-lg text-error mt-1">{{ $totalCancelledOrders }}</div>
            </div>
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-sm p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Refunds</div>
                <div class="stat-value text-lg text-warning mt-1">{{ $totalRefunds }}</div>
            </div>
        </div>

        {{-- Profile & Actions row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 card bg-base-100 border border-base-200 rounded-xl shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-sm font-semibold text-base-content/80 tracking-wide uppercase mb-1">Personal
                        Information</h2>
                    <div class="divider mt-1 mb-3"></div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-base-content/50 text-xs">Full Name</dt>
                            <dd class="font-medium">{{ $buyer->first_name }} {{ $buyer->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Email</dt>
                            <dd class="font-medium">{{ $buyer->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Phone</dt>
                            <dd class="font-medium">{{ $buyer->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Member Since</dt>
                            <dd class="font-medium">
                                {{ $buyer->created_at ? \Carbon\Carbon::parse($buyer->created_at)->format('M d, Y') : '—' }}
                            </dd>
                        </div>
                    </dl>
                    @if ($buyer->is_deleted)
                        <div class="mt-3"><span class="badge badge-error">Account Deactivated</span></div>
                    @endif
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 rounded-xl shadow-sm">
                <div class="card-body p-5">
                    <h2 class="card-title text-sm font-semibold text-base-content/80 tracking-wide uppercase mb-1">Quick
                        Actions</h2>
                    <div class="divider mt-1 mb-3"></div>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('orders') }}" class="btn btn-primary btn-sm justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            View Orders
                        </a>
                        <a href="{{ route('edit.buyer') }}" class="btn btn-outline btn-sm justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profile
                        </a>
                        @if ($buyer->seller)
                            <a href="{{ route('dashboard.seller', $buyer->seller) }}"
                                class="btn btn-ghost btn-sm justify-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Switch to Seller View
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts grid --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold tracking-tight">Analytics</h2>
        </div>
        <div class="dashboard-grid">
            @if ($topCategories->isNotEmpty())
                <x-chart id="chart-top-categories" type="doughnut" :labels="$topCategories->keys()->toArray()" title="Top Categories"
                    :datasets="[
                        [
                            'data' => $topCategories->values()->toArray(),
                            'backgroundColor' => [
                                '#6366f1',
                                '#8b5cf6',
                                '#ec4899',
                                '#f97316',
                                '#eab308',
                                '#22c55e',
                                '#06b6d4',
                                '#3b82f6',
                                '#14b8a6',
                                '#f43f5e',
                            ],
                        ],
                    ]" :options="[
                        'plugins' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'bottom',
                                'labels' => ['boxWidth' => 12, 'padding' => 12],
                            ],
                        ],
                    ]" />
            @endif

            @if ($spendingOverTime->isNotEmpty())
                <x-chart id="chart-spending-time" type="line" :labels="$spendingOverTime->keys()->toArray()" title="Spending Over Time"
                    :datasets="[
                        [
                            'label' => 'Spent (₱)',
                            'data' => $spendingOverTime->values()->toArray(),
                            'borderColor' => '#6366f1',
                            'backgroundColor' => 'rgba(99,102,241,0.08)',
                            'fill' => true,
                            'tension' => 0.4,
                            'pointRadius' => 3,
                            'pointBackgroundColor' => '#6366f1',
                            'borderWidth' => 2,
                        ],
                    ]" :options="[
                        'scales' => [
                            'y' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(0,0,0,0.04)']],
                            'x' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            @if ($spendByCategory->isNotEmpty())
                <x-chart id="chart-spend-category" type="bar" :labels="$spendByCategory->keys()->toArray()" title="Spend by Category"
                    :datasets="[
                        [
                            'label' => '₱ Spent',
                            'data' => $spendByCategory->values()->toArray(),
                            'backgroundColor' => [
                                '#6366f1',
                                '#8b5cf6',
                                '#ec4899',
                                '#f97316',
                                '#eab308',
                                '#22c55e',
                                '#06b6d4',
                                '#3b82f6',
                            ],
                            'borderRadius' => 4,
                        ],
                    ]" :options="[
                        'scales' => [
                            'y' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(0,0,0,0.04)']],
                            'x' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            @if ($purchaseFrequency->isNotEmpty())
                <x-chart id="chart-purchase-freq" type="bar" :labels="$purchaseFrequency->keys()->toArray()" title="Purchase Frequency"
                    :datasets="[
                        [
                            'label' => 'Orders',
                            'data' => $purchaseFrequency->values()->toArray(),
                            'backgroundColor' => '#8b5cf6',
                            'borderRadius' => 4,
                        ],
                    ]" :options="[
                        'scales' => [
                            'y' => [
                                'beginAtZero' => true,
                                'ticks' => ['stepSize' => 1],
                                'grid' => ['color' => 'rgba(0,0,0,0.04)'],
                            ],
                            'x' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            @if ($topProducts->isNotEmpty())
                <x-chart id="chart-top-products" type="bar" :labels="$topProducts->keys()->toArray()" title="Top Products by Quantity"
                    :datasets="[
                        [
                            'label' => 'Qty',
                            'data' => $topProducts->values()->toArray(),
                            'backgroundColor' => '#22c55e',
                            'borderRadius' => 4,
                        ],
                    ]" :options="[
                        'indexAxis' => 'y',
                        'scales' => [
                            'x' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(0,0,0,0.04)']],
                            'y' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            <x-chart id="chart-review-ratings" type="bar" :labels="['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars']" title="Review Ratings" :datasets="[
                [
                    'label' => 'Reviews',
                    'data' => $reviewRatings->values()->toArray(),
                    'backgroundColor' => ['#f43f5e', '#f97316', '#eab308', '#22c55e', '#6366f1'],
                    'borderRadius' => 4,
                ],
            ]"
                :options="[
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                            'ticks' => ['stepSize' => 1],
                            'grid' => ['color' => 'rgba(0,0,0,0.04)'],
                        ],
                        'x' => ['grid' => ['display' => false]],
                    ],
                ]" />

            @if ($preferredSellers->isNotEmpty())
                <x-chart id="chart-preferred-sellers" type="doughnut" :labels="$preferredSellers->keys()->toArray()" title="Preferred Sellers"
                    :datasets="[
                        [
                            'data' => $preferredSellers->values()->toArray(),
                            'backgroundColor' => [
                                '#6366f1',
                                '#8b5cf6',
                                '#ec4899',
                                '#f97316',
                                '#eab308',
                                '#22c55e',
                                '#06b6d4',
                            ],
                        ],
                    ]" :options="[
                        'plugins' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'bottom',
                                'labels' => ['boxWidth' => 12, 'padding' => 12],
                            ],
                        ],
                    ]" />
            @endif

            @if ($paymentMethods->isNotEmpty())
                <x-chart id="chart-payment-methods" type="doughnut" :labels="$paymentMethods->keys()->toArray()" title="Payment Methods"
                    :datasets="[
                        [
                            'data' => $paymentMethods->values()->toArray(),
                            'backgroundColor' => ['#6366f1', '#8b5cf6', '#ec4899', '#f97316', '#eab308', '#22c55e'],
                        ],
                    ]" :options="[
                        'plugins' => [
                            'legend' => [
                                'display' => true,
                                'position' => 'bottom',
                                'labels' => ['boxWidth' => 12, 'padding' => 12],
                            ],
                        ],
                    ]" />
            @endif

            @if ($mostExpensiveItems->isNotEmpty())
                <x-chart id="chart-most-expensive" type="bar" :labels="$mostExpensiveItems->pluck('name')->toArray()" title="Most Expensive Items"
                    :datasets="[
                        [
                            'label' => '₱ Price',
                            'data' => $mostExpensiveItems->pluck('price')->toArray(),
                            'backgroundColor' => '#ec4899',
                            'borderRadius' => 4,
                        ],
                    ]" :options="[
                        'indexAxis' => 'y',
                        'scales' => [
                            'x' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(0,0,0,0.04)']],
                            'y' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            @if ($leastExpensiveItems->isNotEmpty())
                <x-chart id="chart-least-expensive" type="bar" :labels="$leastExpensiveItems->pluck('name')->toArray()" title="Least Expensive Items"
                    :datasets="[
                        [
                            'label' => '₱ Price',
                            'data' => $leastExpensiveItems->pluck('price')->toArray(),
                            'backgroundColor' => '#06b6d4',
                            'borderRadius' => 4,
                        ],
                    ]" :options="[
                        'indexAxis' => 'y',
                        'scales' => [
                            'x' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(0,0,0,0.04)']],
                            'y' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif
        </div>
    </div>
@endsection
