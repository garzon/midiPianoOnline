<?php
//lianghonghao@baixing.com

require(ROOT . "/include/class.phpmailer.php");

class Email {
	public $to;
	public $title;
	public $body;

	private $attachments = [];

	public static $mailConfig = [
		'CharSet' => 'utf-8',
		'Host' => 'smtp.gmail.com',
		'Port' => 587,
		'SMTPAuth' => true,
		'Username' => EMAIL_USERNAME,
		'Password' => EMAIL_PASSWORD,
		'SMTPSecure' => 'tls',
	];

	public static function sendMail($to, $subject, $body) {
		$mailer = new PHPMailer();
		$mailer->IsSMTP();
		$mailer->IsHTML();
		foreach (self::$mailConfig as $key => $value) {
			$mailer->$key = $value;
		}
		$mailer->AddAddress($to);
		$mailer->SetFrom(EMAIL_USERNAME, 'Midi Piano Online');
		$mailer->Subject = $subject;
		$mailer->Body = $body ? : 'None';
		$mailer->send();
	}



	public function send() {
		$mailer = new PHPMailer();
		$mailer->IsSMTP();
		$mailer->IsHTML();
		foreach (self::$mailConfig as $key => $value) {
			$mailer->$key = $value;
		}
		$mailer->AddAddress($this->to);
		$mailer->Subject = $this->title;
		$mailer->Body = nl2br(trim($this->body));
		if ($this->attachments) {
			foreach ($this->attachments as $each_attachment) {
				$mailer->AddAttachment($each_attachment['path'], $each_attachment['name']);
			}
		}
		$mailer->Send();
		if ($mailer->ErrorInfo) {
			echo $mailer->ErrorInfo;
			die('Please contact the admin. garzonou@gmail.com');
		}
	}

	public function addAttachment($attachment) {
		$this->attachments[] = $attachment;
	}
}
