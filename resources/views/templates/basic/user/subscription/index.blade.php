@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="pb-100">
    @if ($partial)
    @include($activeTemplate . 'partials.dashboardHeader')
    @endif
    <section class="price-sec">
        <div class="container-fluid">
            <div class="container">
                <div class="row">
                    @foreach ($plans as $plan)
                    <div class="col-lg-3 col-md-6 col price-table">
                        <div class="card text-center">
                            <div class="title">
                                @if (!is_null($alreadybuy) && $alreadybuy->sub_id == $plan->id)
                                <i class="fa fa-bookmark"></i>
                                @else
                                <i class="fa fa-paper-plane"></i>
                                @endif

                                <h2>{{ $plan->name }}</h2>
                            </div>
                            <div class="price">
                                <h4><sup>$</sup>{{ $plan->price }}</h4>
                            </div>
                            <div class="option">
                                <ul>

                                    @if ($plan->plan_type == 1)
                                    <li><i class="fa fa-check"></i> Monthly Charges </li>
                                    @elseif($plan->id == 1)
                                    <li><i class="fa fa-check"></i> Free of Cost </li>
                                    @else
                                    <li><i class="fa fa-check"></i> One Time Payment</li>
                                    @endif
                                    @if ($plan->cf_status == 0)
                                    <li><i class="fa fa-times"></i>Custom field Support</li>
                                    @else
                                    <li><i class="fa fa-check"></i>Custom field Support</li>
                                    @endif
                                    @if ($plan->commission_type == 1)
                                    <li><i class="fa fa-check"></i> {{ $plan->commission }}% fee on each product
                                    </li>
                                    @else
                                    <li><i class="fa fa-check"></i>{{ $plan->commission }}$ fee on each Product
                                    </li>
                                    @endif
                                    <li><i class="fa fa-check"></i>{{ $plan->allowed_product }} products to Add.
                                    </li>
                                </ul>
                            </div>
                            @if (!is_null($alreadybuy))
                            @if ($plan->id != 1 && $alreadybuy->sub_id != $plan->id)
                            <a data-value={{ $plan }}data-ubalance={{ getAmount(auth()->user()->balance) }}class="buyBtn">@lang('Buy Now')</a>
                            @elseif($alreadybuy->sub_id == $plan->id)
                            {{-- <a data-value={{ $plan }}data-ubalance={{ getAmount(auth()->user()->balance) }}>@lang('Current Plan')</a> --}}
                            <a data-value={{ $plan }}data-ubalance={{ getAmount(auth()->user()->balance) }}class="cancelBtn">@lang('Cancel Plan')</a>
                            @else
                            @endif
                            @endif
                            @if (is_null($alreadybuy))
                            @if ($plan->id == 1)
                            <a data-value={{ $plan }} data-ubalance={{ getAmount(auth()->user()->balance) }}>@lang('Current Plan')</a>
                            @else
                            <a data-value={{ $plan }} data-ubalance={{ getAmount(auth()->user()->balance) }} class="buyBtn">@lang('Buy Now')</a>
                            @endif
                            @endif

                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="paymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('user.checkout.payment') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h4>@lang('Make Payment')</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">@lang('Select Method')</label>
                                    <input type="hidden" name=subscriptionid>
                                    <input type="hidden" name=subscription>
                                    <select name="wallet_type" class="form--control" required>
                                        <option value="own">
                                            @lang('Own Wallet') -
                                            {{ $general->cur_sym }}{{ getAmount(auth()->user()->balance) }}
                                        </option>
                                        <option value="online">@lang('Online Payment')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-md px-4 btn--danger" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn-md px-4 btn--base">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Cancell Subscription MODAL --}}
    <div id="cancelSubscription" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Subscription Cancel')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.cancelplan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to cancel the subscription it cannot be undone')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark closebutton" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
    @push('style')
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:300');
        @import url('https://fonts.googleapis.com/css?family=Domine:700');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif !important;
        }

        .price-sec {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding: 100px 0px;
            background-color: #eee;
        }

        .price-sec .ptables-head {
            font-family: 'Domine', serif;
            box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.3);
            padding: 30px 0;
            margin: 0px 0px 100px 0px;
            border-radius: 3px;
            background: linear-gradient(25deg, #feae3f 15%, transparent 0%),
                linear-gradient(-25deg, #f321d7 15%, transparent 0%),
                linear-gradient(-150deg, #64b5f6 15%, transparent 0%),
                linear-gradient(150deg, #f47 15%, transparent 0%);

        }

        @media all and (max-width:600px) {
            .ptables-head h1 {
                font-size: 30px;
            }
        }


        .price-sec .price-table {
            margin: 5px 0px;
        }

        .price-sec .price-table .card {
            position: relative;
            max-width: 300px;
            height: auto;
            min-height: 700px !important;
            background: linear-gradient(-45deg, #fe0847, #feae3f);
            border-radius: 15px;
            margin: 0 auto;
            padding: 40px 20px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, .5);
            transition: .5s;
            overflow: hidden;
        }

        .price-sec .price-table .card:hover {
            transform: scale(1.1);
        }

        .price-table:nth-child(1) .card,
        .price-table:nth-child(1) .card .title i {
            background: linear-gradient(-45deg, #f403d1, #64b5f6);

        }

        .price-table:nth-child(2) .card,
        .price-table:nth-child(2) .card .title i {
            background: linear-gradient(-45deg, #fe6c61, #f321d7);

        }

        .price-table:nth-child(3) .card,
        .price-table:nth-child(3) .card .title i {
            background: linear-gradient(-45deg, #24ff72, #9a4eff);

        }

        .price-table .card:before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40%;
            background: rgba(255, 255, 255, .1);
            z-index: 1;
            transform: skewY(-5deg) scale(1.5);

        }

        .price-table .title i {
            color: #fff;
            font-size: 60px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            text-align: center;
            line-height: 100px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, .2)
        }

        .price-table .title h2 {
            position: relative;
            margin: 20px 0 0;
            padding: 0;
            color: #fff;
            font-size: 28px;
            z-index: 2;
        }

        .price-table .price {
            position: relative;
            z-index: 2;
        }

        .price-table .price h4 {
            margin: 0;
            padding: 20px 0;
            color: #fff;
            font-size: 60px;

        }

        .option {
            position: relative;
            z-index: 2;
        }

        .option ul {
            margin: 0;
            padding: 0;

        }

        .option ul li {
            margin: 0 0 10px;
            padding: 0px 15px;
            list-style: none;
            color: #fff;
            font-size: 16px;
        }

        .card a {
            position: relative;
            z-index: 2;
            background: #fff;
            color: #000;
            width: 150px;
            height: 40px;
            line-height: 40px;
            display: block;
            text-align: center;
            margin: 20px auto 0;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 40px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, .2);

        }

        .card a:hover {
            text-decoration: none;
        }
    </style>
    @endpush
    @push('script')
    <script>
        "use strict";
        $('.cancelBtn').on('click', function() {
            var modal = $('#cancelSubscription');
            var subplan = $(this).attr('data-value');
            subplan = JSON.parse(subplan);
            console.log(subplan.id);
            $('input[name="id"]').val(subplan.id);
            modal.modal('show');
        });
        $('.buyBtn').on('click', function() {
            var modal = $('#paymentModal');
            var subplan = $(this).attr('data-value');
            var ubalance = $(this).attr('data-ubalance');
            subplan = JSON.parse(subplan);
            $('input[name="subscriptionid"]').val(subplan.id);
            $('input[name="subscription"]').val(1);
            if (ubalance > subplan.price) {
                $('option[value="own"]').prop("selected", true);
            } else {
                $('option[value="online"]').prop("selected", true);
            }
            modal.modal('show');
        });
    </script>
    @endpush