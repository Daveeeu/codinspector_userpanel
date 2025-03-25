<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" data-bs-auto-close="outside"
       data-bs-toggle="dropdown" href="javascript:;"><i class="material-icons-outlined">notifications</i>
        @if($notificationCount > 0)
            <span class="badge-notify">{{$notificationCount}}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow">
        <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="notiy-title mb-0">{{ __('topbar_notifications_title') }}</h5>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret option" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                  <span class="material-icons-outlined">
                    more_vert
                  </span>
                </button>
                <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
                    <div>
                        <a id="markAllRead" class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;">
                            <i class="material-icons-outlined fs-6">done_all</i>
                            {{ __('topbar_notifications_mark_all_as_read') }}
                        </a>
                    </div>
                    <div>
                        <a id="deleteAllNotifications" class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;">
                            <i class="material-icons-outlined fs-6">delete</i>
                            {{ __('topbar_notifications_delete_all') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="notify-list">
            @forelse ($userNotifications as $notification)
                <div>
                    <a class="dropdown-item border-bottom py-2 notification-item {{ $notification->read ? 'read-notification' : 'unread-notification' }}"
                       href="javascript:;"
                       data-id="{{ $notification->id }}">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <h5 class="notify-title">{{ __($notification->event) }}</h5>
                                <p class="mb-0 notify-time">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if($notification->read === 1)
                                <div class="notify-close position-absolute end-0 me-3">
                                    <i class="material-icons-outlined fs-6 x-button">close</i>
                                </div>
                            @else
                                <div class="notify-dot position-absolute end-0 me-3"></div>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div class="p-3 d-flex justify-content-center">
                    <p>{{ __('no_more_notifications') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</li>
