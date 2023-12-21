@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="pb-100">
        @include($activeTemplate . 'partials.dashboardHeader')
        @include($activeTemplate . 'user.customsetting')
    @endsection
    @section('customsetting')
        <div class="dashboard-area pt-50">
            <div class="container">
                <form action="{{ route('user.update.customcss') }}" method="POST">
                    @csrf
                    <label>Write Your Css</label>
                    <div class="card-body">
                        <div class="form-group custom-css">
                            <textarea class="form-control" rows="10" name="customcss" id="customCss">{{ $newcs->styletag ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-block btn--primary mr-2 col">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @push('script')
        <!-- NicEdit js-->
        <script src="{{ asset($activeTemplateTrue . 'js/nicEdit.js') }}"></script>
    @endpush
