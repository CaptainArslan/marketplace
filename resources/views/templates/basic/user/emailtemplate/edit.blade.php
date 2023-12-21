@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        @include($activeTemplate . 'user.customsetting')
    @endsection
    @section('customsetting')
        <div class="dashboard-area pt-50">
            <div class="container">
                <form action="{{ route('user.emailtemplate.update', $emailtemplate->id) }}" method="POST">
                    @csrf
                    <div class="form-group col-md-12">
                        <label class="fodnt-weight-bold">@lang('Template Name') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" placeholder="@lang('Template Name')"
                            name="templatename" value="{{ $emailtemplate->name ?? '' }}" required />
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
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="fodnt-weight-bold">@lang('Email Sent From') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" placeholder="@lang('Email address')"
                                name="email_from" value="{{ auth()->user()->email }}" required disabled />
                        </div>
                        <div class="form-group col-md-12" id='email_template__'>
                            <label class="font-weight-bold">@lang('Email Template') <span class="text-danger">*</span></label>
                            <textarea name="emailtemplate"rows="40" cols="100">
                              {{ $emailtemplate->email_template ?? '' }}
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
                    let shortcode = @json($emailtemplate->shortcodes);
                    let sc = JSON.parse(shortcode);
                    let html;
                    for (let [key, value] of Object.entries(sc)) {
                        console.log(key, value);
                        html += `<tr class="code">
                        <td data-label="@lang('Short Code')"><input type="text" class='border-0' size="30" value="${key}" name="codekey[]" required readonly></td>
                        <td data-label="@lang('Description')"><input type="text" class='border-0' size="70" value="${value}"name="codedetail[]" required readonly></td>
                    </tr>`
                    }


                    $(".list").append(html);



                    $(document).on('click', '.removecode', function() {
                        $(this).closest('.code').remove();
                    });
                </script>
            @endpush
