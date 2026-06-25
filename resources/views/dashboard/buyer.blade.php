@use('Illuminate\Support\Js')
@extends('common.index')

@section('title', $buyer->first_name . ' ' . $buyer->last_name . ' — Buyer Dashboard')

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
            <div class="bg-primary text-primary-content rounded-full w-14">
                <span class="text-xl font-bold">{{ strtoupper(substr($buyer->first_name,0,1)) }}</span>
            </div>
        </div>
        <div>
            <h1 class="text-3xl font-bold">{{ $buyer->first_name }} {{ $buyer->last_name }}</h1>
            <p class="text-base-content/60 text-sm">Buyer Dashboard</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-primary mb-2">Personal Info</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex gap-2"><span class="font-semibold w-32 shrink-0 text-base-content/70">Full Name</span><span>{{ $buyer->first_name }} {{ $buyer->last_name }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-32 shrink-0 text-base-content/70">Email</span><span>{{ $buyer->email }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-32 shrink-0 text-base-content/70">Phone</span><span>{{ $buyer->phone ?? '—' }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-32 shrink-0 text-base-content/70">Member Since</span><span>{{ $buyer->created_at ? \Carbon\Carbon::parse($buyer->created_at)->format('M d, Y') : '—' }}</span></div>
                    <div class="flex gap-2"><span class="font-semibold w-32 shrink-0 text-base-content/70">Buyer ID</span><span class="badge badge-outline badge-sm">#{{ $buyer->buyer_id }}</span></div>
                    @if($buyer->is_deleted)<div class="mt-3"><span class="badge badge-error">Account Deactivated</span></div>@endif
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-primary mb-2">Activity Overview</h2>
                <div class="stats stats-vertical shadow-none border border-base-200 rounded-xl text-sm w-full">
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Total Spent</div><div class="stat-value text-success" style="font-size:1.3rem">₱{{ number_format($totalSpent, 2) }}</div></div>
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Orders Placed</div><div class="stat-value" style="font-size:1.3rem">{{ $totalOrdersPlaced }}</div></div>
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Cancelled Orders</div><div class="stat-value text-error" style="font-size:1.3rem">{{ $totalCancelledOrders }}</div></div>
                    <div class="stat py-2 px-3"><div class="stat-title text-xs">Refunds</div><div class="stat-value text-warning" style="font-size:1.3rem">{{ $totalRefunds }}</div></div>
                </div>
                <div class="card-actions justify-start mt-4 gap-2 flex-wrap">
                    <a href="{{ route('orders') }}?buyerId={{ $buyer->buyer_id }}" class="btn btn-primary btn-sm">View Orders</a>
                    <a href="{{ route('edit.buyer') }}?buyerId={{ $buyer->buyer_id }}" class="btn btn-outline btn-sm">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-bold mb-4">Analytics & Charts</h2>
    <div class="dashboard-grid">

        @if($topCategories->isNotEmpty())
        <x-chart id="chart-top-categories" type="doughnut"
            :labels="$topCategories->keys()->toArray()" title="🛍️ Top Categories"
            :datasets="[['data' => $topCategories->values()->toArray(), 'backgroundColor' => ['#6366f1','#8b5cf6','#ec4899','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e']]]"
            :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']]]" />
        @endif

        @if($spendingOverTime->isNotEmpty())
        <x-chart id="chart-spending-time" type="line"
            :labels="$spendingOverTime->keys()->toArray()" title="📈 Spending Over Time"
            :datasets="[['label' => 'Spent (₱)', 'data' => $spendingOverTime->values()->toArray(), 'borderColor' => '#6366f1', 'backgroundColor' => 'rgba(99,102,241,0.15)', 'fill' => true, 'tension' => 0.4, 'pointRadius' => 4]]"
            :options="['scales' => ['y' => ['beginAtZero' => true]]]" />
        @endif

        @if($spendByCategory->isNotEmpty())
        <x-chart id="chart-spend-category" type="bar"
            :labels="$spendByCategory->keys()->toArray()" title="💰 Spend by Category"
            :datasets="[['label' => '₱ Spent', 'data' => $spendByCategory->values()->toArray(), 'backgroundColor' => ['#6366f1','#8b5cf6','#ec4899','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e']]]"
            :options="['scales' => ['y' => ['beginAtZero' => true]]]" />
        @endif

        @if($purchaseFrequency->isNotEmpty())
        <x-chart id="chart-purchase-freq" type="bar"
            :labels="$purchaseFrequency->keys()->toArray()" title="📅 Purchase Frequency"
            :datasets="[['label' => 'Orders', 'data' => $purchaseFrequency->values()->toArray(), 'backgroundColor' => '#8b5cf6']]"
            :options="['scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]]]]" />
        @endif

        @if($topProducts->isNotEmpty())
        <x-chart id="chart-top-products" type="bar"
            :labels="$topProducts->keys()->toArray()" title="🏆 Top Products by Quantity"
            :datasets="[['label' => 'Qty', 'data' => $topProducts->values()->toArray(), 'backgroundColor' => '#22c55e']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

        <x-chart id="chart-review-ratings" type="bar"
            :labels="['⭐ 1', '⭐ 2', '⭐ 3', '⭐ 4', '⭐ 5']" title="⭐ Review Ratings"
            :datasets="[['label' => 'Reviews', 'data' => $reviewRatings->values()->toArray(), 'backgroundColor' => ['#f43f5e','#f97316','#eab308','#22c55e','#6366f1']]]"
            :options="['scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]]]]" />

        @if($preferredSellers->isNotEmpty())
        <x-chart id="chart-preferred-sellers" type="doughnut"
            :labels="$preferredSellers->keys()->toArray()" title="🏪 Preferred Sellers"
            :datasets="[['data' => $preferredSellers->values()->toArray(), 'backgroundColor' => ['#6366f1','#8b5cf6','#ec4899','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e']]]"
            :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']]]" />
        @endif

        @if($paymentMethods->isNotEmpty())
        <x-chart id="chart-payment-methods" type="pie"
            :labels="$paymentMethods->keys()->toArray()" title="💳 Payment Methods"
            :datasets="[['data' => $paymentMethods->values()->toArray(), 'backgroundColor' => ['#6366f1','#8b5cf6','#ec4899','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#14b8a6','#f43f5e']]]"
            :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']]]" />
        @endif

        @if($mostExpensiveItems->isNotEmpty())
        <x-chart id="chart-most-expensive" type="bar"
            :labels="$mostExpensiveItems->pluck('name')->toArray()" title="💎 Most Expensive Items"
            :datasets="[['label' => '₱ Price', 'data' => $mostExpensiveItems->pluck('price')->toArray(), 'backgroundColor' => '#ec4899']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

        @if($leastExpensiveItems->isNotEmpty())
        <x-chart id="chart-least-expensive" type="bar"
            :labels="$leastExpensiveItems->pluck('name')->toArray()" title="🪙 Least Expensive Items"
            :datasets="[['label' => '₱ Price', 'data' => $leastExpensiveItems->pluck('price')->toArray(), 'backgroundColor' => '#06b6d4']]"
            :options="['indexAxis' => 'y', 'scales' => ['x' => ['beginAtZero' => true]]]" />
        @endif

    </div>
</div>
@endsection
