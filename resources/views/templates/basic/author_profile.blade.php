 @extends($activeTemplate . 'layouts.frontend')
 @section('content')
     <section class="pb-100">
         <div class="user-area py-4">
             <div class="container">
                 <div class="row">
                     <div class="col-sm-8">
                         <div class="user-wrapper">
                             <div class="thumb">
                                 <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . $user->image, imagePath()['profile']['user']['size']) }}"
                                     alt="@lang('image')">
                             </div>
                             <div class="content">
                                 <h4 class="name">{{ $user->username }}</h4>
                                 <p class="fs-14px">@lang('Member since') {{ showDateTime($user->created_at, 'F, Y') }}</p>
                             </div>
                         </div>
                     </div>
                     <div class="col-sm-4 text-end">
                         <div class="user-header-status">
                             <div class="left">
                                 <span>@lang('Author Rating')</span>
                                 <div class="ratings">
                                     @php echo displayRating($user->avg_rating) @endphp
                                     ({{ $user->total_response }} @lang('Ratings'))
                                 </div>
                             </div>
                             <div class="right">
                                 <span>@lang('Sales')</span>
                                 <h4>{{ $totalSell }}</h4>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="container pt-50">
             <div class="row gy-5">
                 <div class="col-lg-8">
                     <div class="portfolio-single">
                         <div class="portforlio-single-thumb">
                             <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . $user->cover_image, imagePath()['profile']['cover']['size']) }}"
                                 alt="@lang('image')">
                         </div>
                         <div class="portforlio-single-content">
                             @php echo $user->description; @endphp
                         </div>
                     </div>
                     <div class="main-content">
                         <div class="row gy-3">
                             @forelse ($products as $item)
                                 <div class="col-xl-4 col-md-4">
                                     <div class="product-card style--three p-0">
                                         @if ($item->featured == 1)
                                             <span class="tending-badge"><i class="las la-bolt"></i></span>
                                         @endif
                                         <div class="product-card__thumb">
                                             <a href="{{ route('product.details', [str_slug(__($item->name)), $item->id]) }}"><img
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
                     </div>
                 </div>
                 <div class="col-lg-4">
                     <div class="product-widget">
                         <div class="author-widget">
                             <div class="thumb">
                                 <img src="{{ getImage(imagePath()['level']['path'] . '/' . $user->levell->image, imagePath()['level']['size']) }}"
                                     alt="@lang('image')">
                             </div>
                             <div class="content">
                                 <h5 class="author-name"><a href="#0">{{ $user->levell->name }}</a></h5>
                                 <span class="txt"><a
                                         href="{{ route('author.products', $user->username) }}">@lang('Total Products') :
                                         {{ $totalProduct }}</a></span>
                             </div>
                         </div>
                         <ul class="author-badge-list w-100 border-top mt-3 pt-3">
                             @foreach ($levels as $key => $item)
                                 @if ($key + 1 <= $user->level_id)
                                     <li>
                                         <img src="{{ getImage(imagePath()['level']['path'] . '/' . $item->image, imagePath()['level']['size']) }}"
                                             alt="@lang('image')">
                                     </li>
                                 @endif
                             @endforeach
                         </ul>
                     </div>
                     <div class="product-widget mt-4">
                         <h5 class="title border-bottom mb-3 pb-3">@lang('Email to') {{ $user->username }}</h5>
                         @auth
                             @if ($user->id != auth()->user()->id)
                                 <form action="{{ route('user.email.author') }}" method="POST">
                                     @csrf
                                     <input type="hidden" name="author" value="{{ $user->username }}">
                                     <div class="form-group mb-3">
                                         <input type="text" class="form-control form--control"
                                             value="{{ auth()->user()->email }}" disabled>
                                     </div>
                                     <div class="form-group mb-3">
                                         <textarea name="message" class="form-control form--control border" placeholder="@lang('Your Message')" required>{{ old('message') }}</textarea>
                                     </div>
                                     <button type="submit" class="btn btn--base w-100">@lang('Send Email')</button>
                                 </form>
                             @else
                                 @lang('This is your own profile')
                             @endif
                         @else
                             @lang('Please') <a href="{{ route('user.login') }}" class="text--base">@lang('sign in')</a>
                             @lang('to contact this author').
                         @endauth
                     </div>
                     <div class="product-widget mt-4">
                         <button class="action-sidebar-open"><i class="las la-sliders-h"></i> @lang('Filter')</button>
                         <form action="" id="ms-form">
                             <div class="action-sidebar">
                                 <button class="action-sidebar-close" type="button"><i class="las la-times"></i></button>
                                 <div class="action-widget top-widget">
                                     <h4 class="action-widget__title">@lang('Filter & Refine')</h4>
                                     <!-- <hr> -->
                                 </div><!-- action-widget end -->
                                 <button type="button" data-id="{{ $user->id }}"
                                     class="restbutton text-center btn btn-primary btn-sm d-block w-50 mt-3">@lang('Reset')</button>
                                 <div class="action-widget mt-4">
                                     <h6 class="action-widget__title">@lang('Categories')</h6>
                                     <div class="action-widget__body">
                                         @foreach ($categories1 as $id => $value)
                                             <div class="form-check custom--checkbox">
                                                 <input class="form-check-input filter-by-category" name="categories"
                                                     type="checkbox" data-id="{{ $user->id }}"
                                                     value="{{ $id }}" id="chekbox-{{ $loop->index }}"
                                                     @if (isset($subcategory) && $id == $subcategory->category_id) checked @endif>
                                                 <label class="form-check-label" for="chekbox-{{ $loop->index }}">
                                                     {{ __($value) }}
                                                 </label>
                                             </div><!-- form-check end -->
                                         @endforeach

                                     </div>
                                 </div><!-- action-widget css end -->
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </section>
 @endsection
 @push('script')
     <script>
         $("input[type='checkbox'][name='categories']").on('click', function() {
             var categories = [];
             var id = $(this).attr('data-id');
             $('.filter-by-category:checked').each(function() {
                 if (!categories.includes(parseInt($(this).val()))) {
                     categories.push(parseInt($(this).val()));
                 }
             });
             getFilteredData(categories, id)
         });

         function getFilteredData(categories = null, id) {

             $.ajax({
                 type: "get",
                 url: "{{ route('product.categoryfilter') }}",
                 data: {
                     "categories": categories,
                     "id": id,
                 },
                 dataType: "json",
                 beforeSend: function() {
                     $('.custome_loader').css('display', 'flex')
                 },
                 success: function(response) {
                     $('.custome_loader').css('display', 'none')
                     $(".preloader").hide();
                     if (response.html) {
                         $('.main-content').html(response.html);
                     }
                     if (response.error) {
                         notify('error', response.error);
                     }
                 }
             });
         };
         $(".restbutton").on('click', function() {
             var id = $(this).attr('data-id');
             document.querySelectorAll('#ms-form input[type="checkbox"]:checked')?.forEach(element => {
                 element.checked = false;
             });
             getFilteredData(null, id)


         });
     </script>
 @endpush
