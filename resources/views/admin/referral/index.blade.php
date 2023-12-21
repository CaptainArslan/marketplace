@extends('admin.layouts.app')
@section('panel')
<div class="row">

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body parent">
                <div class="form-group mb-0">
                    <div class="input-group">
                        <input type="number" name="level" placeholder="@lang('Number of Level')" class="form-control input-lg levelGenerate">
                        <div class="input-group-append">
                            <button type="button" class="input-group-text border-0 btn btn--success btn-block generate">
                                @lang('Generate')
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{route('admin.store.refer')}}" method="post">
                    @csrf
                    <input type="hidden" name="commission_type" value="deposit_commission">
                    <div class="d-none levelForm">

                        <div class="form-group">
                            <label class="text--warning font-weight-bold"> @lang('Level & Commission :')
                                @lang('(Old levels will be removed after generating new levels)')
                            </label>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="description referral-desc">
                                        <div class="row">
                                            <div class="col-md-12 planDescriptionContainer">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary btn-block my-3">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @if($depositLevels->count())
    <div class="col-md-6">
        <div class="card border--primary">
            <div class="card-body">
                <div class="table-responsive--sm ">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Level')</th>
                                <th>@lang('Commission')</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($depositLevels as $key => $p)
                                <tr>
                                    <td data-label="Level">@lang('LEVEL')# {{ $p->level }}</td>
                                    <td data-label="Commission">{{ $p->percent }} %</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@push('script')
    <script>
        $(document).ready(function () {
            "use strict";

            var max = 1;
            $(document).ready(function () {
                $(".generate").on('click', function () {
                    var levelGenerate = $(this).parents('.parent').find('.levelGenerate').val();
                    var a = 0;
                    var val = 1;
                    var viewHtml = '';
                    if (levelGenerate !== '' && levelGenerate > 0) {
                        $(this).parents('.parent').find('.levelForm').removeClass('d-none');
                        $(this).parents('.parent').find('.levelForm').addClass('d-block');

                        for (a; a < parseInt(levelGenerate); a++) {
                            viewHtml += `<div class="input-group mt-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text no-right-border">LEVEL ${val}</span>
                            </div>
                            <input name="level[]" type="hidden" readonly value="${val}" required placeholder="Level">

                            <input name="percent[]" class="form-control margin-top-10" type="text" required placeholder="@lang("Commission Percentage")">

                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                                <button class="input-group-text border-0 btn btn--danger margin-top-10 delete_desc" type="button"><i class='fa fa-times'></i></button>
                            </div>
                        </div>`;
                            val++;

                        }
                         $(this).parents('.parent').find('.planDescriptionContainer').html(viewHtml);

                    } else {
                        alert('Level Field Is Required');
                    }
                });

                $(document).on('click', '.delete_desc', function () {
                    $(this).closest('.input-group').remove();
                });
            });


        });
    </script>
@endpush
