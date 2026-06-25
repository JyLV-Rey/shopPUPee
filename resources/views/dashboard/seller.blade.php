@use('Illuminate\Support\Js')
@extends('common.index')

@section('title', $seller->seller_name . ' — Seller Dashboard')

@push('head')
@vite('resources/js/charts.js')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-2">Store Info</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex gap-2"><span class="font-semibold w-36 shrink-0 text-base-content/70">Store Name</span><span>{{ $seller->seller_name }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-36 shrink-0 text-base-content/70">Owner</span><span>{{ $buyer?->first_name }} {{ $buyer?->last_name }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-36 shrink-0 text-base-content/70">Email</span><span>{{ $buyer?->email ?? '—' }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-36 shrink-0 text-base-content/70">Phone</span><span>{{ $buyer?->phone ?? '—' }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-36 shrink-0 text-base-content/70">Seller Since</span><span>{{ $seller->created_at ? \Carbon\Carbon::parse($seller->created_at)->format('M d, Y') : '—' }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-36 shrink-0 text-base-content/70">Seller ID</span><span class="badge badge-outline badge-sm">#{{ $seller->seller_id }}</span></div>
                    @if($seller->is_deleted)<div class="mt-3"><span class="badge badge-error">Store Deactivated</span></div>@endif
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-secondary mb-2">Sales Overview</h2>
                <div class="stats stats-vertical shadow-none border border-base-200 rounded-xl text-sm w-full">
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Total Revenue</div><div class="stat-value text-success" style="font-size:1.3rem">₱{{ number_format($totalRevenue, 2) }}</div></div>
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Products Listed</div><div class="stat-value" style="font-size:1.3rem">{{ $totalProductsListed }}</div></div>
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Total Items Sold</div><div class="stat-value text-primary" style="font-size:1.3rem">{{ $totalItemsSold }}</div></div>
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Cancelled / Refunded</div><div class="stat-value text-error" style="font-size:1.3rem">{{ $totalCancelled }} / {{ $totalRefunded }}</div></div>
                    @if($averageRating !== null)
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Avg. Store Rating</div><div class="stat-value text-warning" style="font-size:1.3rem">{{ $averageRating }} ⭐</div></div>
                    @endif
                </div>
                <div class="card-actions justify-start mt-4 gap-2 flex-wrap">
                    <a href="{{ route('product.create') }}" class="btn btn-secondary btn-sm">Create Product</a>
                    <a href="{{ route('edit.seller') }}?sellerId={{ $seller->seller_id }}" class="btn btn-outline btn-sm">Edit Store</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-bold mb-4">Analytics & Charts</h2>
    <div class="dashboard-grid mb-8">

        @if($monthlyEarnings->isNotEmpty())
        <x-chart id="chart-monthly-earnings" type="line"
            :labels="$monthlyEarnings->keys()->toArray()" title="📈 Revenue Over Time"
            :datasets="[['label' => 'Revenue (₱)', 'data' => $monthlyEarnings->values()->toArray(), 'borderColor' => '#8b5cf6', 'backgroundColor' => 'rgba(139,92,246,0.15)', 'fill' => true, 'tension' => 0.4, 'pointRadius' => 4]]"
            :options="['scales' => ['y' => ['beginAtZero' => true]]]" />
        @endif

        @if($topSellingProducts->isNotEmpty())
        <x-chart id="chart-top-products" type="bar"
            :labels="$topSellingProducts->keys()->toArray()" title="🏆 Top Selling Products"
            :datasets="[['label' => 'Qty Sold', 'data' => $topSellingProducts->values()->toArray(), 'backgroundColor' => '#22c55e']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

        @if($earningsByCategory->isNotEmpty())
        <x-chart id="chart-earnings-category" type="doughnut"
            :labels="$earningsByCategory->keys()->toArray()" title="🗂️ Earnings by Category"
            :datasets="[['data' => $earningsByCategory->values()->toArray(), 'backgroundColor' => ['#6366f1','#8b5cf6','#ec4899','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e']]]"
            :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']]]" />
        @endif

        @if($orderStatusDist->isNotEmpty())
        <x-chart id="chart-order-status" type="pie"
            :labels="$orderStatusDist->keys()->toArray()" title="📦 Order Status Distribution"
            :datasets="[['data' => $orderStatusDist->values()->toArray(), 'backgroundColor' => ['#22c55e','#f97316','#f43f5e','#6366f1','#eab308','#06b6d4']]]"
            :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']]]" />
        @endif

        @if($purchaseFrequency->isNotEmpty())
        <x-chart id="chart-purchase-freq" type="bar"
            :labels="$purchaseFrequency->keys()->toArray()" title="📅 Monthly Sales Volume"
            :datasets="[['label' => 'Orders', 'data' => $purchaseFrequency->values()->toArray(), 'backgroundColor' => '#6366f1']]"
            :options="['scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]]]]" />
        @endif

        @if($topReviewedProducts->isNotEmpty())
        <x-chart id="chart-top-reviewed" type="bar"
            :labels="$topReviewedProducts->keys()->toArray()" title="⭐ Top Reviewed Products"
            :datasets="[['label' => 'Reviews', 'data' => $topReviewedProducts->values()->toArray(), 'backgroundColor' => '#eab308']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]]]]" />
        @endif

        @if($topBuyers->isNotEmpty())
        <x-chart id="chart-top-buyers" type="bar"
            :labels="$topBuyers->keys()->toArray()" title="👥 Top Buyers by Spend"
            :datasets="[['label' => '₱ Spent', 'data' => $topBuyers->values()->toArray(), 'backgroundColor' => '#ec4899']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

        @if($topCategories->isNotEmpty())
        <x-chart id="chart-top-categories" type="bar"
            :labels="$topCategories->keys()->toArray()" title="📊 Top Categories by Units Sold"
            :datasets="[['label' => 'Units Sold', 'data' => $topCategories->values()->toArray(), 'backgroundColor' => ['#6366f1','#8b5cf6','#ec4899','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e']]]"
            :options="['scales' => ['y' => ['beginAtZero' => true]]]" />
        @endif

        @if($mostExpensiveProducts->isNotEmpty())
        <x-chart id="chart-most-expensive" type="bar"
            :labels="$mostExpensiveProducts->keys()->toArray()" title="💎 Most Expensive Products"
            :datasets="[['label' => '₱ Price', 'data' => $mostExpensiveProducts->values()->toArray(), 'backgroundColor' => '#f97316']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

        @if($leastExpensiveProducts->isNotEmpty())
        <x-chart id="chart-least-expensive" type="bar"
            :labels="$leastExpensiveProducts->keys()->toArray()" title="🪙 Least Expensive Products"
            :datasets="[['label' => '₱ Price', 'data' => $leastExpensiveProducts->values()->toArray(), 'backgroundColor' => '#06b6d4']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

    </div>

    @if($lowStockProducts->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <span class="badge badge-warning badge-lg">⚠</span> Low Stock Alerts
            <span class="badge badge-warning">{{ $lowStockProducts->count() }}</span>
        </h2>
        <div class="overflow-x-auto rounded-xl border border-base-200">
            <table class="table table-zebra w-full">
                <thead class="bg-base-200">
                    <tr><th>Product</th><th>Category</th><th>Price</th><th class="text-center">Stock</th><th class="text-center">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                    <tr>
                        <td class="font-medium">{{ $product->name }}</td>
                        <td><span class="badge badge-outline badge-sm">{{ $product->category }}</span></td>
                        <td>₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-center"><span class="badge {{ $product->quantity == 0 ? 'badge-error' : 'badge-warning' }} font-bold">{{ $product->quantity }}</span></td>
                        <td class="text-center"><a href="{{ route('product.edit', $product->product_id) }}" class="btn btn-xs btn-outline btn-primary">Restock</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <h2 class="text-xl font-bold mb-4">All Products</h2>
    <div class="overflow-x-auto rounded-xl border border-base-200">
        <table class="table table-zebra w-full">
            <thead class="bg-base-200">
                <tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th class="text-center">Stock</th><th class="text-center">Status</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($seller->products->sortByDesc('created_at') as $product)
                <tr>
                    <td class="text-base-content/50 text-xs">#{{ $product->product_id }}</td>
                    <td class="font-medium max-w-xs truncate">{{ $product->name }}</td>
                    <td><span class="badge badge-outline badge-sm">{{ $product->category }}</span></td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                    <td class="text-center"><span class="badge {{ $product->quantity < 5 ? 'badge-warning' : 'badge-success' }} badge-sm">{{ $product->quantity }}</span></td>
                    <td class="text-center">@if($product->is_deleted)<span class="badge badge-error badge-sm">Deleted</span>@else<span class="badge badge-success badge-sm">Active</span>@endif</td>
                    <td class="text-center">
                        <a href="{{ route('product.edit', $product->product_id) }}" class="btn btn-xs btn-outline">Edit</a>
                        <a href="{{ route('product.view', $product->product_id) }}" class="btn btn-xs btn-ghost">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-base-content/50 py-8">No products listed yet. <a href="{{ route('product.create') }}" class="link link-primary">Create your first product →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
