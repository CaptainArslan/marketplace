@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="pb-100">
    @include($activeTemplate . 'partials.dashboardHeader')
    <div class="pb-100">
        <div class="dashboard-area pt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tab-content-area">
                            <div class="user-profile-area">
                                <form action="{{ route('user.upload.build') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label>@lang('Upload your build')</label>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-lg-6 form-group">
                                            <label>@lang('Please select app')</label>
                                            <select name="app_type" id="" class="form--control w-100" required>
                                                <option value="">Please Select</option>
                                                <option value="beta">Beta</option>
                                                <!-- <option value="live">Production</option> -->
                                            </select>
                                            <!-- <input type="text" name="firstname" value="" class="form--control"> -->
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>@lang('Your build zip')</label>
                                            <input type="file" name="file" value="{{ old('file') }}" class="form--control" required>
                                        </div>
                                        <div class="col-lg-12 form-group text-end">
                                            <button type="submit" class="btn btn--base w-100">@lang('Update Now')</button>
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
</div>
@endsection
@push('script')

@endpush