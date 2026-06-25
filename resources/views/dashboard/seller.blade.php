@extends('common.index')

@section('title', $seller->seller_name . ' — Seller Dashboard')

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
                <div
                    class="bg-gradient-to-br from-secondary to-secondary/70 text-secondary-content rounded-2xl w-16 h-16 shadow-lg/10 flex items-center justify-center">
                    <span class="text-2xl font-bold">{{ strtoupper(substr($seller->seller_name, 0, 1)) }}</span>
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-bold tracking-tight">{{ $seller->seller_name }}</h1>
                <p class="text-lg text-base-content/50">Seller Dashboard</p>
            </div>
        </div>

        {{-- Stat cards row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-lg/10 p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Total Revenue</div>
                <div class="stat-value text-lg text-success mt-1">₱{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-lg/10 p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Products Listed
                </div>
                <div class="stat-value text-lg text-primary mt-1">{{ $totalProductsListed }}</div>
            </div>
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-lg/10 p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Items Sold</div>
                <div class="stat-value text-lg text-info mt-1">{{ $totalItemsSold }}</div>
            </div>
            <div class="stat bg-base-100 border border-base-200 rounded-xl shadow-lg/10 p-4">
                <div class="stat-title text-lg font-bold text-base-content/50 uppercase tracking-wider">Cancelled /
                    Refunded</div>
                <div class="stat-value text-lg text-error mt-1">{{ $totalCancelled }} / {{ $totalRefunded }}</div>
            </div>
        </div>

        {{-- Store info + Quick actions row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 card bg-base-100 border border-base-200 rounded-xl shadow-lg/10">
                <div class="card-body p-5">
                    <h2 class="card-title text-sm font-semibold text-base-content/80 tracking-wide uppercase mb-1">Store
                        Information</h2>
                    <div class="divider mt-1 mb-3"></div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-base-content/50 text-xs">Store Name</dt>
                            <dd class="font-medium">{{ $seller->seller_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Owner</dt>
                            <dd class="font-medium">{{ $buyer?->first_name }} {{ $buyer?->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Email</dt>
                            <dd class="font-medium">{{ $buyer?->email ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Phone</dt>
                            <dd class="font-medium">{{ $buyer?->phone ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Seller Since</dt>
                            <dd class="font-medium">
                                {{ $seller->created_at ? \Carbon\Carbon::parse($seller->created_at)->format('M d, Y') : '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-base-content/50 text-xs">Avg. Rating</dt>
                            <dd class="font-medium">{{ $averageRating ?? '—' }} / 5</dd>
                        </div>
                    </dl>
                    @if ($seller->is_deleted)
                        <div class="mt-3"><span class="badge badge-error">Store Deactivated</span></div>
                    @endif
                </div>
            </div>

            @auth
                @if (Auth::id() === $seller->buyer_id)
            <div class="card bg-base-100 border border-base-200 rounded-xl shadow-lg/10">
                <div class="card-body p-5">
                    <h2 class="card-title text-sm font-semibold text-base-content/80 tracking-wide uppercase mb-1">Quick Actions</h2>
                    <div class="divider mt-1 mb-3"></div>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('product.create') }}" class="btn btn-secondary btn-sm justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Create Product
                        </a>
                        <a href="{{ route('edit.seller') }}" class="btn btn-outline btn-sm justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Store
                        </a>
                        <a href="{{ route('dashboard.buyer', $buyer) }}" class="btn btn-ghost btn-sm justify-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Switch to Buyer View
                        </a>
                    </div>
                </div>
            </div>
                @endif
            @endauth
        </div>

        {{-- Charts --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold tracking-tight">Analytics</h2>
        </div>
        <div class="dashboard-grid mb-8">
            @if ($monthlyEarnings->isNotEmpty())
                <x-chart id="chart-monthly-earnings" type="line" :labels="$monthlyEarnings->keys()->toArray()" title="Revenue Over Time"
                    :datasets="[
                        [
                            'label' => 'Revenue (₱)',
                            'data' => $monthlyEarnings->values()->toArray(),
                            'borderColor' => '#8b5cf6',
                            'backgroundColor' => 'rgba(139,92,246,0.08)',
                            'fill' => true,
                            'tension' => 0.4,
                            'pointRadius' => 3,
                            'pointBackgroundColor' => '#8b5cf6',
                            'borderWidth' => 2,
                        ],
                    ]" :options="[
                        'scales' => [
                            'y' => ['beginAtZero' => true, 'grid' => ['color' => 'rgba(0,0,0,0.04)']],
                            'x' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            @if ($topSellingProducts->isNotEmpty())
                <x-chart id="chart-top-products" type="bar" :labels="$topSellingProducts->keys()->toArray()" title="Top Selling Products"
                    :datasets="[
                        [
                            'label' => 'Qty Sold',
                            'data' => $topSellingProducts->values()->toArray(),
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

            @if ($earningsByCategory->isNotEmpty())
                <x-chart id="chart-earnings-category" type="doughnut" :labels="$earningsByCategory->keys()->toArray()" title="Earnings by Category"
                    :datasets="[
                        [
                            'data' => $earningsByCategory->values()->toArray(),
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

            @if ($orderStatusDist->isNotEmpty())
                <x-chart id="chart-order-status" type="doughnut" :labels="$orderStatusDist->keys()->toArray()" title="Order Status Distribution"
                    :datasets="[
                        [
                            'data' => $orderStatusDist->values()->toArray(),
                            'backgroundColor' => ['#22c55e', '#f97316', '#f43f5e', '#6366f1', '#eab308', '#06b6d4'],
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

            @if ($purchaseFrequency->isNotEmpty())
                <x-chart id="chart-purchase-freq" type="bar" :labels="$purchaseFrequency->keys()->toArray()" title="Monthly Sales Volume"
                    :datasets="[
                        [
                            'label' => 'Orders',
                            'data' => $purchaseFrequency->values()->toArray(),
                            'backgroundColor' => '#6366f1',
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

            @if ($topReviewedProducts->isNotEmpty())
                <x-chart id="chart-top-reviewed" type="bar" :labels="$topReviewedProducts->keys()->toArray()" title="Top Reviewed Products"
                    :datasets="[
                        [
                            'label' => 'Reviews',
                            'data' => $topReviewedProducts->values()->toArray(),
                            'backgroundColor' => '#eab308',
                            'borderRadius' => 4,
                        ],
                    ]" :options="[
                        'indexAxis' => 'y',
                        'scales' => [
                            'x' => [
                                'beginAtZero' => true,
                                'ticks' => ['stepSize' => 1],
                                'grid' => ['color' => 'rgba(0,0,0,0.04)'],
                            ],
                            'y' => ['grid' => ['display' => false]],
                        ],
                    ]" />
            @endif

            @if ($topBuyers->isNotEmpty())
                <x-chart id="chart-top-buyers" type="bar" :labels="$topBuyers->keys()->toArray()" title="Top Buyers by Spend"
                    :datasets="[
                        [
                            'label' => '₱ Spent',
                            'data' => $topBuyers->values()->toArray(),
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

            @if ($topCategories->isNotEmpty())
                <x-chart id="chart-top-categories" type="bar" :labels="$topCategories->keys()->toArray()" title="Top Categories by Units Sold"
                    :datasets="[
                        [
                            'label' => 'Units Sold',
                            'data' => $topCategories->values()->toArray(),
                            'backgroundColor' => [
                                '#6366f1',
                                '#8b5cf6',
                                '#ec4899',
                                '#f97316',
                                '#eab308',
                                '#22c55e',
                                '#06b6d4',
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

            @if ($mostExpensiveProducts->isNotEmpty())
                <x-chart id="chart-most-expensive" type="bar" :labels="$mostExpensiveProducts->keys()->toArray()" title="Most Expensive Products"
                    :datasets="[
                        [
                            'label' => '₱ Price',
                            'data' => $mostExpensiveProducts->values()->toArray(),
                            'backgroundColor' => '#f97316',
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

            @if ($leastExpensiveProducts->isNotEmpty())
                <x-chart id="chart-least-expensive" type="bar" :labels="$leastExpensiveProducts->keys()->toArray()" title="Least Expensive Products"
                    :datasets="[
                        [
                            'label' => '₱ Price',
                            'data' => $leastExpensiveProducts->values()->toArray(),
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

        {{-- Low Stock Alerts --}}
        @if ($lowStockProducts->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold tracking-tight">Low Stock Alerts</h2>
                    <span class="badge badge-warning">{{ $lowStockProducts->count() }} items</span>
                </div>
                <div class="overflow-x-auto rounded-xl border border-base-200 shadow-lg/10">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-base-content/50">
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockProducts as $product)
                                <tr>
                                    <td class="font-medium">{{ $product->name }}</td>
                                    <td><span class="badge badge-outline badge-sm">{{ $product->category }}</span></td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td class="text-center"><span
                                            class="badge {{ $product->quantity == 0 ? 'badge-error' : 'badge-warning' }} font-bold">{{ $product->quantity }}</span>
                                    </td>
                                    <td class="text-center"><a href="{{ route('product.edit', $product->product_id) }}"
                                            class="btn btn-xs btn-outline btn-primary">Restock</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- All Products table --}}
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold tracking-tight">All Products</h2>
        </div>
        <div class="overflow-x-auto rounded-xl border border-base-200 shadow-lg/10">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-base-content/50">
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
                            <td><span class="badge badge-outline badge-sm">{{ $product->category }}</span></td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td class="text-center"><span
                                    class="badge {{ $product->quantity < 5 ? 'badge-warning' : 'badge-success' }} badge-sm">{{ $product->quantity }}</span>
                            </td>
                            <td class="text-center">
                                @if ($product->is_deleted)
                                <span class="badge badge-error badge-sm">Deleted</span>@else<span
                                        class="badge badge-success badge-sm">Active</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('product.edit', $product->product_id) }}"
                                    class="btn btn-xs btn-outline">Edit</a>
                                <a href="{{ route('product.view', $product->product_id) }}"
                                    class="btn btn-xs btn-ghost">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-base-content/50 py-8">No products listed yet. <a
                                    href="{{ route('product.create') }}" class="link link-primary">Create your first
                                    product →</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
