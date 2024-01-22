@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="pb-100">
        @if ($partial)
        @include($activeTemplate . 'partials.dashboardHeader')
        @endif
        <div class="dashboard-area pt-50">
            <div class="{{ $partial ? 'container' : 'container-fluid' }}">
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
        var api = @json($api);
        var token = @json($token);

        let val = {
            ":token": token,
            ':api': api,
            '&amp;': "&"
        }

        console.log(val);

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: api ?  decodeURIComponent("{{ route('iframe.api.user.transaction', ['api' => ':api','token'=>':token']) }}").replace(/:token|:api|&amp;/gm, (m) => (val[m] ?? m)) : "{{ route('user.transaction') }}",
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
