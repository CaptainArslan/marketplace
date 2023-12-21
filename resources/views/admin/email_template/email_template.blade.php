@extends('admin.layouts.app')


@section('panel')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive table-responsive--sm">
                        <table class=" table align-items-center table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Short Code') </th>
                                    <th>@lang('Email Body')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr>
                                    <td data-label="@lang('Short Code')">@{{ name }}</td>
                                    <td data-label="@lang('EmailBody')">@lang('User Name')</td>
                                </tr>
                                <tr>
                                    <td data-label="@lang('Short Code')">@{{ body }}</td>
                                    <td data-label="@lang('EmailBody')">@lang('Body')</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="uploadImage" tabindex="-1" aria-labelledby="uploadImageLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadImageLabel">Upload Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img class="modal_image_pre mx-auto d-block" src="" alt="">
                        <form id="saveemaillogo" class="mt-3">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" name='newemaillogo' class="custom-file-input"
                                        id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label m-0" for="inputGroupFile01">Choose file</label>
                                </div>

                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-body">
                    <form action="{{ route('admin.email.template.global') }}" method="POST">
                        @csrf
                        <div class="form-row">

                            <div class="form-group col-md-12">
                                <label class="fodnt-weight-bold">@lang('Email Sent From') <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" placeholder="@lang('Email address')"
                                    name="email_from" value="{{ $general_setting->email_from }}" required />
                            </div>
                            <div class="form-group col-md-12" id='email_template__'>
                                <label class="font-weight-bold">@lang('Email Body') <span
                                        class="text-danger">*</span></label>
                                <textarea name="email_template" rows="10" class="form-control form-control-lg nicEdit"
                                    placeholder="@lang('Your email template')">{{ $general_setting->email_template }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-block btn--primary mr-2 col">@lang('Update')</button>
                            <button type="button" class="btn btn-block btn--primary mr-2 col mt-0"
                                onclick="emailTemplate(true)">Edit</button>
                            <button type="button" class="btn btn-block btn--primary mr-2 col mt-0" data-toggle="modal"
                                data-target="#uploadImage">Upload logo</button>
                        </div>
                    </form>
                </div>
            </div><!-- card end -->
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script>
        function changeImage(src) {
            let modal_image_pre = document.querySelector('.modal_image_pre');
            modal_image_pre.src = src;
            document.querySelector('#email_template__+ table').querySelector('img').src = src;
        }

        function emailTemplate(openEditor = false, src) {
            let waitxTimes = 5;
            let waitforElem = setInterval(() => {
                let x = document.querySelector('#nicEditor0');
                let emailEditor = document.querySelector('#email_template__');
                waitxTimes < 0 ? clearInterval(waitforElem) : waitxTimes--;
                if (openEditor) {
                    clearInterval(waitforElem);
                    emailEditor.style.display = 'block';
                    document.querySelector('.Email__Template__')?.remove();
                } else {
                    if (x) {
                        clearInterval(waitforElem);
                        xs = x.innerText;
                        document.querySelector('.Email__Template__')?.remove();
                        emailEditor.insertAdjacentHTML('afterend', xs);
                        emailEditor.style.display = 'none';
                        let
                            y = document.querySelector('#email_template__+ table');
                        y.classList.add('Email__Template__');
                        let
                            editorLabels = document.querySelectorAll('.nicEdit-panel> div');
                        for (var i = 0; i < editorLabels.length - 1; i++) {
                            editorLabels[i].remove();
                        }
                        emailEditor.style.display = 'none';
                        changeImage(src);
                    }
                }
            }, 1000);
        }
        let sajjadImage;
        let changeLogo = document.querySelector('#inputGroupFile01');
        if (changeLogo) {
            changeLogo.addEventListener('change', () => {
                let src = changeLogo.value;
                src = src.split('fakepath\\')[1];
                sajjadImage = src;
                document.querySelector('#uploadImage .modal-body img')?.remove();
                document.querySelector('.image_name__')?.remove();
                $('#uploadImage .modal-body').prepend('<span class="image_name__ mb-3 d-block">' + src + '</span>')
            })
        }
        $.ajax({
            url: "{{ route('admin.email.logo') }}",
            success: function(res) {
                console.log(res);
                emailTemplate(null, res.image);
            }
        });
        $('body').on('submit', '#saveemaillogo', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('_token', $('input[name="_token"]').val());
            $.ajax({
                url: "{{ route('admin.email.savelogo') }}",
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result.error) {
                        alert(result.error);
                    }
                },
                complete: function(){
                    $('.modal').modal('hide');
                }
            });
        })
    </script>

    <script></script>
@endsection
