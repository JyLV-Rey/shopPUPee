@props(['paginator'])

@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    $prev = $current - 1;
    $next = $current + 1;
    $next2 = $current + 2;
@endphp

@if ($last > 1)
    <div class="flex items-center justify-end gap-1.5 mt-8">
        {{-- Left arrow --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-sm btn-ghost btn-square text-base-content/30 cursor-not-allowed">
                ‹
            </span>
        @else
            <a href="{{ request()->fullUrlWithQuery(['page' => $prev]) }}" class="btn btn-sm btn-ghost btn-square">
                ‹
            </a>
        @endif

        {{-- Previous page --}}
        @if ($prev >= 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $prev]) }}"
               class="btn btn-sm btn-ghost btn-square">
                {{ $prev }}
            </a>
        @endif

        {{-- Current page --}}
        <a href="{{ request()->fullUrlWithQuery(['page' => $current]) }}"
           class="btn btn-sm btn-primary btn-square">
            {{ $current }}
        </a>

        {{-- Next page (+1) --}}
        @if ($next <= $last)
            <a href="{{ request()->fullUrlWithQuery(['page' => $next]) }}"
               class="btn btn-sm btn-ghost btn-square">
                {{ $next }}
            </a>
        @endif

        {{-- Next + 1 page (+2) --}}
        @if ($next2 <= $last)
            <a href="{{ request()->fullUrlWithQuery(['page' => $next2]) }}"
               class="btn btn-sm btn-ghost btn-square">
                {{ $next2 }}
            </a>
        @endif

        {{-- Custom page input --}}
        <form method="GET" action="{{ request()->url() }}" class="contents">
            @foreach (request()->query() as $key => $value)
                @if ($key !== 'page' && !is_array($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                @endif
            @endforeach
            <input
                type="number"
                name="page"
                min="1"
                max="{{ $last }}"
                value="{{ $current }}"
                placeholder="{{ $current }}"
                class="input input-bordered input-sm w-16 text-center"
            />
        </form>

        {{-- Right arrow --}}
        @if ($current < $last)
            <a href="{{ request()->fullUrlWithQuery(['page' => $next]) }}" class="btn btn-sm btn-ghost btn-square">
                ›
            </a>
        @else
            <span class="btn btn-sm btn-ghost btn-square text-base-content/30 cursor-not-allowed">
                ›
            </span>
        @endif
    </div>
@endif
