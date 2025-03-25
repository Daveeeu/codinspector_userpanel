<?php

    namespace App\Http\Controllers;

    use App\Models\Notifications;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class NotificationsController extends Controller
    {
        public function index()
        {
            $user = Auth::user();

            // Filter notifications where status == 1 and map them
            $userNotifications = $user->notifications->filter(function ($item) {
                return $item->status == 1;
            })->map(function ($item) {
                return $item->type . '-' . $item->mode;
            });

            return view('notifications.index', compact('userNotifications'));
        }

        /**
         * Frissíti az értesítési beállításokat.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function update(Request $request)
        {
            $user = auth()->user();
            $notifications = $request->input('notifications', []);

            // Retrieve all current notifications for the user
            $currentNotifications = Notifications::where('user_id', $user->id)->get();

            foreach ($currentNotifications as $notification) {
                $key = "{$notification->type}-{$notification->mode}";

                if (isset($notifications[$key])) {
                    $notification->update([
                        'status' => $notifications[$key] == "on",
                    ]);
                } else {
                    $notification->update([
                        'status' => false,
                    ]);
                }
            }

            foreach ($notifications as $key => $value) {
                [$type, $mode] = explode('-', $key);

                Notifications::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'type' => $type,
                        'mode' => $mode,
                    ],
                    [
                        'status' => $value == "on",
                    ]
                );
            }

            // Log activity
            activity()
                ->causedBy($user)
                ->withProperties([
                    'updated_notifications' => $notifications,
                ])
                ->log('User updated notification settings');

            return redirect()->back()->with('success', 'Értesítési beállítások sikeresen mentve!');
        }

    }
