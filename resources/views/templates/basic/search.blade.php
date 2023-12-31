@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="pt-50 pb-100">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-12">
                    <form class="single-search-form pb-3" method="GET" action="{{ route('product.search') }}">
                        <input type="text" name="search" value="{{ request()->search ?? null }}" class="form--control searchfield"
                            placeholder="@lang('Search here')...">
                        <button type="submit" class="btn btn--base single-search-btn">@lang('Search')</button>
                    </form>
                    <p class="fs-14px mt-1"> @lang('Search Result : ')<b class="total_item">{{ count($products) }}</b>
                        @lang('Items Found')</p>
                    <hr>
                </div>
            </div><!-- row end --->


            <div class="row">
                <div class="col-lg-3 mb-lg-0 mb-3">
                    <button class="action-sidebar-open"><i class="las la-sliders-h"></i> @lang('Filter')</button>
                    <form action="" id="ms-form">
                        <div class="action-sidebar">
                            <button class="action-sidebar-close" type="button"><i class="las la-times"></i></button>
                            <div class="action-widget top-widget">
                                <h4 class="action-widget__title">@lang('Filter & Refine')</h4>
                                <!-- <hr> -->
                            </div><!-- action-widget end -->
                            <button type="button"
                                class="resetbutton text-center btn btn-primary btn-sm d-block w-50 mt-3">@lang('Reset')</button>
                            <div class="action-widget mt-4">
                                <h6 class="action-widget__title">@lang('Categories')</h6>
                                <div class="action-widget__body">

                                    @foreach ($categoryForSearchPage as $item)
                                        <div class="form-check custom--checkbox">
                                            <input class="form-check-input filter-by-category" name="categories"
                                                type="checkbox" value="{{ $item->id }}" id="chekbox-{{ $loop->index }}"
                                                @if (isset($subcategory) && $item->id == $subcategory->category_id) checked @endif>
                                            <label class="form-check-label" for="chekbox-{{ $loop->index }}">
                                                {{ __($item->name) }}
                                            </label>
                                        </div><!-- form-check end -->
                                    @endforeach

                                </div>
                            </div><!-- action-widget css end -->

                            <div class="action-widget mt-4">
                                <h6 class="action-widget__title">@lang('Tags')</h6>
                                <div class="action-widget__body scroll--active __tag_wrapper">
                                    @foreach ($tags as $data)
                                        <div class="form-check custom--checkbox">
                                            <input class="form-check-input filter-by-tag" name="tags" type="checkbox"
                                                value="{{ $data }}" id="chekbox-tag-{{ $loop->index }}">
                                            <label class="form-check-label" for="chekbox-tag-{{ $loop->index }}">
                                                {{ __($data) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div><!-- action-widget css end -->
                            <div class="action-widget mt-4">
                                <h6 class="action-widget__title">@lang('By price')</h6>
                                <div class="action-widget__body">
                                    <div class="price-range-form">
                                        <div class="price-range-single">
                                            <span class="currency-icon">{{ __($general->cur_sym) }}</span>
                                            <input type="number" step="any" class="minAmount"
                                                placeholder="{{ $min }}">
                                        </div>
                                        <div class="price-range-single">
                                            <span class="currency-icon">{{ __($general->cur_sym) }}</span>
                                            <input type="number" step="any" class="maxAmount"
                                                placeholder="{{ $max }}">
                                        </div>
                                        <button type="button" class="priceFilter"><i
                                                class="las la-angle-right"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="action-widget mt-4">
                                <h6 class="action-widget__title">@lang('Order By')</h6>
                                <div class="action-widget__body">
                                    <select class="select orderBy">
                                        <option value="1"> @lang('Price ASC')</option>
                                        <option value="2"> @lang('Price DESC')</option>
                                        <option value="3"> @lang('Date ASC')</option>
                                        <option value="4"> @lang('Date DESC')</option>
                                        <option value="5"> @lang('Sell ASC')</option>
                                        <option value="6"> @lang('Sell DESC')</option>
                                        <option value="7"> @lang('Rating ASC')</option>
                                        <option value="8"> @lang('Rating DESC')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-9">

                    <div class="main-content">
                        <div class="row gy-4 card-view-area list-view">
                            @forelse ($products as $item)
                                <div class="col-md-6 card-view">
                                    <div class="product-card">
                                        @if ($item->featured == 1)
                                            <span class="tending-badge"><i class="las la-bolt"></i></span>
                                        @endif
                                        <div class="product-card__thumb">
                                            <a
                                                href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"><img
                                                    src="{{ getImage(imagePath()['p_image']['path'] . '/thumb_' . $item->image, imagePath()['p_image']['size']) }}"
                                                    alt="@lang('image')"></a>
                                        </div>
                                        <div class="product-card__content">
                                            <h6 class="product-title mb-1"><a
                                                    href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}">{{ str_limit(__($item->name), 32) }}</a>
                                            </h6>
                                            <p>@lang('by') <a
                                                    href="{{ route('username.search', strtolower($item->user->username)) }}">{{ $item->user->username }}</a>
                                                @lang('in') <a
                                                    href="{{ route('subcategory.search', [$item->subcategory->id, str_slug($item->subcategory->name)]) }}">{{ __($item->subcategory->name) }}</a>
                                            </p>
                                            <div class="product-card__meta">
                                                <div class="left">
                                                    <p class="mb-1">@lang('Last Updated') -
                                                        {{ showDateTime($item->updated_at, 'd M Y') }}</p>
                                                    <ul class="meta-list">
                                                        <li class="product-sale-amount"><i
                                                                class="las la-shopping-cart text--base"></i> <span
                                                                class="text--base">{{ $item->total_sell }}</span>
                                                            @lang('Sales')</li>

                                                        <li class="ratings">
                                                            @php echo displayRating($item->avg_rating) @endphp
                                                            ({{ $item->total_response }})
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="right">
                                                    <h5 class="product-price mb-2 text-center">
                                                        {{ __($general->cur_sym) }}{{ getAmount($item->regular_price) }}
                                                    </h5>
                                                    <a href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"
                                                        class="cart-btn"><i class="las la-shopping-cart"></i>
                                                        @lang('View Details')</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-xl-12 col-md-12 card-view">
                                    <div class="product-card">
                                        <h6 class="product-title mb-1">{{ __($empty_message) }}</h6>
                                    </div>
                                </div>
                            @endforelse

                        </div><!-- row end -->
                    </div>

                    <div class="row mt-5">
                        <div class="col-lg-12">
                            <ul class="pagination justify-content-end">
                                {{ $products->links() }}
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="custome_loader">
        <div class="spinner-border">
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/jquery-ui.min.js') }}"></script>
@endpush

@push('script')
    <script>
        "use strict";
        var route = @json(URL::current());
        (function($) {

            // grid & list view js
            const gridBtn = $('.grid-view-btn');
            const listBtn = $('.list-view-btn');

            const gridView = $('.grid-view');
            const listView = $('.list-view');


            gridBtn.on('click', function() {
                // button active class check
                if ($(this).hasClass('active')) {
                    return true
                } else {
                    $(this).addClass('active');
                    $(this).siblings('.list-view-btn').removeClass('active');
                }
                // grid & list view check
                if ($(document).find('.main-content .card-view-area').hasClass('list-view')) {
                    $(document).find('.main-content .card-view-area').removeClass('list-view');
                    $(document).find('.main-content .card-view-area').addClass('grid-view');
                }
            })

            listBtn.on('click', function() {
                if ($(this).hasClass('active')) {
                    return true
                } else {
                    $(this).addClass('active');
                    $(this).siblings('.grid-view-btn').removeClass('active');
                }
                // grid & list view check
                if ($(document).find('.main-content .card-view-area').hasClass('grid-view')) {
                    $(document).find('.main-content .card-view-area').removeClass('grid-view');
                    $(document).find('.main-content .card-view-area').addClass('list-view');
                }
            })

            let min = 0;
            let max = 0;

            var orderBy = "1";


            $("#amount").val("{{ $general->cur_sym }}" + {{ $min }} + " - {{ $general->cur_sym }}" +
                {{ $max }});

            $("input[type='checkbox'][name='categories']").on('click', function() {
                var categories = [];
                var tags = [];

                $('.filter-by-category:checked').each(function() {
                    if (!categories.includes(parseInt($(this).val()))) {
                        categories.push(parseInt($(this).val()));
                    }
                });

                getFilteredData(min, max, categories, tags)
            });



            var categories = [];
            var tags = [];
            @if (@$subcategory)
                categories.push("{{ @$subcategory->category_id }}")
            @endif
            @if (@$category)
                categories.push("{{ @$category->id }}")
                $(`.filter-by-category[value="${categories[0]}"]`).prop('checked', true);
            @endif
            @if (@$wordsearch)
                let data = @json($wordsearch);
                console.log(data);
                data?.forEach(function(e) {
                    categories.push(e);
                    $(`.filter-by-category[value="${e}"]`).prop('checked', true);
                });
            @endif

            @if (@$tag)
                tags.push("{{ @$tag }}")
                $(`.filter-by-tag[value="${tags[0]}"]`).prop('checked', true);
            @endif
            $("body").on('click', "input[type='checkbox'][name='tags']", function(e) {
                $('.filter-by-tag:checked').each(function() {
                    if (!tags.includes($(this).val())) {
                        tags.push($(this).val());
                    }
                });
                if (!e.target.checked && tags.indexOf(e.target.value) != -1) {
                    tags.splice(tags.indexOf(e.target.value), 1)
                }

                getFilteredData(min, max, categories, tags)
            });

            function getFilteredData(min, max, categories, tags) {

                $.ajax({
                    type: "get",
                    url: "{{ route('product.filtered') }}",
                    data: {
                        "min": min,
                        "max": max,
                        "categories": categories,
                        'order_by': orderBy,
                        "tags": tags,
                        "search": "{{ request()->search }}",
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $('.single-search-form').addClass('animate-border');
                        $('.custome_loader').css('display', 'flex')
                    },
                    success: function(response) {
                        $('.custome_loader').css('display', 'none')
                        $('.minAmount').attr('placeholder', response.min);
                        $('.maxAmount').attr('placeholder', response.max);
                        $('.total_item').text(response.total_item);
                        $('.single-search-form').removeClass('animate-border');
                        $(".preloader").hide();
                        if (response.html) {
                            $('.main-content').html(response.html);
                        }
                        if (response.tags && response.tags.length > 0) {
                            let html = "";
                            $.each(response.tags, function(i, tag) {
                                html += `
                                <div class="form-check custom--checkbox">
                                    <input class="form-check-input filter-by-tag" ${tags.indexOf(tag) != -1 ? 'checked' : ''} name="tags" type="checkbox" value="${tag}"
                                    id="chekbox-tag-${i}">
                                    <label class="form-check-label" for="chekbox-tag-${i}">
                                        ${tag}
                                    </label>
                                 </div>
                                `;
                            });
                            $('.__tag_wrapper').html(html);
                        }
                        if (response.error) {
                            notify('error', response.error);
                        }
                    }
                });
            };

            $('.minAmount').on('keyup keypress', function(e) {
                min = $(this).val();
                if (e.which == 13) {
                    e.preventDefault();
                    getFilteredData(min, max, categories, tags);
                }

            });
            $('.maxAmount').on('keyup keypress', function(e) {
                max = $(this).val();
                if (e.which == 13) {
                    e.preventDefault();
                    getFilteredData(min, max, categories, tags);
                }
            });
            $('.priceFilter').on('click', function(e) {
                getFilteredData(min, max, categories, tags);
            });
            $('.orderBy').on('change', function(e) {
                orderBy = $(this).val();

                getFilteredData(min, max, categories, tags);
            });

        })(jQuery);
        const cars = [".minAmount", ".maxAmount", ".priceFilter", "input[type='checkbox'][name='categories']",
            "input[type='checkbox'][name='tags']"
        ];

        $("body").on('click', ".resetbutton", function(e) {

            $.ajax({
                type: "get",
                url: "{{ route('resetfilter.products') }}",
                beforeSend: function() {
                    $('.custome_loader').css('display', 'flex')
                },
                success: function(response) {
                    $('.custome_loader').css('display', 'none')
                    if (response.html) {
                        $('.main-content').html(response.html);
                        $('.total_item').text(response.products.data.length);
                    }
                    let html = "";
                    $.each(response.tags, function(i, tag) {
                        html += `
                                <div class="form-check custom--checkbox">
                                    <input class="form-check-input filter-by-tag" name="tags" type="checkbox" value="${tag}"
                                    id="chekbox-tag-${i}">
                                    <label class="form-check-label" for="chekbox-tag-${i}">
                                        ${tag}
                                    </label>
                                 </div>`;
                    });
                    console.log(html);
                    $('.__tag_wrapper').html(html);

                }
            });
            document.querySelectorAll('#ms-form input[type="checkbox"]:checked')?.forEach(element => {
                element.checked = false;
            });
            $('.orderBy').val("1");
            $('.minAmount').val('');
            $('.maxAmount').val('');
        });
    </script>
@endpush

<style>
    .priceFilter {
        background-color: #<?php echo $general->base_color; ?>;
        color: #fff;
    }

    .custome_loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: none;
        justify-content: center;
        align-items: center;
        height: 100%;
        z-index: 9999999;
        background: #ffffff52;
    }

    .custome_loader .spinner-border {
        width: 4rem;
        height: 4rem;
        border: 0.35em solid #<?php echo $general->base_color; ?>;
        border-right-color: transparent;
    }

    .form-control--sm {
        height: 45px !important;
    }
</style>
