<div class="modal col-md-12 fade" id="m_{{ $m_id ?? '' }}" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('user.late.customfield') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4>@lang('This is the Details of the Bumps')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @php
                    $bumps = $item->bumpresponses->pluck('price', 'bump_id')->toArray();
                    dd($bumps);
                @endphp
                <div class="modal-body">
                    <ul class="list-group">

                        <li class="list-group-item dark-bg">@lang('Amount') :
                        </li>
                        <li class="list-group-item dark-bg">@lang('Charge') :
                        </li>
                        <li class="list-group-item dark-bg">@lang('After Charge') :</li>
                        <li class="list-group-item dark-bg">@lang('Conversion Rate') : <span class="withdraw-rate"></span></li>
                        <li class="list-group-item dark-bg">@lang('Payable Amount') : <span class="withdraw-payable"></span>
                        </li>
                    </ul>
                    <ul class="list-group withdraw-detail mt-1">
                    </ul>
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

                                    <label>{{ $field->customfields->name }}</label><br>
                                    <input type='text' class="form--control"
                                        name='field_id[{{ $field->customfield_id }}]'
                                        value="{{ $cf[$field->customfield_id] ?? '' }}" required>


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
                            <button type="submit" class="btn btn-md px-4 btn--base">Submit changes</button>
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
</div>
