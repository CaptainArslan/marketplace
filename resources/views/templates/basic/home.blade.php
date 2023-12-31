@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    <div class="hero bg_img"
        style="background-image: url({{ getImage('assets/images/frontend/banner/' . @$bannerContent->data_values->image, '1920x1500') }});">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-8 text-center">
                    <h2 class="hero__title mb-2 wow fadeInUp" data-wow-duration="0.5s" data-wow-delay="0.3s">
                        {{ __(@$bannerContent->data_values->heading) }}</h2>
                    <p class="wow fadeInUp text-white" data-wow-duration="0.5s" data-wow-delay="0.5s">
                        {{ __(@$bannerContent->data_values->sub_title) }}</p>
                </div>
            </div><!-- row end -->
            <div class="row justify-content-center mt-5">
                <div class="col-xl-7 col-lg-8">
                    <form class="hero-search-form wow fadeInUp" data-wow-duration="0.7s" data-wow-delay="0.7s"
                        method="GET" action="{{ route('product.search') }}">
                        <i class="las la-search icon"></i>
                        <input type="text" name="search" id="hero-search-field" class="form--control searchfield"
                            placeholder="@lang('e.g. php script')">
                        <button type="submit" class="hero-search-btn ">@lang('Search')</button>
                    </form>
                </div>
            </div><!-- row end -->
        </div>
    </div>

    <div class="category-area">
        <div class="container">
        <div class="category-wrapper">
                <h3 class="mb-4">@lang('Browse by categories')</h3>

                <div class="product-two-slider custom-arrow mt-5">

                    @foreach ($categories as $item)
                        <div class="single-slide">
                            <div>
                                <div class="category-item has-link">
                                    <a href="{{ route('category.search', [$item->id, str_slug($item->name)]) }}"
                                        class="item-link"></a>
                                    <div class="category-item__icon">
                                        <img src="{{ getImage(imagePath()['category']['path'] . '/' . $item->image, imagePath()['category']['size']) }}"
                                            alt="image">
                                    </div>
                                    <div class="category-item__content">
                                        <h6 class="caption">{{ __($item->name) }}</h6>
                                    </div>
                                </div><!-- category-item end -->
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
      
    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

    {{-- @if ($catsections != null)

        @foreach ($catsections as $cat)
            @php
                foreach ($cat as $c) {
                    $name = $c->category->name;
                    $id = $c->category->id;
                }
            @endphp
            @include($activeTemplate . 'sections.catsection', [
                'cat' => $cat,
                'name' => $name,
                'id' => $id,
            ])
        @endforeach
    @endif --}}

    @include($activeTemplate . 'sections.mostsold', [
        'catwithmostsold' => $catwithmostsold,
        '$catsections' => $catsections,
    ]);
<!--    <button type="button" class="btn btn-primary" onclick="getclicksetting()">-->
<!--  Launch hand shaking-->
<!--</button>-->
@endsection
@push('script')
    <script>
        function isNullEmptyBlank(str) {
            return str === null || str.match(/^ *$/) !== null;
        }

        $('body').on('click', '.searchedtext', function(e) {
            e.preventDefault();
            var value = $("searchfield").text();
            if (isNullEmptyBlank(value)) {
                alert("please enter the text");
            }


        });

    </script>
@endpush
