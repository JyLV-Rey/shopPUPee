<a href="{{ route('product.view', $product) }}"
    class="card bg-base-100 border border-base-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 w-full overflow-hidden group">
    {{-- Image --}}
    <figure class="h-44 bg-base-200 overflow-hidden relative">
        @if ($product->images->isNotEmpty())
            <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}"
                class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300"
                style="image-rendering: pixelated;" loading="lazy" />
        @else
            <div class="flex items-center justify-center w-full h-full text-base-content/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        {{-- Category badge on image corner --}}
        @if ($product->category)
            <span class="absolute top-2 left-2 badge badge-soft badge-sm bg-base-100/80 backdrop-blur-sm border-0">
                {{ $product->category }}
            </span>
        @endif

        {{-- Stock badge overlay --}}
        @if ($product->quantity !== null)
            <span
                class="absolute top-2 right-2 badge badge-sm {{ $product->quantity > 0 ? 'badge-success' : 'badge-error' }} border-0">
                {{ $product->quantity > 0 ? $product->quantity . ' in stock' : 'Out of stock' }}
            </span>
        @endif
    </figure>

    {{-- Body --}}
    <div class="card-body gap-1.5 p-4">
        {{-- Product name --}}
        <h3 class="card-title text-sm leading-tight line-clamp-2 font-semibold">
            {{ $product->name }}
        </h3>

        {{-- Description (max 20 chars) --}}
        @if ($product->description)
            <p class="text-xs text-base-content/50 line-clamp-1 leading-relaxed">
                {{ Str::limit($product->description, 100) }}
            </p>
        @endif

        {{-- Price --}}
        <div class="flex items-center gap-1.5 mt-1">
            <span class="text-lg font-bold text-primary">₱{{ number_format($product->price, 2) }}</span>
        </div>

        {{-- Reviews row --}}
        @php
            $avgRating = $product->reviews->avg('rating');
            $reviewCount = $product->reviews->count();
        @endphp
        @if ($reviewCount > 0)
            <div class="flex items-center gap-1.5 text-xs text-base-content/60">
                <div class="flex items-center gap-0.5">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-3.5 h-3.5 {{ $i <= round($avgRating) ? 'text-warning' : 'text-base-content/20' }}"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                <span>{{ number_format($avgRating, 1) }} ({{ $reviewCount }})</span>
            </div>
        @else
            <p class="text-xs text-base-content/30">No reviews yet</p>
        @endif

        {{-- Seller name --}}
        @if ($product->seller)
            <div class="flex items-center gap-2 pt-1 border-t border-base-200/50 mt-1">
                <div class="avatar placeholder">
                    <div class="bg-neutral text-neutral-content rounded-full w-5 h-5 flex items-center justify-center">
                        <span class="text-[10px] font-bold leading-none">{{ strtoupper(substr($product->seller->seller_name, 0, 1)) }}</span>
                    </div>
                </div>
                <span class="text-xs text-base-content/50 truncate flex-1">{{ $product->seller->seller_name }}</span>
            </div>
        @endif
    </div>
</a>
