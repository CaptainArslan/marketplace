<!DOCTYPE html>

<html lang="en" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.seo')
    <!-- bootstrap 5  -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/bootstrap.min.css') }}">

    <!-- yajraa dataTables -->

    <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- line-awesome webfont -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/line-awesome.min.css') }}">

    <!-- image and videos view on page plugin -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/lightcase.css') }}">

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/animate.min.css') }}">
    <!-- custom select css -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/nice-select.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/select2.min.css') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- slick slider css -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
    <!-- dashdoard main css -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
    <!-- Custom css -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">

    <script src="https://kit.fontawesome.com/0bb027dfd0.js" crossorigin="anonymous"></script>

    <!-- site color -->
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php?color1=' . $general->base_color . '&color2=' . $general->secondary_color) }}">

    <style>
        .activate {
            display: table;
            background: #0a58ca;
            box-shadow: 0 4px 20px rgba(86, 40, 238, .15);
            line-height: 20px;
            padding: 12px;
            border-radius: 6px;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.3s ease, box-shadow 0.3s ease;
            margin-right: 12px;
        }

        .activate span {
            display: inline-block;
            vertical-align: top;
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            margin: 0 4px 0 0;
            position: relative;
            overflow: hidden;
        }

        .activate span:before {
            content: '';
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            background: #5628ee;
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .activate span svg {
            position: absolute;
            width: 12px;
            height: 12px;
            left: 50%;
            top: 50%;
            margin: -6px 0 0 -6px;
            z-index: 1;
        }

        .activate span svg:nth-child(1) {
            width: 20px;
            height: 20px;
            top: 0;
            left: 0;
            fill: none;
            margin: 0;
            stroke: #fff;
            stroke-width: 1px;
            stroke-dashoffset: 94.248;
            stroke-dasharray: 47.124;
        }

        .activate span svg:nth-child(2) {
            fill: #5628ee;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .activate span svg:nth-child(3) {
            fill: #5628ee;
            transform: translateY(20px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease;
        }

        #iframecheck {
            position: fixed;
            top: 50%;
            left: 50%;
            height: 300px;
            width: 500px;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        .activate:hover {
            box-shadow: 0 8px 24px rgba(86, 40, 238, .15);
        }

        .activate:hover span svg:nth-child(2) {
            transform: translateY(-20px);
        }

        .activate:hover span svg:nth-child(3) {
            transform: translateY(0);
        }

        .activate:active {
            transform: scale(0.94);
            box-shadow: 0 4px 16px rgba(63, 220, 117, .18);
        }

        .activate.loading span {
            background: none;
            transition: background 0.1s ease 0.3s;
        }

        .activate.loading span:before {
            transform: scale(1);
        }

        .activate.loading span svg:nth-child(1) {
            animation: turn 1.6s linear infinite forwards, path 1.6s linear infinite forwards;
        }

        .activate.loading span svg:nth-child(2) {
            transform: translateY(-20px);
        }

        .activate.loading span svg:nth-child(3) {
            opacity: 0;
            transform: translateY(0) scale(0.6);
        }

        .activate.loading div.ul {
            transform: rotateX(90deg);
        }

        .activate.loading.done {
            background: #3fdc75;
            box-shadow: 0 4px 20px rgba(63, 220, 117, .15);
        }

        .activate.loading.done span {
            background: #fff;
            transition: background 0.1s ease 0s;
        }

        .activate.loading.done span:before {
            background: #3fdc75;
            transform: scale(0);
        }

        .activate.loading.done span svg:nth-child(1) {
            animation: none;
        }

        .activate.loading.done span svg:nth-child(3) {
            fill: #3fdc75;
            opacity: 1;
            transform: scale(1);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s, opacity 0.4s ease 0.25s;
        }

        .activate.loading.done div.ul {
            transform: rotateX(180deg);
        }

        .activate div.ul {
            padding: 0;
            margin: 0;
            list-style: none;
            height: 20px;
            width: 70px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }

        .activate div.ul small {
            --rotateX: 0deg;
            backface-visibility: hidden;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 100%;
            transform-origin: 50% 50%;
            transform: rotateX(var(--rotateX)) translateZ(10px);
        }

        .activate:hover div.ul small {
            color: #f3f4f9 !important;
        }

        .activate div.ul small:nth-child(2) {
            --rotateX: -90deg;
        }

        .activate div.ul small:nth-child(3) {
            --rotateX: -180deg;
        }

        @keyframes turn {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes path {
            100% {
                stroke-dashoffset: 0;
            }
        }
    </style>

    @stack('style-lib')
    @stack('style')
</head>

<body>

    <script></script>

    <div class="preloader">
        <div class="preloader-container">
            <span class="animated-preloader"></span>
        </div>
    </div>

    <!-- scroll-to-top start -->
    <div class="scroll-to-top">
        <span class="scroll-icon">
            <i class="las la-angle-double-up"></i>
        </span>
    </div>
    <!-- scroll-to-top end -->

    <div class="page-wrapper">
        @if ($partial)
            @include($activeTemplate . 'partials.header')
        @endif
        
        @yield('content')
        @yield('customsetting')

        @if ($partial)
            @include($activeTemplate . 'partials.footer')
        @endif
    </div>

    @guest
    @php
    $cookie = App\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp

    @if (@$cookie->data_values->status && !session('cookie_accepted'))
    <div class="cookie-remove">
        <div class="cookie__wrapper">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <p class="txt my-2">
                        @php echo @$cookie->data_values->description @endphp<br>
                        <a href="{{ @$cookie->data_values->link }}" target="_blank" class="text--base mt-2">@lang('Read Policy')</a>
                    </p>
                    <button class="btn btn--base my-2 policy cookie">@lang('Accept')</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endguest


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- jQuery library -->
    <script src="{{ asset($activeTemplateTrue . 'js/jquery-3.5.1.min.js') }}"></script>
    <!-- bootstrap js -->
    <script src="{{ asset($activeTemplateTrue . 'js/bootstrap.bundle.min.js') }}"></script>
    <!-- lightcase plugin -->
    <script src="{{ asset($activeTemplateTrue . 'js/lightcase.js') }}"></script>
    <!-- custom select js -->
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.nice-select.min.js') }}"></script>
    {{-- <script src="{{ asset($activeTemplateTrue . 'js/select2.min.js') }}"></script> --}}
    <!-- Select2 -->
    <!-- slick slider js -->
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js" integrity="sha512-42PE0rd+wZ2hNXftlM78BSehIGzezNeQuzihiBCvUEB3CVxHvsShF86wBWwQORNxNINlBPuq7rG4WWhNiTVHFg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- scroll animation -->
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="https://cdn.tiny.cloud/1/uqh78k98mlo4sup356qjfnu025it3ivsd92c8d96i07rk159/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- dashboard custom js -->

    <script src="{{ asset($activeTemplateTrue . 'js/app.js') }}"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <!--End of Tawk.to Script-->
    @stack('script-lib')

    @stack('script')

    @include('partials.plugins')

    @include('partials.notify')
    <script>
        (function($) {
            "use strict";
            $('.cookie').on('click', function() {
                var url = "{{ route('cookie.accept') }}";
                $.get(url, function(response) {
                    if (response.success) {
                        notify('success', response.success);
                        $('.cookie-remove').html('');
                    }
                });
            });


        })(jQuery);
    </script>
    <script>
        $('body').on('click', '.crossnotify', function(e) {
            e.preventDefault();
            var value = $(this).attr('data-id');
            $.ajax({
                type: "get",
                url: "{{ route('user.notify.dell') }}",
                data: {
                    "id": value,
                },
                dataType: "json",
                success: function(response) {
                    if (response == "success") {
                        $('.notification[delNot=' + value + ']').closest('li').remove();
                        let count = $('.notifycount').text();
                        $('.notifycount').text(count - 1);
                        console.log("Deleted")
                        console.log("beforedel " + localStorage.oldcount)
                        if (localStorage.oldcount && localStorage.oldcount > 0) {
                            var oldcount = localStorage.oldcount;
                            localStorage.setItem("oldcount", oldcount - 1);
                            console.log("afterdel " + localStorage.oldcount)
                        }
                    } else {
                        alert("Something Went Wrong")
                    }
                }
            });


        });
        @if(session('copytext'))
        navigator.clipboard.writeText('{{ session('
            copytext ') }}').then(function() {
            console.log('copied');
        }, function() {
            console.log('failed');
        });
        @endif

        //Send the handshake request
        // const csrfToken = 'o1bCpi1dCNGLzVs5omCefzyeblVBHlxUFnPO2OGV';
        // console.log(csrfToken);
        // fetch("http://localhost/HTS/market_place/user/iframe-handshake", {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': csrfToken,
        //         },
        //         body: JSON.stringify({}),
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         // Handle the handshake response
        //         $('#exampleModal').modal("show");
        //         console.log(data);
        //     })
        //     .catch(error => {
        //         // Handle the handshake error
        //         console.error(error);
        //     });
    </script>

    <script>
        $(".select2-auto-tokenize").select2({
            tags: [],
            tokenSeparators: [",", " "]
        });

        $('.select2-basic').select2();
        $('.select2-multi-select').select2();
    </script>



    {!! $general->suggestion_box !!}
</body>

</html>