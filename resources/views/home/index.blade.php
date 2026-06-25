@extends('common.index')

@section('title', 'shopPUPee — Home')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">

        {{-- Hero --}}
        <div class="text-center mb-12">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-content shadow-lg mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black tracking-tight">shopPUPee</h1>
            <p class="text-base-content/60 text-lg mt-2 max-w-lg mx-auto">Discover unique finds from campus sellers, all in
                one place.</p>
        </div>

        {{-- Search bar --}}
        <form method="GET" action="{{ route('search') }}" class="max-w-2xl mx-auto mb-12">
            <div class="join w-full shadow-md">
                <input type="search" name="searchTerm" placeholder="What are you looking for?"
                    class="input input-bordered join-item flex-1" />
                <button type="submit" class="btn btn-primary join-item px-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>

        {{-- Category pills --}}
        @if ($categories->isNotEmpty())
            <div class="mb-14 flex justify-center">
                <div class="flex flex-wrap items-center justify-center gap-2 pb-2" id="category-strip">
                    <a href="{{ route('search') }}"
                        class="badge badge-outline badge-lg hover:badge-primary transition-colors cursor-pointer whitespace-nowrap">All</a>
                    @foreach ($categories as $category)
                        <a href="{{ route('search', ['searchCategory' => $category]) }}"
                            class="badge badge-outline badge-lg hover:badge-primary transition-colors cursor-pointer text-center whitespace-normal self-center text-wrap">
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Trending Now --}}
        @if ($trending->isNotEmpty())
            <section class="mb-14">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">Trending Now</h2>
                        <p class="text-sm text-base-content/50">Most popular picks from the community</p>
                    </div>
                    <a href="{{ route('search') }}" class="btn btn-ghost btn-sm text-primary">View All &rarr;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($trending as $product)
                        <x-product_box :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Featured Products --}}
        @if ($featured->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">Featured Products</h2>
                        <p class="text-sm text-base-content/50">Fresh listings from our sellers</p>
                    </div>
                    <a href="{{ route('search') }}" class="btn btn-ghost btn-sm text-primary">View All &rarr;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($featured as $product)
                        <x-product_box :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection
