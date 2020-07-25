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
use SendGrid\Response;


class QSendgrid {

    private $noReplyEmail;
    private $sendgrid;

    /**
     * Constructor
     *
     * @param string $noReplyEmail No reply email address
     * @param string $sendgridApiKey Sendgrid API key
     * @throws \Exception
     */
    public function __construct($noReplyEmail = '', $sendgridApiKey = '')
    {
        $this->setup($noReplyEmail, $sendgridApiKey);
    }

    /**
     * Setup QSendgrid
     *
     * @param  string $noReplyEmail
     * @param  string $sendgridApiKey
     * @return void
     * @throws \Exception
     */
    private function setup($noReplyEmail, $sendgridApiKey)
    {
        if ('' === $noReplyEmail || '' === $sendgridApiKey) {
            throw new \RuntimeException('No reply email or Sendgrid API key is missing');
        }

        $this->noReplyEmail = $noReplyEmail;
        $this->sendgrid = new Sendgrid($sendgridApiKey);
    }

    /**
     * Send email with or without attachments
     *
     * @param $toMail
     * @param  string $subject
     * @param $mailContent
     * @param null $attachments
     * @param string $fromName
     * @return bool
     */
    public function send($toMail, $subject, $mailContent, $attachments = null, $fromName = 'No Reply'): bool
    {
        if (!$toMail || !$subject || !$mailContent) {
            throw new \RuntimeException('To email address, subject, or content is missing');
        }

        if ($attachments !== null && !\is_array($attachments)) {
            throw new \RuntimeException('Attachments must be an array of strings (paths to the attachments files)');
        }

        $from = new Email($fromName, $this->noReplyEmail);

        $to = new Email(null, $toMail);

        $content = new Content('text/html', $mailContent);

        $mail = new Mail($from, $subject, $to, $content);

        /**
         * Build attachments
         */
        if ($attachments !== null) {
            foreach ($attachments as $path) {

                if (! file_exists($path)) {
                    throw new \RuntimeException("File in path '" . $path . "' does not exist");
                }

                $attachment = new Attachment();
                $attachment->setContent(base64_encode(file_get_contents($path)));
                $attachment->setFilename(basename($path));
                $attachment->setDisposition('attachment');
                $mail->addAttachment($attachment);
            }
        }

        /** @var Response $response */
        $response = $this->sendgrid->client->mail()->send()->post($mail);

        return $response->statusCode() >= 200 && $response->statusCode() < 300;
    }

    /**
     * Send email with or without attachments
     * Also, both html and plain text content are required
     *
     * @param $toMail
     * @param string $subject
     * @param $mailContent
     * @param $mailTextPlainContent
     * @param null $attachments
     * @param string $fromName
     * @return bool
     */
    public function sendWithTextPlain($toMail, $subject, $mailContent, $mailTextPlainContent, $attachments = null, $fromName = 'No Reply'): bool
    {
        if (!$toMail || !$subject || !$mailContent || !$mailTextPlainContent) {
            throw new \RuntimeException('To email address, subject, or content (html or plain text) is missing');
        }

        if ($attachments !== null && !\is_array($attachments)) {
            throw new \RuntimeException('Attachments must be an array of strings (paths to the attachments files)');
        }

        $from = new Email($fromName, $this->noReplyEmail);

        $to = new Email(null, $toMail);

        $content = new Content('text/html', $mailContent);
        $textPlainContent = new Content('text/plain', $mailTextPlainContent);

        $mail = new Mail($from, $subject, $to, $textPlainContent);

        $mail->addContent($content);

        /**
         * Build attachments
         */
        if ($attachments !== null) {
            foreach ($attachments as $path) {

                if (! file_exists($path)) {
                    throw new \RuntimeException("File in path '" . $path . "' does not exist");
                }

                $attachment = new Attachment();
                $attachment->setContent(base64_encode(file_get_contents($path)));
                $attachment->setFilename(basename($path));
                $attachment->setDisposition('attachment');
                $mail->addAttachment($attachment);
            }
        }

        /** @var Response $response */
        $response = $this->sendgrid->client->mail()->send()->post($mail);

        return $response->statusCode() >= 200 && $response->statusCode() < 300;
    }
}