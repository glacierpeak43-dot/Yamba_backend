<?php

namespace App\Http\Controllers;

use App\Events\ActionEvent;
use App\Http\Resources\NotificationsResource;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function  getUserNotifications()
    {
        $user = Auth::user();
        $notifications =  Notifications::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        //        return $notifications;
        return response(['success' => true, 'notifications' => NotificationsResource::collection($notifications),]);
    }


    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notifications =  Notifications::find($id);
        if ($notifications->user_id == $user->id) {

            if ($notifications->type == 'alert' && $notifications->read == false) {
                $notification =  new Notifications();
                $notification->type = 'alert viewed';
                $notification->message = "$user->name viewed your emergency alert";
                $notification->user_id = $notifications->location->user_id;
                $notification->save();
            }
            $notifications->update(
                ['read' => true]
            );
            broadcast(new ActionEvent("Notification", $user, "Notification Updated"))->toOthers();

            return response(['success' => true, 'message' => 'notification updated']);
        } else {
            broadcast(new ActionEvent("Notification", $user, "Failed to Update Notification"))->toOthers();

            return response(['success' => false, 'message' => 'failed to update'], 403);
        }
    }

    public function deleteUserNotification($id)
    {
        $user = Auth::user();
        $notifications =  Notifications::find($id);
        if ($notifications->user_id == $user->id) {
            $notifications->delete();
            broadcast(new ActionEvent("Notification", $user, "Notification Updated"))->toOthers();

            return response(['success' => true, 'message' => 'notification updated']);
        } else {
            broadcast(new ActionEvent("Notification", $user, "Failed to Update Notification"))->toOthers();

            return response(['success' => false, 'message' => 'failed to updated'], 403);
        }
    }
}
