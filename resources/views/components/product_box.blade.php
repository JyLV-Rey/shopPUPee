<a href="{{ route('product.view', $product) }}" class="card card-compact bg-base-100 shadow-sm hover:shadow-md transition-shadow duration-200 w-full">
  <figure class="h-48 bg-base-200 overflow-hidden">
    @if ($product->images->isNotEmpty())
      <img
        src="{{ $product->images->first()->image_url }}"
        alt="{{ $product->name }}"
        class="object-cover w-full h-full"
        style="image-rendering: pixelated;"
        loading="lazy"
      />
    @else
      <div class="flex items-center justify-center w-full h-full text-base-content/30">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    @endif
  </figure>

  <div class="card-body gap-1">
    <h3 class="card-title text-base leading-tight line-clamp-2">
      {{ $product->name }}
    </h3>

    <div class="flex items-center gap-2 text-sm text-base-content/70">
      <span class="font-semibold text-lg text-primary">
        ₱{{ number_format($product->price, 2) }}
      </span>

      @if ($product->quantity !== null)
        <span class="text-xs {{ $product->quantity > 0 ? 'text-success' : 'text-error' }}">
          {{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
        </span>
      @endif
    </div>

    @if ($product->seller)
      <p class="text-xs text-base-content/50 truncate">
        {{ $product->seller->seller_name }}
      </p>
    @endif

    @if ($product->category)
      <div class="mt-1">
        <span class="badge badge-soft badge-sm">{{ $product->category }}</span>
      </div>
    @endif
  </div>
</a>
