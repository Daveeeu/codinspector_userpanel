<aside class="sidebar-wrapper">
    <div class="sidebar-header">
        <div class="logo-icon">
            <img src="/logo.svg" class="logo-img"  style="width: 150px;" alt="">
        </div>
        <div class="sidebar-close">
            <span class="material-icons-outlined">close</span>
        </div>
    </div>
    <div class="sidebar-nav" data-simplebar="true">

        <!--navigation-->
        <ul class="metismenu" id="sidenav">
            <li>
                <a href="{{ route('home') }}">
                    <div class="parent-icon"><i class="material-icons-outlined">home</i>
                    </div>
                    <div class="menu-title">{{ __('sidebar_home') }}</div>
                </a>
            </li>

            <li class="menu-label">{{ __('sidebar_data_label') }}</li>
            <li class="{{ Route::is('store.*') ? 'mm-active' : '' }}">
                <a href="{{route('store.index')}}">
                    <div class="parent-icon"><i class="material-icons-outlined">description</i></div>
                    <div class="menu-title">{{ __('sidebar_stores') }}</div>
                </a>
            </li>
            <li class="{{ Route::is('feedback.*') ? 'mm-active' : '' }}">
                <a href="<?php echo e(route('feedback.index')); ?>">
                    <div class="parent-icon"><i class="material-icons-outlined">group</i>
                    </div>
                    <div class="menu-title">{{ __('sidebar_feedbacks') }}</div>
                </a>
            </li>
            <li class="{{ Route::is('queries.*') ? 'mm-active' : '' }}">
                <a href="{{route('queries.index')}}">
                    <div class="parent-icon"><i class="material-icons-outlined">person_search</i>
                    </div>
                    <div class="menu-title">{{ __('sidebar_queries') }}</div>
                </a>
            </li>

            <li class="menu-label">{{ __('sidebar_tools_label') }}</li>
            <li class="{{ Route::is('exceptions.*') ? 'mm-active' : '' }}">
                <a href="{{route('exceptions.index')}}">
                    <div class="parent-icon"><i class="material-icons-outlined">warning</i></div>
                    <div class="menu-title">{{ __('sidebar_exceptions') }}</div>
                </a>
            </li>
            <li class="{{ Route::is('manual-query.*') ? 'mm-active' : '' }}">
                <a href="{{route('manual-query.index')}}">
                    <div class="parent-icon"><i class="material-icons-outlined">search</i></div>
                    <div class="menu-title">{{ __('sidebar_manual_query') }}</div>
                </a>
            </li>

            <li class="menu-label">{{ __('sidebar_info_label') }}</li>
            <li class="{{ Route::is('account.*') ? 'mm-active' : '' }}">
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{route('account.settings')}}"><i
                        class="material-icons-outlined">person_outline</i>{{ __('sidebar_account_settings') }}</a>
            </li>
            <li class="{{ Route::is('notifications.*') ? 'mm-active' : '' }}">
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('notifications.index') }}"><i
                        class="material-icons-outlined">notifications</i>{{ __('sidebar_notifications') }}</a>
            </li>
            <li class="{{ Route::is('partner_program.*') ? 'mm-active' : '' }}">
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('partner_program.index') }}"><i
                        class="material-icons-outlined">account_balance</i>{{ __('sidebar_partner_program') }}</a>
            </li>
        </ul>
        <!--end navigation-->
    </div>
    <div class="sidebar-bottom gap-4">
        <div class="dark-mode">
            <a href="javascript:;" class="footer-icon dark-mode-icon">
                <i class="material-icons-outlined">{{ __('sidebar_dark_mode') }}</i>
            </a>
        </div>
        <div class="dropdown dropup-center dropup dropdown-laungauge">
            <a class="dropdown-toggle dropdown-toggle-nocaret footer-icon" href="javascript:;" data-bs-toggle="dropdown">
                <img src="{{ URL::asset('build/images/county/02.png') }}" width="22" alt="">
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/01.png') }}" width="20" alt=""><span class="ms-2">Angol</span></a>
                </li>
                <li><a class="dropdown-item d-flex align-items-center py-2" href="javascript:;"><img src="{{ URL::asset('build/images/county/02.png') }}" width="20" alt=""><span class="ms-2">Magyar</span></a>
                </li>
                <!-- További nyelvek -->
            </ul>
        </div>
        <div class="dropdown dropup-center dropup dropdown-help">
            <a class="footer-icon  dropdown-toggle dropdown-toggle-nocaret option" href="javascript:;"
               data-bs-toggle="dropdown" aria-expanded="false">
            <span class="material-icons-outlined">
              info
            </span>
            </a>
            <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined fs-6">help_outline</i>{{ __('sidebar_faq') }}</a></div>
                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined fs-6">description</i>{{ __('sidebar_documentation') }}</a></div>
                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined fs-6">contact_mail</i>{{ __('sidebar_contact') }}</a></div>
                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined fs-6">new_releases</i>{{ __('sidebar_whats_new') }}</a></div>
                <div>
                    <hr class="dropdown-divider">
                </div>
                <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                            class="material-icons-outlined fs-6">bug_report</i>{{ __('sidebar_bug_report') }}</a></div>
            </div>
        </div>

    </div>
</aside>
