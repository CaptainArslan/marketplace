@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tab-content-area">
                            <div class="user-profile-area">
                                <form action="{{ route('user.meeting.update', $meetings->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>@lang('Meeting Agenda') <sup class="text--danger">*</sup></label>
                                            </div>
                                            <input type="hidden" name="productid" value="{{ $meetings->product_id }}" class="form--control" required>
                                            <input type="text" name="agenda" value="{{ $meetings->agenda }}"
                                                placeholder="@lang('Enter Meeting agenda')" class="form--control" required>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Meeting Date') <sup class="text--danger">*</sup></label>
                                                <input type="date" value="{{ $meetings->meeting_date }}"
                                                    name="meetingdate" placeholder="@lang('Enter Meeting Date')" class="form--control"
                                                    required>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label>@lang('Meeting Time') <sup class="text--danger">*</sup></label>
                                                <input type="time" value="{{ $meetings->meeting_time }}"
                                                    name="meetingtime" placeholder="@lang('Enter Meeting Time')" class="form--control"
                                                    required>
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

        $("body").on("click", ".customfield", function() {

            $(".addcustomfield").html('');


            $.ajax({
                url: "{{ route('user.customfield.product') }}",
                success: function(result) {

                    if (result.status == 'error') {
                        let html = `<div col-lg-4 class="form-group customfieldinput">
                    <button type="button" class="removefield"><i class="las la-times"></i></button>
                    <label>@lang('FieldType') <sup class="text--danger">*</sup></label>
                        <input type="text" name="field_name[]" placeholder="@lang('Enter Field type')"
                    class="form--control">
                    </div>`

                        $(".addcustomfield").append(html);
                        $('input[name="field_name[]"]').required = true;

                    } else {

                        result.customfield.forEach(fields => {
                            let html = `<label class="form-check-label pl-3">
                            <input type="checkbox" name="field_name[]" value="${fields.name}"
                                class="form-check-input p-2">
                                ${fields.name}
                        </label>`
                            $(".addcustomfield").append(html);

                        });
                        // $(".customfield").addClass('d-none ');


                    }
                }
            });
            $(document).on('click', '.removefield', function() {
                $(this).closest('.customfieldinput').remove();
                $('input[name="field_name[]"]').required = false;
            });
        });
        $("body").on('change', "#category", function(e) {
            e.preventDefault();
            let otherid = $(this).find('option:selected').attr('data-name');
            if (otherid === "Others") {
                $(".othercategory").removeClass('d-none');
                $("input[name='othercategory']").required = true
            } else
                $(".othercategory").addClass('d-none');
            $("input[name='othercategory']").required = false
        });
        $('#uploadchoice').on('change', function(e) {
            e.preventDefault();
            var data = $(this).val();
            if (data == 1) {
                $(".zip").removeClass('d-none ');
                $('input[name="soucelink"]').required = false;
                $(".url").addClass('d-none');
            } else if (data == 2) {
                $(".zip").addClass('d-none');
                $(".zipfile").required = false;
                $(".url").removeClass('d-none');
                $('input[name="soucelink"]').required = true;
            } else {
                $(".zip").addClass('d-none');
                $(".url").addClass('d-none');

            }




        });
        $(".productbumps").on('click', function(e) {

            var html = `<div class="col-lg 4 removebumps">
             <button type="button" class="removeBtn"><i class="las la-times"></i></button>
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
            var value = $('.extended-price').val();
            var buyerFee = $('.buyer-fee').val();
            var authorFee = "{{ auth()->user()->levell->product_charge }}";

            var minPrice = parseFloat(buyerFee) + parseFloat((parseFloat(buyerFee) * parseInt(authorFee)) / 100);

            if (parseFloat(value) < parseFloat(minPrice)) {
                alert('Minimum price ' + minPrice);
                $('.extended-price').val('');
                $('.final-extended-price').val(0);
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


                $('.select2-multi-select').select2();

                $('.select2-basic').select2();
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
        $(".profilePicUpload").on('change', function() {
            proPicURL(this);
        });

        $(".remove-image").on('click', function() {
            $(".profilePicPreview").css('background-image', 'none');
            $(".profilePicPreview").removeClass('has-image');
        });

        $(".select2-auto-tokenize").select2({
            tags: [],
            tokenSeparators: [",", " "]
        });


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
                                                <input type="file" class="upload up from--control validate" name="screenshot[]" accept=".jpg,.jpeg,.png" required onchange="screenshotURL(this);" />
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
