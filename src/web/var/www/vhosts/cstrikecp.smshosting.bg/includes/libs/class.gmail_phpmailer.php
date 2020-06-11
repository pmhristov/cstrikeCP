<?php
require_once('/var/www/phpmailer/class.phpmailer.php');

class Gmail_PHPMailer extends PHPMailer {
	public $Mailer     = 'smtp';
	public $SMTPAuth   = true;
	public $SMTPSecure = 'ssl';
	
	public $Host = SMTP_HOST;
	public $Port = SMTP_PORT;
	
	public $Username = SMTP_USER;
	public $Password = SMTP_PASS;
}
?>
