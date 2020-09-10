<?php

namespace marcusvbda\vstack\Services;

use Mail;

class SendMail
{

    public static function to($to, $subject, $html,$file = null)
    {
        Mail::send([], [], function ($message) use ($to, $html, $subject,$file) {
            $message->to($to)
                ->subject($subject)
                ->setBody($html, 'text/html');
            if(@$file)  $message->attach($file);
        });
    }
}
