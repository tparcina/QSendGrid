<?php 

/*
 * This file is part of QAlliance.
 *
 * (c) Vicko Franic <vicko@q-alliance.com>
 *
 */
namespace QAlliance;

use Sendgrid as SendGrid;
use Sendgrid\Email;
use Sendgrid\Mail;
use Sendgrid\Attachment;
use Sendgrid\Content;
 use QAlliance\Exceptions\BadRequestException;

/**
 * @method send(string $to, string $subject, string $content)
 */
class QSendgrid {

	private $noReplyEmail;
	private $sendgrid;

	/**
	 * Constructor
	 * 
	 * @param string $noReplyEmail		No reply email address
	 * @param string $sendgridApiKey 	Sendgrid API key
	 */
	function __construct($noReplyEmail = '', $sendgridApiKey = '')
	{
		$this->setup($noReplyEmail, $sendgridApiKey);
	}

	/**
	 * Setup QSendgrid
	 * 
	 * @param  string $noReplyEmail
	 * @param  string $sendgridApiKey
	 * @return void
	 */
	private function setup($noReplyEmail, $sendgridApiKey)
	{
		if (strlen($noReplyEmail) == 0 or strlen($sendgridApiKey) == 0) {
			throw new BadRequestException("No reply email or Sendgrid API key is missing");
		}

		$this->noReplyEmail = $noReplyEmail;
		$this->sendgrid = new Sendgrid($sendgridApiKey);
	}

	/**
	 * Send email with or without attachments
	 * 
	 * @param  string $to
	 * @param  string $subject
	 * @param  string $content
	 * @param  array $attachments
	 * @return bool
	 */
	public function send($to, $subject, $content, $attachmentsFilePath = null)
	{
		if (!$to or !$subject or !$content) {
			throw new BadRequestException("To email address, subject, or content is missing");
		}

		if (isset($attachmentsFilePath) and !is_array($attachmentsFilePath)) {
			throw new BadRequestException("Attachments must be an array of strings (paths to the attachement files)");
		}

		$from = new Email(null, $this->noReplyEmail);
		$to = new Email(null, $to);
		$content = new Content("text/html", $content);
		$mail = new Mail($from, $subject, $to, $content);

		if (isset($attachmentsFilePath)) {
			foreach ($attachmentsFilePath as $path) {

				if (! file_exists($path)) {
					throw new BadRequestException("File in path '" . $path . "' does not exist");
				}

		        $attachment = new Attachment();
		        $attachment->setContent(base64_encode(file_get_contents($path)));
		        $attachment->setFilename(basename($path));
		        $attachment->setDisposition("attachment");
		        $mail->addAttachment($attachment);
			}
		}

		$response = $this->sendgrid->client->mail()->send()->post($mail);

		if ($response->statusCode() >= 200 and $response->statusCode() < 300) {
			return true;
		}
		return false;
	}
}