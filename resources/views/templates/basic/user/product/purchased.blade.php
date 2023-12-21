@extends($activeTemplate . 'layouts.frontend')

@push('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/starrr.css') }}">
@endpush
@php
    if (isset($pid)) {
        $nid = $pid;
    } else {
        $nid = null;
    }
@endphp
@section('content')
    <div class="pb-100">
        <script src="https://kit.fontawesome.com/0bb027dfd0.js" crossorigin="anonymous"></script>
        @include($activeTemplate . 'partials.dashboardHeader')
        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive--md mt-4">
                            <table id='data-table' class="table table-bordered data-table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('Purchase Code')</th>
                                        <th>@lang('Product Name')</th>
                                        <th>@lang('Support')</th>
                                        <th>@lang('Purchased At')</th>
                                        <th>@lang('Support End')</th>
                                        <th>@lang('Bump Fee')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Additional Info Required')</th>
                                        <th>@lang('Ticket')</th>
                                        <th>@lang('Action')</th>
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
    <script src="{{ asset($activeTemplateTrue . 'js/starrr.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        'use strict';

        $(document).ready(function() {
            var nid = @json($nid);
            if (nid == null) {
                nid = '';
            }
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.purchased.product', ['nid' => $nid]) }}",
                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'product_id',
                        name: 'product_id'
                    },
                    {
                        data: 'support',
                        name: 'support'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'support_time',
                        name: 'support_time'
                    },

                    {
                        data: 'bump_fee',
                        name: 'bump_fee'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'additionalinfo',
                        name: 'additionalinfo'
                    },
                                        {
                        data: 'createticket',
                        name: 'createticket'
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
                            `<table class="table bumptable${data.id} d-none" border=1><thead><tr><th>Bump Name</th><th>Require</th></tr></thead><tbody>`;
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
                        $('body').on('click', `.viewdetails${data.id}`, function() {
                           sel = $(sel).removeClass('d-none');
                            Swal.fire(
                                sel
                            )
                        });
                    }
                    $(`.reviewBtn${data.id}`).on('click', function() {
                        var modal = $(`#reviewModal${data.id}`);
                        modal.find(`input[name="product_id${data.id}"]`).val($(this).data(
                            'id'));
                        var $s2input = $(`input[name="rating${data.id}"]`);
                        var index = 5;
                        var i = 0;
                        for (i; i < indx; i++) {
                            $(`#star${i}`).starrr({
                                max: 5,
                                rating: 5,
                                change: function(e, value) {
                                    $s2input.val(value).trigger('input');
                                }
                            });

                        }
                    });
                    if (data.productcustomfields.length > 0) {
                        if (data.customfieldresponse.length > 0 || data.customfieldresponse != null) {
                            var cf = data.customfieldresponse.reduce((acc, curr) => {
                                acc[curr.customfield_id] = curr.field_value;
                                return acc;
                            }, {});
                        }
                        var modalHtml = `<div class="modal fade" id="addcustomfieldmodal${data.id}"  data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.late.customfield') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4>@lang('Fill the Following CustomField')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">

                        <input type='hidden' class="form--control" name='sell_id' value="${data.id}"
                                required>
                        <div class="col-lg-12">
                            <p class="text-success"></p>`;
                        data.productcustomfields.forEach(x => {
                            let clr
                            var o = new Object();
                            if (x.customfields.type == 'color') {
                                let inv = cf[x.customfield_id] ?? '';
                                if (inv != '') {
                                    clr = JSON.parse(inv.replaceAll('&quot;', '"'));
                                }
                                x.customfields.customfielditem.forEach(y => {
                                    let label = y.label
                                    modalHtml += `<label>${y.label}</label><br>
                                <input type="` + x.customfields.type + `" class="form--control"
                                    name='field_label[${y.id}]'
                                    value="${(clr ? clr[y.label]:'')}">`;
                                });

                            } else if (x.customfields.type != 'color') {
                                modalHtml += `<div class="col-lg-12 form-group">
                                                        <label>${x.customfields.name}</label><br>
                                                        <input type='${x.customfields.type}' class="form--control"
                                                            name='field_id[${x.customfield_id}]'
                                                            value="${cf[x.customfield_id] ?? ''}">
                                                    </div>`;
                            }
                            modalHtml += `<input type="hidden" class="form--control"
                                    name='field_id[]'
                                    value="${x.customfield_id}">`
                        });

                        if (data.request_by == 0) {

                            if (data.customfieldresponse.length <= 0 || data.customfieldresponse ==
                                null || (data.request_by == 0 && data.approve_edit == 1)) {
                                modalHtml += `<button type="submit" name="submitbutton${data.id}" class="btn btn-md px-4 btn--base" data-submit='addcustomfieldmodal${data.id}' >Submit
                                changes</button>
                            <span class="text-danger mb-7" style="color:red">Note:Please Update the above
                                CustomField</span>`;
                            } else if (data.request_by == 0 && data.approve_edit == 0) {
                                modalHtml += `<a  data-value="1" class="btn request_edit${data.id}
                            ajax btn-md px-4 btn--base">Request for edit</a>`;
                            } else {
                                modalHtml += `<span style=" font-size:px;color:red">Note: No Updated Response is provided from the
                                            Buyer</span>`;
                            }
                        } else {
                            modalHtml += `<span class="text-danger mb-7" style="color:red">Note: Your Current Request is Under
                            Process</span>`
                        }
                        modalHtml += `</div></form></div></div></div>`;
                        $('body').append(modalHtml);
                        cf = data.customfieldresponse.reduce((acc, curr) => {
                            acc[curr.customfield_id] = curr.field_value;
                            return acc;
                        }, {});
                        let xcf = Object.keys(cf);
                        $('body').on('mouseover', `.editfieldresponse${data.id}`, function(e) {
                            if (data.request_by == 1 && data.approve_edit == 0) {
                                $(this).prop("title", "Your Current Request is Under Proces");
                                // Swal.fire({
                                //     icon: 'error',
                                //     title: 'Oops...',
                                //     text: 'Your Current Request is under Process!',
                                // })
                            }
                        });
                        $('body').on('click', `.request_edit${data.id}`, function(e) {
                            e.preventDefault();
                            $.ajax({
                                url: "{{ route('user.edit.request') }}/" + data.id,
                                success: function(data) {
                                    $(".text-success").text(data);
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
                    var review = `<div class="modal fade" id=reviewModal${data.id} data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.rating') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4>@lang('Give Review')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <label for="">@lang('Give your rating')</label><br>
                            <div class='starrr' id='star${data.id}'></div>

                            <input type='hidden' name="rating${data.id}" value='0' id='star2_input' required>
                            <input type="hidden" name="product_id${data.id}" value="" required>

                            <div class="form-group">
                                <label for="">@lang('Write your opinion')</label>
                                <textarea name="review" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-md px-4 btn--danger"
                        data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn-md px-4 btn--base">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>`;
                    $('body').append(review);
                }

            });
        });
    </script>
@endpush
