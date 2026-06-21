@extends('common.index')
@section('title', 'Search')
@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">
  {{-- Search Controls --}}
  <form method="GET" action="{{ route('search') }}" class="mb-8">
    <div class="card bg-base-100 shadow-sm border border-base-200">
      <div class="card-body p-4 sm:p-6">
        {{-- Main search row --}}
        <div class="flex flex-col sm:flex-row gap-3">
          {{-- Search input --}}
          <div class="form-control flex-1">
            <div class="join w-full">
              <input
                type="text"
                name="searchTerm"
                value="{{ $searchTerm }}"
                placeholder="Search products..."
                class="input input-bordered join-item w-full"
              />
              <button type="submit" class="btn btn-primary join-item">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        {{-- Filter row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3">
          {{-- Category --}}
          <div class="form-control">
            <select name="searchCategory" class="select select-bordered w-full">
              <option value="">All Categories</option>
              @foreach ($categories as $category)
                <option value="{{ $category }}" @selected($searchCategory === $category)>
                  {{ $category }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Store --}}
          <div class="form-control">
            <input
              type="text"
              name="searchStore"
              value="{{ $searchStore }}"
              placeholder="Store name..."
              class="input input-bordered w-full"
            />
          </div>

          {{-- Max price --}}
          <div class="form-control">
            <input
              type="number"
              name="maxPrice"
              value="{{ $maxPrice }}"
              placeholder="Max price"
              min="0"
              step="0.01"
              class="input input-bordered w-full"
            />
          </div>

          {{-- Sort --}}
          <div class="form-control">
            <select name="sortBy" class="select select-bordered w-full">
              <option value="name" @selected($sortBy === 'name')>Name</option>
              <option value="price" @selected($sortBy === 'price')>Price</option>
              <option value="created_at" @selected($sortBy === 'created_at')>Newest</option>
              <option value="quantity" @selected($sortBy === 'quantity')>Stock</option>
            </select>
          </div>
        </div>

        {{-- Sort direction + action row --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mt-2">
          <label class="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              name="isDescending"
              value="1"
              class="checkbox checkbox-primary checkbox-sm"
              @checked($isDescending)
            />
            <span class="text-sm">Descending</span>
          </label>

          <div class="flex gap-2">
            <a href="{{ route('search') }}" class="btn btn-ghost btn-sm">Clear</a>
            <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  {{-- Results --}}
  @if ($products->isEmpty())
    <div class="text-center py-20 text-base-content/50">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <p class="text-lg font-medium">No products found</p>
      <p class="text-sm mt-1">Try adjusting your search or filters.</p>
    </div>
  @else
    <div class="flex items-center justify-between mb-4">
      <p class="text-sm text-base-content/60">
        {{ $products->count() }} product{{ $products->count() !== 1 ? 's' : '' }} found
      </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      @foreach ($products as $product)
        <x-product_box :product="$product" />
      @endforeach
    </div>
  @endif
</div>

@endsection

