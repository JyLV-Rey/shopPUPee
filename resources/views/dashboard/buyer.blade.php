@use('Illuminate\Support\Js')
@extends('common.index')

@section('title', $buyer->first_name . ' ' . $buyer->last_name . ' — Buyer Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
    .chart-card { display: flex; flex-direction: column; }
    .chart-card canvas { max-height: 220px; }
    .stat-value { font-size: 1.4rem !important; }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">

    {{-- ── Page heading ──────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="avatar placeholder">
            <div class="bg-primary text-primary-content rounded-full w-14">
                <span class="text-xl font-bold">{{ strtoupper(substr($buyer->first_name,0,1)) }}</span>
            </div>
        </div>
        <div>
            <h1 class="text-3xl font-bold">{{ $buyer->first_name }} {{ $buyer->last_name }}</h1>
            <p class="text-base-content/60 text-sm">Buyer Dashboard</p>
        </div>
    </div>

    {{-- ── Header two-column ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Left: Profile card --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-primary mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Personal Info
                </h2>
                <div class="space-y-2 text-sm">
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 shrink-0 text-base-content/70">Full Name</span>
                        <span>{{ $buyer->first_name }} {{ $buyer->last_name }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 shrink-0 text-base-content/70">Email</span>
                        <span>{{ $buyer->email }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 shrink-0 text-base-content/70">Phone</span>
                        <span>{{ $buyer->phone ?? '—' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 shrink-0 text-base-content/70">Member Since</span>
                        <span>{{ $buyer->created_at ? \Carbon\Carbon::parse($buyer->created_at)->format('M d, Y') : '—' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-32 shrink-0 text-base-content/70">Buyer ID</span>
                        <span class="badge badge-outline badge-sm">#{{ $buyer->buyer_id }}</span>
                    </div>
                    @if($buyer->is_deleted)
                        <div class="mt-3">
                            <span class="badge badge-error">Account Deactivated</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Stats + action buttons --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-primary mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Activity Overview
                </h2>

                <div class="stats stats-vertical shadow-none border border-base-200 rounded-xl text-sm w-full">
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Total Spent</div>
                        <div class="stat-value text-success" style="font-size:1.3rem">₱{{ number_format($totalSpent, 2) }}</div>
                    </div>
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Orders Placed</div>
                        <div class="stat-value" style="font-size:1.3rem">{{ $totalOrdersPlaced }}</div>
                    </div>
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Cancelled Orders</div>
                        <div class="stat-value text-error" style="font-size:1.3rem">{{ $totalCancelledOrders }}</div>
                    </div>
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Refunds</div>
                        <div class="stat-value text-warning" style="font-size:1.3rem">{{ $totalRefunds }}</div>
                    </div>
                </div>

                <div class="card-actions justify-start mt-4 gap-2 flex-wrap">
                    <a href="{{ route('orders') }}?buyerId={{ $buyer->buyer_id }}"
                       class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        View Orders
                    </a>
                    <a href="{{ route('edit.buyer') }}?buyerId={{ $buyer->buyer_id }}"
                       class="btn btn-outline btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts section ────────────────────────────────────────────────── --}}
    <h2 class="text-xl font-bold mb-4">Analytics & Charts</h2>

    <div class="dashboard-grid">

        {{-- 1. Top Categories Doughnut --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🛍️ Top Categories</h3>
                @if($topCategories->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-top-categories"></canvas>
                @endif
            </div>
        </div>

        {{-- 2. Spending Over Time Line --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">📈 Spending Over Time</h3>
                @if($spendingOverTime->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-spending-time"></canvas>
                @endif
            </div>
        </div>

        {{-- 3. Spend by Category Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">💰 Spend by Category</h3>
                @if($spendByCategory->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-spend-category"></canvas>
                @endif
            </div>
        </div>

        {{-- 4. Purchase Frequency Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">📅 Purchase Frequency</h3>
                @if($purchaseFrequency->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-purchase-freq"></canvas>
                @endif
            </div>
        </div>

        {{-- 5. Top Products Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🏆 Top Products by Quantity</h3>
                @if($topProducts->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-top-products"></canvas>
                @endif
            </div>
        </div>

        {{-- 6. Review Ratings Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">⭐ Review Ratings</h3>
                <canvas id="chart-review-ratings"></canvas>
            </div>
        </div>

        {{-- 7. Preferred Sellers Doughnut --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🏪 Preferred Sellers</h3>
                @if($preferredSellers->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-preferred-sellers"></canvas>
                @endif
            </div>
        </div>

        {{-- 8. Payment Methods Pie --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">💳 Payment Methods</h3>
                @if($paymentMethods->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-payment-methods"></canvas>
                @endif
            </div>
        </div>

        {{-- 9. Most Expensive Items Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">💎 Most Expensive Items</h3>
                @if($mostExpensiveItems->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-most-expensive"></canvas>
                @endif
            </div>
        </div>

        {{-- 10. Least Expensive Items Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🪙 Least Expensive Items</h3>
                @if($leastExpensiveItems->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-least-expensive"></canvas>
                @endif
            </div>
        </div>

    </div>{{-- /.dashboard-grid --}}
</div>{{-- /.container --}}

{{-- ── Chart.js initialisation ─────────────────────────────────────────── --}}
@push('scripts')
<script>
(function () {
    // Palette helper
    const palette = [
        '#6366f1','#8b5cf6','#ec4899','#f97316','#eab308',
        '#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e',
    ];

    function getCtx(id) {
        const el = document.getElementById(id);
        return el ? el.getContext('2d') : null;
    }

    // Common options
    const baseOpts = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
    };

    // ── 1. Top Categories Doughnut ─────────────────────────────────────────
    @if($topCategories->isNotEmpty())
    new Chart(getCtx('chart-top-categories'), {
        type: 'doughnut',
        data: {
            labels: {{ Js::from($topCategories->keys()->values()) }},
            datasets: [{
                data: {{ Js::from($topCategories->values()) }},
                backgroundColor: palette,
            }],
        },
        options: { ...baseOpts, plugins: { legend: { display: true, position: 'bottom' } } },
    });
    @endif

    // ── 2. Spending Over Time Line ─────────────────────────────────────────
    @if($spendingOverTime->isNotEmpty())
    new Chart(getCtx('chart-spending-time'), {
        type: 'line',
        data: {
            labels: {{ Js::from($spendingOverTime->keys()->values()) }},
            datasets: [{
                label: 'Spent (₱)',
                data: {{ Js::from($spendingOverTime->values()) }},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true } } },
    });
    @endif

    // ── 3. Spend by Category Bar ────────────────────────────────────────────
    @if($spendByCategory->isNotEmpty())
    new Chart(getCtx('chart-spend-category'), {
        type: 'bar',
        data: {
            labels: {{ Js::from($spendByCategory->keys()->values()) }},
            datasets: [{
                label: '₱ Spent',
                data: {{ Js::from($spendByCategory->values()) }},
                backgroundColor: palette,
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true } } },
    });
    @endif

    // ── 4. Purchase Frequency Bar ───────────────────────────────────────────
    @if($purchaseFrequency->isNotEmpty())
    new Chart(getCtx('chart-purchase-freq'), {
        type: 'bar',
        data: {
            labels: {{ Js::from($purchaseFrequency->keys()->values()) }},
            datasets: [{
                label: 'Orders',
                data: {{ Js::from($purchaseFrequency->values()) }},
                backgroundColor: '#8b5cf6',
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
    });
    @endif

    // ── 5. Top Products Bar ──────────────────────────────────────────────────
    @if($topProducts->isNotEmpty())
    new Chart(getCtx('chart-top-products'), {
        type: 'bar',
        data: {
            labels: {{ Js::from($topProducts->keys()->values()) }},
            datasets: [{
                label: 'Qty',
                data: {{ Js::from($topProducts->values()) }},
                backgroundColor: '#22c55e',
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } },
        },
    });
    @endif

    // ── 6. Review Ratings Bar ────────────────────────────────────────────────
    new Chart(getCtx('chart-review-ratings'), {
        type: 'bar',
        data: {
            labels: ['⭐ 1', '⭐ 2', '⭐ 3', '⭐ 4', '⭐ 5'],
            datasets: [{
                label: 'Reviews',
                data: {{ Js::from($reviewRatings->values()) }},
                backgroundColor: ['#f43f5e','#f97316','#eab308','#22c55e','#6366f1'],
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
    });

    // ── 7. Preferred Sellers Doughnut ───────────────────────────────────────
    @if($preferredSellers->isNotEmpty())
    new Chart(getCtx('chart-preferred-sellers'), {
        type: 'doughnut',
        data: {
            labels: {{ Js::from($preferredSellers->keys()->values()) }},
            datasets: [{
                data: {{ Js::from($preferredSellers->values()) }},
                backgroundColor: palette,
            }],
        },
        options: { ...baseOpts, plugins: { legend: { display: true, position: 'bottom' } } },
    });
    @endif

    // ── 8. Payment Methods Pie ───────────────────────────────────────────────
    @if($paymentMethods->isNotEmpty())
    new Chart(getCtx('chart-payment-methods'), {
        type: 'pie',
        data: {
            labels: {{ Js::from($paymentMethods->keys()->values()) }},
            datasets: [{
                data: {{ Js::from($paymentMethods->values()) }},
                backgroundColor: palette,
            }],
        },
        options: { ...baseOpts, plugins: { legend: { display: true, position: 'bottom' } } },
    });
    @endif

    // ── 9. Most Expensive Items ──────────────────────────────────────────────
    @if($mostExpensiveItems->isNotEmpty())
    new Chart(getCtx('chart-most-expensive'), {
        type: 'bar',
        data: {
            labels: {{ Js::from($mostExpensiveItems->pluck('name')->values()) }},
            datasets: [{
                label: '₱ Price',
                data: {{ Js::from($mostExpensiveItems->pluck('price')->values()) }},
                backgroundColor: '#ec4899',
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } },
        },
    });
    @endif

    // ── 10. Least Expensive Items ────────────────────────────────────────────
    @if($leastExpensiveItems->isNotEmpty())
    new Chart(getCtx('chart-least-expensive'), {
        type: 'bar',
        data: {
            labels: {{ Js::from($leastExpensiveItems->pluck('name')->values()) }},
            datasets: [{
                label: '₱ Price',
                data: {{ Js::from($leastExpensiveItems->pluck('price')->values()) }},
                backgroundColor: '#06b6d4',
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } },
        },
    });
    @endif
})();
</script>
@endpush
@endsection

