{{-- <div class="modal col-md-12 fade " id="m_{{ $m_id ?? '' }}" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.late.customfield') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4>@lang('Fill the Following CustomField')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="text-success"></p>
                            @php
                                $cf = $item->customfieldresponse->pluck('field_value', 'customfield_id')->toArray();

                            @endphp
                            <input type='hidden' class="form--control" name='sell_id' value="{{ $item->id }}"
                                required>

                            @foreach ($item->productcustomfields as $field)
                                <div class="col-lg-12 form-group">
                                    @if ($field->customfields->type == 'color')
                                        <label>Primary Color</label><br>
                                        <input type={{ $field->customfields->type }} class="form--control"
                                            name='field_id[]'
                                            value="{{(json_decode($cf[$field->customfield_id] ?? '')->primary ?? '') }}"
                                            required>
                                        <label>Seccondary Color</label><br>
                                        <input type={{ $field->customfields->type }} class="form--control"
                                            name='field_id[{{ $field->customfield_id }}]'
                                            value="{{(json_decode($cf[$field->customfield_id] ?? '')->seccondary ?? '') }}"
                                            required>
                                    @else

                                        <label>{{ $field->customfields->name }}</label><br>
                                        <input type={{ $field->customfields->type }} class="form--control"
                                            name='field_id[{{ $field->customfield_id }}]'
                                            value="{{ $cf[$field->customfield_id] ?? '' }}" required>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if ($item->request_by == 0)
                    <div class="modal-footer requestbuttons">
                        <button type="button" class="btn btn-md px-4 btn--danger"
                            data-bs-dismiss="modal">@lang('No')</button>


                        <?php
                        /*
                        show this button to sellar approve edit when approved_edit=0 and request by 1
                        on click sellar approve edit button change approved_edit =1 and request by to 0
                        approved_edit =1 mean approval by sellar button click or need changes button clicked

                        request  for edit change  approved edit to 0  if 1 and request by to 1

                        */
                        // if(count($cf)==0 || ($item->request_by==0 && $item->approved_edit==1))

                        //elseif($item->request_by==0 && $item->approved_edit==0)
                        // count($cf) < $item->productcustomfields->count()c
                        ?>

                        @if (count($cf) == 0 || ($item->request_by == 0 && $item->approve_edit == 1))
                            <button type="submit" name="submitbutton" class="btn btn-md px-4 btn--base">Submit
                                changes</button>
                            <span class="text-danger mb-7" style="color:red">Note:Please Update the above
                                CustomField</span>
                        @elseif($item->request_by == 0 && $item->approved_edit == 0)
                            <a data-id="{{ $item->id }}" data-value="1"
                                class="btn request_edit ajax btn-md px-4 btn--base">Request for edit</a>
                        @endif
                    @else
                        <span class="text-danger mb-7" style="color:red">Note: Your Current Request is Under
                            Process</span>
                @endif
        </div>
        </form>
    </div>
</div>
</div> --}}
