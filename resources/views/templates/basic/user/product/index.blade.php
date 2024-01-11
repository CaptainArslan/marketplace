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
                    @if (isset($warning) && $warning == 1)
                    <div class="alert alert-danger">
                        <strong>Attention Please!</strong> Please Upgrade the Plan You Reached the Max limit of
                        allowed Products.
                    </div>
                    @endif
                    <div class="text-end  @if (isset($warning) && $warning == 1)  @endif">
                        <a class="btn btn-sm btn--base addnewBtn" data-id="{{ $warning ?? '' }}" href="{{ $api ? route('iframe.api.product.new').'?token='.request()->token : route('user.product.new') }}">
                            <i class="las la-plus-circle fs-6"></i> @lang('Add New')
                        </a>
                    </div>
                    <div class="table-responsive--md mt-4">

                        <table id='data-table' class="table table-bordered data-table custom--table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Subcategory')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Total Sell')</th>
                                    <th>@lang('Update Status')</th>
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

        var api = @json($api);
        var token = @json($token);

        let val = {
            ":token": token,
            ':api': api,
            '&amp;': "&"
        }


        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: api ? decodeURIComponent("{{ route('iframe.api.product.all', ['api' => ':api','token'=>':token']) }}").replace(/:token|:api|&amp;/gm, (m) => (val[m] ?? m)) : "{{ route('user.product.all') }}",
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category_id',
                    name: 'category_id'
                },
                {
                    data: 'sub_category_id',
                    name: 'sub_category_id'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'total_sell',
                    name: 'total_sell'
                },
                {
                    data: 'update_status',
                    name: 'update_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ], // <-- Missing comma was added here
            "createdRow": function(row, data, dataIndex) {
                var url = api ? decodeURIComponent("{{ route('iframe.api.product.delete', ['api' => ':api','token'=>':token']) }}").replace(/:token|:api|&amp;/gm, (m) => (val[m] ?? m)) : "{{ route('user.product.delete') }}";
                var modalHtml = `
                        <div id="deleteModal${dataIndex}" class="modal fade" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true" d-none>
                            <div class="modal-dialog">
                                <form  action="${url}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="${data.encrupted_id}" required>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">@lang('Delete Product')</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>@lang('Are you sure you want to delete this product?')</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn--primary btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                                            <button type="submit" class="btn btn--danger btn-sm">@lang('Delete')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `;
                $('body').append(modalHtml);
                // Add a data attribute to the row with the ID of the associated data
                $(row).attr('data-id', data.id);

                // Add a click event listener to the row that displays an alert with the associated data
                let x = row.querySelector('[data-bs-target="#deleteModal"]');
                x?.setAttribute('data-bs-target', '#deleteModal' + dataIndex);
            }
        });
    });
    $('body').on('click', '.addnewBtn', function(e) {
        var warning = $(this).attr('data-id');
        console.log(warning);
        if (warning == 1) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "Please Upgrade the Plan You Reached the Max limit of allowed Products",
            })
        }
    });
</script>
@endpush