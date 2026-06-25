@props([
    'id',
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => '250px',
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

<div class="card bg-base-100 shadow-sm border border-base-200">
    <div class="card-body p-4">
        <div style="position: relative; height: {{ $height }}; width: 100%;">
            <canvas id="{{ $id }}" data-chart='{!! $spec !!}'></canvas>
        </div>
    </div>
</div>
