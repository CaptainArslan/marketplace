@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th scope="col">@lang('TRX') | @lang('Date')</th>
                                <th scope="col">@lang('User')</th>
                                <th scope="col">@lang('Level') | @lang('From')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('Description')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $data)
                                <tr>
                                    <td data-label="@lang('TRX') | @lang('Date')">
                                        <div class="font-weight-bold">{{__($data->trx)}}</div>
                                        {{showDateTime($data->created_at,'Y-m-d')}}
                                    </td>
                                    <td data-label="@lang('User')">
                                        <span class="font-weight-bold d-block">{{ @$data->user->fullname }}</span>

                                        <span class="small"> <a href="{{ route('admin.users.detail', @$data->user->id??0) }}"><span>@</span>{{ @$data->user->username }}</a> </span>
                                    </td>

                                    <td data-label="@lang('Level') | @lang('From')">
                                        <span class="font-weight-bold d-block">{{__(ordinal($data->level))}}</span>

                                        <span class="small"> <a href="{{ route('admin.users.detail', @$data->bywho->id??0) }}"><span>@</span>{{ @$data->bywho->username }}</a> </span>
                                    </td>
                                    <td data-label="@lang('Amount')">
                                        <span class="font-weight-bold d-block" data-toggle="tooltip" title="@lang('Transacted Amount') : {{__($general->cur_sym)}}{{getAmount($data->trx_amo)}}">{{__($general->cur_sym)}}{{getAmount($data->commission_amount)}}</span>

                                        <span data-toggle="tooltip" title="@lang('Percent')" class="font-weight-bold">{{ getAmount($data->percent) }}%</span>
                                    </td>
                                    <td data-label="@lang('Description')">
                                        {{__($data->title)}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ trans($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ $logs->links('admin.partials.paginate') }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')


    @if(request()->routeIs('admin.users.commission'))
        <form action="" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @else
        <form action="{{ route('admin.report.commission.search') }}" method="GET" class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>
    @endif

@endpush
