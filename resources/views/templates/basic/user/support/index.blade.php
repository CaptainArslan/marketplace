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
                            <a class="btn-sm btn--base" href="{{ route('ticket.open') }}">
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
    <script>
        $(document).ready(function() {
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ticket') }}",
                columns: [{
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'seller_id',
                        name: 'seller_id'
                    },
                    {
                        data: 'last_reply',
                        name: 'last_reply'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
@endpush
