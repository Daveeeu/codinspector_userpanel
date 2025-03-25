<header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4" style="display: flex; justify-content: space-between">
        <div class="btn-toggle">
            <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
        </div>
        <ul class="navbar-nav gap-1 nav-right-links align-items-center">
            <li class="nav-item d-lg-none mobile-search-btn">
                <a class="nav-link" href="javascript:;"><i class="material-icons-outlined">search</i></a>
            </li>

            <x-user-notification/>

            <li class="nav-item dropdown">
                <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                    <img src="{{ URL::asset('build/images/avatars/01.png') }}" class="rounded-circle p-1 border" width="45" height="45">
                </a>
                <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
                    <a class="dropdown-item  gap-2 py-2" href="javascript:;">
                        <div class="text-center">
                            <img src="{{ URL::asset('build/images/avatars/01.png') }}" class="rounded-circle p-1 shadow mb-3" width="90" height="90"
                                 alt="">
                            <h5 class="user-name mb-0 fw-bold">{{ __('topbar_user_dropdown_hello') }} {{\Illuminate\Support\Facades\Auth::user()['first_name']}}</h5>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{route('account.settings')}}"><i
                            class="material-icons-outlined">person_outline</i>{{ __('topbar_user_dropdown_profile') }}</a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('notifications.index') }}"><i
                            class="material-icons-outlined">notifications</i>{{ __('topbar_user_dropdown_notifications') }}</a>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('partner_program.index') }}"><i
                            class="material-icons-outlined">account_balance</i>{{ __('topbar_user_dropdown_partner_program') }}</a>
                    <hr class="dropdown-divider">
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2">
                            <i data-lucide="log-out" class="material-icons-outlined">power_settings_new</i>
                            {{ __('topbar_user_dropdown_logout') }}
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
</header>
