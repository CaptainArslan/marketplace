@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')

        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">


                        <div class="table-responsive--md mt-4">

                            <table id='data-table' class="table table-bordered data-table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('MeetingAgenda')</th>
                                        <th>@lang('Meeting Date')</th>
                                        <th>@lang('Meeting Time')</th>
                                        <th>@lang('Meeting Link')</th>
                                        <th>@lang('Status')</th>
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
    <script>
        "use strict";
        function getstatus(data) {
return data.status;
                    }
        $(document).ready(function() {
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.meeting.all') }}",
                columns: [{
                        data: 'agenda',
                        name: 'agenda'
                    },
                    {
                        data: 'meeting_date',
                        name: 'meeting_date'
                    },
                    {
                        data: 'meeting_time',
                        name: 'meeting_time'
                    }, {
                        data: 'meeting_link',
                        name: 'meeting_link'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ], // <-- Missing comma was added here
                "createdRow": function(row, data, dataIndex) {
                    var modalHtml = `
               <div id="approveModal${data.id }" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item dark-bg">@lang('Agenda') : <span class="meeting-agenda ">${data.agenda}</span>
                        </li>
                        <li class="list-group-item dark-bg">@lang('Meeting Date') : <span class="meeting-date">${data.meeting_date}</span>
                        </li>
                        <li class="list-group-item dark-bg">@lang('Meeting Time') : <span class="meeting-time">${data.meeting_time}</span>
                        </li>
                        <li class="list-group-item dark-bg">@lang('Status') : <span class="meeting-status ">${data.status}</span>
                        </li>
                    </ul>
                    <ul class="list-group meeting-detail mt-1">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn-sm"
                        data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
    `;


                    $('body').append(modalHtml);
                    var responseModal = `<div class="modal col-md-12 fade" id="m_${data.id}" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.meeting.response') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4>@lang('Meeting Details')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-12>
                            <input type='hidden' class="form--control" name='meeting_id' value="${data.id}"
                                required>
                            <div class="col-lg-12 form-group">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>@lang('Meeting Agenda') <sup class="text--danger">*</sup></label>
                                    </div>
                                    <input type="hidden" name="meeting id" value="${data.id}"required>
                                    <input type="text" name="agenda" value="${data.agenda}"
                                        placeholder="@lang('Enter Meeting agenda')" class="form--control" disabled>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Meeting Date') <sup class="text--danger">*</sup></label>
                                        <input type="date" value="${data.meeting_date}" name="meetingdate"
                                            placeholder="@lang('Enter Meeting Date')" class="form--control" disabled>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>@lang('Meeting Time') <sup class="text--danger">*</sup></label>
                                        <input type="time" value="${data.meeting_time}" name="meetingtime"
                                            placeholder="@lang('Enter Meeting Time')" class="form--control" disabled>
                                    </div>
                                </div>
                                <div class="col-lg-12 ${data.status.includes("Approved") ? 'd-none':''}">
                                    <div class="form-group">
                                        <label>@lang(' Add Meeting Link') <sup class="text--danger">*</sup></label>
                                    </div>
                                    <input type="text" name="meetinglink"
                                        placeholder="@lang('Enter the meeting Link')" class="form--control">
                                        <span class="text-danger mb-7" style="color:red">Note:Please Give Link if Available</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer requestbuttons">
                    ${data.status.includes("Pending") ? `<button type="submit" name="submitbutton" class="btn btn-md px-4 btn--base avbtn d-none">Available</button>
                        <button type="submit" name="notavbtn" class="btn btn-md px-4 btn--base notavbtn">Not Available</button>` : ''}
                </div>
            </form>
        </div>
    </div>`;

                    $('body').append(responseModal);
                    let x = document.querySelector(`#m_${data.id}`);
                    let xinput = x.querySelector('input[name="meetinglink"]')
                    xinput.addEventListener('keyup',(e)=>{
                        let valueInput = e.target.value.trim();
                        console.log(e.target.value);
                        if (valueInput != '') {
                        console.log('success');
                            $('.notavbtn').addClass("d-none");
                            $('.avbtn').removeClass("d-none");
                        } else {
                            $('.notavbtn').removeClass("d-none");
                            $('.avbtn').addClass("d-none");
                        }
                    })
                }
            });
        });
    </script>
@endpush
