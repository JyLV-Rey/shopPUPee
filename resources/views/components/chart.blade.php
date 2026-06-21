@props([
    'id',
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => '250px',
    'title' => '',
])

@php
    $spec = json_encode([
        'type' => $type,
        'data' => [
            'labels' => $labels,
            'datasets' => $datasets,
        ],
        'options' => [
            'plugins' => [
                'title' => $title ? ['display' => true, 'text' => $title] : null,
            ],
        ],
    ]);
@endphp

<div class="card bg-base-100 shadow-sm border border-base-200">
    <div class="card-body p-4">
        <div style="position: relative; height: {{ $height }}; width: 100%;">
            <canvas id="{{ $id }}" data-chart='{{ $spec }}'></canvas>
        </div>
    </div>
</div>
