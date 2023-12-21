<div class="user-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="user-wrapper">
                    <div class="thumb">
                        <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . auth()->user()->image, imagePath()['profile']['user']['size']) }}"
                            alt="@lang('image')">
                    </div>
                    <div class="content">
                        <h4 class="name">{{ auth()->user()->getFullnameAttribute() }}</h4>
                        <p class="fs-14px">@lang('Member since') {{ showDateTime(auth()->user()->created_at, 'F, Y') }}</p>

                    </div>
                </div>
            </div>
            <div class="col-sm-4 text-end ">
                <div class="user-header-status">
                    <div class="left {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <span>@lang('Author Rating')</span>
                        <div class="ratings">
                            @php echo displayRating(auth()->user()->avg_rating) @endphp
                            ({{ auth()->user()->total_response }} @lang('Ratings'))
                        </div>
                    </div>
                    <div class="right">
                        <span>@lang('Purchased')</span>
                        <h4>{{ auth()->user()->buy()->where('status', 1)->count() }}</h4>
                    </div>
                </div>

                <!-- Example split danger button -->
                <div class="btn-group  {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                    <a
                        href="https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri={{ route('gohighlevel.callback') }}&client_id={{ env('GHL_CLIENT') }}&scope=calendars.readonly campaigns.readonly contacts.write contacts.readonly locations.readonly calendars/events.readonly locations/customFields.readonly locations/customValues.write opportunities.readonly calendars/events.write opportunities.write users.readonly users.write locations/customFields.write">
                        <button type="button"
                            class="btn btn-primary"{{ !is_null(connected()) ? 'disabled' : '' }}>{{ !is_null(connected()) ? 'Connected' : 'Connect CRM' }}</button>
                    </a>
                    @if (!is_null(connected()))
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="px-3 py-1"><b>Loc ID
                                    :</b>{{ Str::limit(connected()->location_id, 10, $end = '....') }}</li>
                            <li class="px-3 py-1"><b>Loc Name :</b>{{ connected()->location_name }}</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            {{-- <li class="px-3"><a
                                    href="https://marketplace.gohighlevel.com/oauth/chooselocation?response_type=code&redirect_uri={{ route('gohighlevel.callback') }}&client_id={{ env('GHL_CLIENT') }}&scope=calendars.readonly campaigns.readonly contacts.write contacts.readonly locations.readonly calendars/events.readonly locations/customFields.readonly locations/customValues.write opportunities.readonly calendars/events.write opportunities.write users.readonly users.write locations/customFields.write"
                                    class="btn btn-primary btn-sm d-block">Change Location</a>
                            <li> --}}
                            <li class="px-3 mt-2"><a href="javascript:void(0)" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-bs-toggle="tooltip" data-bs-placement="top"
                                    class="btn btn-danger btn-sm d-block">Disconnect</a>
                            <li>

                        </ul>
                    @endif
                </div>
            </div>
        </div><!-- row end -->
        {{-- Delete Model --}}
        <div id="deleteModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">>
            <div class="modal-dialog">
                <form action="{{ route('gohighlevel.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" required>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">@lang('Disconnect with CRM')</h5>
                            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>@lang('Are you sure you want tp disconnect')</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--primary btn-sm"
                                data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn--danger btn-sm">@lang('Disconnect')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12">
                <ul class="nav nav-tabs user-nav-tabs">


                    <li class="nav-item">
                        <a href="{{ route('user.home') }}"
                            class="nav-link {{ menuActive('user.home') }}">@lang('Dashboard')</a>
                    </li>

                    @if ($general->referral_system)
                        <li class="nav-item">
                            <a href="{{ route('user.referral.users') }}"
                                class="nav-link {{ menuActive('user.referral*') }}">@lang('Referral')</a>
                        </li>
                    @endif

                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.product.all') }}"
                            class="nav-link {{ menuActive('user.product*') }}">@lang('Your Products')</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user.deposit.history') }}"
                            class="nav-link {{ menuActive('user.deposit*') }}">@lang('Deposit')</a>
                    </li>
                    {{-- <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
              <a href="{{route('user.withdraw.history')}}" class="nav-link {{menuActive('user.withdraw*')}}">@lang('Withdraw')</a>
            </li> --}}
                    <li class="nav-item">
                        <a href="{{ route('user.transaction') }}"
                            class="nav-link {{ menuActive('user.transaction*') }}">@lang('Transactions')</a>

                    </li>
                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.sell.log') }}"
                            class="nav-link {{ menuActive('user.sell.log') }}">@lang('Sell Logs')</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.purchased.product') }}"
                            class="nav-link {{ menuActive('user.purchased.product') }}">@lang('Purchase Logs')</a>
                    </li>
                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.track.sell') }}"
                            class="nav-link {{ menuActive('user.track.sell*') }}">@lang('Track Sell')</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('ticket') }}"
                            class="nav-link {{ menuActive('ticket*') }}">@lang('Support')</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.meeting.all') }}"
                            class="nav-link {{ menuActive('user.meeting*') }}">@lang('Your Meetings')</a>
                    </li>
                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.allCustomfield') }}"
                            class="nav-link {{ menuActive('user.allCustomfield*') }}">@lang('Setting')</a>
                    </li>
                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.getplans') }}"
                            class="nav-link {{ menuActive('user.getplans*') }}">@lang('Upgrade Plan')</a>
                    </li>
                    {{-- <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.allCustomfield') }}"
                            class="nav-link {{ menuActive('user.allCustomfield*') }}">@lang('CustomField')</a>
                    </li>
                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.customcss') }}"
                            class="nav-link {{ menuActive('user.customcss*') }}">@lang('CustomCss')</a>
                    </li>
                    <li class="nav-item {{ auth()->user()->seller == 0 ? 'd-none' : '' }}">
                        <a href="{{ route('user.emailtemplate') }}"
                            class="nav-link {{ menuActive('user.emailtemplate*') }}">@lang('EmailTemplate')
                        </a>
                    </li> --}}

                </ul>
            </div>
        </div>
    </div>
</div><!-- user-area end -->
