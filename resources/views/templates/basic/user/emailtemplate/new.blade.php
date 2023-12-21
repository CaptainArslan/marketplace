@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        @include($activeTemplate . 'user.customsetting')
    @endsection
    @section('customsetting')
        <div class="dashboard-area pt-50">
            <div class="container">
                <form action="{{ route('user.emailtemplate.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-12">
                        <label class="fodnt-weight-bold">@lang('Template Name') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" placeholder="@lang('Template Name')"
                            name="templatename" value="" required />
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive table-responsive--sm">
                                    <table class=" table align-items-center table--light">
                                        <thead>
                                            <tr>
                                                <th>@lang('Short Code') </th>
                                                <th>@lang('Description')</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            <tr class="code">
                                                <td data-label="@lang('Short Code')"><input type="text" class='border-0'
                                                        size="30" name="codekey[]" value="emaillogo" readonly></td>
                                                <td data-label="@lang('Description')"><input type="text" class='border-0'
                                                        size="70" name="codedetail[]"
                                                        value="Your Company Logo uploaded in the profile Section" readonly>
                                                </td>
                                            </tr>
                                            <tr class="code">
                                                <td data-label="@lang('Short Code')"><input type="text" class='border-0'
                                                        size="30" name="codekey[]" value="name" readonly>
                                                </td>
                                                <td data-label="@lang('Description')"><input type="text" class='border-0'
                                                        size="70" name="codedetail[]" value="Buyer Name" readonly>
                                                </td>
                                            </tr>
                                            <tr class="code">
                                                <td data-label="@lang('Short Code')"><input type="text" class='border-0'
                                                        size="30" name="codekey[]" value="method_name" readonly></td>
                                                <td data-label="@lang('Description')"><input type="text" class='border-0'
                                                        size="70" name="codedetail[]" value="Payment method Name"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="code">
                                                <td data-label="@lang('Short Code')"><input type="text" class='border-0'
                                                        size="30" name="codekey[]" value="total_amount" readonly> </td>
                                                <td data-label="@lang('Description')"><input type="text" class='border-0'
                                                        size="70" name="codedetail[]" value="The total Cart Amount"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="code">
                                                <td data-label="@lang('Short Code')"><input type="text" class='border-0'
                                                        size="30" name="codekey[]" value="post_balance" readonly> </td>
                                                <td data-label="@lang('Description')"><input type="text" class='border-0'
                                                        size="70" name="codedetail[]" value="Buyer Remaining balance"
                                                        readonly>
                                                </td>
                                            </tr>
                                            <tr class="code">
                                                <td data-label="@lang('Short Code')"><input type="text" class='border-0'
                                                        size="30" name="codekey[]" value=" product_list" readonly></td>
                                                <td data-label="@lang('Description')"><input type="text" class='border-0'
                                                        size="70" name="codedetail[]"
                                                        value="The product details(Name,Price,Bumpfee,support,Supportfee,Supporttime)"
                                                        readonly>
                                                </td>
                                            </tr>

                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="d-flex my-4" style="align-items: center; gap: 20px">
                            <div class="profile-thumb ps-2 " style="width: 6rem ; height: 6rem ;">
                                <div class="avatar-preview">
                                    <div class="profilePicPreview "
                                        style="width: 6rem ; height: 6rem ;background-image: url({{ getImage(imagePath()['profile']['user']['path'] . '/' . auth()->user()->company_logo ? :'', imagePath()['profile']['user']['size']) }})"
                                        alt="company Logo">
                                    </div>
                                </div>
                                <div class="avatar-edit">
                                    <input type='file' name="logoimage" class="logoPicUpload" id="profilePicUpload3"
                                        accept=".png" />
                                    <label for="profilePicUpload3" style="width: 30px;height: 30px;line-height: 24px;"><i
                                            class="la la-pencil"></i></label>
                                </div>
                            </div>
                            <labeL class="fodnt-weight-bold">@lang('Company Logo(OPTIONAL)')</label>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="fodnt-weight-bold">@lang('Email Sent From') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" placeholder="@lang('Email address')"
                                name="email_from" value="{{ auth()->user()->email }}" required disabled />
                        </div>
                        <div class="form-group col-md-12" id='email_template__'>
                            <label class="font-weight-bold">@lang('Description') <span class="text-danger">*</span></label>
                            <textarea name="emailtemplate"rows="40" cols="100">
                              Welcome to TinyMCE!
                            </textarea>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-block btn--primary mr-2 col">@lang('Update')</button>
                    </div>
                </form>
            @endsection




            @push('script')
                <!-- NicEdit js-->
                <script src="{{ asset($activeTemplateTrue . 'js/nicEdit.js') }}"></script>
                <script>
                    tinymce.init({
                        selector: 'textarea',
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat | code',
                        tinycomments_mode: 'embedded',
                        tinycomments_author: 'Author name',
                        mergetags_list: [{
                                value: 'First.Name',
                                title: 'First Name'
                            },
                            {
                                value: 'Email',
                                title: 'Email'
                            },
                        ]
                    });
                </script>
                <script>
                    "use strict";

                    $("body").on("click", ".addshortcodes", function() {
                        let html =
                            `<tr class="code">
                        <td data-label="@lang('Short Code')"><input type="text" class='border-0' size="30" name="codekey[]" required></td>
                        <td data-label="@lang('Description')"><input type="text" class='border-0' size="70" name="codedetail[]"><button type="button" class="removecode"><i class="las la-times" required></i></button></td>
                    </tr>`
                        $(".list").append(html);

                    });

                    $(document).on('click', '.removecode', function() {
                        $(this).closest('.code').remove();
                    });

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
                    $(".logoPicUpload").on('change', function() {
                        proPicURL(this);
                    });

                    $(".remove-image").on('click', function() {
                        $(".profilePicPreview").css('background-image', 'none');
                        $(".profilePicPreview").removeClass('has-image');
                    });
                </script>
            @endpush
