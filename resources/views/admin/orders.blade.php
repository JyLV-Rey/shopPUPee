@extends('common.index')

@section('title', 'Admin — Orders')

@section('content')
<div class="flex flex-col lg:flex-row min-h-[calc(100vh-4rem)]">
    <x-admin_sidebar />

    <div class="flex-1 p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold tracking-tight">Manage Orders</h1>
            <span class="badge badge-lg">{{ $orders->total() }} total</span>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-base-200 shadow-sm">
            <table class="table table-zebra w-full">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-base-content/50">
                        <th>Order ID</th>
                        <th>Buyer</th>
                        <th>Date</th>
                        <th class="text-center">Items</th>
                        <th>Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="text-xs text-base-content/50">#{{ $order->order_id }}</td>
                        <td class="font-medium">{{ $order->buyer?->first_name }} {{ $order->buyer?->last_name ?? '—' }}</td>
                        <td class="text-sm">{{ $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at)->format('M d, Y') : '—' }}</td>
                        <td class="text-center">{{ $order->items->sum('quantity') }}</td>
                        <td>₱{{ number_format($order->items->sum(fn($i) => ($i->product?->price ?? 0) * $i->quantity), 2) }}</td>
                        <td class="text-center">
                            @php
                                $statusColors = ['Pending' => 'badge-warning', 'Paid' => 'badge-info', 'Shipped' => 'badge-primary', 'Cancelled' => 'badge-error', 'Refunded' => 'badge-ghost'];
                            @endphp
                            <span class="badge badge-sm {{ $statusColors[$order->status] ?? 'badge-ghost' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex flex-col items-center gap-1">
                                <form method="POST" action="{{ route('admin.order.status', $order) }}" class="flex items-center gap-1">
                                    @csrf
                                    <span class="text-[10px] text-base-content/40">Pay:</span>
                                    <select name="status" class="select select-bordered select-xs w-24">
                                        <option value="Paid" @selected($order->status === 'Paid')>Paid</option>
                                        <option value="Shipped" @selected($order->status === 'Shipped')>Shipped</option>
                                        <option value="Cancelled" @selected($order->status === 'Cancelled')>Cancel</option>
                                        <option value="Refunded" @selected($order->status === 'Refunded')>Refund</option>
                                    </select>
                                    <button type="submit" class="btn btn-xs btn-outline">Update</button>
                                </form>
                                @if ($order->delivery)
                                <form method="POST" action="{{ route('delivery.status', $order->delivery) }}" class="flex items-center gap-1">
                                    @csrf
                                    <span class="text-[10px] text-base-content/40">Delivery:</span>
                                    <select name="delivery_status" class="select select-bordered select-xs w-24">
                                        <option value="Preparing" @selected($order->delivery->delivery_status === 'Preparing')>Preparing</option>
                                        <option value="In Transit" @selected($order->delivery->delivery_status === 'In Transit')>In Transit</option>
                                        <option value="Delivered" @selected($order->delivery->delivery_status === 'Delivered')>Delivered</option>
                                        <option value="Failed" @selected($order->delivery->delivery_status === 'Failed')>Failed</option>
                                    </select>
                                    <button type="submit" class="btn btn-xs btn-outline">Set</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-8 text-base-content/50">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
</div>
@endsection
