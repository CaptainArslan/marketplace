@extends($activeTemplate.'layouts.frontend')
@section('content')

<div class="pb-100">
    @include($activeTemplate.'partials.dashboardHeader')
    <div class="dashboard-area pt-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-end">
                        <a href="{{ route('user.referral.users') }}" class="btn btn-sm btn--base"><i class="las la-list"> </i>@lang('My Referred Users')</a>
                    </div>

                    <div class="table-responsive--md mt-4">
                        <table class="table custom--table">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('From')</th>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Percent')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $data)
                                    <tr @if($data->amount < 0) class="halka-golapi" @endif>
                                        <td data-label="@lang('Date')">{{showDateTime($data->created_at,'d M, Y')}}</td>
                                        <td data-label="@lang('From')"><strong>{{@$data->bywho->username}}</strong></td>
                                        <td data-label="@lang('Level')">{{__(ordinal($data->level))}} @lang('Level')</td>
                                        <td data-label="@lang('Percent')">{{getAmount($data->percent)}} %</td>
                                        <td data-label="@lang('Amount')">{{__($general->cur_sym)}}{{getAmount($data->commission_amount)}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-right" colspan="100%">{{__($empty_message)}}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

