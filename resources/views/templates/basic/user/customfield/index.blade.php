@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="pb-100">
    @if ($partial)
    @include($activeTemplate . 'partials.dashboardHeader')
    @endif
    @include($activeTemplate . 'user.customsetting')
    @endsection
    @section('customsetting')
    <div class="dashboard-area pt-50">
        <div class="{{ $partial ? 'container' : 'container-fluid' }}">
            <div class="row">
                <div class="col-lg-12">
                    @if (isset($warning) && $warning == 1)
                    <div class="alert alert-danger">
                        <strong>Attention Please!</strong> Please Upgrade the Plan To add the Custom Setting.
                    </div>
                    @else
                    <div class="text-end">
                        <a class="btn btn-sm btn--base" href="{{ route('user.customfield.new') }}">
                            <i class="las la-plus-circle fs-6"></i> @lang('Add New')
                        </a>
                    </div>
                    <div class="mt-4">
                        <table id='data-table' class="table table-bordered data-table custom--table responsive w-100 display">
                            <thead>
                                <tr>
                                    <th>@lang('FieldName')</th>
                                    <th>@lang('FieldType')</th>
                                    <th>@lang('Fieldoptions')</th>
                                    <th>@lang('Labels')</th>
                                    <th>@lang('Placeholder')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script>
    "use strict";

    var api = @json($api);
    var token = @json($token);

    let val = {
        ":token": token,
        ':api': api,
        '&amp;': "&"
    }

    $(document).ready(function() {
        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: api ? decodeURIComponent("{{ route('iframe.api.allCustomfield', ['api' => ':api','token'=>':token']) }}").replace(/:token|:api|&amp;/gm, (m) => (val[m] ?? m)) : "{{ route('user.allCustomfield') }}",
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'type',
                    name: 'type'
                }, {
                    data: 'fieldoption',
                    name: 'fieldoption'
                }, {
                    data: 'labels',
                    name: 'labels'
                },
                {
                    data: 'placeholder',
                    name: 'placeholder'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "createdRow": function(row, data, dataIndex) {
                var modalHtml = `
            <div id="approveModal${data.id}" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item dark-bg">@lang('Name') :${data.name}</li>
                        <li class="list-group-item dark-bg">@lang('Type') :${data.type}</li>
                        <li class="list-group-item dark-bg">@lang('Placeholder') :${data.placeholder}
                        </li>`;
                if (data.status === '1') {
                    modalHtml += `<li class="list-group-item dark-bg">Status : Active<li>`;
                } else if (data.status === 0) {
                    modalHtml += `<li class="list-group-item dark-bg">Status : Disabled<li>`;
                }

                modalHtml += `</ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn-sm"
                        data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>`;
                $('body').append(modalHtml);

                var dellModal = `<div id="deleteModal${data.id}" class="modal fade" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                        aria-hidden="true">>
                                        <div class="modal-dialog">
                                            <form action="{{ route('user.customfield.delete') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="customfield_id" value="${data.id }}"
                                                    required>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">@lang('Delete CustomField')</h5>
                                                        <button type="button" class="close"
                                                            data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>@lang('Are you sure you wnt to delete this CustomField?')</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn--primary btn-sm"
                                                            data-bs-dismiss="modal">@lang('Close')</button>
                                                        <button type="submit"
                                                            class="btn btn--danger btn-sm">@lang('Delete')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>`;

                $('body').append(dellModal);
                //  {{-- ACTIVATE METHOD MODAL --}}
                var activeModal = `<div id="activateModal${data.id}" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('CustomField Activation Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.customfield.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="${data.id}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to activate this CustomField?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark closebutton"
                            data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Activate')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>`;
                $('body').append(activeModal);

                // {{-- DEACTIVATE METHOD MODAL --}}
                var deactiveModal = `<div id="deactivateModal${data.id}" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('CustomField Disable Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.customfield.deactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="${data.id}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to disable this Customfield')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark closebutton"
                            data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Disable')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>`;
                $('body').append(deactiveModal);
            }
        });
    });
</script>
@endpush