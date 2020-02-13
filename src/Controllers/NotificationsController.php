<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use App\Notifications\Alert;
use App\User;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function get(User $user)
    {
        // 
        $notifications = $user->notifications;
        $user->notifications()->delete();
        return ["success" => true, "notifications" => $notifications];
    }

    public function destroy(User $user, $id)
    {
        $notification = $user->notifications()->where("id", $id)->firstOrFail();
        $notification->delete();
        return ["success" => true];
    }
}
