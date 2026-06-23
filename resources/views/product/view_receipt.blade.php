@extends('common.index')

@section('title', 'Receipt — Order #' . $order->order_id)

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">

    {{-- ─── SUCCESS BANNER ─────────────────────────────────────── --}}
    @if ($justOrdered)
        <div class="alert alert-success mb-8 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 shrink-0 stroke-current" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="font-bold text-lg">Order Placed Successfully!</h3>
                <p class="text-sm">Thank you for your purchase. Your order is now being processed.</p>
            </div>
        </div>
    @endif

    {{-- ─── RECEIPT HEADER ─────────────────────────────────────── --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Receipt
            </h1>
            <p class="text-base-content/60 mt-1 text-sm">
                Order #{{ $order->order_id }} &nbsp;·&nbsp;
                {{ $order->ordered_at ? $order->ordered_at->format('M d, Y · h:i A') : 'N/A' }}
            </p>
        </div>
        <a href="{{ route('orders') }}" class="btn btn-outline btn-sm">← Back to Orders</a>
    </div>

    <div class="flex flex-col gap-6">

        {{-- ─── TOP ROW: Order Info + Customer Info ────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Order Info Card --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-base">Order Info</h2>
                    <div class="flex flex-col gap-2 mt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60 text-sm">Order ID</span>
                            <span class="font-mono font-semibold">#{{ $order->order_id }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60 text-sm">Status</span>
                            @php
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
                            <span class="badge {{ $statusClasses }}">{{ $order->status }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60 text-sm">Date Ordered</span>
                            <span class="text-sm">
                                {{ $order->ordered_at ? $order->ordered_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-base">Customer Info</h2>
                    <div class="flex flex-col gap-2 mt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60 text-sm">Name</span>
                            <span class="font-semibold">
                                {{ $order->buyer->first_name }} {{ $order->buyer->last_name }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-base-content/60 text-sm">Email</span>
                            <span class="text-sm truncate max-w-[60%]">{{ $order->buyer->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─── ORDER ITEMS ─────────────────────────────────────── --}}
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-base mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Items Ordered
                </h2>

                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="w-16"></th>
                                <th>Product</th>
                                <th class="text-right">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                @php
                                    $image = $item->product->images->first();
                                    $subtotal = $item->product->price * $item->quantity;
                                @endphp
                                <tr>
                                    <td>
                                        @if ($image)
                                            <img src="{{ $image->image_url }}"
                                                alt="{{ $item->product->name }}"
                                                class="w-14 h-14 object-cover rounded-lg border border-base-300">
                                        @else
                                            <div class="w-14 h-14 bg-base-200 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-base-300" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="font-semibold">{{ $item->product->name }}</p>
                                        @if ($item->product->seller)
                                            <p class="text-xs text-base-content/50">
                                                Seller: {{ $item->product->seller->seller_name }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="text-right">₱{{ number_format($item->product->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right font-semibold">₱{{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                $grandTotal = $order->items->sum(fn($i) => $i->product->price * $i->quantity);
                            @endphp
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th class="text-right text-primary text-lg">₱{{ number_format($grandTotal, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ─── SELLER ADDRESS CARD ─────────────────────────────── --}}
        @php
            $sellers = $order->items
                ->filter(fn($i) => $i->product->seller !== null)
                ->pluck('product.seller')
                ->unique('seller_id');
        @endphp
        @if ($sellers->isNotEmpty())
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-base mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Seller Address
                    </h2>
                    @foreach ($sellers as $seller)
                        <div class="bg-base-200 rounded-xl p-4 mb-2">
                            <p class="font-semibold">{{ $seller->seller_name }}</p>
                            @if ($seller->address)
                                <p class="text-sm text-base-content/60">
                                    {{ implode(', ', array_filter([
                                        $seller->address->unit_floor,
                                        $seller->address->street,
                                        $seller->address->barangay,
                                        $seller->address->city,
                                        $seller->address->province,
                                        $seller->address->postal_code,
                                    ])) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ─── BOTTOM ROW: Delivery + Address ─────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Delivery Info --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-base">Delivery Info</h2>

                    @if ($order->delivery)
                        <div class="flex flex-col gap-2 mt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60 text-sm">Status</span>
                                @php
                                    $dClasses = match($order->delivery->delivery_status ?? '') {
                                        'Preparing'  => 'badge-warning',
                                        'Shipped'    => 'badge-primary',
                                        'Delivered'  => 'badge-success',
                                        'Cancelled'  => 'badge-error',
                                        'Returned'   => 'badge-secondary',
                                        default      => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $dClasses }}">{{ $order->delivery->delivery_status }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-base-content/60 text-sm">Courier</span>
                                <span class="text-sm font-medium">{{ $order->delivery->courier_service ?? '—' }}</span>
                            </div>
                            @if ($order->delivery->tracking_number)
                                <div class="flex items-center justify-between">
                                    <span class="text-base-content/60 text-sm">Tracking #</span>
                                    <span class="font-mono text-sm">{{ $order->delivery->tracking_number }}</span>
                                </div>
                            @endif
                            @if ($order->delivery->delivery_date)
                                <div class="flex items-center justify-between">
                                    <span class="text-base-content/60 text-sm">Expected Date</span>
                                    <span class="text-sm">
                                        {{ \Carbon\Carbon::parse($order->delivery->delivery_date)->format('M d, Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Delivery Steps --}}
                        <div class="mt-4">
                            @php
                                $steps = ['Preparing', 'Shipped', 'Delivered'];
                                $currentStep = match($order->delivery->delivery_status) {
                                    'Preparing' => 0,
                                    'Shipped'   => 1,
                                    'Delivered' => 2,
                                    default     => -1,
                                };
                            @endphp
                            <ul class="steps steps-horizontal w-full">
                                @foreach ($steps as $idx => $step)
                                    <li class="step {{ $idx <= $currentStep ? 'step-primary' : '' }} text-xs">
                                        {{ $step }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-info mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="h-6 w-6 shrink-0 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Delivery info is still being processed.</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Delivery Address --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-base">Delivery Address</h2>
                    @if ($order->delivery && $order->delivery->address)
                        @php $addr = $order->delivery->address; @endphp
                        <div class="mt-2 flex flex-col gap-1">
                            @if ($addr->unit_floor)
                                <p class="text-sm font-medium">{{ $addr->unit_floor }}, {{ $addr->street }}</p>
                            @else
                                <p class="text-sm font-medium">{{ $addr->street }}</p>
                            @endif
                            <p class="text-sm text-base-content/60">
                                Barangay {{ $addr->barangay }}, {{ $addr->city }}
                            </p>
                            <p class="text-sm text-base-content/60">
                                {{ $addr->province }}, {{ $addr->region }} {{ $addr->postal_code }}
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-base-content/60 mt-2">No delivery address on record.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- ─── PAYMENT INFO ────────────────────────────────────── --}}
        @if ($order->payment)
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-base mb-2">Payment Info</h2>
                    <div class="flex flex-wrap gap-6 mt-2">
                        <div>
                            <p class="text-xs text-base-content/50 uppercase tracking-wide">Method</p>
                            <p class="font-semibold">{{ $order->payment->payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 uppercase tracking-wide">Status</p>
                            @php
                                $pClasses = match($order->payment->payment_status) {
                                    'Pending'   => 'badge-warning',
                                    'Success'   => 'badge-success',
                                    'Cancelled' => 'badge-error',
                                    'Refunded'  => 'badge-secondary',
                                    default     => 'badge-ghost',
                                };
                            @endphp
                            <span class="badge {{ $pClasses }}">{{ $order->payment->payment_status }}</span>
                        </div>
                        @if ($order->payment->paid_at)
                            <div>
                                <p class="text-xs text-base-content/50 uppercase tracking-wide">Paid At</p>
                                <p class="text-sm">
                                    {{ \Carbon\Carbon::parse($order->payment->paid_at)->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    class="h-6 w-6 shrink-0 stroke-current">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Payment information is still being processed.</span>
            </div>
        @endif

        {{-- ─── REFUND / CANCEL INFO ───────────────────────────── --}}
        @if ($order->cancel)
            <div class="card border-2 border-dashed border-error bg-error/5">
                <div class="card-body">
                    <h2 class="card-title text-error text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Order Cancelled
                    </h2>
                    <p class="text-sm"><span class="font-semibold">Reason:</span> {{ $order->cancel->cancel_reason }}</p>
                    @if ($order->cancel->cancel_date)
                        <p class="text-sm text-base-content/60">
                            Date: {{ \Carbon\Carbon::parse($order->cancel->cancel_date)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if ($order->refund)
            <div class="card border-2 border-dashed border-error bg-error/5">
                <div class="card-body">
                    <h2 class="card-title text-error text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Refund Info
                    </h2>
                    <p class="text-sm"><span class="font-semibold">Reason:</span> {{ $order->refund->refund_reason }}</p>
                    <p class="text-sm">
                        <span class="font-semibold">Status:</span>
                        <span class="badge badge-secondary badge-sm">{{ $order->refund->refund_status }}</span>
                    </p>
                    @if ($order->refund->processed_at)
                        <p class="text-sm text-base-content/60">
                            Processed: {{ \Carbon\Carbon::parse($order->refund->processed_at)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- ─── BACK TO ORDERS ─────────────────────────────────── --}}
        <div class="flex justify-start mt-2">
            <a href="{{ route('orders') }}" class="btn btn-outline gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to My Orders
            </a>
        </div>

    </div>
</div>
@endsection
