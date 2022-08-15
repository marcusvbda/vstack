<?php

namespace marcusvbda\vstack\Services;

use Auth;
use App\User;
use marcusvbda\vstack\Notifications\Message;

class Messages
{
    public static function send($type, $message)
    {
        session()->push('quick.alerts', (object) ["type" => $type, "message" => $message, "closeable" => true]);
    }

    public static function notify($type, $message, $id = null)
    {
        $user = $id ? User::findOrFail($id) : Auth::user();
        $user->notify(new Message($message, $type));
    }
}
