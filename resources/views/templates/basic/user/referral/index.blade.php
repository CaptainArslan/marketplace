@extends($activeTemplate.'layouts.frontend')
    @section('content')

        <div class="pb-100">
            @include($activeTemplate.'partials.dashboardHeader')

            <div class="dashboard-area pt-50">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-end">
                                <a href="{{ route('user.referral.commissions.logs') }}" class="btn btn-sm btn--base"><i class="las la-list"> </i>@lang('Commission Logs')</a>
                            </div>
                            <div class="table-responsive--md mt-4">

                                <table class="table custom--table">
                                    <thead>
                                        <tr>
                                            <tr>
                                                <th>@lang('S.N.')</th>
                                                <th>@lang('Full Name')</th>
                                                <th>@lang('Joined At')</th>
                                            </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($referees as $user)
                                            <td data-lable="@lang('S.N.')">{{ $referees->firstItem() + $loop->index }}</td>
                                            <td data-lable="@lang('Full Name')">{{ $user->fullname }}</td>
                                            <td data-lable="@lang('Joined At')">{{ showDateTime($user->created_at, 'd M, Y h:i A') }}</td>
                                        @empty
                                            <tr>
                                                <td class="text-center" colspan="100%">{{__($empty_message)}}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="pagination--sm justify-content-end">
                                    {{$referees->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
