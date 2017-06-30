<?php
 
use QAlliance\QSendgrid;
 
class QSendgridTest extends PHPUnit_Framework_TestCase {

	private $sendgridApiKey;
	private $noReplyEmail;
	private $toEmail;

	public function setUp()
	{
		global $sendgridApiKey;
		global $noReplyEmail;
		global $toEmail;

		$this->assertGreaterThan(0, strlen($sendgridApiKey), "Sendgrid API key must be set");
		$this->assertGreaterThan(0, strlen($noReplyEmail), "No reply email must be set");
		$this->assertGreaterThan(0, strlen($toEmail), "Send to email adress must be set");

		$this->sendgridApiKey = $sendgridApiKey;
		$this->noReplyEmail = $noReplyEmail;
		$this->toEmail = $toEmail;
	}

	public function testQSendgridSetup()
	{
		$qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);
		$this->assertInstanceOf(QSendgrid::class, $qSendgrid);
	}

	public function testQSendgridSendEmailFunctionality()
	{
		$qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);
		$result = $qSendgrid->send($this->toEmail, "QSendgrid Testing", "<h1>This is a QSendgrid test email.");

		$this->assertTrue($result);
	}

	public function testQSendgridSendEmailWithAttachmentFunctionality()
	{
		$attachmentUrls = [
			'./src/attachments/sample.jpg'
		];

		foreach ($attachmentUrls as $url) {
			$this->assertFileExists($url);
		}

		$qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);
		$result = $qSendgrid->send($this->toEmail, "QSendgrid Testing", "<h1>This is a QSendgrid test email.", $attachmentUrls);

		$this->assertTrue($result);
	} 

}