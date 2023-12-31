@extends('admin.layouts.app')

@section('panel')
@php
if(isset($tempproduct)){
$product =$tempproduct->product;
}

@endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <b>@lang('Product Image')</b>
                                <div class="image-upload mt-2">
                                    <div class="thumb">
                                        <div class="avatar-preview">

                                                <div class="profilePicPreview"
                                                    style="background-image:url({{ getImage(imagePath()['p_image']['path'] . '/' . $product->image, imagePath()['p_image']['size']) }})">
                                                    <button type="button" class="remove-image"><i
                                                            class="fa fa-times"></i></button>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold">@lang('Product Name')</label>
                                        <input type="text" class="form-control" placeholder="@lang('Enter name')"
                                            value="{{ $product->name }}" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold">@lang('Category')</label>
                                        <input type="text" class="form-control" value="{{ $product->category->name }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold">@lang('Subcategory')</label>
                                        <input type="text" class="form-control" value="{{ $product->subcategory->name }}"
                                            disabled>
                                    </div>
                                </div>
                                @if ($product->othercategoriesproduct)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Other Category')</label>
                                            @foreach ($product->othercategoriesproduct as $pcategory)
                                                @foreach ($othercategories as $othercat)
                                                    @if ($pcategory->othercategory_id == $othercat->id)
                                                        <input type="text" value="{{ $othercat->category_name }}"
                                                            class="form-control" disabled>
                                                        <span style="color:red">Note:Pending for Approvals</span>
                                                    @endif
                                                @endforeach
                                            @endforeach


                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Other SubCategory')</label>
                                            @foreach ($product->othercategoriesproduct as $pcategory)
                                                @foreach ($othercategories as $othercat)
                                                    @if ($pcategory->othercategory_id == $othercat->id)
                                                        <input type="text" value="{{ $othercat->subcategory_name }}"
                                                            class="form-control" disabled>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold">@lang('Demo Link')</label>
                                        <input type="url" class="form-control" value="{{ $product->demo_link }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold">@lang('Regular Price')</label>
                                        <div class="input-group">
                                            <input type="url" class="form-control"
                                                value="{{ getAmount($product->regular_price) }}" disabled>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span
                                                        class="currency_symbol">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold">@lang('Extended Price')</label>
                                        <div class="input-group">
                                            <input type="url" class="form-control"
                                                value="{{ getAmount($product->extended_price) }}" disabled>
                                            <div class="input-group-append">
                                                <div class="input-group-text"><span
                                                        class="currency_symbol">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="row">

                                @foreach ($product->category_details as $key => $item)
                                    <div class="col-md-12">
                                        <div class="form-group ">
                                            <label
                                                class="form-control-label font-weight-bold">{{ inputTitle($key) }}</label>

                                            @if (count($item) > 1)
                                                <select class="form-control select2-multi-select" multiple="multiple"
                                                    disabled>
                                                    @foreach ($item as $data)
                                                        <option value="" selected>{{ str_replace('_', ' ', @$data) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            @if (count($item) == 1)
                                                <select class="form-control select2-basic" disabled>
                                                    <option value="" selected>{{ str_replace('_', ' ', @$item[0]) }}
                                                    </option>
                                                </select>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-md-12">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('Tags')</label>
                                        <select class="form-control select2-auto-tokenize" multiple="multiple" disabled>
                                            @if (@$product->tag)
                                                @foreach ($product->tag as $item)
                                                    <option value="{{ $item }}" selected>{{ __($item) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group ">
                                        <label class="form-control-label font-weight-bold">@lang('HTML Description')</label>
                                        <div class="border border--primary">
                                            <div class="m-3">
                                                @php echo $product->description; @endphp
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label font-weight-bold"
                                            class="form-control-label font-weight-bold">@lang('Message To Reviewer')</label>
                                        <textarea placeholder="@lang('Enter your message')" disabled>{{ __($product->message) }}</textarea>
                                    </div>
                                </div>
                                @if ($product->bumps)
                                    <div class="row addvarients">
                                        <div class="col-lg-12">
                                            <label class="form-control-label font-weight-bold">@lang('Product Bumps')</label>
                                        </div>

                                        @foreach ($product->bumps as $bump)
                                            <div class="col-lg 4 removebumps">
                                                <button type="button" class="removeBtn"><i
                                                        class="las la-times"></i></button>
                                                <div class="col-lg-12">
                                                    <label>@lang('Name') <sup class="text--danger">*</sup></label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form--control varient_title"
                                                            name="varient_title[]" value="{{ $bump->name }}"
                                                            placeholder="@lang('Enter Bump Name')" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 form-group">
                                                    <label>@lang('Price For Bump')</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="number"
                                                            class="form--control buyer-fee varient_price"
                                                            name="varient_price[]" value={{ $bump->price }}
                                                            placeholder="Enter Pump Price" disabled>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text h-100">
                                                                {{ $general->cur_text }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 form-group is_quantity">
                                                    <label>@lang('Quantity if any ') <sup class="text--danger">*</sup></label>
                                                    @if ($bump->is_quantity != 0)
                                                    @endif
                                                    <select name="is_quantity[]" id="is_quantity" class="form--control">

                                                        <option value="0">@lang('No')</option>
                                                        @if ($bump->is_quantity != 0)
                                                            <option value="1" selected>
                                                                @lang('Yes')
                                                            </option>
                                                        @endif

                                                    </select>
                                                </div>
                                                @if ($bump->is_quantity != 0)
                                                    <div class="col-lg-12 form-group min_quantity">
                                                        <label>@lang('Min Quantity') <sup class="text--danger">*</sup></label>
                                                        <div class="input-group mb-2 mr-sm-2">
                                                            <input type="number" class="form--control regular-price"
                                                                name="min_quantity[]" min="1"
                                                                value={{ $bump->min_quantity }}
                                                                placeholder="@lang('Enter Quantity')" step="any">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($product->productcustomfields && count($product->productcustomfields) > 0)
                                    <div class="col-md-12">
                                        <div>
                                            <label class="form-control-label font-weight-bold"
                                                class="form-control-label font-weight-bold">@lang('CustomFields')</label>
                                        </div>
                                        <ul>
                                            @foreach ($product->productcustomfields as $custom)
                                                @foreach ($customfield as $cf)
                                                    @php
                                                        if ($cf->id == $custom->customfield_id && $custom->product_id == $product->id) {
                                                            $checked = 'checked';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                    @endphp
                                                    <li>
                                                        <label class="form-check-label pl-3">
                                                            <input type="checkbox" name="field_name[]"
                                                                value="{{ $cf->type }}" class="form-check-input p-2"
                                                                {{ $checked }} disabled>
                                                            {{ $cf->name }}
                                                        </label>
                                                    </li>
                                                @endforeach
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($product->screenshot)
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold"
                                                class="form-control-label font-weight-bold">@lang('Product Screenshots')</label>
                                        </div>
                                        <div class="row">
                                            @foreach ($product->screenshot as $item)
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="image-upload mt-2">
                                                            <div class="thumb">
                                                                <div class="avatar-preview">
                                                                    @if (request()->routeIs('admin.product.update.pending.view') || request()->routeIs('admin.product.resubmit.view'))
                                                                        <div class="profilePicPreview"
                                                                            style="background-image: url({{ getImage(imagePath()['temp_p_screenshot']['path'] . '/' . $item) }})">
                                                                            <button type="button" class="remove-image"><i
                                                                                    class="fa fa-times"></i></button>
                                                                        </div>
                                                                    @else
                                                                        <div class="profilePicPreview"
                                                                            style="background-image: url({{ getImage(imagePath()['p_screenshot']['path'] . '/' . $item) }})">
                                                                            <button type="button" class="remove-image"><i
                                                                                    class="fa fa-times"></i></button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Product Approval Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.approve.product') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to approve this product?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Approve')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="softModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you sure to soft reject this product')?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.softreject.product') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" placeholder="@lang('Enter reason for soft rejection')" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--warning">@lang('Soft Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="hardModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you sure to hard reject this product')?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.hardreject.product') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold mt-2">@lang('Reason for rejection')</label>
                            <textarea name="message" placeholder="@lang('Enter reason for hard Rejection')" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Hard Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="resubmitApproveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Resubmitted Product Approval Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.resubmit.approve.product') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to approve this product?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Approve')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="resubmitSoftModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you sure to softreject this resubmitted product')?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.resubmit.softreject.product') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" placeholder="@lang('Enter reason for soft rejection')" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--warning">@lang('Soft Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="resubmitHardModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you sure to hardreject this resubmitted product')?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.resubmit.hardreject.product') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold mt-2">@lang('Reason for rejection')</label>
                            <textarea name="message" placeholder="@lang('Enter reason for hard Rejection')" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Hard Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="updateApproveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Product Approval Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.approve.product.update.pending') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to approve this product?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Approve')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="updateRejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you sure to reject update for this product')?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.reject.product.update.pending') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" placeholder="@lang('Enter reason for rejection')" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if (request()->routeIs('admin.product.update.pending.view'))
        <button class="btn btn-sm btn--info box--shadow1 text--small updateApproveBtn" data-toggle="modal"
            data-target="#updateApproveModal" data-id="{{ $product->id }}"><i class="las la-check-circle"></i>
            @lang('Approve Update') </button>

        <button class="btn btn-sm btn--danger box--shadow1 text--small updateRejectBtn" data-toggle="modal"
            data-target="#updateRejectModal" data-id="{{ $product->id }}"><i class="las la-times-circle"></i>
            @lang('Reject Update') </button>
    @elseif(request()->routeIs('admin.product.resubmit.view'))
        <button class="btn btn-sm btn--info box--shadow1 text--small resubmitApproveBtn" data-toggle="modal"
            data-target="#resubmitApproveModal" data-id="{{ $product->id }}"><i class="las la-check-circle"></i>
            @lang('Approve') </button>

        @if ($product->status != 2)
            <button class="btn btn-sm btn--warning box--shadow1 text--small resubmitSoftBtn" data-toggle="modal"
                data-target="#resubmitSoftModal" data-id="{{ $product->id }}"><i class="las la-spinner"></i>
                @lang('Soft Reject') </button>
        @endif

        @if ($product->status != 3)
            <button class="btn btn-sm btn--danger box--shadow1 text--small resubmitHardBtn" data-toggle="modal"
                data-target="#resubmitHardModal" data-id="{{ $product->id }}"><i class="las la-times-circle"></i>
                @lang('Hard Reject') </button>
        @endif
    @else
        <button class="btn btn-sm btn--info box--shadow1 text--small approveBtn" data-toggle="modal"
            data-target="#approveModal" data-id="{{ $product->id }}"><i class="las la-check-circle"></i>
            @lang('Approve') </button>

        @if ($product->status != 2)
            <button class="btn btn-sm btn--warning box--shadow1 text--small softBtn" data-toggle="modal"
                data-target="#softModal" data-id="{{ $product->id }}"><i class="las la-spinner"></i> @lang('Soft Reject')
            </button>
        @endif

        @if ($product->status != 3)
            <button class="btn btn-sm btn--danger box--shadow1 text--small hardBtn" data-toggle="modal"
                data-target="#hardModal" data-id="{{ $product->id }}"><i class="las la-times-circle"></i>
                @lang('Hard Reject') </button>
        @endif
    @endif
    <a href="javascript:window.history.back();" class="btn btn-sm btn--primary box--shadow1 text--small"><i
            class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.select2-auto-tokenize').select2({
                dropdownParent: $('.card-body'),
                tags: true,
                tokenSeparators: [',']
            });

            $('.select2-multi-select').select2({
                dropdownParent: $('.card-body'),
            });

            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });

            $('.approveBtn').on('click', function() {
                var modal = $('#approveModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.hardBtn').on('click', function() {
                var modal = $('#hardModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.softBtn').on('click', function() {
                var modal = $('#softModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.resubmitApproveBtn').on('click', function() {
                var modal = $('#resubmitApproveModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.resubmitHardBtn').on('click', function() {
                var modal = $('#resubmitHardModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.resubmitSoftBtn').on('click', function() {
                var modal = $('#resubmitSoftModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.updateApproveBtn').on('click', function() {
                var modal = $('#updateApproveModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });

            $('.updateRejectBtn').on('click', function() {
                var modal = $('#updateRejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
            });
        })(jQuery);
    </script>

@endpush
