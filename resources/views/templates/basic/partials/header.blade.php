<header class="header">
    <div class="header__top">
        <div class="container secondary_color">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-6">
                    <ul class="header-menu-list justify-content-md-start justify-content-center">
                        <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                        <li><a href="{{ route('all.products') }}">@lang('Products')</a></li>
                        @foreach ($pages as $k => $data)
                            <li>
                                <a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                            </li>
                        @endforeach
                        {{-- <li><a href="{{route('blogs')}}">@lang('Blogs')</a></li>
              <li><a href="{{route('contact')}}">@lang('Contact')</a></li> --}}
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 text-md-end">
                    <div class="d-flex flex-wrap align-items-center justify-content-md-end justify-content-center">
                        @if (auth()->user())
                            <a onclick="toggleCart()" class="menu-cart-btn me-3" title="notification">

                                <i class="las la-bell"></i>
                                <audio id="myAudio">
                                    <source src="{{ getImage(imagePath()['audio']['path'] . '/notify.mp3') }}"
                                        type="audio/mpeg">
                                </audio>

                                <span class="cart-badge notifycount">
                                    {{ getnotifycount() ?? 0 }}
                                </span>
                            </a>
                            <a href="{{ route('wishlists') }}" class="menu-cart-btn me-3" data-toggle=tooltip
                                title="wishList">

                                <i class="las la-heart"></i>
                                <span class="cart-badge">
                                    @php

                                        $itemscount = App\WishlistProduct::where('user_id', auth()->user()->id)->count();

                                    @endphp
                                    {{ $itemscount ?? 0 }}

                                </span>
                            </a>
                        @endif
                        <a href="{{ route('carts') }}" class="menu-cart-btn me-3" data-toggle=tooltip title="Cart">

                            <i class="las la-cart-arrow-down"></i>
                            <span class="cart-badge">
                                @php
                                    if (auth()->user()) {
                                        $ordersCount = auth()
                                            ->user()
                                            ->myOrder->count();
                                    } else {
                                        $ordersCount = App\Order::where('order_number', session()->get('order_number'))->count();
                                    }
                                @endphp
                                {{ $ordersCount }}
                            </span>
                        </a>
                        @auth
                            <button type="button" class="menu-cart-btn me-3" data-bs-toggle="tooltip"
                                title="Tooltip on bottom">
                                <span class="cart-badge">
                                    {{ $general->cur_sym }}{{ showAmount(auth()->user()->balance) }}
                                </span>
                            </button>
                        @endauth
                        <ul class="header-menu-list me-3">
                            @auth
                                <li>
                                    <div class="dropdown mb-1">
                                        <button class="btn btn-sm btn--base dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ auth()->user()->username }}
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('user.home') }}">@lang('Dashboard')</a></li>
                                            <li><a class="dropdown-item"
                                                    href="{{ route('user.change-password') }}">@lang('Change Password')</a></li>
                                            <li><a class="dropdown-item"
                                                    href="{{ route('user.profile-setting') }}">@lang('Profile Settings')</a></li>
                                            <li><a class="dropdown-item"
                                                    href="{{ route('user.twofactor') }}">@lang('2FA Security')</a></li>
                                            <li><a class="dropdown-item"
                                                    href="{{ route('user.logout') }}">@lang('Logout')</a></li>
                                        </ul>
                                    </div>
                                </li>
                            @else
                                {{ session()->put('last_location', url()->current()) }}
                                <li>
                                    <a href="{{ route('user.login') }}"><i class="las la-user"></i> @lang('Sign in')</a>
                                </li>
                                <li>
                                    <a href="{{ route('user.register') }}"><i class="las la-user-plus"></i>
                                        @lang('Sign up')</a>
                                </li>
                            @endauth
                        </ul>
                        {{-- <select name="site-language" class="laguage-select langSel">
                            @foreach ($language as $item)
                                <option value="{{ __($item->code) }}"
                                    @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}</option>
                            @endforeach
                        </select> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header__bottom" style="background:white">
        <div class="container">

            <nav class="navbar navbar-expand-xl p-0 align-items-center">
                <a class="site-logo site-title" href="{{ route('home') }}"><img
                        src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}"
                        alt="@lang('site-logo')"></a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="menu-toggle"></span>
                </button>

                <div class="collapse navbar-collapse mt-lg-0 mt-3" id="navbarSupportedContent">
                    <ul class="navbar-nav main-menu ms-auto">

                        @foreach ($categories as $key => $item)
                            @if ($key < 8)
                               <li class="menu_has_children" >
                                    <a href="javascript:void(0)" style="color:#095fe8">{{ __($item->name) }}</a>
                                    @if (count($item->subCategories) > 0)
                                        <ul class="sub-menu"
                                            style="max-height: 50vh; overflow-y:auto;flex-wrap: unset;">
                                            @foreach ($item->subCategories->where('status', 1) as $data)
                                                <li><a
                                                        href="{{ route('subcategory.search', [$data->id, str_slug($data->name)]) }}">{{ __($data->name) }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>

                            @endif
                        @endforeach
                    </ul>

                    <div class="nav-right">
                        <button class="header-serch-btn toggle-close"><i class="fa fa-search"></i></button>
                        <div class="header-top-search-area">
                            <form class="header-search-form" method="GET" action="{{ route('product.search') }}">
                                <input type="search" name="search" id="header_search" class="searchfield"
                                    placeholder="@lang('Search here')...">
                                <button class="header-search-btn" type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                    </div>

                </div>
            </nav>
        </div>
    </div><!-- header__bottom end -->


    @include('templates.basic.partials.notifications.main')
    <div class="modal fade sajjadiframe" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Iframe modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ route('viewofiframe') }}" frameborder="0" width="100%"
                        height="500px"></iframe>
                </div>
            </div>
        </div>
    </div>
    </div>
</header>
@push('script')
    <script>
        "use strict"

        function getclicksetting() {
            fetch("{{ route('user.Getacustomfield') }}").then(t => {
                return t.json();
            }).then(t => {
                if (t) {
                    parent.postMessage({
                        key: 'handshakecall',
                        result: t
                    }, '*');
                }
            }).catch(x => {
                console.log("Something Went wrong");
            });
        }

        function notifyclicklistener(e) {
            
            let data = e.data;
            if (typeof data == 'object') {
                if (data?.key) {
                    if (data.key == 'handshakecall') {
                        console.log(data);
                        if (data?.result) {
                            let result = data.result;
                            $('#exampleModal').modal("show");
                            console.log(data.result);

                        }
                    }

                }
                if (data?.name) {

                }
            }
        }

        window.addEventListener('message', notifyclicklistener, false);

        // let notificationworker = new Worker('{{ asset('assets/workers/notification.js') }}');
        // notificationworker.postMessage({
        //     key: 'init',
        //     url: "{{ route('user.notify.count') }}"
        // });
        // notificationworker.onmessage = messagelistener;


        let cLocation = location.href;
        if (cLocation.includes('dashboard')) {
            setTimeout(toggleCart, 1000);
            setTimeout(toggleCart, 3000);
        }
        notifylisteners();
        getNotifications();
    </script>
@endpush
