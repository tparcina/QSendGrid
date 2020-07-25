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

		$this->assertGreaterThan(0, strlen($sendgridApiKey), 'Sendgrid API key must be set');
		$this->assertGreaterThan(0, strlen($noReplyEmail), 'No reply email must be set');
		$this->assertGreaterThan(0, strlen($toEmail), 'Send to email address must be set');

		$this->sendgridApiKey = $sendgridApiKey;
		$this->noReplyEmail = $noReplyEmail;
		$this->toEmail = $toEmail;
	}

    /**
     * Test setup
     *
     * @throws Exception
     */
    public function testQSendgridSetup()
	{
		$qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);

		$this->assertInstanceOf(QSendgrid::class, $qSendgrid);
	}

    /**
     * Test basic email
     *
     * @throws Exception
     */
    public function testQSendgridSendEmailFunctionality()
	{
		$qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);

		$result = $qSendgrid->send($this->toEmail, 'QSendgrid Testing', '<h1>This is a QSendgrid test email.</h1>', null, 'QSendgrid Test Mail');

		$this->assertTrue($result);
	}

    /**
     * Test mail with attachments
     *
     * @throws Exception
     */
	public function testQSendgridSendEmailWithAttachmentFunctionality()
	{
		$attachmentUrls = [
			'./src/attachments/sample1.jpg',
			'./src/attachments/sample2.jpg'
		];

		foreach ($attachmentUrls as $url) {
			$this->assertFileExists($url);
		}

		$qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);
		$result = $qSendgrid->send($this->toEmail, 'QSendgrid Testing', '<h1>This is a QSendgrid test email with attachments.</h1>', $attachmentUrls, 'QSendgrid Test Mail With Attachments');

		$this->assertTrue($result);
	}

    /**
     * Test basic email with plain text content
     *
     * @throws Exception
     */
    public function testQSendgridSendEmailWithPlainTextContentFunctionality()
    {
        $qSendgrid = new QSendgrid($this->noReplyEmail, $this->sendgridApiKey);

        $result = $qSendgrid->sendWithTextPlain($this->toEmail, 'QSendgrid Testing', '<h1>This is a QSendgrid test email with plain text.</h1>', 'This is a QSendgrid test email with plain text', null, 'QSendgrid Test Mail');

        $this->assertTrue($result);
    }
}