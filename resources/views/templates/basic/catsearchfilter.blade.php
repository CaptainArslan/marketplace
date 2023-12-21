                         <div class="row gy-3">
                             @forelse ($products as $item)
                                 <div class="col-xl-4 col-md-4">
                                     <div class="product-card style--three p-0">
                                         @if ($item->featured == 1)
                                             <span class="tending-badge"><i class="las la-bolt"></i></span>
                                         @endif
                                         <div class="product-card__thumb">
                                             <a
                                                 href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"><img
                                                     src="{{ getImage(imagePath()['p_image']['path'] . '/thumb_' . $item->image, imagePath()['p_image']['size']) }}"
                                                     alt="@lang('product-image')"></a>
                                         </div>

                                         <div class="product-card__content bg-white">
                                             <p class="mb-1">@lang('by') <a
                                                     href="{{ route('username.search', strtolower($item->user->username)) }}"
                                                     class="text--base">{{ __($item->user->username) }}</a>
                                                 @lang('in') <a
                                                     href="{{ route('subcategory.search', [$item->subcategory->id, str_slug($item->subcategory->name)]) }}"
                                                     class="text--base">{{ __($item->subcategory->name) }}</a></p>
                                             <h6 class="product-title mb-1"><a
                                                     href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}">{{ str_limit(__($item->name), 32) }}</a>
                                             </h6>
                                             <div class="product-card__meta">
                                                 <div class="left">
                                                     <h5 class="product-price mb-3">
                                                         {{ __($general->cur_sym) }}{{ getAmount($item->regular_price) }}
                                                     </h5>
                                                     <ul class="meta-list">
                                                         <li class="product-sale-amount"><i
                                                                 class="las la-shopping-cart text--base"></i> <span
                                                                 class="text--base">{{ $item->total_sell }}</span>
                                                             @lang('Sales')</li>
                                                     </ul>
                                                 </div>
                                                 <div class="right">
                                                     <a href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"
                                                         class="cart-btn"><i class="las la-shopping-cart"></i>
                                                         @lang('Purchase')</a>
                                                 </div>
                                             </div>
                                         </div>
                                     </div><!-- product-card end -->
                                 </div>
                             @empty
                                 <div class="col-xl-12 col-md-12 card-view">
                                     <div class="product-card">
                                         <h6 class="product-title mb-1">{{ __($empty_message) }}</h6>
                                     </div>
                                 </div>
                             @endforelse

                         </div><!-- row end -->
