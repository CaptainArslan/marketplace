@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive--md mt-4">
                            <table  id='data-table' class="table table-bordered data-table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('Date')</th>
                                        <th>@lang('TRX')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Charge')</th>
                                        <th>@lang('Post Balance')</th>
                                        <th>@lang('Details')</th>
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
<script>
    $(document).ready(function() {
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.transaction') }}",
            columns: [{
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'trx',
                    name: 'trx'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'charge',
                    name: 'charge'
                },
                {
                    data: 'post_balance',
                    name: 'post_balance'
                },
                {
                    data: 'details',
                    name: 'details',

                },
            ], // <-- Missing comma was added here
        });
    });
</script>
@endpush
