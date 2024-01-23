@extends($activeTemplate . 'layouts.frontend')
@php
if (isset($pid)) {
$notifyid = $pid;
} else {
$notifyid = '';
}
@endphp
@section('content')
<div class="pb-100">
    @if ($partial)
    @include($activeTemplate . 'partials.dashboardHeader')
    @endif
    <div class="dashboard-area pt-50">
        <div class="{{ $partial ? 'container' : 'container-fluid' }}">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mt-4">
                        <table id='data-table' class="table table-bordered data-table custom--table responsive w-100 display">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Product')</th>
                                    <th>@lang('Licence Type')</th>
                                    <th>@lang('Support Time')</th>
                                    <th>@lang('Product Price')</th>
                                    <th>@lang('Support Fee')</th>
                                    <th>@lang('Bumps Fee')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Source Required')</th>
                                    <th>@lang('Actions')</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('body').on('submit', '#sellmodalform', function(e) {

    })
</script>
<script>
    $(document).ready(function() {
        var nid = @json($notifyid);

        var api = @json($api);
        var token = @json($token);

        let val = {
            ":token": token,
            ':api': api,
            '&amp;': "&"
        }

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: api ? decodeURIComponent("{{ route('iframe.api.sell.log', ['api' => ':api','token'=>':token']) }}").replace(/:token|:api|&amp;/gm, (m) => (val[m] ?? m)) : "{{ route('user.sell.log') }}",
            columns: [{
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'product_id',
                    name: 'product_id'
                },
                {
                    data: 'license',
                    name: 'license'
                },
                {
                    data: 'support_time',
                    name: 'support_time'
                },
                {
                    data: 'product_price',
                    name: 'product_price'
                },
                {
                    data: 'support_fee',
                    name: 'support_fee'
                },
                {
                    data: 'bump_fee',
                    name: 'bump_fee'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'source_required',
                    name: 'source_required'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "createdRow": function(row, data, dataIndex) {
                var sel = '';
                if (data.bump_fee != 0) {
                    var item = dataIndex
                    sel +=
                        '<table class="table d-none" border=1><thead><tr><th>Bump Name</th><th>Require</th></tr></thead><tbody>';
                    data.bumpresponses.forEach(x => {
                        sel += `<tr>`;
                        sel += `<td>${x.bump.name}</td>`;
                        if (x.pages != 0) {
                            sel += `<td> ${x.pages}</td>`;
                        } else {
                            sel +=
                                `<td><span class="badge badge--success">@lang('Yes')</span></td>`;
                        }
                        sel += `</tr>`;
                    });
                    sel += '</tbody></table>';
                    $('body').append(sel);
                    $('.viewdetails' + data.id).on('click', function() {
                        sel = $(sel).removeClass('d-none');
                        Swal.fire(
                            sel
                        )
                    });
                }
                if (data.productcustomfields.length > 0) {
                    if (data.customfieldresponse.length > 0 || data.customfieldresponse != null) {
                        var cf = data.customfieldresponse.reduce((acc, curr) => {
                            acc[curr.customfield_id] = curr.field_value;
                            return acc;
                        }, {});
                    }
                    var modalHtml = `<div class="modal fade" id="addsourcemodal${data.id}"  data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.addsource.product') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4>@lang('Add the Source of the Product')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="text-success"></p>`;
                    data.productcustomfields.forEach(x => {
                        let clr
                        if (x.customfields.type == 'color') {
                            let inv = cf[x.customfield_id] ?? '';
                            if (inv != '') {
                                clr = JSON.parse(inv.replaceAll('&quot;', '"'));
                            }
                            x.customfields.customfielditem.forEach(y => {
                                modalHtml += `<label>${y.label}</label><br>
                                <input type="` + x.customfields.type + `" class="form--control"
                                    name='field_id[${y.label}]'
                                    value="${(clr ? clr[y.label]:'')}" disabled>`;
                            });


                        } else if (x.customfields.type != 'color') {
                            modalHtml += `<div class="col-lg-12 form-group">
                                                        <label>${x.customfields.name}</label><br>
                                                        <input type='${x.customfields.type}' class="form--control"
                                                            name='field_id[` + x.customfield_id + `]'
                                                            value="${cf[x.customfield_id] ?? ''}" disabled>
                                                    </div>`;
                        }

                    });
                    modalHtml += `<div class="col-md-12 form-group"><label>@lang('Upload Choice') <sup class="text--danger">*</sup></label>
                                <select name="uploadchoice" id="uploadchoice${data.id}" class="form--control" required>
                                    <option value="">Choose Method</option>
                                    <option value="1">@lang('Zip File')</option>
                                    <option value="2">@lang('URL')</option>
                                </select>
                            </div>                     <div class="col-lg-12 form-group zip${data.id} d-none">
                                <label>@lang('Upload File') <code>(@lang('only zip'))</code> <sup
                                        class="text--danger">*</sup></label>
                                <div id="uploader" class="it">
                                    <div class="row uploadDoc">
                                        <div class="col-xxl-12 col-xl-12">
                                            <div class="fileUpload btn btn-orange">
                                                <img src="{{ asset('assets/images/first.svg') }}" class="icon">
                                                <span class="upl fs-12px" id="upload">@lang('Upload')</span>
                                                <input type="file" class="upload up from--control zipfile${data.id}"
                                                    name="file" accept=".zip" onchange = "fileURL(this)"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group url` + data.id + ` d-none">
                                <label>@lang('Source Link') <sup class="text--danger">*</sup></label>
                                <input type="url" name="sourcelink` + data.id + `" placeholder="@lang('Enter product Source URL')"
                                    class="form--control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">`;
                    if (data.request_by == 1 && data.approve_edit == 0) {
                        modalHtml +=
                            `<a class="btn requestbuttons request_allow${data.id} ajax btn-md px-4 btn--base">Allow Changes</a>`;
                    } else if (data.request_by == 0 && data.approve_edit == 0 && data
                        .customfieldresponse.length > 0 && data.customfieldresponse != null) {
                        modalHtml += `<button type="submit" class="btn btn-md px-4 btn--base">Upload Content</button>
                        <a data-id="${data.id}" class="btn request_edit${data.id} ajax btn-md px-4 btn--base">Request
                            Changes</a>`;
                    } else {
                        modalHtml += `<span style=" font-size:px;color:red">Note: No Updated Response is provided from the
                            Buyer</span>`;
                    }
                    modalHtml += `</div></form></div> </div> </div>`;
                    $('body').append(modalHtml);

                    $('body').on('mouseover', `.editfieldresponse${data.id}`, function(e) {
                        if (data.request_by == 1 && data.approve_edit == 0) {
                            $(this).prop("title", "Buyer is asking for Changes");

                            // Swal.fire(
                            //     'Buyer Request for changes',
                            //     '',
                            //     'question'
                            // )
                        }
                    });
                    $('body').on('change', `#uploadchoice${data.id}`, function(e) {
                        e.preventDefault();
                        var data1 = $(this).val();
                        if (data1 == 1) {
                            $(`.zip${data.id}`).removeClass('d-none ');
                            $(`input[name="zipfile${data.id}"]`).required = true;
                            $(`input[name="soucelink${data.id}"]`).required = false;
                            $(`.url${data.id}`).addClass('d-none');
                        } else if (data1 == 2) {
                            $(`.zip${data.id}`).addClass('d-none');
                            $(`input[name="zipfile${data.id}"]`).required = false;
                            $(`.url${data.id}`).removeClass('d-none');
                            $(`input[name="soucelink${data.id}"]`).required = true;
                        } else {
                            $(`.zip${data.id}`).addClass('d-none');
                            $(`.url${data.id}`).addClass('d-none');
                            $(`input[name="zipfile${data.id}"]`).required = true;
                            $(`input[name="soucelink${data.id}"]`).required = false;
                        }
                    });
                    $('body').on('click', `.request_allow${data.id}`, function(e) {

                        $.ajax({
                            url: "{{ route('user.edit.allow') }}/" + data.id,
                            success: function(data2) {
                                $(".text-success").text(data2);
                                $('.requestbuttons').addClass('d-none');
                                $(".modal").modal('hide');
                                Swal.fire(
                                    'Done!',
                                    data,
                                    'success'
                                )
                            }
                        });
                    });
                    $('body').on('click', `.request_edit${data.id}`, function(e) {
                        e.preventDefault();
                        $.ajax({
                            url: "{{ route('user.request.edit') }}/" + data.id,
                            success: function(data) {
                                $('.requestbuttons').addClass('d-none');
                                $(".modal").modal('hide');
                                Swal.fire(
                                    'Done!',
                                    data,
                                    'success'
                                )

                            }
                        });
                    });
                }

            }
        });
    });
</script>
@endpush