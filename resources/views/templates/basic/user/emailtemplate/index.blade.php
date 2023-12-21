@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        @include($activeTemplate . 'user.customsetting')
    @endsection
    @section('customsetting')
        <div class="dashboard-area pt-50">
            <div class="container">

                <div class="row">
                    <div class="col-lg-12">
                        {{-- @if (isset($warning) && $warning == 1)
                            <div class="alert alert-danger">
                                <strong>Attention Please!</strong> Please Upgrade the Plan To add the CustoM Setting.
                            </div>
                        @else --}}
                        <div class="text-end">
                            @if (is_null(findcustomtemplate(auth()->user()->id)))
                                <a class="btn btn-sm btn--base" href="{{ route('user.emailtemplate.new') }}">
                                    <i class="las la-plus-circle fs-6"></i> @lang('Add New')
                                </a>
                            @endif
                        </div>
                        <div class="table-responsive--md mt-4">

                            <table id='data-table' class="table table-bordered data-table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('TemplateName')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @forelse ($emailtemplates as $template)
                                        <tr>
                                            <td data-label="@lang('Name')">{{ __($template->name) }}</td>
                                            <td data-label="@lang('Status')">
                                                @if ($template->status == 1)
                                                    <span
                                                        class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                                @else
                                                    <span
                                                        class="text--small badge font-weight-normal badge--warning">@lang('Disabled')</span>
                                                @endif
                                            </td>
                                            <td data-label="@lang('Action')">
                                                <a href="{{ route('user.emailtemplate.edit', $template->id) }}"
                                                    class="icon-btn bg--primary"><i class="las la-edit"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="@lang('Update')"></i></a>
                                                <a href="javascript:void(0)" class="icon-btn bg--danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $loop->index }}"><i
                                                        class="lar la-trash-alt" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="@lang('Delete')"></i></a>
                                                @if ($template->status == 0)
                                                    <a href="javascript:void(0)" class="icon-btn bg--danger activateBtn"
                                                        data-bs-toggle="modal" data-bs-target="#activateModal"
                                                        data-info="{{ $emailtemplates }}" data-id="{{ $template->id }}"
                                                        data-original-title="@lang('Enable')"><i
                                                            class="la la-eye"></i></a>
                                                @else
                                                    <a href="javascript:void(0)" class="icon-btn bg--success deactivateBtn"
                                                        data-bs-toggle="modal" data-bs-target="#deactivateModal"
                                                        data-info="{{ $emailtemplates }}" data-id="{{ $template->id }}"
                                                        data-original-title="@lang('Disable')"><i
                                                            class="la la-eye"></i></a>
                                                @endif
                                            </td> --}}
                                    {{-- Delete Model --}}
                                    {{-- <div id="deleteModal{{ $loop->index }}" class="modal fade"
                                                data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                aria-labelledby="staticBackdropLabel" aria-hidden="true">>
                                                <div class="modal-dialog">
                                                    <form action="{{ route('user.emailtemplate.delete') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="emailtemplate_id"
                                                            value="{{ $template->id }}" required>
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">@lang('Delete  Emailt template')</h5>
                                                                <button type="button" class="close"
                                                                    data-bs-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>@lang('Are you sure you wnt to delete this Template?')</p>
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
                                            </div>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="100%">{{ __($empty_message) }}</td>
                                        </tr>
                                    @endforelse --}}
                                </tbody>
                            </table>
                            {{--
                            <div class="pagination--sm justify-content-end">
                                {{ $emailtemplates->links() }}
                            </div> --}}
                        </div>
                    </div>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        "use strict";



        $(document).ready(function() {
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.emailtemplate') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
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
                    var dellModal = `<div id="deleteModal${data.id}" class="modal fade" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                        aria-hidden="true">>
                                        <div class="modal-dialog">
                                            <form action="{{ route('user.emailtemplate.delete') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="emailtemplate_id" value="${data.id }}"
                                                    required>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">@lang('Delete Email Template')</h5>
                                                        <button type="button" class="close"
                                                            data-bs-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>@lang('Are you sure you wnt to delete Email Template?')</p>
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
                    <h5 class="modal-title">@lang('EmailTemplate Activation Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.emailtemplate.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="${data.id}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to activate this Emailtemplate?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark closebutton"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Activate')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>`;
                    $('body').append(activeModal);

                    // {{-- DEACTIVATE METHOD MODAL --}}
                    var deactiveModal = `<div id="deactivateModal${data.id}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-title${data.id}" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title${data.id}">@lang('Confirm to  Disable Email template')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('user.emailtemplate.deactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="${data.id}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to disable this Email template')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark closebutton"
                            data-bs-dismiss="modal" >@lang('Close')</button>
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
