@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="pb-100">
    @include($activeTemplate . 'partials.dashboardHeader')
    <div class="dashboard-area pt-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-end">
                        @php
                        $url = $partial ? route('ticket.open') : route('iframe.api.ticket.open', ['token' => request()->token]) ;
                        @endphp
                        <a class="btn-sm btn--base" href="{{ $url }}">
                            <i class="las la-plus-circle fs-6"></i> @lang('Create Ticket')
                        </a>
                    </div>
                    <div class="table-responsive--md mt-4">

                        <table id='data-table' class="table table-bordered data-table custom--table">
                            <thead>
                                <tr>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Submitted by ')</th>
                                    <th>@lang('Submitted to ')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')

@endpush