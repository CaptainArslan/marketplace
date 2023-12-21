@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php
    use Carbon\Carbon;
@endphp
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tab-content-area">
                            <div class="user-profile-area">
                                <form action="{{ route('user.meeting.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                             <input type="hidden" name="product_id" value="{{ $product_id }}"
                                                    class="form--control" required>
                                            <div class="form-group">
                                                <label>@lang('Meeting agenda') <sup class="text--danger">*</sup></label>
                                                <input type="text" name="agenda" placeholder="@lang('Enter agenda or subject')"
                                                    class="form--control" required>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label>@lang('Meeting Date') <sup class="text--danger">*</sup></label>
                                                    <input type="date" name="meetingdate"
                                                    min="{{Carbon::now()->format('Y-m-d')}}" placeholder="@lang('Enter Meeting Date')"
                                                        class="form--control" required>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label>@lang('Meeting Time') <sup class="text--danger">*</sup></label>
                                                    <input type="time" name="meetingtime"
                                                min="08:00:00" max="20:00:00"
                                                placeholder="@lang('Enter Meeting Time')"
                                                        class="form--control" required>
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
@endpush
