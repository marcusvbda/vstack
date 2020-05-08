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
        $notifications = $user->notifications()->where("read_at", null)->get();
        $user->notifications()->update(['read_at' => now()]);
        return ["success" => true, "notifications" => $notifications];
    }

    public function destroy(User $user, $id)
    {
        $notification = $user->notifications()->where("id", $id)->firstOrFail();
        $notification->read_at = now();
        $notification->save();
        return ["success" => true];
    }
}
