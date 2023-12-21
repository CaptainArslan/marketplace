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
                        <div class="tab-content-area">
                            <div class="user-profile-area">
                                <form action="{{ route('user.customfield.update', $customfields->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row formdata">
                                        <div class="col-lg-12">
                                            <div class="form-group">

                                                <label>@lang('Field Name') <sup class="text--danger">*</sup></label>

                                            </div>
                                            <input type="hidden" name="id" value="{{ $customfields->id }}" required>
                                            <input type="text" name="name" value="{{ $customfields->name }}"
                                                placeholder="@lang('Enter field name')" class="form--control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>@lang('Field Type') <sup class="text--danger">*</sup></label>
                                            <select name="type" id="type" class="form--control typeselect" required>
                                                <option value="">Choose Field Type</option>

                                                <option value="email"
                                                    {{ $customfields->type == 'email' ? 'selected' : '' }}>Email</option>
                                                <option value="phone"
                                                    {{ $customfields->type == 'phone' ? 'selected' : '' }}>Phone</option>
                                                <option value="text"
                                                    {{ $customfields->type == 'text' ? 'selected' : '' }}>
                                                    Text</option>
                                                <option value="textarea"
                                                    {{ $customfields->type == 'textarea' ? 'selected' : '' }}>Textarea
                                                </option>
                                                <option value="url" {{ $customfields->type == 'url' ? 'selected' : '' }}>
                                                    Url</option>
                                                <option
                                                    value="color"{{ $customfields->type == 'color' ? 'selected' : '' }}>
                                                    Colour</option>
                                            </select>
                                        </div>
                                        <div class="form-group fieldoption d-none">
                                            <label>@lang('Field options') <sup class="text--danger">*</sup></label>
                                            <select name="fieldoptions" id="fieldoptions" class="form--control">
                                                <option value="">Choose Field Options</option>
                                                <option value="0"
                                                    {{ $customfields->fieldoption == '0' ? 'selected' : '' }}>Single
                                                </option>
                                                <option value="1"
                                                    {{ $customfields->fieldoption == '1' ? 'selected' : '' }}>Multiple
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group placeholder">
                                            <label>@lang('Field Placeholder') <sup class="text--danger">*</sup></label>
                                            <input type="text" name="placeholder"
                                                value="{{ $customfields->placeholder }}" placeholder="@lang('Enter field placeholder')"
                                                class="form--control" required>
                                        </div>
                                        <div class="text-end d-none addlabel">
                                            <a class="btn btn-sm btn--base addlables">
                                                <i class="las la-plus-circle fs-6"></i> @lang('Add New')
                                            </a>
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
        var cf = @json($customfields);
        var cfitem = cf.customfielditem;
        if (cf.type === 'color') {
            $(".placeholder").addClass('d-none');
            $('input[name="placeholder"]').prop('required', false);
        }
       //function of label input
        function labelinput(value = '') {
            return `<div class="form-group colorlabel">
                       <div col-lg-4 class="form-group colorlabelinput">
                            <button type="button" class="removelabel"><i class="las la-times"></i></button>
                                <label>@lang('Field label') <sup class="text--danger">*</sup></label>
                                <input type="text" name="label[]" value=${value} placeholder="@lang('Enter field label')" class="form--control" required>
                         </div>
                    </div>`;
        }
        //load previous Data
        if (cfitem != null) {
            if (cf.fieldoption == 1) {
                $(".addlabel").removeClass('d-none');
            }
            cfitem.forEach(label => {
                $(".formdata").append(labelinput(label.label));
            });
            $(".fieldoption").removeClass('d-none');
        }
        // when type change to color etc
        $("body").on('change', ".typeselect", function(e) {
            e.preventDefault();
            let type = $(this).val();
            if (type === "color") {
                if (cf.type === 'color' && cfitem != null) {
                    if (cf.fieldoption == 1) {
                        $(".addlabel").removeClass('d-none');
                    }
                    cfitem.forEach(label => {
                        $(".formdata").append(labelinput(label.label));
                    });
                    $(".placeholder").addClass('d-none');
                    $('input[name="placeholder"]').prop('required', false);
                }
                $(".fieldoption").removeClass('d-none');
                $("#fieldoptions").required = true;
            } else {
                $(".fieldoption").addClass('d-none');
                $("#fieldoptions").required = false;
                $(".placeholder").removeClass('d-none');
                $('input[name="placeholder"]').prop('required', true);
                $(".addlabel").addClass('d-none');
                $('.colorlabel').remove();
            }
        });

        //when type color and field option changes
        $("body").on('change', "#fieldoptions", function() {
            let type = $(this).val();
            if (type != "") {
                $(".placeholder").addClass('d-none');
                $('input[name="placeholder"]').prop('required', false);
            }
            if (type === "1") {
                $('.colorlabel').remove();
                $(".addlabel").removeClass('d-none');
                if (cfitem != null && cf.fieldoption == 1) {
                    cfitem.forEach(label => {
                        console.log("sajjad");
                        $(".formdata").append(labelinput(label.label));
                    });
                } else {
                    $(".formdata").append(labelinput());
                }
            } else if (type === "0") {
                $('.colorlabel').remove();
                $(".addlabel").addClass('d-none');
                if (cfitem != null && cf.fieldoption == 0) {
                    cfitem.forEach(label => {
                        $(".formdata").append(labelinput(label.label));
                    });
                } else {
                    $(".formdata").append(labelinput());
                }

            }
        });


        $("body").on('click', ".addlables", function(e) {
            e.preventDefault()
            $(".formdata").append(labelinput());
        });
        $("body").on('click', '.removelabel', function() {
            $(this).closest('.colorlabelinput').remove();
            $('input[name="label[]"]').prop('required', false);
        });
        console.log();
    </script>
@endpush
