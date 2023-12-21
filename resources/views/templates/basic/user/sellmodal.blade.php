<div class="modal fade" id="m_{{ $m_id ?? '' }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                            <p class="text-success"></p>
                            @php
                                $cf = $item->customfieldresponse->pluck('field_value', 'customfield_id')->toArray();
                            @endphp
                            @foreach ($item->productcustomfields as $field)
                                @if ($field->customfields->type == 'color')
                                    <label>Primary Color</label><br>
                                    <input type={{ $field->customfields->type }} class="form--control"
                                        name='field_id[{{ $field->customfield_id }}]'
                                        value="{{ json_decode($cf[$field->customfield_id])->primary }}" disabled>
                                    <label>Seccondary Color</label><br>
                                    <input type={{ $field->customfields->type }} class="form--control"
                                        name='field_id[{{ $field->customfield_id }}]'
                                        value="{{ json_decode($cf[$field->customfield_id])->seccondary }}" disabled>
                                @else
                                    <div class="col-lg-12 form-group">
                                        <label>{{ $field->customfields->name }}</label><br>
                                        <input type='text' class="form--control"
                                            name='field_id[{{ $field->customfield_id }}]'
                                            value="{{ $cf[$field->customfield_id] ?? '' }}" disabled>
                                    </div>
                                @endif
                            @endforeach
                            {{-- @php
                             $fieldresponse = 0;
                             $changes = 0;
                             @endphp
                            @foreach ($customfieldresponse as $field)
                            <div class="col-lg-12 form-group">
                                 @php
                                if ($field->product_id == $item->product->id){
                             $fieldresponse = 0;
                             if($field->req_by_buyer == 1){
                                   $changes=1;
                                }
                                }
                             @endphp
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}" required>
                                    <label> {{ Str::title($field->field_name) }}</label><br>
                                    <input type="{{ $field->customfield->type }}" name="field_value[]" class="form--control"
                                        value="{{ $field->field_value }}" placeholder="{{ $field->customfield->type }}"
                                        disabled>
                                @else
                                @endif --}}

                            {{-- @php
                                    $response = \App\CustomFieldResponse::where('product_id', $item->product->id)
                                        ->where('buyer_id', auth()->user()->id)
                                        ->where('customfield_id', $field->id)
                                        ->first();
                                @endphp --}}
                            {{-- @endforeach --}}
                            {{-- @if ($fieldresponse == 0)
                            <span style="color:red">Note: No Response is provided from the Buyer</span>
                            @endif --}}

                            <div class="col-md-12 form-group">
                                <label>@lang('Upload Choice') <sup class="text--danger">*</sup></label>

                                <select name="uploadchoice" id="uploadchoice" class="form--control" required>
                                    <option value="">Choose Method</option>
                                    <option value="1">@lang('Zip File')</option>
                                    <option value="2">@lang('URL')</option>
                                </select>
                            </div>
                            <div class="col-lg-12 form-group zip d-none">
                                <label>@lang('Upload File') <code>(@lang('only zip'))</code> <sup
                                        class="text--danger">*</sup></label>
                                <div id="uploader" class="it">
                                    <div class="row uploadDoc">
                                        <div class="col-xxl-12 col-xl-12">
                                            <div class="fileUpload btn btn-orange">
                                                <img src="{{ asset('assets/images/first.svg') }}" class="icon">
                                                <span class="upl fs-12px" id="upload">@lang('Upload')</span>
                                                <input type="file" class="upload up from--control zipfile"
                                                    name="file" accept=".zip" onchange="fileURL(this);" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 form-group url d-none">
                                <label>@lang('Source Link') <sup class="text--danger">*</sup></label>
                                <input type="url" name="sourcelink" placeholder="@lang('Enter product Source URL')"
                                    class="form--control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @if ($item->request_by == 1 && $item->approve_edit == 0)
                        <a data-id="{{ $item->id }}"
                            class="btn requestbuttons request_allow ajax btn-md px-4 btn--base">Allow Changes</a>
                    @elseif($item->request_by == 0 && $item->approve_edit == 0 && count($cf) != 0)
                        <button type="submit" class="btn btn-md px-4 btn--base">Upload Content</button>
                        <a data-id="{{ $item->id }}" class="btn request_edit ajax btn-md px-4 btn--base">Request
                            Changes</a>
                    @else
                        <span style=" font-size:px;color:red">Note: No Updated Response is provided from the
                            Buyer</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
