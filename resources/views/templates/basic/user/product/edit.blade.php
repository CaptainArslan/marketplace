@extends($activeTemplate . 'layouts.frontend')

@section('content')

    @include($activeTemplate . 'partials.dashboardHeader')

    <div class="pb-100">
        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tab-content-area">
                            <div class="user-profile-area">
                                <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-6 form-group">
                                                    <label>@lang('Product Image') <sup class="text--danger">*</sup></label>
                                                    <div class="user-profile-header">
                                                        <div class="profile-thumb product-profile-thumb">
                                                            <div class="avatar-preview">
                                                                <div class="profilePicPreview productPicPreview"
                                                                    style="background-image: url({{ getImage(imagePath()['p_image']['path'] . '/' . $product->image, imagePath()['p_image']['size']) }})">
                                                                </div>
                                                            </div>
                                                            <div class="avatar-edit">
                                                                <input type='file' name="image"
                                                                    class="profilePicUpload" id="profilePicUpload1"
                                                                    accept=".png, .jpg, .jpeg">
                                                                <label for="profilePicUpload1"><i
                                                                        class="la la-pencil"></i></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <label>@lang('Product Name') <sup
                                                                        class="text--danger">*</sup></label>
                                                                <input type="text" name="name"
                                                                    placeholder="@lang('Enter product name')" class="form--control"
                                                                    value="{{ $product->name }}" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>@lang('Category') <sup
                                                                        class="text--danger">*</sup></label>
                                                                <input type="text" value="{{ $product->category->name }}"
                                                                    class="form--control" disabled>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>@lang('Subcategory') <sup
                                                                        class="text--danger">*</sup></label>
                                                                <input type="text" class="form--control"
                                                                    value="{{ $product->subcategory->name }}" disabled>
                                                            </div>
                                                            @if ($product->othercategories)
                                                                <div class="form-group">
                                                                    <label>@lang('Other Category') <sup
                                                                            class="text--danger">*</sup></label>
                                                                    <input type="text"
                                                                        value="{{ $product->othercategories->category_name }}"
                                                                        class="form--control" disabled>
                                                                    <span style="color:red">Note: This will be approved by
                                                                        Admin
                                                                        Only</span>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>@lang('Other Subcategory') <sup
                                                                            class="text--danger">*</sup></label>
                                                                    <input type="text" class="form--control"
                                                                        value="{{ $product->othercategories->subcategory_name }}"
                                                                        disabled>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row ">
                                                <div class="col-lg-4 form-group productbumps">
                                                    <button type="button" class="btn btn--base w-100">@lang(' Add Product Bumps')
                                                    </button>
                                                </div>
                                                <div class="row addvarients">
                                                    @if ($product->bumps)
                                                        @foreach ($product->bumps as $bump)
                                                            <div class="col-lg 4 removebumps">
                                                                <button type="button" class="removeBtn"><i
                                                                        class="las la-times"></i></button>
                                                                <div class="col-lg-12">
                                                                    <label>@lang('Name') <sup
                                                                            class="text--danger">*</sup></label>
                                                                    <div class="input-group mb-2 mr-sm-2">
                                                                        <input type="text"
                                                                            class="form--control varient_title"
                                                                            name="varient_title[]"
                                                                            value="{{ $bump->name }}"
                                                                            placeholder="@lang('Enter Bump Name')" step="any">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-8 form-group">
                                                                    <label>@lang('Price For Bump')</label>
                                                                    <div class="input-group mb-2 mr-sm-2">
                                                                        <input type="number"
                                                                            class="form--control buyer-fee varient_price"
                                                                            name="varient_price[]"
                                                                            value='{{ $bump->price }}'
                                                                            placeholder="Enter Pump Price">
                                                                        <div class="input-group-append">
                                                                            <div class="input-group-text h-100">
                                                                                {{ $general->cur_text }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 form-group is_quantity">
                                                                    <label>@lang('Quantity if any ') <sup
                                                                            class="text--danger">*</sup></label>

                                                                    <select name="is_quantity[]" id="is_quantity"
                                                                        class="form--control">

                                                                        <option value="0">@lang('No')</option>
                                                                        @if ($bump->is_quantity != 0)
                                                                            <option value="1" selected>
                                                                                @lang('Yes')
                                                                            </option>
                                                                        @endif

                                                                    </select>
                                                                </div>

                                                                <div
                                                                    class="col-lg-12 form-group min_quantity {{ $bump->min_quantity == 0 ? 'd-none' : '' }}">
                                                                    <label>@lang('Min Quantity') <sup
                                                                            class="text--danger">*</sup></label>
                                                                    <div class="input-group mb-2 mr-sm-2">
                                                                        <input type="number"
                                                                            class="form--control regular-price"
                                                                            name="min_quantity[]"
                                                                            value={{ $bump->min_quantity }}
                                                                            placeholder="@lang('Enter Quantity')"
                                                                            step="any">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            </div>
                                            {{-- yaha sy --}}
                                            <div class="row">
                                                <div class="col-lg-5 form-group">
                                                    <label>@lang('Regular Price') <sup class="text--danger">*</sup></label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="number" class="form--control regular-price"
                                                            name="regular_price" placeholder="@lang('Enter Amount')"
                                                            value="{{ getAmount($product->regular_price) - $product->category->buyer_fee }}"
                                                            step="any" required>
                                                        <div class="input-group-text">{{ $general->cur_text }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 form-group">
                                                    <label>@lang('Buyer Fee')</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form--control buyer-fee"
                                                            value="{{ getAmount($product->category->buyer_fee) }}"
                                                            readonly>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text h-100">%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 form-group">
                                                    <label>@lang('Final Regular Price')</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form--control final-regular-price"
                                                            value="{{ getAmount($product->regular_price) }}" readonly>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text h-100">{{ $general->cur_text }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-5 form-group">
                                                    <label>@lang('Extended Price') <sup class="text--danger">*</sup></label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="number" class="form--control extended-price"
                                                            name="extended_price" placeholder="@lang('Enter Amount')"
                                                            value="{{ getAmount($product->extended_price) - $product->category->buyer_fee }}"
                                                            step="any" required>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text h-100">{{ $general->cur_text }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2 form-group">
                                                    <label>@lang('Buyer Fee')</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form--control buyer-fee"
                                                            value="{{ getAmount($product->category->buyer_fee) }}"
                                                            readonly>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text h-100">%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 form-group">
                                                    <label>@lang('Final Extended Price')</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form--control final-extended-price"
                                                            value="{{ getAmount($product->extended_price) }}" readonly>
                                                        <div class="input-group-append">
                                                            <div class="input-group-text h-100">{{ $general->cur_text }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 form-group">
                                                <label>@lang('Demo Link') <sup class="text--danger">*</sup></label>
                                                <input type="url" name="demo_link" placeholder="@lang('Enter product demo link')"
                                                    value="{{ $product->demo_link }}" class="form--control" required>
                                            </div>

                                            <div class="col-md-2 form-group">
                                                <label>@lang('Support') <sup class="text--danger">*</sup></label>
                                                <small><code>({{ $general->regular }} @lang('months'))</code></small>
                                                <select name="support" id="support" class="form--control" required>
                                                    <option value="1">@lang('Yes')</option>
                                                    <option value="0">@lang('No')</option>
                                                </select>
                                            </div>

                                            <div class="col-md-5 form-group" id="support-charge-div">

                                            </div>

                                            <div class="col-md-5 form-group" id="discount-div">

                                            </div>

                                            <div class="col-lg-12 form-group" id="category-details">

                                            </div>

                                            @foreach ($product->category->categoryDetails as $item)
                                                @if ($item->type == 1)
                                                    @php
                                                        $catname = str_replace(' ', '_', strtolower($item->name));
                                                        $selected = $product->category_details[$catname] ?? null;
                                                    @endphp

                                                    @if ($selected)
                                                        <div class="col-md-12 form-group">
                                                            <label>{{ $item->name }} <sup
                                                                    class="text--danger">*</sup></label>
                                                            <select class="form--control select2-basic"
                                                                name="c_details[{{ $catname }}][]" required>

                                                                @foreach ($item->options as $data)
                                                                    @php
                                                                        $myselect = in_array(str_replace(' ', '_', $data), $selected);
                                                                    @endphp

                                                                    <option value="{{ str_replace(' ', '_', $data) }}"
                                                                        @if ($myselect) selected @endif>
                                                                        {{ $data }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    @else
                                                        <div class="col-md-12 form-group">
                                                            <label>{{ $item->name }} <sup
                                                                    class="text--danger">*</sup></label>
                                                            <select class="form--control select2-basic"
                                                                name="c_details[{{ $catname }}][]" required>

                                                                @foreach ($item->options as $data)
                                                                    <option value="{{ str_replace(' ', '_', $data) }}">
                                                                        {{ $data }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    @endif
                                                @elseif($item->type == 2)
                                                    @php
                                                        $catname = str_replace(' ', '_', strtolower($item->name));
                                                        $selected = $product->category_details[$catname] ?? null;
                                                    @endphp

                                                    @if ($selected)
                                                        <div class="col-md-12 form-group">
                                                            <label>{{ $item->name }} <sup
                                                                    class="text--danger">*</sup></label>
                                                            <select class="form--control select2-multi-select"
                                                                name="c_details[{{ $catname }}][]"
                                                                multiple="multiple" required>

                                                                @foreach ($item->options as $data)
                                                                    @php
                                                                        $myselect = in_array(str_replace(' ', '_', $data), $selected);
                                                                    @endphp
                                                                    <option value="{{ str_replace(' ', '_', $data) }}"
                                                                        @if ($myselect) selected @endif>
                                                                        {{ $data }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    @else
                                                        <div class="col-md-12 form-group">
                                                            <label>{{ $item->name }} <sup
                                                                    class="text--danger">*</sup></label>
                                                            <select class="form--control select2-multi-select"
                                                                name="c_details[{{ $catname }}][]"
                                                                multiple="multiple" required>

                                                                @foreach ($item->options as $data)
                                                                    <option value="{{ str_replace(' ', '_', $data) }}">
                                                                        {{ $data }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach

                                            <div class="col-md-12 form-group">
                                                <label>@lang('Tags') <sup class="text--danger">*</sup></label>
                                                <code>(@lang('Separate multiple tags by') ,(@lang('comma')) @lang('or')
                                                    @lang('enter') @lang('key'))</code>
                                                <select name="tag[]" class="form--control select2-auto-tokenize"
                                                    multiple="multiple" required>
                                                    @if (@$product->tag)
                                                        @foreach ($product->tag as $item)
                                                            <option value="{{ $item }}" selected>
                                                                {{ $item }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-12 form-group">
                                                <label>@lang('Description') <code>(@lang('HTML or plain text allowed'))</code></label>
                                                <textarea name="description" class="form-control nicEdit" rows="15" placeholder="@lang('Enter your message')">{{ $product->description }}</textarea>
                                            </div>
                                            <div class="col-lg-12 form-group">
                                                <label>@lang('Message To Reviewer') <code>(@lang('Max 255 charecters'))</code></label>
                                                <textarea name="message" class="form--control" placeholder="@lang('Enter your message')">{{ $product->message }}</textarea>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>@lang('Upload Choice') <sup class="text--danger">*</sup></label>

                                                <select name="uploadchoice" id="uploadchoice" class="form--control"
                                                    required>
                                                    <option value="">Choose Method</option>
                                                    <option value="1">@lang('Zip File')</option>
                                                    <option value="2">@lang('URL')</option>
                                                    <option value="3">@lang('Custom Action')</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-3 form-group zip d-none">
                                                <label>@lang('Upload File') <code>(@lang('only zip'))</code> <sup
                                                        class="text--danger">*</sup></label>
                                                <div id="uploader" class="it">
                                                    <div class="row uploadDoc">
                                                        <div class="col-xxl-12 col-xl-12">
                                                            <div class="fileUpload btn btn-orange">
                                                                <img src="{{ asset('assets/images/first.svg') }}"
                                                                    class="icon">
                                                                <span class="upl fs-12px"
                                                                    id="upload">@lang('Upload')</span>
                                                                <input type="file" class="upload up from--control"
                                                                    name="file" value={{ $product->file }}
                                                                    accept=".zip" onchange="fileURL(this);" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 form-group url d-none">
                                                <label>@lang('Source Link') <sup class="text--danger">*</sup></label>
                                                <input type="url" name="sourcelink" value="{{ $product->file }}"
                                                    placeholder="@lang('Enter product Source URL')" class="form--control">
                                            </div>
                                            <div class="row customaction d-none">
                                                <div class="col-lg-4 form-group addfieldbutton ">
                                                    <a href="{{ route('user.customfield.new') }}"><button type="button"
                                                            class="btn btn--base w-100 addfieldbutton">@lang(' Add Custom Field (if any)')
                                                        </button></a>
                                                </div>
                                                <div class="row addcustomfield">
                                                    @if ($product->productcustomfields && count($product->productcustomfields) > 0)
                                                        @foreach ($product->productcustomfields as $custom)
                                                            @foreach ($customfield as $cf)
                                                                @php
                                                                    if ($cf->id == $custom->customfield_id && $custom->product_id == $product->id) {
                                                                        $checked = 'checked';
                                                                    } else {
                                                                        $checked = '';
                                                                    }
                                                                @endphp
                                                                <label class="form-check-label pl-3 ">
                                                                    <input type="checkbox" class='customfield'
                                                                        name="field_name[]" value="{{ $cf->id }}"
                                                                        class="form-check-input p-2" {{ $checked }}>
                                                                    {{ $cf->name }}
                                                                </label>
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="row addedField">
                                                <div class="d-flex justify-content-end">
                                                    <button type="button"
                                                        class="btn--base w-auto px-2 py-0 d-inline-block rounded fs-6 mb-2"
                                                        id="upload-ss"><i class="fas fa-plus-square"></i>
                                                        @lang('New Screenshot')</button>
                                                </div>
                                                @foreach ($product->screenshot as $images)
                                                    <div class="col-lg-3 form-group">
                                                        <label>@lang('Upload Screenshot') <sup class="text--danger">*</sup></label>
                                                        <div id="uploader" class="it">
                                                            <div class="row uploadDoc">
                                                                <div class="col-xxl-12 col-xl-12">
                                                                    <div class="fileUpload btn btn-orange">
                                                                        <img src="{{ asset('assets/images/first.svg') }}"
                                                                            class="icon">
                                                                        <span class="upl fs-12px" id="upload">
                                                                            {{ $images }}</span>
                                                                        <input type="file"
                                                                            class="upload up from--control validate"
                                                                            name="screenshot[]" accept=".jpg,.jpeg,.png"
                                                                            onchange="screenshotURL(this);" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="col-lg-12 form-group">
                                                <button type="submit"
                                                    class="btn btn--base w-100">@lang('Resubmit Now')</button>
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
        //Bumps Button
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
                                                <input type="number" class="form--control buyer-fee varient_price" name="varient_price[]" placeholder="Enter Pump Price" >
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
                                                    name="min_quantity[]" value="0" placeholder="@lang('Enter Quantity')" step="any">
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

        //Custom Field Button
        $("body").on("click", ".customfield", function() {
            if ($(this).is(':checked')) {
                $('input[name="field_name[]"]').required = true;
            } else {
                $('input[name="field_name[]"]').required = false;
            }


        });

        $('.regular-price').on('focusout', function() {
            var value = $('.regular-price').val();
            var buyerFee = $('.buyer-fee').val();
            var authorFee = "{{ auth()->user()->levell->product_charge }}";

            var minPrice = parseFloat(buyerFee) + parseFloat((parseFloat(buyerFee) * parseInt(authorFee)) / 100);

            if (parseFloat(value) < parseFloat(minPrice)) {
                alert('Minimum price' + minPrice);
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
        //Upload Options
        var choice = @json($product->file);
        if (choice == null) {
            $(".customaction").removeClass('d-none');
            $('input[name="field_name[]"]').required = true;
            $('#uploadchoice option[value="3"]').attr('selected', 'selected');
        } else {
            var check = choice.includes('.zip');
            if (check) {
                $(".zip").removeClass('d-none ');
                $(".zipfile").required = true;
                $('#uploadchoice option[value="1"]').attr('selected', 'selected');

            } else {
                $(".url").removeClass('d-none');
                $('input[name="soucelink"]').required = true;
                $('#uploadchoice option[value="2"]').attr('selected', 'selected');
            }
        }
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
                $('input[name="zipfile"]').required = false;
                $('input[name="soucelink"]').required = false;
                $('input[name="field_name[]"]').required = false;
            }
        });
        $('.extended-price').on('focusout', function() {
            var value = $('.extended-price').val();
            var buyerFee = $('.buyer-fee').val();
            var authorFee = "{{ auth()->user()->levell->product_charge }}";

            var minPrice = parseFloat(buyerFee) + parseFloat((parseFloat(buyerFee) * parseInt(authorFee)) / 100);

            if (parseFloat(value) < parseFloat(minPrice)) {
                alert('Minimum price' + minPrice);
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



        var fileTypes = ['zip']; //acceptable file types
        function fileURL(input) {
            if (input.files && input.files[0]) {
                var extension = input.files[0].name.split('.').pop().toLowerCase(), //file extension from input file
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
            $('select[name="support"]').val('{{ $product->support }}');

            $('#support').on('change', function() {
                var value = $(this).find('option:selected').val();

                if (value == 1) {
                    var htmlDiscount =
                        `<label>@lang('Discount For Extended Support (%)') <sup class="text--danger">*</sup></label> <code>(@lang('for') {{ $general->extended }} @lang('months'))</code>
                                <input type="number" id="discount" placeholder="@lang('Enter discount percentage')"  value="{{ $product->support_discount }}" class="form--control" name="support_discount" step="any" required>`;

                    $('#discount-div').html(htmlDiscount);

                    var htmlSupportCharge =
                        `<label>@lang('Extended Support Charge (%)') <sup class="text--danger">*</sup></label> <code>(@lang('for') {{ $general->extended }} @lang('months'))</code>
                            <input type="number" id="support-charge" placeholder="@lang('Enter charge percentage')" value="{{ $product->support_charge }}" class="form--control" name="support_charge" step="any" required>`;

                    $('#support-charge-div').html(htmlSupportCharge);

                }
                if (value == 0) {
                    var htmlDiscount = ``;
                    var htmlSupportCharge = ``;
                    $('#discount-div').html(htmlDiscount);
                    $('#support-charge-div').html(htmlSupportCharge);
                }
            }).change();

            $(document).on('input', '#discount', function() {
                var discount = $('#discount').val();

                if (parseInt(discount) > 100) {
                    alert('Discount can\'t be more than 100%');
                    $('#discount').val(0)
                }
            });

            $(document).on('input', '#support-charge', function() {
                var supportCharge = $('#support-charge').val();

                if (parseInt(supportCharge) > 100) {
                    alert('Support charge can\'t be more than 100%');
                    $('#support-charge').val(0);
                }
            });

            $(document).on('change', '.up', function() {
                var id = $(this).attr('id'); /* gets the filepath and filename from the input */
                var profilePicValue = $(this).val();
                var fileNameStart = profilePicValue.lastIndexOf('\\'); /* finds the end of the filepath */
                profilePicValue = profilePicValue.substr(fileNameStart + 1).substring(0,
                    20); /* isolates the filename */
                if (profilePicValue != '') {
                    $(this).closest('.fileUpload').find('.upl').html(
                        profilePicValue); /* changes the label text */
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
