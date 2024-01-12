@extends($activeTemplate . 'layouts.frontend')
@section('content')
@if ($partial)
@include($activeTemplate . 'partials.dashboardHeader')
@endif
<div class="pb-100">
    <div class="dashboard-area pt-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content-area">
                        <div class="user-profile-area">
                            <form action="{{ $api ? route('iframe.api.product.store').'?token='.request()->token  :  route('user.product.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 form-group">
                                        <label>@lang('Product Image') <sup class="text--danger">*</sup></label>
                                        <div class="user-profile-header p-0">
                                            <div class="profile-thumb product-profile-thumb">
                                                <div class="avatar-preview">
                                                    <div class="profilePicPreview productPicPreview" style="background-image: url({{ getImage('/', imagePath()['p_image']['size']) }})">
                                                    </div>
                                                </div>
                                                <div class="avatar-edit">
                                                    <input type='file' name="image" class="profilePicUpload_1" id="profilePicUpload_1" accept=".png, .jpg, .jpeg" required>
                                                    <label for="profilePicUpload_1"><i class="la la-pencil"></i></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>@lang('Product Name') <sup class="text--danger">*</sup></label>
                                                    <input type="text" name="name" placeholder="@lang('Enter product name')" class="form--control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>@lang('Category') <sup class="text--danger">*</sup></label>
                                                    <select name="category_id" id="category" class="form--control" required>
                                                        <option value="">Choose Category</option>
                                                        @foreach ($categories as $item)
                                                        <option data-subcategory="{{ $item->subcategories }}" data-category_details="{{ $item->categoryDetails }}" data-buyerfee="{{ $item->buyer_fee }}" data-name="{{ $item->name }}" value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group othercategory d-none">
                                                    <label>@lang('Other Category Name') <sup class="text--danger">*</sup></label>
                                                    <input type="text" name="othercategory" placeholder="@lang('Enter Category name')" class="form--control">
                                                    <span style="color:red">Note: This will be approved by Admin
                                                        Only</span>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Subcategory') <sup class="text--danger">*</sup></label>
                                                    <select name="sub_category_id" id="subcategory" class="form--control" required></select>
                                                </div>
                                                <label>@lang('ShortCode') <sup class="text--danger">*</sup><span class='lastproductcode'></span></label>
                                                <input type="text" id="shortcode" name="shortcode" placeholder="@lang('Enter product ShortCode')" class="form--control">
                                                <div class="form-group othercategory d-none">
                                                    <label>@lang('Subcategory Name') <sup class="text--danger">*</sup></label>
                                                    <input type="text" name="othersubcategory" placeholder="@lang('Enter Subcategory name')" class="form--control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-lg-4 form-group productbumps">
                                            <button type="submit" class="btn btn--base w-100">@lang(' Add Product Bumps')
                                            </button>
                                        </div>
                                        <div class="row addvarients">

                                        </div>

                                    </div>

                                    <div class="col-lg-5 form-group">
                                        <label>@lang('Regular Price') <sup class="text--danger">*</sup></label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="number" class="form--control regular-price" name="regular_price" placeholder="@lang('Enter Amount')" step="any" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 form-group">
                                        <label>@lang('Buyer Fee')</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" class="form--control buyer-fee" value="{{ auth()->user()->levell->product_charge }}" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 form-group">
                                        <label>@lang('Final Regular Price')</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" class="form--control final-regular-price" value="0" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-5 form-group">
                                        <label>@lang('Extended Price') <sup class="text--danger">*</sup></label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="number" class="form--control extended-price" name="extended_price" placeholder="@lang('Enter Amount')" step="any">
                                            <div class="input-group-append">
                                                <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 form-group">
                                        <label>@lang('Buyer Fee')</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" class="form--control buyer-fee" value="{{ auth()->user()->levell->product_charge }}" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 form-group">
                                        <label>@lang('Final Extended Price')</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <input type="text" class="form--control final-extended-price" value="0" readonly>
                                            <div class="input-group-append">
                                                <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label>@lang('Demo Link') <sup class="text--danger">*</sup></label>
                                        <input type="url" name="demo_link" placeholder="@lang('Enter product demo link')" class="form--control" required>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label>@lang('Support') <sup class="text--danger">*</sup></label>
                                        <small><code>({{ $general->regular }} @lang('months'))</code></small>
                                        <select name="support" id="support" class="form--control" required>
                                            <option value="0">@lang('No')</option>
                                            <option value="1">@lang('Yes')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 form-group" id="support-charge-div">

                                    </div>
                                    <div class="col-md-5 form-group" id="discount-div">

                                    </div>

                                    <div class="col-lg-12 form-group" id="category-details">

                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label>@lang('Keywords') <sup class="text--danger">*</sup></label>
                                        <select name="tag[]" class="form--control select2-auto-tokenize" multiple="multiple">
                                        </select>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label>@lang('Description') <code>(@lang('HTML or plain text allowed'))</code></label>
                                        <textarea name="description" class="form-control nicEdit" rows="15" placeholder="@lang('Enter your message')"></textarea>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        {{-- <label>@lang('Message To Reviewer') <code>(@lang('Max 255 charecters'))</code></label> --}}
                                        <label>@lang('Message To Admin') <code>(@lang('Max 255 charecters'))</code></label>
                                        <textarea name="message" class="form--control" placeholder="@lang('Enter your message')"></textarea>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label>@lang('Upload Choice') <sup class="text--danger">*</sup></label>

                                        <select name="uploadchoice" id="uploadchoice" class="form--control" required>
                                            <option value="">Choose Method</option>
                                            <option value="1">@lang('Zip File')</option>
                                            <option value="2">@lang('URL')</option>
                                            @if ($customfield_status == 1)
                                            {
                                            <option value="3">@lang('Custom Action')</option>
                                            }
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <div class="col-lg-3 form-group zip d-none">
                                            <label>@lang('Upload File') <code>(@lang('only zip'))</code> <sup class="text--danger">*</sup></label>
                                            <div id="uploader" class="it">
                                                <div class="row uploadDoc">
                                                    <div class="col-xxl-12 col-xl-12">
                                                        <div class="fileUpload btn btn-orange">
                                                            <img src="{{ asset('assets/images/first.svg') }}" class="icon">
                                                            <span class="upl fs-12px" id="upload">@lang('Upload')</span>
                                                            <input type="file" class="upload up from--control zipfile" name="file" accept=".zip" onchange="fileURL(this);" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group url d-none">
                                        <label>@lang('Source Link') <sup class="text--danger">*</sup></label>
                                        <input type="url" name="sourcelink" placeholder="@lang('Enter product Source URL')" class="form--control">
                                    </div>
                                    <div class="row customaction d-none">
                                        <div class="col-lg-4 form-group addfieldbutton ">
                                            <a href="{{ route('user.customfield.new') }}"><button type="button" class="btn btn--base w-100 addfieldbutton">@lang(' Add Custom Field (if any)')
                                                </button></a>
                                        </div>
                                        <div class="row addcustomfield">
                                            @foreach ($customfields as $field)
                                            <label class="form-check-label pl-3">
                                                <input type="checkbox" class="customfield" name="field_name[]" placeholder="{{ $field->placeholder }}" value="{{ $field->id }}" class="form-check-input p-2">
                                                {{ $field->name }}
                                            </label>
                                            @endforeach
                                        </div>

                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label>@lang('Shareable link /Download Link ') <sup class="text--danger">*</sup></label>
                                        <input type="url" name="shearablelink" placeholder="@lang('Enter the shareable or download link of your product')" class="form--control">
                                    </div>
                                    <div class="row addedField">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn--base w-auto px-2 py-0 d-inline-block rounded fs-6 mb-2" id="upload-ss"><i class="fas fa-plus-square"></i>
                                                @lang('New Screenshot')</button>
                                        </div>
                                        <div class="col-lg-3 form-group">
                                            <label>@lang('Upload Screenshot') <sup class="text--danger">*</sup></label>
                                            <div id="uploader" class="it">
                                                <div class="row uploadDoc">
                                                    <div class="col-xxl-12 col-xl-12">
                                                        <div class="fileUpload btn btn-orange">
                                                            <img src="{{ asset('assets/images/first.svg') }}" class="icon">
                                                            <span class="upl fs-12px" id="upload">@lang('Upload')</span>
                                                            <input type="file" class="upload up from--control validate" name="screenshot[]" accept=".jpg,.jpeg,.png" onchange="screenshotURL(this);" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit Now')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




@push('script')
<!-- NicEdit js-->
<script src="{{ asset($activeTemplateTrue . 'js/nicEdit.js') }}"></script>

<script>
    "use strict";
    //Custom Field Button
    $("body").on("click", ".customfield", function() {
        if ($(this).is(':checked')) {
            $('input[name="field_name[]"]').required = true;
        } else {
            $('input[name="field_name[]"]').required = false;
        }


    });

    function findSubcategoryShortcode(subcategoryId) {
        let data = @json($categories);
        for (let i = 0; i < data.length; i++) {
            const category = data[i];
            for (let j = 0; j < category.subcategories.length; j++) {
                const subcategory = category.subcategories[j];

                if (subcategory.id == subcategoryId) {
                    let url = "{{ route('user.product.get.shortcode', ':id') }}"
                    url = url.replace(':id', subcategoryId);
                    $.ajax({
                        type: "get",
                        url: url,

                        success: function(response) {
                            if (response.status == "success") {

                                $('.lastproductcode').text(' ');
                                $('.lastproductcode').text('LastProductCode : ' + response.code);
                            }
                            if (response.error) {
                                notify('error', response.error);
                            }
                        }
                    });
                    $('#shortcode').val('');
                    $('#shortcode').val(subcategory.shortcode);
                    return subcategory.shortcode;
                }
            }
        }
        return null;
    }
    // $("body").on('change', "#subcategory", function(e) {
    //     e.preventDefault();
    //     let subcatid = $(this).find('option:selected').val();
    //     let shortcode = findSubcategoryShortcode(subcatid);
    // });
    // $("body").on('change', "#category", function(e) {
    //     e.preventDefault();
    //     let otherid = $(this).find('option:selected').attr('data-name');
    //     if (otherid === "Funnels") {

    //         $("input[name='shearablelink']").prop("required", 'true')
    //     } else {
    //         $("input[name='shearablelink']").prop("required", 'false')
    //     }
    //     if (otherid === "Others") {
    //         $(".othercategory").removeClass('d-none');
    //         // $("input[name='othercategory']").prop("required", 'false');
    //     } else {
    //         $(".othercategory").addClass('d-none');
    //         // $("input[name='othercategory']").prop("required", 'false')
    //     }
    // });
    $('#uploadchoice').on('change', function(e) {
        e.preventDefault();
        var data = $(this).val();
        if (data == 1) {
            $(".zip").removeClass('d-none ');
            $(".customaction").addClass('d-none');
            $('input[name="zipfile"]').required = true;
            $('input[name="field_name[]"]').required = false;
            $('input[name="soucelink"]').required = false;
            $(".url").addClass('d-none');
        } else if (data == 2) {
            $(".zip").addClass('d-none');
            $('input[name="zipfile"]').required = false;
            $(".url").removeClass('d-none');
            $(".customaction").addClass('d-none');
            $('input[name="field_name[]"]').required = false;
            $('input[name="soucelink"]').required = true;
        } else if (data == 3) {
            $(".zip").addClass('d-none');
            $(".url").addClass('d-none');
            $(".customaction").removeClass('d-none');
            $('input[name="zipfile"]').required = false;
            $('input[name="soucelink"]').required = false;
            $('input[name="field_name[]"]').required = true;
        } else {
            $(".zip").addClass('d-none');
            $(".url").addClass('d-none');
            $(".customaction").addClass('d-none');
            $('input[name="zipfile"]').required = true;
            $('input[name="soucelink"]').required = false;
            $('input[name="field_name[]"]').required = false;
        }




    });
    $(".productbumps").on('click', function(e) {

        var html = `<div class="col-lg 4 removebumps">
             <button type="button" class="removeBtn"><i class="fas fa-trash text-danger"></i></button>
                <div class="col-lg-12">
                                            <label>@lang('Name') <sup class="text--danger">*</sup></label>
                                            <div class="input-group mb-2 mr-sm-2">
                                                <input type="text" class="form--control varient_title"
                                                    name="varient_title[]" placeholder="@lang('Enter Bump Name')" step="any">
                                            </div>
                                        </div>
                                        <div class="col-lg-8 form-group">
                                            <label>@lang('Price For Bump')</label>
                                            <div class="input-group mb-2 mr-sm-2">
                                                <input type="number" class="form--control varient_price" name="varient_price[]" placeholder="Enter Pump Price" >
                                             <div class="input-group-append">
                                                    <div class="input-group-text h-100">{{ $general->cur_text }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group is_quantity">
                                            <label>@lang('Quantity if any ') <sup class="text--danger">*</sup></label>
                                            <select name="is_quantity[]" id="is_quantity" class="form--control">
                                                <option value="0">@lang('No')</option>
                                                <option value="1">@lang('Yes')</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-12 form-group min_quantity d-none">
                                            <label>@lang('Min Quantity') <sup class="text--danger">*</sup></label>
                                            <div class="input-group mb-2 mr-sm-2">
                                                <input type="number" class="form--control regular-price"
                                                    name="min_quantity[]" value="1" placeholder="@lang('Enter Quantity')" step="any">
                                            </div>
                                        </div>
                                        </div>`;

        $(".addvarients").append(html);
        $('input[name="varient_title"]').required = true;
        $('input[name="varient_price"]').required = true;
        $('input[name="is_quantity"]').required = true;

    });


    $(document).on('click', '.removeBtn', function() {
        $(this).closest('.removebumps').remove();
        $('input[name="varient_title"]').required = false;
        $('input[name="varient_price"]').required = false;
        $('input[name="is_quantity"]').required = false;
    });
    $(document).on('change', '#is_quantity', function(e) {
        e.preventDefault();
        var data = $(this).val();
        if (data == 1) {
            $(".min_quantity").removeClass('d-none');
            $('input[name="min_quantity"]').required = true;

        }
        if (data == 0) {
            $(".min_quantity").addClass('d-none');
            $('input[name="min_quantity"]').required = false;
        }
    });
    $('.regular-price').on('focusout', function() {
        var value = $('.regular-price').val();
        var buyerFee = $('.buyer-fee').val();
        var authorFee = "{{ auth()->user()->levell->product_charge }}";

        var minPrice = parseFloat(buyerFee) + parseFloat((parseFloat(buyerFee) * parseInt(authorFee)) / 100);
        if (parseFloat(value) < parseFloat(minPrice)) {
            alert('Minimum price ' + minPrice);
            $('.regular-price').val('');
            $('.final-regular-price').val(0);
        }

        if (parseFloat(value) >= parseFloat(minPrice)) {

            var finalValue = parseFloat(value) + parseInt(buyerFee);
            if (isNaN(finalValue)) {
                $('.final-regular-price').val(0);
            }
            if (finalValue) {
                $('.final-regular-price').val(parseFloat(finalValue));
            }
        }

    });

    $('.extended-price').on('focusout', function() {
        var value = $('.extended-price').val() ? $('.extended-price').val() : 0;
        var buyerFee = $('.buyer-fee').val() ? 0 : '';
        var authorFee = "{{ auth()->user()->levell->product_charge }}";

        console.log(value + " " + buyerFee + " " + authorFee);

        var minPrice = parseFloat(buyerFee) + parseFloat((parseFloat(buyerFee) * parseInt(authorFee)) / 100);

        if (parseFloat(value) < parseFloat(minPrice)) {
            alert('Minimum price ' + minPrice);
            $('.extended-price').val('');
            $('.final-extended-price').val('');
        }

        if (parseFloat(value) >= parseFloat(minPrice)) {

            var finalValue = parseFloat(value) + parseInt(buyerFee);
            if (isNaN(finalValue)) {
                $('.final-extended-price').val(0);
            }
            if (finalValue) {
                $('.final-extended-price').val(parseFloat(finalValue));
            }
        }
    });

    bkLib.onDomLoaded(function() {
        $(".nicEdit").each(function(index) {
            $(this).attr("id", "nicEditor" + index);
            new nicEditor({
                fullPanel: true
            }).panelInstance('nicEditor' + index, {
                hasPanel: true
            });
        });
    });

    $(document).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain', function() {
        $('.nicEdit-main').focus();
    });

    $('#support').on('change', function() {
        var value = $(this).find('option:selected').val();

        if (value == 1) {
            var htmlDiscount =
                `<label>@lang('Discount For Extended Support (%)') <sup class="text--danger">*</sup></label> <code>(@lang('for') {{ $general->extended }} @lang('months '))</code>
                            <input type="number" id="discount" placeholder="@lang('Enter discount percentage')" class="form--control" name="support_discount" step="any" required>`;

            $('#discount-div').html(htmlDiscount);

            var htmlSupportCharge =
                `<label>@lang('Extended Support Charge (%)') <sup class="text--danger">*</sup></label> <code>(@lang('for') {{ $general->extended }} @lang('months '))</code>
                            <input type="number" id="support-charge" placeholder="@lang('Enter charge percentage')" class="form--control" name="support_charge" step="any" required>`;

            $('#support-charge-div').html(htmlSupportCharge);
        }

        if (value == 0) {
            var htmlDiscount = ``;
            var htmlSupportCharge = ``;
            $('#discount-div').html(htmlDiscount);
            $('#support-charge-div').html(htmlSupportCharge);
        }
    }).change();


    var fileTypes = ['zip']; //acceptable file types
    function fileURL(input) {
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase(); //file extension from input file
            alert(extension);
            isSuccess = fileTypes.indexOf(extension) > -1; //is extension in acceptable types
            if (isSuccess) { //yes
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(input).closest('.fileUpload').find(".icon").attr('src',
                        `{{ asset('assets/images/') }}/${extension}.svg`);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                iziToast.error({
                    message: 'This type of file is not allowed',
                    position: "topRight"
                });

                $('.validate').val('').closest('.fileUpload').find(".icon").attr('src',
                    `{{ asset('assets/images/first.svg') }}`);
            }
        }
    }
    var fileTypesSS = ['jpg', 'jpeg', 'png'];

    function screenshotURL(input) {

        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase(), //file extension from input file
                isSuccess = fileTypesSS.indexOf(extension) > -1; //is extension in acceptable types
            if (isSuccess) { //yes
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(input).closest('.fileUpload').find(".icon").attr('src',
                        `{{ asset('assets/images/') }}/${extension}.svg`);
                }
                reader.readAsDataURL(input.files[0]);
            } else {

                iziToast.error({
                    message: 'This type of file is not allowed',
                    position: "topRight"
                });

                $('.validate').val('').closest('.fileUpload').find(".icon").attr('src',
                    `{{ asset('assets/images/first.svg') }}`);

            }
        }
    }


    (function($) {
        $(document).on('change', '.up', function() {
            var id = $(this).attr('id');
            var profilePicValue = $(this).val();
            var fileNameStart = profilePicValue.lastIndexOf('\\');
            profilePicValue = profilePicValue.substr(fileNameStart + 1).substring(0, 20);
            if (profilePicValue != '') {
                $(this).closest('.fileUpload').find('.upl').html(profilePicValue);
            }
        });

        $('#category').on('change', function() {
            var subcategory = $(this).find('option:selected').data('subcategory');
            var categoryDetails = $(this).find('option:selected').data('category_details');
            var buyerFee = $(this).find('option:selected').data('buyerfee');
            var authorFee = "{{ auth()->user()->levell->product_charge }}";
            var minPrice = parseFloat(buyerFee) + parseFloat((parseFloat(buyerFee) * parseInt(authorFee)) /
                100);

            $('.buyer-fee').val(parseFloat(buyerFee));

            if ($('.regular-price').val() == '') {
                $('.final-regular-price').val(0);
            } else {

                if (parseFloat($('.regular-price').val()) < parseFloat(minPrice)) {
                    alert('Minimum price ' + minPrice);
                    $('.regular-price').val('');
                    $('.final-regular-price').val(0);
                }

                if (parseFloat($('.regular-price').val()) >= parseFloat(minPrice)) {
                    $('.final-regular-price').val(parseFloat($('.regular-price').val()) + parseFloat(
                        buyerFee));
                }
            }

            if ($('.extended-price').val() == '') {
                $('.final-extended-price').val(0);
            } else {

                if (parseFloat($('.extended-price').val()) < parseFloat(minPrice)) {
                    alert('Minimum price ' + minPrice);
                    $('.extended-price').val('');
                    $('.final-extended-price').val(0);
                }

                if (parseFloat($('.extended-price').val()) >= parseFloat(minPrice)) {
                    $('.final-extended-price').val(parseFloat($('.extended-price').val()) + parseFloat(
                        buyerFee));
                }
            }

            $('#subcategory').empty();

            $.each(subcategory, function(index, value) {
                if (value.status == 1) {
                    if (index == 1) {
                        findSubcategoryShortcode(value.id);
                    }
                    $('#subcategory').append(`<option value="${value.id}">${value.name}</option>`);
                }
            });

            $('#category-details').empty();
            var htmal = ``;
            var name;

            $.each(categoryDetails, function(index, value) {
                name = value.name;
                name = name.toLowerCase();
                name = name.replace(' ', '_');

                if (value.type == 1) {

                    htmal += `<label>${value.name} <sup class="text--danger">*</sup></label>

                                    <select class="form--control select2-basic" name="c_details[${name}][]" required>`;
                    if (value.options) {
                        $.each(value.options, function(i, val) {
                            htmal += `<option value=${val.replace(' ','_')}>${val}</option>`
                        });
                    }
                    htmal += `</select>`;

                }

                if (value.type == 2) {

                    htmal +=
                        `<label>${value.name} <sup class="text--danger">*</sup></label>

                                    <select class="form--control select2-multi-select" name="c_details[${name}][]" multiple="multiple" required>`;
                    if (value.options) {
                        $.each(value.options, function(i, val) {
                            htmal += `<option value=${val.replace(' ','_')}>${val}</option>`
                        });
                    }
                    htmal += `</select>`;

                }

            });

            $('#category-details').append(htmal);


            // $('.select2-multi-select').select2();

            // $('.select2-basic').select2();
        }).change();

        $(document).on('input', '#discount', function() {
            var discount = $('#discount').val();

            if (parseInt(discount) > 100) {
                alert('Discount can\'t be more than 100%');
                $('#discount').val(0);
            }
        });

        $(document).on('input', '#support-charge', function() {
            var supportCharge = $('#support-charge').val();

            if (parseInt(supportCharge) > 100) {
                alert('Support charge can\'t be more than 100%');
                $('#support-charge').val(0);
            }
        });

    })(jQuery);

    function proPicURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = $(input).parents('.profile-thumb').find('.profilePicPreview');
                $(preview).css('background-image', 'url(' + e.target.result + ')');
                $(preview).addClass('has-image');
                $(preview).hide();
                $(preview).fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".profilePicUpload_1").on('change', function() {
        proPicURL(this);
    });

    $(".remove-image").on('click', function() {
        $(".profilePicPreview").css('background-image', 'none');
        $(".profilePicPreview").removeClass('has-image');
    });

    // $(".select2-auto-tokenize").select2({
    //     tags: [],
    //     tokenSeparators: [",", " "]
    // });


    $('#upload-ss').on('click', function() {
        var html = `<div class="col-lg-3 form-group remove-data">
                                <label>@lang('Upload Screenshot') <sup class="text--danger">*</sup></label>
                                <div id="uploader" class="it">
                                    <div class="row uploadDoc">
                                        <div class="col-xxl-12 col-xl-12">
                                            <div class="fileUpload btn btn-orange">
                                                <button type="button" class="input-field-close removeBtn"><i class="las la-times"></i></button>
                                                <img src="{{ asset('assets/images/first.svg') }}" class="icon">
                                                <span class="upl fs-12px" id="upload">@lang('Upload')</span>
                                                <input type="file" class="upload up from--control validate" name="screenshot[]" accept=".jpg,.jpeg,.png"  onchange="screenshotURL(this);" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
        $('.addedField').append(html);
    });

    $(document).on('click', '.removeBtn', function() {
        $(this).closest('.remove-data').remove();
    });
</script>
@endpush