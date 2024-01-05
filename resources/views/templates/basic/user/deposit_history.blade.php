@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="pb-100">
    @if ($partial)
    @include($activeTemplate . 'partials.dashboardHeader')
    @endif
    <div class="dashboard-area pt-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-end">
                        <a class="btn btn-sm btn--base" href="{{ route('user.deposit') }}">
                            <i class="las la-university fs-6"></i> @lang('Deposit Now')
                        </a>
                    </div>
                    <div class="table-responsive--md mt-4">

                        <table id='data-table' class="table table-bordered data-table custom--table">
                            <thead>
                                <tr>
                                    <th>@lang('Transaction ID')</th>
                                    <th>@lang('Gateway')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Time')</th>
                                    <th>@lang('More')</th>
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

</div>
@endsection


@push('script')
<script>
    var api = @json($api);
    var token = @json($token);

    let val = {
        ":token": token,
        ':api': api,
        '&amp;': "&"
    }

    "use strict";
    $(document).ready(function() {
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: api ? decodeURIComponent("{{ route('iframe.api.deposit.history', ['api' => ':api','token'=>':token']) }}").replace(/:token|:api|&amp;/gm, (m) => (val[m] ?? m)) : "{{ route('user.deposit.history') }}",
            columns: [{
                    data: 'trx',
                    name: 'trx'
                },
                {
                    data: 'method_code',
                    name: 'method_code'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ], // <-- Missing comma was added here
            "createdRow": function(row, data, dataIndex) {
                var modalHtml = `
        <div id="approveModal${dataIndex}" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item dark-bg">@lang('Amount') : ` + parseInt(data.amount) + ` $</li>
                        <li class="list-group-item dark-bg">@lang('Charge') : ` + parseInt(data.charge) + `$</li>
                        <li class="list-group-item dark-bg">@lang('After Charge') :` + (parseInt(data.amount) + parseInt(
                    data.charge)) + `$
                        </li>
                        <li class="list-group-item dark-bg">@lang('Conversion Rate') : ` + parseInt(data.rate) + `$</li>
                        <li class="list-group-item dark-bg">@lang('Payable Amount') :` + parseInt(data.final_amo) + `$ </li>
                    </ul>
                    <ul class="list-group withdraw-detail mt-1">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn-sm"
                        data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
    `;
                $('body').append(modalHtml);
                let x = row.querySelector('[data-bs-target="#approveModal"]');
                x?.setAttribute('data-bs-target', '#approveModal' + dataIndex);

            }
        });
    });
</script>
@endpush