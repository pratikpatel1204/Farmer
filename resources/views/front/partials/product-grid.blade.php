{{-- resources/views/front/partials/product-grid.blade.php
     Listing pages ka grid + pagination.
     Variables: $products (paginator), $columns (Bootstrap row-cols classes, optional) --}}
@php
    $fallbackIcons = [['fa-carrot', 'thumb-red'], ['fa-wheat-awn', 'thumb-yellow'], ['fa-seedling', 'thumb-lime'], ['fa-apple-whole', 'thumb-red'], ['fa-tractor', 'thumb-brown'], ['fa-lemon', 'thumb-yellow']];
@endphp

<div class="row {{ $columns ?? 'row-cols-2 row-cols-md-3' }} g-2 g-sm-3 product-grid">
    @foreach ($products as $product)
        @php
            $cardType = \App\Support\Market::typeOf($product);
            $cat = \App\Support\Market::categoryFor(data_get($product, 'categorys.name') ?? $product->category);
            [$icon, $tint] = $cat
                ? [$cat['icon'], 'thumb-' . $cat['color']]
                : $fallbackIcons[$loop->index % count($fallbackIcons)];
        @endphp
        <div class="col">
            @include('front.partials.product-card', ['product' => $product, 'type' => $cardType, 'icon' => $icon, 'tint' => $tint])
        </div>
    @endforeach
</div>

@if ($products->hasPages())
    <div class="farm-pagination">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endif
