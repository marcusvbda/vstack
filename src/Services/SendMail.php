<?php


namespace marcusvbda\vstack\Services;

use Mail;
use Illuminate\Support\Facades\Storage;

class SendMail
{

	public static function to($to, $subject, $html, $file = null, $before_send = null)
	{
		$to = filter_var($to, FILTER_SANITIZE_EMAIL);
		if (!self::validEmail($to)) return;

		Mail::send([], [], function ($message) use ($to, $html, $subject, $file) {
			$message->to($to)
				->subject($subject)
				->setBody($html, 'text/html');
			if (@$file) {
				$message->attach(storage_path("app/" . $file));
			}
		});
		Storage::disk("local")->delete($file);
		if ($before_send) {
			$before_send($to, $subject, $html);
		}
	}

	public static function validEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}
