@extends($activeTemplate . 'layouts.frontend')
@section('content')
@if ($partial)
@include($activeTemplate . 'partials.dashboardHeader')
@endif
<section class="pt-100 pb-100">
    <div class="{{ $partial ? 'container' : 'container-fluid' }}">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card custom--card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-10 d-flex flex-wrap align-items-center">
                                <h4 class="ms-2">@lang('New Ticket For') {{ $product->name ??'' }} </h4>
                            </div>
                            <div class="col-sm-2 text-end">
                                @php
                                $url = $partial ? route('ticket') : route('iframe.api.ticket', ['token' => request()->token]);
                                @endphp
                                <a href="{{ $url }}" class="btn--base btn-sm">@lang('My Support Tickets')</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                        $url = ($partial) ? route('ticket.store') : route('iframe.api.ticket.store', ['token' => request()->token]);
                        @endphp
                        <form action="{{ $url }}" method="post" enctype="multipart/form-data" onsubmit="return submitUserForm();">
                            @csrf
                            <div class="row">
                                @if(isset($product))
                                <input type="hidden" class="form--control" name="product" value="{{$product->id}}">
                                @endif
                                <div class="form-group col-lg-6">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form--control" name="name" value="{{ @$user->firstname . ' ' . @$user->lastname }}" readonly>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>@lang('E-mail Address')</label>
                                    <input type="email" class="form--control" name="email" value="{{ @$user->email }}" readonly>
                                </div>
                                {{-- <div class="form-group col-lg-12 forseller">
                                        <input type="checkbox" name="ticket_for" class="ticket_for"
                                            value="{{ old('ticket_for') }}">
                                <label>@lang('For Specific Seller')</label>
                            </div>
                            <div class="form-group sellers d-none">
                                <label>@lang('Sellers') <sup class="text--danger">*</sup></label>
                                <select name="seller_id" id="seller" class="form--control">
                                    <option value="">Choose Seller</option>
                                    @foreach ($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->username }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="form-group col-lg-12">
                                <label>@lang('Subject')</label>
                                <input name="subject" value="{{ old('subject') }}" placeholder="@lang('Enter ticket subject')..." class="form--control" required>
                            </div>
                            <div class="form-group col-lg-12">
                                <label>@lang('Message')</label>
                                <textarea name="message" placeholder="@lang('Your reply')..." class="form--control"></textarea>
                            </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-10 col-9">
                                <label for="supportTicketFile" class="form-label">@lang('Select one file or multiple files')</label>
                                <input class="form-control custom--file-upload" type="file" accept=".jpg, .jpeg, .png, .pdf" name="attachments[]" multiple>
                                <div class="form-text text--muted">@lang('Allowed File Extensions: .jpg, .jpeg, .png, .pdf')</div>
                            </div>
                            <div class="col-md-2 col-3 text-end mt-2">
                                <a href="javascript:void(0)" onclick="extraTicketAttachment()" class="text-center w-100 py-2 bt-sm btn-sm btn--base reply-add">
                                    <i class="las la-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div id="fileUploadsContainer"></div>
                    </div>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn--base"><i class="lab la-telegram-plane"></i>
                            @lang('Submit')</button>
                        <button class=" btn btn--danger" type="button" onclick="formReset()">&nbsp;@lang('Cancel')</button>
                    </div>
                    </form>
                </div>
            </div><!-- card end -->
        </div>
    </div>
    </div>
</section>
@endsection


@push('script')
<script>
    "use strict";

    function extraTicketAttachment() {
        $("#fileUploadsContainer").append(`
                <div class="row">
                    <div class="col-md-10 col-9">
                        <input class="form-control custom--file-upload mt-3" type="file" accept=".jpg, .jpeg, .png, .pdf" name="attachments[]" multiple>
                    </div>
                    <div class="col-md-2 col-3 text-end mt-3">
                        <button type="button" class="text-center w-100 py-2 bt-sm btn-sm btn--base bg--danger cancel-attachment"><i class="las la-times"></i></button>
                    </div>
                </div>
            `)
    }

    function formReset() {
        // window.location.href = "{{ url()->current() }}"
        window.location.href = "javascript:window.history.back();"
    }

    $('body').on('click', '.cancel-attachment', function(e) {
        $(this).closest('.row').remove();
    })

    $('body').on('change', '.ticket_for', function(e) {
        if ($(this).is(":checked")) {
            $(this).val(1);
            $(".sellers").removeClass('d-none');
        } else {
            $(".sellers").addClass('d-none');
            $(this).val(0);
        }
    })
</script>
@endpush
@push('style')
<style>
    .cancel-attachment {
        height: 46px !important;
    }
</style>
@endpush