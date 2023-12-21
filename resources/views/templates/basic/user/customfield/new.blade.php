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
                                <form action="{{ route('user.customfield.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 formdata">
                                            <div class="form-group">
                                                <label>@lang('Field Name') <sup class="text--danger">*</sup></label>
                                                <input type="text" name="name" placeholder="@lang('Enter field name')"
                                                    class="form--control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>@lang('Field Type') <sup class="text--danger">*</sup></label>
                                                <select name="type" id="type" class="form--control typeselect"
                                                    required>
                                                    <option value="">Choose Field Type</option>
                                                    <option value="email">Email</option>
                                                    <option value="phone">Phone</option>
                                                    <option value="text">Text</option>
                                                    <option value="textarea">Textarea</option>
                                                    <option value="url">Url</option>
                                                    <option value="color">Color</option>
                                                </select>
                                            </div>
                                            <div class="form-group fieldoption d-none">
                                                <label>@lang('Field options') <sup class="text--danger">*</sup></label>
                                                <select name="fieldoptions" id="fieldoptions" class="form--control">
                                                    <option value="">Choose Field Options</option>
                                                    <option value="0">Single</option>
                                                    <option value="1">Multiple</option>
                                                </select>
                                            </div>
                                            <div class="text-end d-none addlabel">
                                                <a class="btn btn-sm btn--base addlables">
                                                    <i class="las la-plus-circle fs-6"></i> @lang('Add New')
                                                </a>
                                            </div>
                                            <div class="form-group placeholder">
                                                <label>@lang('Field Placeholder') <sup class="text--danger">*</sup></label>
                                                <input type="text" name="placeholder" placeholder="@lang('Enter field placeholder')"
                                                    class="form--control" required>
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
        $("body").on('change', ".typeselect", function(e) {
            e.preventDefault();
            let type = $(this).val();
            if (type === "color") {
                $(".fieldoption").removeClass('d-none');
                $("#fieldoptions").required = true;
            } else
                $("#fieldoptions").required = false
        });
        var label = `<div class="form-group colorlabel">
                       <div col-lg-4 class="form-group colorlabelinput">
                            <button type="button" class="removelabel"><i class="las la-times"></i></button>
                                <label>@lang('Field label') <sup class="text--danger">*</sup></label>
                                <input type="text" name="label[]" placeholder="@lang('Enter field label')" class="form--control" required>
                         </div>
                    </div>`;

        $("body").on('change', "#fieldoptions", function(e) {
            e.preventDefault();
            let type = $(this).val();
            if (type === "1") {
                $(".addlabel").removeClass('d-none');
                $(".placeholder").addClass('d-none');
                $('input[name="placeholder"]').prop('required', false);
            } else if (type === "0") {
                $('.colorlabelinput').remove();
                $(".addlabel").addClass('d-none');
                $(".placeholder").addClass('d-none');
                $('input[name="placeholder"]').prop('required', false);
                $(".formdata").append(label);

            }
        });

        $("body").on('click', ".addlables", function(e) {
            e.preventDefault()
            $(".formdata").append(label);
        });
        $(document).on('click', '.removelabel', function() {
            $(this).closest('.colorlabelinput').remove();
            $('input[name="label[]"]').prop('required', false);
        });
</script>
@endpush
