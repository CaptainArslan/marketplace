@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.breadcrumb')
    <!-- cart section start -->
    <section class="pt-100 pb-100">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-header">
                            <p>@lang('You have')
                                @php

                                    $itemscount = App\WishlistProduct::where('user_id', auth()->user()->id)->count();
                                @endphp
                                {{ $itemscount }} @lang('products in your Wishlist')</p>
                        </div>
                        <div class="card-body">
                            @forelse ($items as $item)
                                <div class="single-cart">
                                    <div class="single-cart__thumb">
                                        <a href="{{ route('product.details', [str_slug(__($item->product->name)), $item->product->id]) }}"
                                            class="d-block"><img
                                                src="{{ getImage(imagePath()['p_image']['path'] . '/thumb_' . $item->product->image, imagePath()['p_image']['size']) }}"
                                                alt="image"></a>
                                    </div>
                                    <div class="single-cart__content">
                                        <h6 class="single-cart__title"><a
                                                href="{{ route('product.details', [str_slug(__($item->product->name)), $item->product->id]) }}">{{ __($item->product->name) }}</a>
                                        </h6>
                                        <span class="fs-14px">@lang('Product by') - <a
                                                href="{{ route('username.search', strtolower($item->product->user->username)) }}"
                                                class="text-decoration-underline text--base"></a></span>
                                        <ul class="d-flex flex-wrap cart-feature-list mt-2">
                                            <li class="fs-12px">
                                                <span class="fw-bold">@lang('License') : </span>
                                                    <span>@lang('Regular Licences')<Span>
                                            </li>
                                            <li class="fs-12px">
                                                <span class="fw-bold">@lang('Support') : </span>
                                                @if ($item->support == 1)
                                                    <span>@lang('Yes')<Span>
                                                        @elseif($item->support == 0)
                                                            <span>@lang('No')<Span>
                                                @endif
                                            </li>
                                        </ul>

                                    </div>
                                    <div class="single-cart__price">
                                        <a href="javascript:void(0)" class="cart-row-delete mb-3" data-bs-toggle="modal"
                                            data-bs-target="#cartRemoveModal{{ $loop->index }}"><i
                                                class="las la-trash-alt"></i></a>
                                        <div class="price">{{ $general->cur_sym }}{{ getAmount($item->product->regular_price) }}
                                        </div>
                                    </div>
                                    <a
                                        href="{{ route('product.details', [str_slug(__($item->product->name)), $item->product->id]) }}"><button
                                            type="submit"
                                            class="btn btn-md btn--base justify-content-center text-center mt-3 w-100"><i
                                                class="las la-cart-arrow-down fs-5 me-2"></i>
                                            @lang('Add to Cart')</button></a>
                                    <div class="modal fade" id="cartRemoveModal{{ $loop->index }}"
                                        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                        aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('remove.wishlist', Crypt::encrypt($item->id)) }}"
                                                    method="GET">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h4>@lang('WishList Removal Confirmation')</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <p>@lang('Are you sure to remove this product from your wishList')?</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-md px-4 btn--danger"
                                                            data-bs-dismiss="modal">@lang('No')</button>
                                                        <button type="submit"
                                                            class="btn btn-md px-4 btn--base">@lang('Yes')</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- single-cart end -->
                            @empty
                                <h5>@lang('No product in your wishlist')</h5>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- cart section end -->

    <div class="modal fade" id="loginMessageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4 text-center">
                    <h4 class="text-danger">@lang('Login Required'):(</h4>
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <hr>
                            <p>@lang('Make sure that you are loggin to our system')</p>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn--base w-100" data-bs-dismiss="modal">@lang('Ok, got it')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
