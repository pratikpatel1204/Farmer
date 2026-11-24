{{-- resources/views/front/partials/product-card.blade.php
     Variables: $product, $type ('want' | 'have'), $icon, $tint
     $product Eloquent Product (member, categorys) ho ya purani DB row (owner_name, owner_area), dono chalega. --}}
@php
    $categoryName = data_get($product, 'categorys.name');
    $ownerName = data_get($product, 'member.full_name') ?? data_get($product, 'owner_name');
    $city = data_get($product, 'member.citys.name');
    $state = data_get($product, 'member.states.name');
    $location = collect([$city, $state])->filter()->implode(', ') ?: (data_get($product, 'member.area') ?? data_get($product, 'owner_area'));
    $productUrl = url('product/' . $product->slug);
    // Main image, warna People Want me category ki image
    $thumbImage = \App\Support\Market::productImage($product);
@endphp
<div class="product-card card-{{ $type }}">
    <a href="{{ $productUrl }}" class="product-thumb {{ $thumbImage ? '' : $tint }}">
        @if ($thumbImage)
            <img src="{{ $thumbImage }}" alt="{{ $product->title }}" loading="lazy">
        @else
            <i class="fa-solid {{ $icon }} thumb-icon" aria-hidden="true"></i>
        @endif

        <span class="product-type {{ $type === 'want' ? 'type-want' : 'type-have' }}">
            @if ($categoryName)
                <i class="fa-solid fa-tag"></i>{{ $categoryName }}
            @elseif ($type === 'want')
                <i class="fa-solid fa-cart-shopping"></i>Wanted
            @else
                <i class="fa-solid fa-seedling"></i>Available
            @endif
        </span>
    </a>

    <div class="product-body">
        <h3 class="product-title"><a href="{{ $productUrl }}">{{ $product->title }}</a></h3>
        <ul class="product-owner">
            @if ($ownerName)
                <li>
                    <i class="fa-solid {{ $type === 'want' ? 'fa-user-tie' : 'fa-user' }}"></i>
                    <span>{{ $ownerName }}</span>
                </li>
            @endif
            @if ($location)
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $location }}</span>
                </li>
            @endif
        </ul>
        <a href="{{ $productUrl }}" class="btn btn-product">
            @if ($type === 'want')
                <i class="fa-solid fa-hand-holding-heart me-1"></i>People Want
            @else
                <i class="fa-solid fa-phone me-1"></i>Contact seller
            @endif
        </a>
    </div>
</div>
