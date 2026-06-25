@extends('common.index')
@section('title', 'Shopping Cart')
@section('content')

<div class="max-w-6xl mx-auto px-4 py-10">

  <div class="flex items-center gap-3 mb-8">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    <h1 class="text-2xl font-bold">Shopping Cart</h1>
    @if ($cartItems->isNotEmpty())
      <span class="badge badge-primary" id="item-count-badge">{{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }}</span>
    @endif
  </div>

  @if ($cartItems->isEmpty())
    <div class="flex flex-col items-center justify-center py-32 gap-4 text-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-base-content/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <p class="text-xl font-semibold text-base-content/40">Your cart is empty</p>
      <p class="text-sm text-base-content/30">Add some products to get started.</p>
      <a href="{{ route('search') }}" class="btn btn-primary btn-sm mt-2">Start Shopping</a>
    </div>

  @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <div class="lg:col-span-2 flex flex-col gap-4">
        @foreach ($grouped as $sellerName => $items)

          <div class="rounded-xl border border-base-200 bg-base-100 shadow-sm overflow-hidden">

            <div class="flex items-center gap-2 px-4 py-3 bg-base-200/50 border-b border-base-200">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
              </svg>
              <span class="text-xs font-semibold text-base-content/70 uppercase tracking-wide">{{ $sellerName }}</span>
            </div>

            @foreach ($items as $item)
              @php $product = $item->product; @endphp
              <div class="flex items-center gap-4 px-4 py-4 {{ !$loop->last ? 'border-b border-base-200' : '' }}" data-cart-item="{{ $item->cart_item_id }}" data-price="{{ $product->price }}" data-qty="{{ $item->quantity }}">

                {{-- Checkbox --}}
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="checkbox checkbox-primary checkbox-sm cart-select" value="{{ $item->cart_item_id }}" checked />
                </label>

                <a href="{{ route('product.view', $product->product_id) }}" class="shrink-0">
                  @if ($product->images->isNotEmpty())
                    <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-lg object-cover border border-base-200" style="image-rendering: pixelated;" />
                  @else
                    <div class="w-16 h-16 rounded-lg bg-base-200 flex items-center justify-center shrink-0">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-base-content/20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                  @endif
                </a>

                <div class="flex-grow min-w-0">
                  <a href="{{ route('product.view', $product->product_id) }}" class="text-sm font-semibold hover:text-primary line-clamp-1 leading-snug">{{ $product->name }}</a>
                  <p class="text-xs text-base-content/50 mt-0.5">₱{{ number_format($product->price, 2) }} each</p>

                  <div class="flex items-center gap-1 mt-2">
                    <form method="POST" action="{{ route('cart.update', $item->cart_item_id) }}">
                      @csrf
                      <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}" />
                      <button type="submit" class="btn btn-xs btn-square btn-ghost border border-base-300 leading-none" {{ $item->quantity <= 1 ? 'disabled' : '' }}>−</button>
                    </form>
                    <span class="w-7 text-center text-sm font-semibold qty-display">{{ $item->quantity }}</span>
                    <form method="POST" action="{{ route('cart.update', $item->cart_item_id) }}">
                      @csrf
                      <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}" />
                      <button type="submit" class="btn btn-xs btn-square btn-ghost border border-base-300 leading-none" {{ $item->quantity >= $product->quantity ? 'disabled' : '' }}>+</button>
                    </form>
                    @if ($item->quantity >= $product->quantity)
                      <span class="text-error text-xs ml-1">Max</span>
                    @endif
                  </div>
                </div>

                <div class="flex flex-col items-end gap-2 shrink-0">
                  <span class="text-sm font-bold text-primary subtotal">₱{{ number_format($product->price * $item->quantity, 2) }}</span>
                  <form method="POST" action="{{ route('cart.destroy', $item->cart_item_id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-ghost text-error px-1" title="Remove">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </form>
                </div>

              </div>
            @endforeach
          </div>
        @endforeach
      </div>

      <div class="lg:col-span-1">
        <div class="rounded-xl border border-base-200 bg-base-100 shadow-sm p-5 sticky top-24 flex flex-col gap-4">
          <div class="flex items-center justify-between">
            <h2 class="font-bold text-base">Order Summary</h2>
            <label class="flex items-center gap-1.5 text-xs text-base-content/50 cursor-pointer">
              <input type="checkbox" id="select-all" class="checkbox checkbox-primary checkbox-xs" checked />
              Select All
            </label>
          </div>

          <div class="flex flex-col gap-2 text-sm">
            <div class="flex justify-between text-base-content/60">
              <span>Selected Items</span>
              <span id="selected-count">{{ $cartItems->count() }}</span>
            </div>
            <div class="flex justify-between text-base-content/60">
              <span>Total Qty</span>
              <span id="selected-qty">{{ $cartItems->sum('quantity') }}</span>
            </div>
            <div class="border-t border-base-200 my-1"></div>
            <div class="flex justify-between font-bold text-base">
              <span>Total</span>
              <span class="text-primary" id="selected-total">₱{{ number_format($total, 2) }}</span>
            </div>
          </div>

          <a id="checkout-btn" href="{{ route('product.confirm') }}?cartItems={{ $cartItemIds }}"
             class="btn btn-primary btn-block btn-sm">
            Proceed to Checkout
          </a>
          <a href="{{ route('search') }}" class="btn btn-ghost btn-block btn-sm text-xs">Continue Shopping</a>
        </div>
      </div>

    </div>
  @endif

</div>

@push('scripts')
<script>
(function() {
  const checkboxes = document.querySelectorAll('.cart-select');
  const selectAll = document.getElementById('select-all');
  const countEl = document.getElementById('selected-count');
  const qtyEl = document.getElementById('selected-qty');
  const totalEl = document.getElementById('selected-total');
  const checkoutBtn = document.getElementById('checkout-btn');

  function updateSummary() {
    let selected = [];
    let count = 0;
    let qty = 0;
    let total = 0;

    document.querySelectorAll('[data-cart-item]').forEach(row => {
      const cb = row.querySelector('.cart-select');
      if (cb && cb.checked) {
        const id = cb.value;
        selected.push(id);
        count++;
        const price = parseFloat(row.dataset.price);
        const q = parseInt(row.dataset.qty);
        qty += q;
        total += price * q;
      }
    });

    countEl.textContent = count;
    qtyEl.textContent = qty;
    totalEl.textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    if (selected.length === 0) {
      checkoutBtn.classList.add('btn-disabled');
      checkoutBtn.removeAttribute('href');
    } else {
      checkoutBtn.classList.remove('btn-disabled');
      checkoutBtn.href = '{{ route("product.confirm") }}?cartItems=' + selected.join(',');
    }
  }

  checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));

  if (selectAll) {
    selectAll.addEventListener('change', function() {
      checkboxes.forEach(cb => cb.checked = this.checked);
      updateSummary();
    });
  }

  updateSummary();
})();
</script>
@endpush
@endsection
