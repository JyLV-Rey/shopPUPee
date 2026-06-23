@extends('common.index')

@section('title', $seller->seller_name . ' — Seller Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
    .chart-card { display: flex; flex-direction: column; }
    .chart-card canvas { max-height: 220px; }
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
            <div class="bg-secondary text-secondary-content rounded-full w-14">
                <span class="text-xl font-bold">{{ strtoupper(substr($seller->seller_name, 0, 1)) }}</span>
            </div>
        </div>
        <div>
            <h1 class="text-3xl font-bold">{{ $seller->seller_name }}</h1>
            <p class="text-base-content/60 text-sm">Seller Dashboard</p>
        </div>
    </div>

    {{-- ── Header two-column ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Left: Store / owner info --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Store Info
                </h2>
                <div class="space-y-2 text-sm">
                    <div class="flex gap-2">
                        <span class="font-semibold w-36 shrink-0 text-base-content/70">Store Name</span>
                        <span>{{ $seller->seller_name }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-36 shrink-0 text-base-content/70">Owner</span>
                        <span>{{ $buyer?->first_name }} {{ $buyer?->last_name }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-36 shrink-0 text-base-content/70">Email</span>
                        <span>{{ $buyer?->email ?? '—' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-36 shrink-0 text-base-content/70">Phone</span>
                        <span>{{ $buyer?->phone ?? '—' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-36 shrink-0 text-base-content/70">Seller Since</span>
                        @php
                            $appliedAt = $seller->application?->application_date
                                ?? $seller->created_at
                                ?? null;
                        @endphp
                        <span>{{ $appliedAt ? \Carbon\Carbon::parse($appliedAt)->format('M d, Y') : '—' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-semibold w-36 shrink-0 text-base-content/70">Seller ID</span>
                        <span class="badge badge-outline badge-sm">#{{ $seller->seller_id }}</span>
                    </div>
                    @if($seller->is_deleted)
                        <div class="mt-3">
                            <span class="badge badge-error">Store Deactivated</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Revenue stats + action buttons --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sales Overview
                </h2>

                <div class="stats stats-vertical shadow-none border border-base-200 rounded-xl text-sm w-full">
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Total Revenue</div>
                        <div class="stat-value text-success" style="font-size:1.3rem">₱{{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Products Listed</div>
                        <div class="stat-value" style="font-size:1.3rem">{{ $totalProductsListed }}</div>
                    </div>
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Total Items Sold</div>
                        <div class="stat-value text-primary" style="font-size:1.3rem">{{ $totalItemsSold }}</div>
                    </div>
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Cancelled / Refunded</div>
                        <div class="stat-value text-error" style="font-size:1.3rem">{{ $totalCancelled }} / {{ $totalRefunded }}</div>
                    </div>
                    @if($averageRating !== null)
                    <div class="stat py-2 px-3">
                        <div class="stat-title text-xs">Avg. Store Rating</div>
                        <div class="stat-value text-warning" style="font-size:1.3rem">
                            {{ $averageRating }} ⭐
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-actions justify-start mt-4 gap-2 flex-wrap">
                    <a href="{{ route('product.create') }}" class="btn btn-secondary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Product
                    </a>
                    <a href="{{ route('edit.seller') }}?sellerId={{ $seller->seller_id }}" class="btn btn-outline btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Store
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts section ────────────────────────────────────────────────── --}}
    <h2 class="text-xl font-bold mb-4">Analytics & Charts</h2>

    <div class="dashboard-grid mb-8">

        {{-- 1. Revenue Over Time Line --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">📈 Revenue Over Time</h3>
                @if($monthlyEarnings->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-monthly-earnings"></canvas>
                @endif
            </div>
        </div>

        {{-- 2. Top Selling Products Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🏆 Top Selling Products</h3>
                @if($topSellingProducts->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-top-products"></canvas>
                @endif
            </div>
        </div>

        {{-- 3. Category Breakdown Doughnut --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🗂️ Earnings by Category</h3>
                @if($earningsByCategory->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-earnings-category"></canvas>
                @endif
            </div>
        </div>

        {{-- 4. Order Status Distribution --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">📦 Order Status Distribution</h3>
                @if($orderStatusDist->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-order-status"></canvas>
                @endif
            </div>
        </div>

        {{-- 5. Monthly Purchase Frequency Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">📅 Monthly Sales Volume</h3>
                @if($purchaseFrequency->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-purchase-freq"></canvas>
                @endif
            </div>
        </div>

        {{-- 6. Top Reviewed Products --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">⭐ Top Reviewed Products</h3>
                @if($topReviewedProducts->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-top-reviewed"></canvas>
                @endif
            </div>
        </div>

        {{-- 7. Top Buyers Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">👥 Top Buyers by Spend</h3>
                @if($topBuyers->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-top-buyers"></canvas>
                @endif
            </div>
        </div>

        {{-- 8. Top Categories Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">📊 Top Categories by Units Sold</h3>
                @if($topCategories->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-top-categories"></canvas>
                @endif
            </div>
        </div>

        {{-- 9. Most Expensive Products Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">💎 Most Expensive Listed Products</h3>
                @if($mostExpensiveProducts->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-most-expensive"></canvas>
                @endif
            </div>
        </div>

        {{-- 10. Least Expensive Products Bar --}}
        <div class="card bg-base-100 shadow-md border border-base-200 chart-card">
            <div class="card-body">
                <h3 class="card-title text-sm">🪙 Least Expensive Listed Products</h3>
                @if($leastExpensiveProducts->isEmpty())
                    <p class="text-base-content/50 text-sm italic">No data yet.</p>
                @else
                    <canvas id="chart-least-expensive"></canvas>
                @endif
            </div>
        </div>

    </div>{{-- /.dashboard-grid --}}

    {{-- ── Low Stock Alerts ─────────────────────────────────────────────── --}}
    @if($lowStockProducts->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span class="badge badge-warning badge-lg">⚠</span>
            Low Stock Alerts
            <span class="badge badge-warning">{{ $lowStockProducts->count() }}</span>
        </h2>
        <div class="overflow-x-auto rounded-xl border border-base-200">
            <table class="table table-zebra w-full">
                <thead class="bg-base-200">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                    <tr>
                        <td class="font-medium">{{ $product->name }}</td>
                        <td>
                            <span class="badge badge-outline badge-sm">{{ $product->category }}</span>
                        </td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $product->quantity == 0 ? 'badge-error' : 'badge-warning' }} font-bold">
                                {{ $product->quantity }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('product.edit', $product->product_id) }}"
                               class="btn btn-xs btn-outline btn-primary">Restock</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Products table ────────────────────────────────────────────────── --}}
    <h2 class="text-xl font-bold mb-4">All Products</h2>
    <div class="overflow-x-auto rounded-xl border border-base-200">
        <table class="table table-zebra w-full">
            <thead class="bg-base-200">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($seller->products->sortByDesc('created_at') as $product)
                <tr>
                    <td class="text-base-content/50 text-xs">#{{ $product->product_id }}</td>
                    <td class="font-medium max-w-xs truncate">{{ $product->name }}</td>
                    <td>
                        <span class="badge badge-outline badge-sm">{{ $product->category }}</span>
                    </td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $product->quantity < 5 ? 'badge-warning' : 'badge-success' }} badge-sm">
                            {{ $product->quantity }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($product->is_deleted)
                            <span class="badge badge-error badge-sm">Deleted</span>
                        @else
                            <span class="badge badge-success badge-sm">Active</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="flex gap-1 justify-center">
                            <a href="{{ route('product.edit', $product->product_id) }}"
                               class="btn btn-xs btn-outline">Edit</a>
                            <a href="{{ route('product.view', $product->product_id) }}"
                               class="btn btn-xs btn-ghost">View</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-base-content/50 py-8">
                        No products listed yet.
                        <a href="{{ route('product.create') }}" class="link link-primary">Create your first product →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>{{-- /.container --}}

{{-- ── Chart.js initialisation ─────────────────────────────────────────── --}}
@push('scripts')
<script>
(function () {
    const palette = [
        '#6366f1','#8b5cf6','#ec4899','#f97316','#eab308',
        '#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e',
    ];

    function getCtx(id) {
        const el = document.getElementById(id);
        return el ? el.getContext('2d') : null;
    }

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
    };

    // ── 1. Monthly Earnings Line ──────────────────────────────────────────
    @if($monthlyEarnings->isNotEmpty())
    new Chart(getCtx('chart-monthly-earnings'), {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyEarnings->keys()->values()) !!},
            datasets: [{
                label: 'Revenue (₱)',
                data: {!! json_encode($monthlyEarnings->values()) !!},
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139,92,246,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true } } },
    });
    @endif

    // ── 2. Top Selling Products ────────────────────────────────────────────
    @if($topSellingProducts->isNotEmpty())
    new Chart(getCtx('chart-top-products'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSellingProducts->keys()->values()) !!},
            datasets: [{
                label: 'Qty Sold',
                data: {!! json_encode($topSellingProducts->values()) !!},
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

    // ── 3. Earnings by Category Doughnut ──────────────────────────────────
    @if($earningsByCategory->isNotEmpty())
    new Chart(getCtx('chart-earnings-category'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($earningsByCategory->keys()->values()) !!},
            datasets: [{
                data: {!! json_encode($earningsByCategory->values()) !!},
                backgroundColor: palette,
            }],
        },
        options: { ...baseOpts, plugins: { legend: { display: true, position: 'bottom' } } },
    });
    @endif

    // ── 4. Order Status Distribution ──────────────────────────────────────
    @if($orderStatusDist->isNotEmpty())
    new Chart(getCtx('chart-order-status'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($orderStatusDist->keys()->values()) !!},
            datasets: [{
                data: {!! json_encode($orderStatusDist->values()) !!},
                backgroundColor: ['#22c55e','#f97316','#f43f5e','#6366f1','#eab308','#06b6d4'],
            }],
        },
        options: { ...baseOpts, plugins: { legend: { display: true, position: 'bottom' } } },
    });
    @endif

    // ── 5. Monthly Purchase Frequency Bar ─────────────────────────────────
    @if($purchaseFrequency->isNotEmpty())
    new Chart(getCtx('chart-purchase-freq'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($purchaseFrequency->keys()->values()) !!},
            datasets: [{
                label: 'Orders',
                data: {!! json_encode($purchaseFrequency->values()) !!},
                backgroundColor: '#6366f1',
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
    });
    @endif

    // ── 6. Top Reviewed Products ──────────────────────────────────────────
    @if($topReviewedProducts->isNotEmpty())
    new Chart(getCtx('chart-top-reviewed'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topReviewedProducts->keys()->values()) !!},
            datasets: [{
                label: 'Reviews',
                data: {!! json_encode($topReviewedProducts->values()) !!},
                backgroundColor: '#eab308',
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } },
        },
    });
    @endif

    // ── 7. Top Buyers Bar ─────────────────────────────────────────────────
    @if($topBuyers->isNotEmpty())
    new Chart(getCtx('chart-top-buyers'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topBuyers->keys()->values()) !!},
            datasets: [{
                label: '₱ Spent',
                data: {!! json_encode($topBuyers->values()) !!},
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

    // ── 8. Top Categories by Units Sold ───────────────────────────────────
    @if($topCategories->isNotEmpty())
    new Chart(getCtx('chart-top-categories'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topCategories->keys()->values()) !!},
            datasets: [{
                label: 'Units Sold',
                data: {!! json_encode($topCategories->values()) !!},
                backgroundColor: palette,
            }],
        },
        options: { ...baseOpts, scales: { y: { beginAtZero: true } } },
    });
    @endif

    // ── 9. Most Expensive Products ────────────────────────────────────────
    @if($mostExpensiveProducts->isNotEmpty())
    new Chart(getCtx('chart-most-expensive'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($mostExpensiveProducts->keys()->values()) !!},
            datasets: [{
                label: '₱ Price',
                data: {!! json_encode($mostExpensiveProducts->values()) !!},
                backgroundColor: '#f97316',
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } },
        },
    });
    @endif

    // ── 10. Least Expensive Products ──────────────────────────────────────
    @if($leastExpensiveProducts->isNotEmpty())
    new Chart(getCtx('chart-least-expensive'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($leastExpensiveProducts->keys()->values()) !!},
            datasets: [{
                label: '₱ Price',
                data: {!! json_encode($leastExpensiveProducts->values()) !!},
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
