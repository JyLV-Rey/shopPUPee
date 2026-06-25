@props([
    'id',
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => '260px',
    'title' => '',
    'options' => [],
])

@php
    $defaults = [
        'responsive' => true,
        'maintainAspectRatio' => true,
        'plugins' => [
            'legend' => ['display' => false],
            'title' => $title ? ['display' => true, 'text' => $title] : null,
        ],
    ];

    if (! $title) {
        unset($defaults['plugins']['title']);
    }

    $mergedOptions = array_merge_recursive($defaults, $options);

    $spec = json_encode([
        'type' => $type,
        'data' => [
            'labels' => array_values($labels),
            'datasets' => $datasets,
        ],
        'options' => $mergedOptions,
    ], JSON_HEX_TAG | JSON_HEX_APOS);
@endphp

<div class="card bg-base-100 border border-base-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
    <div class="card-body p-5">
        @if($title)
            <h3 class="text-sm font-semibold text-base-content/80 tracking-wide uppercase mb-3">
                {{ $title }}
            </h3>
            <div class="divider mt-0 mb-3"></div>
        @endif
        <div style="position: relative; height: {{ $height }}; width: 100%;">
            <canvas id="{{ $id }}" data-chart='{!! $spec !!}'></canvas>
        </div>
    </div>
</div>
