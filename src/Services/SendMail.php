<?php

namespace marcusvbda\vstack\Services;

use Mail;
use Illuminate\Support\Facades\Storage;
class SendMail
{

    public static function to($to, $subject, $html,$file = null)
    {
        Mail::send([], [], function ($message) use ($to, $html, $subject,$file) {
            $message->to($to)
                ->subject($subject)
                ->setBody($html, 'text/html');
            if(@$file)  {
                $message->attach(storage_path("app/".$file));
            }
        });
        Storage::disk("local")->delete($file);
    }
}
