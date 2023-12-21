<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="section-header text-center">
            <h2 class="section-title">Most Sold Products of Different Categories</h2>
            <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed, iste maiores dolore
                iusto in vero unde amet, ipsam laborum eveniet, veritatis dolor incidunt blanditiis
                voluptatibus.</p>
        </div>
    </div>
</div>
<ul class="nav nav-tabs px-5 mt-5" id="myTabContent" role="tablist">
    @foreach ($catwithmostsold as $key => $value)
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $key == 0 ? 'active' : '' }}" id="h{{ $key }}-tab" data-bs-toggle="tab"
                data-bs-target="#h{{ $key }}" type="button" role="tab" aria-controls="home"
                aria-selected="true">{{ $value->name }}</button>
        </li>
    @endforeach

</ul>
<div class="tab-content" id="myTabContent">
    @foreach ($catwithmostsold as $key => $value)
        <div class="tab-pane fade show {{ $key == 0 ? 'active' : '' }}" id="h{{ $key }}" role="tabpanel"
            aria-labelledby="h{{ $key }}-tab">
            <section class="pt-100 pb-100 px-xxl-5 bg_img"
                style="background-image: url({{ asset($activeTemplateTrue . '/images/bg2.jpg') }});">
                <div class="container-fluid">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-lg-6 wow fadeInLeft" data-wow-duration="0.5s" data-wow-delay="0.3s">
                            <div class="section-header mb-0 text-lg-start text-center mb-3">
                                <h2 class="section-title">Most Sold Products of {{ $value->name }}</h2>
                                <p class="mt-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec
                                    congue
                                    euismod
                                    malesuada.</p>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end text-center">
                            <a href="{{ route('best.sell.category', $value->id) }}"
                                class="btn btn--base2 mt-4">@lang('View All Items')</a>
                        </div>
                    </div><!-- row end -->

                    <div class="row gy-4 justify-content-center">

                        @foreach ($catsections as $data)
                            @foreach ($data as $item)
                                @if ($item->category_id == $value->id)
                                    <div class="col-xl-3 col-md-6">
                                        <div class="product-card style--three p-0">
                                            @if ($item->featured == 1)
                                                <span class="tending-badge align-right"><i
                                                        class="las la-bolt"></i></span>
                                            @endif
                                            <span class="badge-right"><i class="las la-fire"></i></span>
                                            <div class="product-card__thumb">
                                                <a
                                                    href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"><img
                                                        src="{{ getImage(imagePath()['p_image']['path'] . '/thumb_' . $item->image, imagePath()['p_image']['size']) }}"
                                                        alt="@lang('product-image')"></a>
                                            </div>

                                            <div class="product-card__content">
                                                <p class="mb-2">@lang('by') <a
                                                        href="{{ route('username.search', strtolower($item->user->username)) }}"
                                                        class="text--base">{{ __($item->user->username) }}</a>
                                                    @lang('in') <a
                                                        href="{{ route('subcategory.search', [$item->subcategory->id, str_slug($item->subcategory->name)]) }}"
                                                        class="text--base">{{ __($item->subcategory->name) }}</a>
                                                </p>
                                                <h6 class="product-title mb-1"><a
                                                        href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}">{{ str_limit(__($item->name), 32) }}</a>
                                                </h6>
                                                <div class="product-card__meta align-items-center">
                                                    <div class="left">
                                                        <ul class="meta-list">
                                                            <li class="product-sale-amount"><i
                                                                    class="las la-shopping-cart text--base"></i>
                                                                <span class="text--base">{{ $item->total_sell }}</span>
                                                                @lang('Sales')
                                                            </li>
                                                            <li class="ratings">
                                                                @php echo displayRating($item->avg_rating) @endphp
                                                                ({{ $item->total_response }})
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="right">
                                                        <h5 class="product-price">
                                                            {{ __($general->cur_sym) }}{{ getAmount($item->regular_price) }}
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="product-card__btn-area">
                                                    <a href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"
                                                        class="cart-btn style--two"><i class="las la-shopping-cart"></i>
                                                        @lang('Details')</a>
                                                    <a href="{{ $item->demo_link }}" class="cart-btn"><i
                                                            class="las la-eye"></i>
                                                        @lang('Live Preview')</a>
                                                </div>
                                            </div>
                                        </div><!-- product-card end -->
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div><!-- row end -->
                </div>
            </section>
        </div>
    @endforeach
</div>
