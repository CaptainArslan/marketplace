<div class="dashboard-area pt-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="content">
                    <h4 class="name">Custom Setting for {{ auth()->user()->getFullnameAttribute() }}</h4>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-12">
                <ul class="nav nav-tabs user-nav-tabs">
                    <li class="nav-item">
                        <a href="{{ route('user.allCustomfield') }}"
                            class="nav-link {{ menuActive('user.allCustomfield*') }}">@lang('Custom Field')</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('user.emailtemplate') }}"
                            class="nav-link {{ menuActive('user.emailtemplate*') }}">@lang('Email Template')
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('user.customcss') }}"
                            class="nav-link {{ menuActive('user.customcss*') }}">@lang('CustomCss')</a>
                    </li> --}}

                </ul>
            </div>
        </div>
    </div>
</div>
