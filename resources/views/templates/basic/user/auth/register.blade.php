@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $authenticationContent = getContent('authentication.content', true);
        $policyElements = getContent('policy.element', false);
        $topAuthors = \App\User::where('status', 1)
            ->where('top_author', 1)
            ->limit(12)
            ->get(['image', 'username']);
        $footerContent = getContent('footer.content', true);
    @endphp

    <!-- account section start -->
    <div class="account-area style--two">
        <div class="account-area-bg bg_img"
            style="background-image: url({{ getImage('assets/images/frontend/authentication/' . @$authenticationContent->data_values->image, '1920x1080') }});">
        </div>
        <div class="account-area-left style--two">
            <div class="account-area-left-inner">
                <div class="text-center mb-5">
                    <span class="subtitle text--base fw-bold border-left">@lang('Welcome to')
                        {{ __($general->sitename) }}</span>
                    <h2 class="title text-white">@lang('Create an Account')</h2>
                    <p class="fs-14px text-white mt-4">@lang('Already you have an account?')</p><a href="{{ route('user.login') }}"
                        class="text--base"><button type="button" class="btn btn-primary">Sign in Now</button></a>
                </div>

                @if (count($topAuthors) > 0)
                    <h5 class="text-white text-center mt-5 mb-3">@lang('Our Top Authors')</h5>
                    <div class="top-author-slider">
                        @foreach ($topAuthors as $item)
                            <div class="single-slide">
                                <a href="{{ route('username.search', strtolower($item->username)) }}" class="s-top-author">
                                    <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . $item->image, imagePath()['profile']['user']['size']) }}"
                                        alt="image">
                                </a>
                            </div>
                        @endforeach

                    </div><!-- top-author-slider end -->
                @endif
            </div>
        </div>

        <div class="account-wrapper style--two">
            <div class="account-logo text-center">
                <a class="site-logo" href="{{ route('home') }}"><img
                        src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="@lang('site-logo')"></a>
            </div>
            <form class="account-form" action="{{ route('user.register') }}" method="POST"
                onsubmit="return submitUserForm();">
                @csrf
                @if (session()->get('reference') != null && $general->referral_system)
                    <h6 class="text-white text-center mb-3">@lang('Referred By'): {{ session()->get('reference') }}</h6>
                @endif
                <div class="row">
                    <div class="form-group col-lg-6">
                        <label class="text-white">@lang('First Name') <sup class="text--danger">*</sup></label>
                        <div class="custom-icon-field">
                            <i class="las la-user fs-4"></i>
                            <input type="text" name="firstname" value="{{ old('firstname') }}" autocomplete="off"
                                placeholder="@lang('Enter first name')" class="form--control" required>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label class="text-white">@lang('Last Name') <sup class="text--danger">*</sup></label>
                        <div class="custom-icon-field">
                            <i class="las la-user fs-4"></i>
                            <input type="text" name="lastname" value="{{ old('lastname') }}" autocomplete="off"
                                placeholder="@lang('Enter last name')" class="form--control" required>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="username" class="text-white">{{ __('Username') }}</label>
                        <div class="custom-icon-field">
                            <i class="las la-user fs-4"></i>
                            <input id="username" type="text" class="form--control checkUser" name="username"
                                value="{{ old('username') }}" placeholder="@lang('Enter username')" required>
                            <small class="text-danger usernameExist"></small>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="email" class="text-white">@lang('E-Mail Address')</label>
                        <div class="custom-icon-field">
                            <i class="las la-envelope fs-4"></i>
                            <input id="email" type="email" class="form--control checkUser" name="email"
                                value="{{ old('email') }}" placeholder="@lang('Enter email address')" required>
                        </div>
                    </div>



                    <div class="form-group col-lg-6">
                        <label class="text-white" for="country">{{ __('Country') }}</label>
                        <div class="custom-icon-field">
                            <i class="las la-globe fs-4"></i>
                            <select name="country" id="country" class="form--control">
                                @foreach ($countries as $key => $country)
                                    <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}"
                                        data-code="{{ $key }}">{{ __($country->country) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="mobile" class="text-white">@lang('Mobile')</label>
                        <div class="custom-icon-field custom-icon-field--style">

                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text mobile-code"></span>
                                    <input type="hidden" name="mobile_code">
                                    <input type="hidden" name="country_code">
                                </div>
                                <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}"
                                    class="form--control checkUser" placeholder="@lang('Your Phone Number')">
                            </div>
                            <small class="text-danger mobileExist"></small>
                        </div>
                    </div>

                    <div class="form-group col-lg-6 hover-input-popup">
                        <label for="password" class="text-white">@lang('Password')</label>
                        <div class="custom-icon-field">
                            <i class="las la-key fs-4"></i>
                            <input id="password" type="password" class="form--control" name="password"
                                placeholder="@lang('Enter password')" required>
                            @if ($general->secure_password)
                                <div class="input-popup">
                                    <p class="error lower">@lang('1 small letter minimum')</p>
                                    <p class="error capital">@lang('1 capital letter minimum')</p>
                                    <p class="error number">@lang('1 number minimum')</p>
                                    <p class="error special">@lang('1 special character minimum')</p>
                                    <p class="error minimum">@lang('6 character password')</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label class="text-white">@lang('Confirm Password') <sup class="text--danger">*</sup></label>
                        <div class="custom-icon-field">
                            <i class="las la-key fs-4"></i>
                            <input type="password" name="password_confirmation" placeholder="@lang('Re-enter password')"
                                class="form--control" required>
                        </div>
                    </div>

                    @if ($general->agree)
                        <div class="form-group col-lg-12">
                            <input type="checkbox" id="agree" name="agree">
                            <span class="text-white d-inline" for="agree">@lang('I agree with')
                                @foreach ($policyElements as $item)
                                    <a class="text--base"
                                        href="{{ route('policy', [$item->id, str_slug($item->data_values->heading)]) }}">{{ __(@$item->data_values->heading) }}</a>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </span>
                        </div>
                    @endif

                    <div class="form-group google-captcha col-lg-12">
                        @php echo recaptcha() @endphp
                    </div>

                    <div class="form-group col-lg-12">
                        @include($activeTemplate . 'partials.custom-captcha')
                    </div>
                    <div>
                        <div class="col">
                            <button type="submit" class="btn btn--base w-100">@lang('Sign up now')</button>
                        </div>
                    </div>
            </form>

            <div class="account-footer text-center">
                <span class="text-white">{{ __(@$footerContent->data_values->copyright) }}</span>
            </div>
        </div>
    </div>
    <!-- account section end -->
@endsection

@push('script')
    <script>
        "use strict";

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML =
                    '<span class="text-danger">@lang('Captcha field is required.')</span>';
                return false;
            }
            return true;
        }

        (function($) {
            @if ($mobile_code)
                $(`option[data-code={{ $mobile_code }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            @if ($general->secure_password)
                $('input[name=password]').on('input', function() {
                    secure_password($(this));
                });
            @endif

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response['data'] && response['type'] == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response['data'] != null) {
                        $(`.${response['type']}Exist`).text(`${response['type']} already exist`);
                    } else {
                        $(`.${response['type']}Exist`).text('');
                    }
                });
            });

        })(jQuery);

        $('.header, .footer-section').addClass('d-none');
    </script>
@endpush

@push('style')
    <style>
        .country-code .input-group-prepend .input-group-text {
            background: #fff !important;
        }

        .country-code select {
            border: none;
        }

        .country-code select:focus {
            border: none;
            outline: none;
        }

        .hover-input-popup {
            position: relative;
        }

        .hover-input-popup:hover .input-popup {
            opacity: 1;
            visibility: visible;
        }

        .input-popup {
            position: absolute;
            bottom: 130%;
            left: 50%;
            width: 280px;
            background-color: #1a1a1a;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
            opacity: 0;
            visibility: hidden;
            -webkit-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
        }

        .input-popup::after {
            position: absolute;
            content: '';
            bottom: -19px;
            left: 50%;
            margin-left: -5px;
            border-width: 10px 10px 10px 10px;
            border-style: solid;
            border-color: transparent transparent #1a1a1a transparent;
            -webkit-transform: rotate(180deg);
            -ms-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .input-popup p {
            padding-left: 20px;
            position: relative;
        }

        .input-popup p::before {
            position: absolute;
            content: '';
            font-family: 'Line Awesome Free';
            font-weight: 900;
            left: 0;
            top: 4px;
            line-height: 1;
            font-size: 18px;
        }

        .input-popup p.error {
            text-decoration: line-through;
        }

        .input-popup p.error::before {
            content: "\f057";
            color: #ea5455;
        }

        .input-popup p.success::before {
            content: "\f058";
            color: #28c76f;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
