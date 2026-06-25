@props(['images' => [], 'productName' => ''])

<div class="sticky top-24">
    {{-- Main image --}}
    <div class="aspect-square bg-base-200 rounded-xl overflow-hidden mb-3 shadow-sm" id="main-image-container">
        @if ($images->isNotEmpty())
            <img id="main-image" src="{{ $images->first()->image_url }}" alt="{{ $productName }}"
                 class="w-full h-full object-cover" style="image-rendering: pixelated;" />
        @else
            <div class="flex items-center justify-center w-full h-full text-base-content/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Thumbnails --}}
    @if ($images->count() > 1)
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach ($images as $image)
                <button type="button" class="thumbnail-btn w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary focus:border-primary transition-colors flex-shrink-0 {{ $loop->first ? 'border-primary' : '' }}"
                        data-src="{{ $image->image_url }}">
                    <img src="{{ $image->image_url }}" alt="" class="w-full h-full object-cover" style="image-rendering: pixelated;" loading="lazy" />
                </button>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.querySelectorAll('.thumbnail-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.thumbnail-btn').forEach(b => b.classList.remove('border-primary'));
            this.classList.add('border-primary');
            document.getElementById('main-image').src = this.dataset.src;
        });
    });
</script>
@endpush
