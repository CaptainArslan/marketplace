@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-50 pb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="product-details-top mb-4">
                        <h3 class="product-details-title mb-2">{{ __($product->name) }}</h3>
                        <ul class="product-details-meta style--two">
                            <li>
                                <a
                                    href="{{ route('category.search', [$product->category->id, str_slug($product->category->name)]) }}">{{ __($product->category->name) }}</a>
                            </li>
                            <li class="ratings">
                                @php echo displayRating($product->avg_rating) @endphp
                                ({{ $product->total_response }})
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="product-thumb-slider-area">
                        <div class="product-details-thumb">
                            @if ($product->featured == 1)
                                <div class="tending-badge-two">
                                    <span class="caption">@lang('Featured')</span>
                                    <i class="las la-bolt"></i>
                                </div>
                            @endif
                            <img src="{{ getImage(imagePath()['p_image']['path'] . '/' . $product->image, imagePath()['p_image']['size']) }}"
                                alt="image">

                            @if ($product->screenshot)
                                <a href="{{ getImage(imagePath()['p_screenshot']['path'] . '/' . $product->screenshot[0]) }}"
                                    class="overlay-icon" data-rel="lightcase:myCollection:slideshow"><i
                                        class="las la-image"></i></a>

                                @foreach ($product->screenshot as $item)
                                    @if ($loop->iteration == 1)
                                        @continue
                                    @endif
                                    <a href="{{ getImage(imagePath()['p_screenshot']['path'] . '/' . $item) }}"
                                        data-rel="lightcase:myCollection:slideshow"></a>
                                @endforeach
                            @endif

                        </div>
                    </div><!-- product-thumb-slider end -->
                    {{-- @php
                        if (Str::contains($product->demo_link, '.png')) {
                            $href = 'onclick=$("#'.$product->id .'").modal("show") data-toggle=modal data-target=#'.$product->id.'';
                        } else {
                            $href = 'href='.$product->demo_link .'';

                        }
                    @endphp
                    <!-- Large modal -->
                    <div class="product-details-meta style--three mt-5 mb-4">
                        <div class="left">
                            <div class="btn--group justify-content-md-start justify-content-center">
                                <a {{ $href }}  target="_blank"
                                    class="btn btn-md btn--base d-inline-flex align-items-center justify-content-center text-center"><i
                                        class="las la-desktop fs-5 me-2"></i> @lang('Live Preview')</a>
                                @if ($product->demo_link)
                                    <div id="{{ $product->id }}" class="modal fade" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg" style="max-width: calc(100% - 50px)">
                                            <div class="modal-content" style="background-color: transparent; border: none">
                                                <div class="modal-body" style="padding: 0;">
                                                    <img src="{{ $product->demo_link }}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($product->screenshot)
                                    <a href="{{ getImage(imagePath()['p_screenshot']['path'] . '/' . $product->screenshot[0]) }}"
                                        class="btn btn-md btn-outline--base" data-rel="lightcase:myCollection:slideshow"><i
                                            class="las la-image fs-5 me-2"></i> @lang('Screenshot')</a>

                                    @foreach ($product->screenshot as $item)
                                        @if ($loop->iteration == 1)
                                            @continue
                                        @endif
                                        <a href="{{ getImage(imagePath()['p_screenshot']['path'] . '/' . $item) }}"
                                            data-rel="lightcase:myCollection:slideshow"></a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="right">
                            <ul class="socail-list justify-content-md-end justify-content-center">
                                <li class="caption">@lang('Share'): </li>
                                <li><a
                                        href="http://www.facebook.com/sharer.php?u={{ urlencode(url()->current()) }}&p[title]={{ str_slug($product->name) }}"><i
                                            class="fab fa-facebook-f"></i></a></li>
                                <li><a
                                        href="http://twitter.com/share?text={{ str_slug($product->name) }}&url={{ urlencode(url()->current()) }}"><i
                                            class="fab fa-twitter"></i></a></li>
                                <li><a
                                        href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ str_slug($product->name) }}"><i
                                            class="fab fa-pinterest-p"></i></a></li>
                                <li><a
                                        href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ str_slug($product->name) }}"><i
                                            class="fab fa-linkedin-in"></i></a></li>
                            </ul>
                        </div>
                    </div> --}}
                    <div class="product-details-meta style--three mt-5 mb-4">
                        <div class="left">
                            <div class="btn--group justify-content-md-start justify-content-center">
                                <a href="{{ $product->demo_link }}" target="_blank"
                                    class="btn btn-md btn--base d-inline-flex align-items-center justify-content-center text-center"><i
                                        class="las la-desktop fs-5 me-2"></i> @lang('Live Preview')</a>

                                @if ($product->screenshot)
                                    <a href="{{ getImage(imagePath()['p_screenshot']['path'] . '/' . $product->screenshot[0]) }}"
                                        class="btn btn-md btn-outline--base" data-rel="lightcase:myCollection:slideshow"><i
                                            class="las la-image fs-5 me-2"></i> @lang('Screenshot')</a>

                                    @foreach ($product->screenshot as $item)
                                        @if ($loop->iteration == 1)
                                            @continue
                                        @endif
                                        <a href="{{ getImage(imagePath()['p_screenshot']['path'] . '/' . $item) }}"
                                            data-rel="lightcase:myCollection:slideshow"></a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="right">
                            <ul class="socail-list justify-content-md-end justify-content-center">
                                <li class="caption">@lang('Share'): </li>
                                <li><a
                                        href="http://www.facebook.com/sharer.php?u={{ urlencode(url()->current()) }}&p[title]={{ str_slug($product->name) }}"><i
                                            class="fab fa-facebook-f"></i></a></li>
                                <li><a
                                        href="http://twitter.com/share?text={{ str_slug($product->name) }}&url={{ urlencode(url()->current()) }}"><i
                                            class="fab fa-twitter"></i></a></li>
                                <li><a
                                        href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ str_slug($product->name) }}"><i
                                            class="fab fa-pinterest-p"></i></a></li>
                                <li><a
                                        href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ str_slug($product->name) }}"><i
                                            class="fab fa-linkedin-in"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="product-details-content mt-50">
                        <ul class="nav nav-tabs custom--nav-tabs style--two">
                            <li class="nav-item">
                                <a class="nav-link active" id="overview">@lang('Overview')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="review">@lang('Review')</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('product.comments', [str_slug(__($product->name)), $product->id]) }}"
                                    class="nav-link">@lang('Comment')</a>
                            </li> --}}
                        </ul>
                        <div class="tab-content overview">
                            <div class="pt-4">
                                @lang($product->description)
                            </div>
                        </div>
                        <div class="tab-content review d-none">
                            <div class="pt-4">
                                <h3 class="mb-3">{{ count($product->ratings) }} @lang('Total Reviews Found')</h3>

                                @forelse ($ratings as $item)
                                    <div class="single-review">
                                        <div class="single-review__top">
                                            <div class="left">
                                                <h6 class="author-name"><a
                                                        href="{{ route('username.search', strtolower($item->user->username)) }}">{{ $item->user->username }}</a>
                                                </h6>
                                                <span class="time">{{ showDateTime($item->created_at) }}</span>
                                            </div>
                                            <div class="right">
                                                <div class="ratings">
                                                    @for ($i = 0; $i < $item->rating; $i++)
                                                        <i class="las la-star"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <p>{{ __($item->review) }}</p>
                                    </div><!-- single-review end -->
                                @empty
                                    <div class="single-review">
                                        <div class="single-review__top">
                                            <div class="left">
                                                <h6 class="author-name">@lang('No Review Found')</h6>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="pagination--sm justify-content-end mt-3">
                                {{ $ratings->links() }}
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-5 mb-3">@lang('More products by') <a
                            href="javascript:void(0)"><em>{{ $product->user->username }}</em></a></h5>
                    <div class="more-product-slider">
                        @foreach ($moreProducts as $item)
                            <div class="single-slide">
                                <div class="product-card">
                                    @if ($item->featured == 1)
                                        <span class="tending-badge"><i class="las la-bolt"></i></span>
                                    @endif
                                    <div class="product-card__thumb">
                                        <a href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"><img
                                                src="{{ getImage(imagePath()['p_image']['path'] . '/thumb_' . $item->image, imagePath()['p_image']['size']) }}"
                                                alt="@lang('image')"></a>
                                    </div>
                                    <div class="product-card__content">
                                        <h6 class="product-title mb-1"><a
                                                href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}">{{ __($item->name) }}</a>
                                        </h6>
                                        <p>@lang('by') <a
                                                href="{{ route('username.search', strtolower($item->user->username)) }}">{{ $item->user->username }}</a>
                                            @lang('in') <a
                                                href="{{ route('subcategory.search', [$item->subcategory->id, str_slug($item->subcategory->name)]) }}">{{ __($item->subcategory->name) }}</a>
                                        </p>
                                        <div class="product-card__meta">
                                            <div class="left">
                                                <h5 class="product-price mb-2">
                                                    {{ __($general->cur_sym) }}{{ getAmount($item->regular_price) }}</h5>
                                                <ul class="meta-list">
                                                    <li><i class="las la-download"></i> {{ $item->total_sell }}
                                                        @lang('Sales')</li>
                                                    <li class="ratings">
                                                        @php echo displayRating($item->avg_rating) @endphp
                                                        ({{ $item->total_response }})
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="right">
                                                <a href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"
                                                    class="cart-btn"><i class="las la-shopping-cart"></i>
                                                    @lang('Purchase')</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @include($activeTemplate . 'partials.product-right')

            </div>
        </div>
    </section>
    <!-- product details section end -->


@endsection

@push('shareImage')
    {{-- <!-- Google / Search Engine Tags --> --}}
    <meta itemprop="name" content="{{ __($product->name) }}">
    <meta itemprop="description" content="{{ strip_tags(__($product->description)) }}">
    <meta itemprop="image"
        content="{{ getImage(imagePath()['p_image']['path'] . '/' . $product->image, imagePath()['p_image']['size']) }}">

    {{-- <!-- Facebook Meta Tags --> --}}
    <meta property="og:image"
        content="{{ getImage(imagePath()['p_image']['path'] . '/' . $product->image, imagePath()['p_image']['size']) }}" />
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __($product->name) }}">
    <meta property="og:description" content="{{ strip_tags(__($product->description)) }}">
    <meta property="og:image:type"
        content="{{ getImage(imagePath()['p_image']['path'] . '/' . $product->image, imagePath()['p_image']['size']) }}" />
    @php $social_image_size = explode('x', imagePath()['p_image']['size']) @endphp
    <meta property="og:image:width" content="{{ $social_image_size[0] }}" />
    <meta property="og:image:height" content="{{ $social_image_size[1] }}" />
    <meta property="og:url" content="{{ url()->current() }}">
@endpush
@push('script')
    <script>
        $('body').on('click', '#review', function(e) {
            $(this).addClass('active');
            $('#overview').removeClass('active');
            $('.review').removeClass('d-none');
            $('.overview').addClass('d-none');

        })
        $('body').on('click', '#overview', function(e) {
            $(this).addClass('active');
            $('#review').removeClass('active');
            $('.overview').removeClass('d-none');
            $('.review').addClass('d-none');

        })
    </script>
@endpush
