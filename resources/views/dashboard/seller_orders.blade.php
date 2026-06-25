@extends('common.index')

@section('title', $seller->seller_name . ' — Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $seller->seller_name }}</h1>
            <p class="text-sm text-base-content/50">Outgoing Orders</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('dashboard.seller.orders', $seller) }}" class="btn btn-xs {{ !request('status') ? 'btn-primary' : 'btn-ghost' }}">All</a>
        <a href="{{ route('dashboard.seller.orders', [$seller, 'status' => 'Pending']) }}" class="btn btn-xs {{ request('status') === 'Pending' ? 'btn-primary' : 'btn-ghost' }}">Pending</a>
        <a href="{{ route('dashboard.seller.orders', [$seller, 'status' => 'Paid']) }}" class="btn btn-xs {{ request('status') === 'Paid' ? 'btn-primary' : 'btn-ghost' }}">Paid</a>
        <a href="{{ route('dashboard.seller.orders', [$seller, 'status' => 'Shipped']) }}" class="btn btn-xs {{ request('status') === 'Shipped' ? 'btn-primary' : 'btn-ghost' }}">Shipped</a>
        <a href="{{ route('dashboard.seller.orders', [$seller, 'status' => 'Cancelled']) }}" class="btn btn-xs {{ request('status') === 'Cancelled' ? 'btn-primary' : 'btn-ghost' }}">Cancelled</a>
        <a href="{{ route('dashboard.seller.orders', [$seller, 'status' => 'Refunded']) }}" class="btn btn-xs {{ request('status') === 'Refunded' ? 'btn-primary' : 'btn-ghost' }}">Refunded</a>
    </div>

    @if ($orders->isEmpty())
        <div class="text-center py-20 text-base-content/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p class="text-lg font-medium">No orders found</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
            <div class="card bg-base-100 border border-base-200 rounded-xl shadow-sm">
                <div class="card-body p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <span class="text-xs text-base-content/40">Order #{{ $order->order_id }}</span>
                            <p class="text-sm font-medium">{{ $order->buyer?->first_name }} {{ $order->buyer?->last_name }}</p>
                            <p class="text-xs text-base-content/50">{{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y \a\t h:i A') : '' }}</p>
                        </div>
                        @php
                            $statusColors = ['Pending' => 'badge-warning', 'Paid' => 'badge-info', 'Shipped' => 'badge-primary', 'Cancelled' => 'badge-error', 'Refunded' => 'badge-ghost'];
                        @endphp
                        <span class="badge badge-sm {{ $statusColors[$order->status] ?? 'badge-ghost' }}">{{ $order->status }}</span>
                    </div>

                    {{-- Items from THIS seller only --}}
                    <div class="divide-y divide-base-200">
                        @foreach ($order->items as $item)
                            @if ($item->product && $item->product->seller_id === $seller->seller_id)
                            <div class="flex items-center gap-3 py-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-base-content/50">₱{{ number_format($item->product->price, 2) }} × {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm font-semibold">₱{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Delivery status + Actions --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 mt-3 pt-3 border-t border-base-200">
                        <div class="flex items-center gap-2 text-xs text-base-content/50">
                            <span>Delivery:</span>
                            @if ($order->delivery)
                            <span class="badge badge-sm badge-outline">{{ $order->delivery->delivery_status }}</span>
                            @if ($order->delivery->courier_service)
                                <span>via {{ $order->delivery->courier_service }}</span>
                            @endif
                            @else
                            <span class="text-base-content/30">Not set</span>
                            @endif
                        </div>

                        @if ($order->delivery && !in_array($order->status, ['Cancelled', 'Refunded']))
                        <form method="POST" action="{{ route('delivery.status', $order->delivery) }}" class="flex items-center gap-1">
                            @csrf
                            <select name="delivery_status" class="select select-bordered select-xs w-32">
                                <option value="Preparing" @selected($order->delivery->delivery_status === 'Preparing')>Preparing</option>
                                <option value="In Transit" @selected($order->delivery->delivery_status === 'In Transit')>In Transit</option>
                                <option value="Delivered" @selected($order->delivery->delivery_status === 'Delivered')>Delivered</option>
                                <option value="Failed" @selected($order->delivery->delivery_status === 'Failed')>Failed</option>
                            </select>
                            <button type="submit" class="btn btn-xs btn-outline">Update</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
