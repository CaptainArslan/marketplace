@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.setplan.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="form-row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>@lang('Name')</label>
                                                <input class="form-control" type="text" name="name">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>@lang('Price')</label>
                                                <input name="price" class="form-control" type="number"
                                                    placeholder="@lang('EnterPrice')" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('Select Type')</label>
                                                <select name="plantype" class="form-control" required>
                                                    <option value="1">@lang('Monthly')</option>
                                                    <option value="2">@lang('OnTime')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>@lang('Select Plan Type')</label>
                                                <select name="Commission type" class="form-control" required>
                                                    <option value="1">@lang('Percentage')</option>
                                                    <option value="2">@lang('Fixed')</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
