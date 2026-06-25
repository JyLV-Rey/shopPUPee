@extends('common.index')

@section('title', 'Confirm Order')

@section('content')
<div class="container mx-auto px-4 py-10 max-w-4xl">

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-8">
        <div class="bg-success text-success-content rounded-full p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold">Confirm Your Order</h1>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('product.place') }}" id="order-form">
        @csrf
        {{-- Pass cart item IDs as hidden input --}}
        <input type="hidden" name="cart_item_ids"
            value="{{ $cartItems->pluck('cart_item_id')->implode(',') }}">

        <div class="flex flex-col gap-6">

            {{-- ─── 1. ORDER DETAILS ─────────────────────────────────── --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Order Details
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th class="w-16"></th>
                                    <th>Product</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
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
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-base-300"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                <tr>
                                    <th colspan="4" class="text-right text-base">Total</th>
                                    <th class="text-right text-primary text-lg">₱{{ number_format($total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ─── 2. SELLER INFORMATION ────────────────────────────── --}}
            @php
                $uniqueSellers = $cartItems
                    ->filter(fn($i) => $i->product->seller !== null)
                    ->pluck('product.seller')
                    ->unique('seller_id');
            @endphp
            @if ($uniqueSellers->isNotEmpty())
                <div class="card bg-base-100 shadow-md border border-base-200">
                    <div class="card-body">
                        <h2 class="card-title text-lg mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Seller Information
                        </h2>
                        <div class="flex flex-col gap-3">
                            @foreach ($uniqueSellers as $seller)
                                <div class="bg-base-200 rounded-xl p-4">
                                    <p class="font-semibold">{{ $seller->seller_name }}</p>
                                    @if ($seller->address)
                                        <p class="text-sm text-base-content/60">
                                            {{ $seller->address->street }},
                                            {{ $seller->address->city }}
                                            {{ $seller->address->postal_code }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ─── 3. DELIVERY ADDRESS ──────────────────────────────── --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Delivery Address
                    </h2>

                    @if ($addresses->isEmpty())
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>You have no saved addresses.
                                <a href="{{ route('address.add') }}" class="link link-primary">Add an address</a>
                                before placing your order.
                            </span>
                        </div>
                    @else
                        <div class="flex flex-col gap-3">
                            @foreach ($addresses as $index => $address)
                                <label class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border border-base-300 hover:bg-base-200 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                    <input type="radio" name="address_id" value="{{ $address->address_id }}"
                                        class="radio radio-primary mt-1"
                                        {{ $index === 0 ? 'checked' : '' }} required>
                                    <div>
                                        <p class="font-semibold">
                                            {{ implode(', ', array_filter([
                                                $address->unit_floor,
                                                $address->street,
                                                $address->barangay,
                                            ])) }}
                                        </p>
                                        <p class="text-sm text-base-content/60">
                                            {{ implode(', ', array_filter([
                                                $address->city,
                                                $address->province,
                                                $address->region,
                                                $address->postal_code,
                                            ])) }}
                                        </p>
                                        @if ($address->additional_notes)
                                            <p class="text-xs text-base-content/40 mt-1">
                                                Note: {{ $address->additional_notes }}
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('address.add') }}" class="btn btn-outline btn-sm">+ Add New Address</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─── 4. PAYMENT METHOD ────────────────────────────────── --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-warning" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Payment Method
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach ([
                            ['value' => 'COD', 'label' => 'Cash on Delivery', 'icon' => '💵'],
                            ['value' => 'Card', 'label' => 'Credit / Debit Card', 'icon' => '💳'],
                            ['value' => 'Wallet', 'label' => 'Digital Wallet', 'icon' => '📱'],
                            ['value' => 'UPI', 'label' => 'UPI', 'icon' => '🏦'],
                        ] as $method)
                            <label class="flex flex-col items-center gap-2 p-4 rounded-xl border border-base-300 cursor-pointer hover:bg-base-200 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="payment_method" value="{{ $method['value'] }}"
                                    class="radio radio-primary"
                                    {{ $method['value'] === 'COD' ? 'checked' : '' }}>
                                <span class="text-2xl">{{ $method['icon'] }}</span>
                                <span class="text-sm font-medium text-center">{{ $method['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─── 5. COURIER SERVICE ───────────────────────────────── --}}
            <div class="card bg-base-100 shadow-md border border-base-200">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Courier Service
                    </h2>

                    <div class="form-control w-full max-w-xs">
                        <select name="courier_service" class="select select-bordered" required>
                            <option value="" disabled>Select courier</option>
                            @foreach (['J&T Express', 'GoGo Xpress', 'Entrego', '2GO', 'JRS Express', 'Ninja Van', 'LBC'] as $courier)
                                <option value="{{ $courier }}">{{ $courier }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ─── CONFIRM BUTTON ───────────────────────────────────── --}}
            <div class="flex justify-end">
                <button type="submit" id="confirm-btn"
                    class="btn btn-primary btn-lg gap-2"
                    {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    Confirm Order — ₱{{ number_format($total, 2) }}
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    // Prevent double-submit
    document.getElementById('order-form').addEventListener('submit', function () {
        const btn = document.getElementById('confirm-btn');
        btn.disabled = true;
        btn.innerHTML = `<span class="loading loading-spinner loading-sm"></span> Processing...`;
    });
</script>
@endsection
