@extends('common.index')

@section('title', $product->name . ' — View Product')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm breadcrumbs mb-6 text-base-content/50">
        <ul>
            <li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
            <li><a href="{{ route('search') }}" class="hover:text-primary">Products</a></li>
            @if ($product->category)
                <li><a href="{{ route('search', ['searchCategory' => $product->category]) }}" class="hover:text-primary">{{ $product->category }}</a></li>
            @endif
            <li class="text-base-content/70">{{ Str::limit($product->name, 30) }}</li>
        </ul>
    </nav>

    {{-- Main product layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        {{-- Left: Image gallery --}}
        <x-product_gallery :images="$product->images" :productName="$product->name" />

        {{-- Right: Product details --}}
        <div>
            {{-- Category + Status chips --}}
            <div class="flex flex-wrap items-center gap-2 mb-3">
                @if ($product->category)
                    <span class="badge badge-soft badge-sm">{{ $product->category }}</span>
                @endif
                @if ($product->quantity !== null)
                    <span class="badge badge-sm {{ $product->quantity > 0 ? 'badge-success' : 'badge-error' }}">
                        {{ $product->quantity > 0 ? 'In Stock (' . $product->quantity . ')' : 'Out of Stock' }}
                    </span>
                @endif
                @if ($product->is_deleted)
                    <span class="badge badge-error badge-sm">Disabled</span>
                @endif
            </div>

            {{-- Name --}}
            <h1 class="text-3xl font-bold tracking-tight mb-3">{{ $product->name }}</h1>

            {{-- Rating row --}}
            @php
                $avgRating = $product->reviews->avg('rating');
                $reviewCount = $product->reviews->count();
            @endphp
            <div class="flex items-center gap-2 mb-4">
                @if ($reviewCount > 0)
                    <div class="flex items-center gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-warning' : 'text-base-content/15' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm text-base-content/50">{{ number_format($avgRating, 1) }} <span class="text-base-content/30">({{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }})</span></span>
                @else
                    <span class="text-sm text-base-content/30">No reviews yet</span>
                @endif
            </div>

            {{-- Price --}}
            <div class="mb-6">
                <span class="text-4xl font-bold text-primary">₱{{ number_format($product->price, 2) }}</span>
            </div>

            {{-- Divider --}}
            <div class="divider"></div>

            {{-- Description --}}
            @if ($product->description)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-base-content/70 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-sm text-base-content/70 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
                </div>
            @endif

            {{-- Quick actions --}}
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <div class="join flex-1">
                    <button class="btn btn-outline btn-sm join-item" onclick="decrementQty()">−</button>
                    <input id="qty-input" type="number" value="1" min="1" max="{{ $product->quantity ?? 999 }}"
                           class="input input-bordered input-sm join-item w-16 text-center" />
                    <button class="btn btn-outline btn-sm join-item" onclick="incrementQty()">+</button>
                </div>
                <button class="btn btn-primary flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add to Cart
                </button>
            </div>

            {{-- Seller card --}}
            @if ($product->seller)
                <div class="card bg-base-100 border border-base-200 rounded-xl mt-6">
                    <div class="card-body p-4">
                        <a href="{{ route('dashboard.seller', $product->seller) }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                            <div class="avatar placeholder">
                                <div class="bg-secondary text-secondary-content rounded-full w-12 h-12 flex items-center justify-center">
                                    <span class="text-lg font-bold leading-none">{{ strtoupper(substr($product->seller->seller_name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-sm">{{ $product->seller->seller_name }}</p>
                                <p class="text-xs text-base-content/40">View Store Profile →</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Reviews section --}}
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold tracking-tight">Customer Reviews</h2>
            @if ($reviewCount > 0)
                <div class="flex items-center gap-2 text-sm">
                    <div class="flex items-center gap-0.5">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-warning' : 'text-base-content/15' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="font-semibold">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-base-content/30">· {{ $reviewCount }} total</span>
                </div>
            @endif
        </div>

        @if ($product->reviews->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($product->reviews as $review)
                    <x-review_card :review="$review" />
                @endforeach
            </div>
        @else
            <div class="text-center py-16 text-base-content/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <p class="text-sm">This product has no reviews yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function incrementQty() {
        const input = document.getElementById('qty-input');
        const max = parseInt(input.getAttribute('max')) || 999;
        const val = parseInt(input.value) || 1;
        if (val < max) input.value = val + 1;
    }
    function decrementQty() {
        const input = document.getElementById('qty-input');
        const val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
    }
</script>
@endpush
