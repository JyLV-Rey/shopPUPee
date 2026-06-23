@extends('common.index')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-5xl">

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <h1 class="text-3xl font-bold">My Orders</h1>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filter / Sort bar --}}
    <form method="GET" action="{{ route('orders') }}" class="flex flex-wrap gap-3 mb-6 items-end">
        <div class="form-control">
            <label class="label pb-1"><span class="label-text text-xs font-semibold">Sort by Date</span></label>
            <select name="sort" class="select select-bordered select-sm" onchange="this.form.submit()">
                <option value="desc" {{ request('sort', 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
                <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>Oldest First</option>
            </select>
        </div>
        <div class="form-control">
            <label class="label pb-1"><span class="label-text text-xs font-semibold">Filter by Status</span></label>
            <select name="status" class="select select-bordered select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach (['Pending','Processing','Shipped','Delivered','Cancelled','Refunded'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Empty state --}}
    @if ($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 gap-6 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-base-300" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <div>
                <h2 class="text-xl font-bold text-base-content">No orders yet</h2>
                <p class="text-base-content/60 mt-1">Looks like you haven't placed any orders.</p>
            </div>
            <a href="{{ route('search') }}" class="btn btn-primary">Browse Products</a>
        </div>
    @else
        {{-- Order cards --}}
        <div class="flex flex-col gap-5">
            @foreach ($orders as $order)
                @php
                    $total = $order->items->sum(fn($item) => $item->product->price * $item->quantity);
                    $itemNames = $order->items->pluck('product.name');
                    $preview = $itemNames->take(3)->implode(', ');
                    $remaining = $itemNames->count() - 3;

                    $statusClasses = match($order->status) {
                        'Pending'    => 'badge-warning',
                        'Processing' => 'badge-info',
                        'Shipped'    => 'badge-primary',
                        'Delivered'  => 'badge-success',
                        'Cancelled'  => 'badge-error',
                        'Refunded'   => 'badge-secondary',
                        default      => 'badge-ghost',
                    };
                @endphp

                <div class="card bg-base-100 shadow-md border border-base-200 hover:shadow-lg transition-shadow duration-200">
                    <div class="card-body p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            {{-- Left: ID + status + date --}}
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-bold text-base-content">#{{ $order->order_id }}</span>
                                    <span class="badge {{ $statusClasses }} badge-sm">{{ $order->status }}</span>
                                </div>
                                <p class="text-sm text-base-content/60">
                                    {{ $order->ordered_at ? $order->ordered_at->format('M d, Y · h:i A') : 'N/A' }}
                                </p>
                            </div>

                            {{-- Right: total --}}
                            <div class="text-right">
                                <p class="text-xs text-base-content/50 uppercase tracking-wide">Total</p>
                                <p class="text-xl font-bold text-primary">₱{{ number_format($total, 2) }}</p>
                            </div>
                        </div>

                        {{-- Items preview --}}
                        <div class="mt-3 bg-base-200 rounded-lg px-4 py-2">
                            <p class="text-sm text-base-content/70">
                                <span class="font-semibold text-base-content">Items: </span>
                                {{ $preview }}{{ $remaining > 0 ? " ...and {$remaining} more" : '' }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="card-actions justify-end mt-3">
                            <a href="{{ route('product.receipt', ['orderId' => $order->order_id]) }}"
                                class="btn btn-primary btn-sm">
                                View Receipt
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
