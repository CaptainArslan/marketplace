@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <a href="javascript:void(0)" class="btn btn-sm btn--primary box--shadow1 text--small addBtn mb-4"><i
                class="fa fa-fw fa-plus"></i>Add New</a>
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('Pkg Name')</th>
                                    <th scope="col">@lang('Price')</th>
                                    <th scope="col">@lang('Type')</th>
                                    <th scope="col">@lang('CustomField')</th>
                                    <th scope="col">@lang('Allowed Product')</th>
                                    <th scope="col">@lang('Discount')</th>
                                    <th scope="col">@lang('Commission Type')</th>
                                    <th scope="col">@lang('Commission')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody class="list">
                                @foreach ($plans as $plan)
                                    <tr>
                                        <td data-label="@lang('Name')">{{ $plan->name }}</td>
                                        <td data-label="@lang('Price')">
                                            {{ __($general->cur_text) }} {{ getAmount($plan->price) }}</td>
                                        @if ($plan->plan_type == 1)
                                            <td data-label="@lang('Plan type')">Monthly Plan</td>
                                        @else
                                            <td data-label="@lang('Plan type')">One Time Plan</td>
                                        @endif
                                        @if ($plan->cf_status == 0)
                                            <td data-label="@lang('Cf Status')"><span
                                                    class="badge badge--danger">@lang('No')</span></td>
                                        @else
                                            <td data-label="@lang('Cf Status')"><span
                                                    class="badge badge--success">@lang('Yes')</span></td>
                                        @endif
                                        <td data-label="@lang('Allowed Poduct')">{{ $plan->allowed_product }}</td>
                                        <td data-label="@lang('Discount')">{{ $plan->discount }}</td>
                                        @if ($plan->commission_type == 1)
                                            <td data-label="@lang('Commission Type')">Percentage</td>
                                            <td data-label="@lang('Commision type')">{{ $plan->commission }} %</td>
                                        @else
                                            <td data-label="@lang('Commission Type')">Fixed</td>

                                            <td data-label="@lang('Commision type')">{{ __($general->cur_text) }}
                                                {{ $plan->commission }}</td>
                                        @endif
                                        @if ($plan->status == 0)
                                            <td data-label="@lang('Status')"><span
                                                    class="badge badge--danger">@lang('Disabled')</span></td>
                                        @else
                                            <td data-label="@lang('Status')"><span
                                                    class="badge badge--success">@lang('Active')</span></td>
                                        @endif
                                        <td data-label="@lang('Action')">

                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                class="icon-btn bg--primary editBtn" data-id="{{ $plan->id }}"
                                                data-value="{{ $plan }}"><i class="editplan las la-edit"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="@lang('Click for edit')"></i></a>
                                            @if ($plan->status == 0)
                                                <button type="button" class="icon-btn btn--success ml-1 activateBtn"
                                                    data-toggle="modal" data-target="#activateModal"
                                                    data-id="{{ $plan->id }}" data-original-title="@lang('Enable')">
                                                    <i class="la la-eye"></i>
                                                </button>
                                            @else
                                                <button type="button" class="icon-btn btn--danger ml-1 deactivateBtn"
                                                    data-toggle="modal" data-target="#deactivateModal"
                                                    data-id="{{ $plan->id }}" data-original-title="@lang('Disable')">
                                                    <i class="la la-eye-slash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <div class="card-footer py-4">
                    {{ $products->links('admin.partials.paginate') }}
                </div> --}}
            </div>
        </div>
    </div>
    {{-- Add METHOD MODAL --}}
    <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Add New Plan')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.setplan.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Name')</label>
                                                <input class="form-control" type="text" name="name">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Price')</label>
                                                <input name="price" class="form-control" type="number"
                                                    placeholder="@lang('Enter Price')" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Select Type')</label>
                                                <select name="plantype" class="form-control" required>
                                                    <option value="1">@lang('Monthly')</option>
                                                    <option value="2">@lang('OnTime')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Allowed Custom Field')</label>
                                                <select name="customfield" class="form-control" required>
                                                    <option value="0">@lang('No')</option>
                                                    <option value="1">@lang('Yes')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Number of Allowed Products')</label>
                                                <input name="productallowed" class="form-control" type="number"
                                                    placeholder="@lang('Enter Number of Products')"required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('First Time Discount')</label>
                                                <input name="discount" class="form-control" type="number"
                                                    placeholder="@lang('Discount percentage ')"required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Select Commission Type')</label>
                                                <select name="commisiontype" class="form-control commissiontype" required>
                                                    <option value="1">@lang('Percentage')</option>
                                                    <option value="2">@lang('Fixed')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 percentage">
                                            <div class="form-group">
                                                <label>@lang('Commission %')</label>
                                                <input name="percomm" class="form-control" type="number"
                                                    placeholder="@lang('Enter Commision Percentage')">
                                            </div>
                                        </div>
                                        <div class="col-md-12 fixed d-none">
                                            <div class="form-group">
                                                <label>@lang('Fixed Commission')</label>
                                                <input name="fixedcomm" class="form-control" type="number"
                                                    placeholder="@lang('Enter Commision Price')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    {{-- Update METHOD MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Update the existing Plan')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.setplan.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Name')</label>
                                                <input class="form-control" type="text" name="name"
                                                    value="">
                                                <input class="form-control" type="hidden" name="id"
                                                    value="{{ $plan->id }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Price')</label>
                                                <input name="price" class="form-control" type="number"
                                                    placeholder="@lang('Enter Price')" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <label>@lang('Select Type')</label>
                                                <select id="ptype" name="plantype" class="form-control" required>
                                                    <option value="1">@lang('Monthly')</option>
                                                    <option value="2">@lang('OnTime')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Allowed Custom Field')</label>
                                                <select id="ctype" name="customfield" class="form-control" required>
                                                    <option value="0">@lang('No')</option>
                                                    <option value="1">@lang('Yes')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Number of Allowed Products')</label>
                                                <input name="productallowed" class="form-control" type="number"
                                                    placeholder="@lang('Enter Number of Products')"required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('First Time Discount')</label>
                                                <input name="discount" class="form-control" type="number"
                                                    value="0" placeholder="@lang('Discount percentage ')"required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>@lang('Select Commission Type')</label>
                                                <select id="cmmtype" name="commisiontype"
                                                    class="form-control commissiontype" required>
                                                    <option value="1">@lang('Percentage')</option>
                                                    <option value="2">@lang('Fixed')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 percentage">
                                            <div class="form-group">
                                                <label>@lang('Commission %')</label>
                                                <input name="percomm" class="form-control" type="number"
                                                    placeholder="@lang('Enter Commision Percentage')"required>
                                            </div>
                                        </div>
                                        <div class="col-md-12 fixed d-none">
                                            <div class="form-group">
                                                <label>@lang('Fixed Commission')</label>
                                                <input name="fixedcomm" class="form-control" type="number"
                                                    placeholder="@lang('Enter Commision Price')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    {{-- ACTIVATE METHOD MODAL --}}
    <div id="activateModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Plan Activation Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.plan.activate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to activate this?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Activate')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DEACTIVATE METHOD MODAL --}}
    <div id="deactivateModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Plan Disable Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.plan.deactivate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to disable this Plan')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Disable')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        @if (session('copytext'))
            navigator.clipboard.writeText('{{ session('copytext') }}').then(function() {
                console.log('copied');
            }, function() {
                console.log('failed');
            });
        @endif

        $('body').on('change', '.commissiontype', function() {
            var value = $(this).val();
            if (value == 1) {
                $('.percentage').removeClass('d-none');
                $('.fixed').addClass('d-none');

            } else {
                $('.percentage').addClass('d-none');
                $('.fixed').removeClass('d-none');
            }

        });

        $('.addBtn').on('click', function() {
            var modal = $('#addModal');
            modal.modal('show');
        });
        $('.editBtn').on('click', function() {

            var id = $(this).attr('data-id');
            var plan = $(this).attr('data-value');
            plan = JSON.parse(plan);
            console.log(plan);
            $('#editModal input[name="name"]').val(plan.name);
            $('#editModal input[name="id"]').val(plan.id);
            $('#editModal input[name="price"]').val(plan.price);
            $('#editModal input[name="productallowed"]').val(plan.allowed_product);
            $("#ctype").val(plan.cf_status);
            $("#ptype").val(plan.plan_type);
            $("#cmmtype").val(plan.commission_type);
            if (plan.commission_type == 1) {
                $('input[name="percomm"]').val(plan.commission);
            } else {
                $('input[name="fixedcomm"]').removeClass('d-none');
                $('input[name="percomm"]').addClass(plan.commission);
                $('input[name="percomm"]').val(plan.commission);
            }


            var modal = $('#editModal');
            modal.modal('show');


        });
        $('.activateBtn').on('click', function() {
            var modal = $('#activateModal');
            modal.find('input[name=id]').val($(this).data('id'));
        });

        $('.deactivateBtn').on('click', function() {
            var modal = $('#deactivateModal');
            modal.find('input[name=id]').val($(this).data('id'));
        });
    </script>
@endpush
