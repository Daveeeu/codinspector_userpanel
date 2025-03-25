<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserNotificationController extends Controller
{
    public function markAllRead()
    {
        $user = auth()->user();

        try{
            $notifications = $user->userNotifications()->where('read', 0)->get();

            if ($notifications->isEmpty()) {
                return response()->json(['success' => true]);
            }

            foreach ($notifications as $notification) {
                $notification->update(['read' => 1]);

                activity()
                    ->causedBy($user)
                    ->performedOn($notification)
                    ->withProperties([
                        'notification_id' => $notification->id,
                        'event'           => $notification->event,
                        'store_id'        => $notification->store_id,
                    ])
                    ->log('Notification marked as read');
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error marking notifications as read', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    public function markAsDeleted(Request $request)
    {
        $user = auth()->user();

        $notification = $user->userNotifications()->find($request->id);

        if ($notification) {
            $notification->update(['deleted' => 1]);

            activity()
                ->causedBy($user)
                ->performedOn($notification)
                ->withProperties([
                    'notification_id' => $notification->id,
                    'event'           => $notification->event,
                    'store_id'        => $notification->store_id,
                ])
                ->log('Notification deleted');

            return response()->json(['success' => true]);
        }


        return response()->json(['success' => false], 404);
    }

    public function deleteAll()
    {
        $user = auth()->user();

        try{
            $notifications = $user->userNotifications()->where('read', 1)->where('deleted', 0)->get();

            if ($notifications->isEmpty()) {
                return response()->json(['success' => true]);
            }

            foreach ($notifications as $notification) {
                $notification->update(['deleted' => 1]);

                activity()
                    ->causedBy($user)
                    ->performedOn($notification)
                    ->withProperties([
                        'notification_id' => $notification->id,
                        'event'           => $notification->event,
                        'store_id'        => $notification->store_id,
                    ])
                    ->log('Notification marked as deleted');
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error marking notifications as deleted', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    public function getModalContent(Request $request)
    {

        try{
            $notificationId = $request->input('id');
            $notification = UserNotification::findOrFail($notificationId);

            $notification->update(['read' => 1]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($notification)
                ->withProperties([
                    'notification_id' => $notification->id,
                    'event'           => $notification->event,
                    'store_id'        => $notification->store_id,
                ])
                ->log('Notification read');


            $title = '';
            $description = '';

            switch ($notification->event) {
                case 'subscription_reminder':
                    $title = __($notification->event . '_title');
                    $description = __($notification->event . '_description', [
                        'storeDomain' => $notification->store->domain,
                        'renewalDate' => $notification->store->subscription->end_date,
                    ]);
                    $description .= '<br><br>'. __($notification->event . '_no_action_required');
                    break;
                case 'successful_payment':
                    $title = __($notification->event . '_title');
                    $description = __($notification->event . '_description', [
                        'storeDomain' => $notification->store->domain,
                    ]);
                    $description .= '<br><br>'. __($notification->event . '_subscription_updated');
                    break;
                case 'failed_payment':
                    $title = __($notification->event . '_title');
                    $description = __($notification->event . '_description', [
                        'storeDomain' => $notification->store->domain,
                    ]);
                    $description .= '<br><br>'. __($notification->event . '_reason');
                    $description .= '<br><br>'. __($notification->event . '_solution');

                    $expireDate = Carbon::parse($notification->store->subscription->end_date)
                        ->addDays(30)
                        ->format('F j, Y');

                    $description .= '<br><br>'. __($notification->event . '_subscription_warning', [
                        'expireDate' => $expireDate,
                    ]);
                    break;
                case 'store_deleted':
                    $title = __($notification->event . '_title');
                    $description = __($notification->event . '_description', [
                        'storeDomain' => $notification->deleted_store_domain,
                    ]);
                    $description .= '<br><br>'. __($notification->event . '_reason');
                    $description .= '<br><br>'. __($notification->event . '_contact_us');
                    break;
            }

            return response()->json([
                'success'     => true,
                'title'       => $title,
                'description' => $description,
            ]);
        }catch (\Exception $e) {

            Log::error('Error getting notification modal content', [
                'user_id' => auth()->user(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }
}
