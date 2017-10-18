<?php

namespace Instante\Utils;

use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Mail\SendmailMailer;
use Nette\NotSupportedException;

/**
 * Nette Mailer decorator service for fast and efficient mail sending.
 *
 * @TODO tests
 */
class MailDecorator
{
    private $mailer;

    public function __construct(IMailer $mailer = NULL)
    {
        if (!class_exists('Nette\Mail\Message')) {
            throw new NotSupportedException('You have to install nette/mail composer package to use '
                . __CLASS__);
        }

        $this->mailer = $mailer ?: new SendmailMailer;
    }

    /**
     * Send mail by calling a single method - as you often don't need more...
     *
     * @param string|array $recipients
     * @param string $subject
     * @param string $body
     * @param string|NULL $sender
     * @return void
     * @throws SendException
     */
    public function sendMail($recipients, $subject, $body, $sender = NULL)
    {
        $mail = new Message;
        if ($sender !== NULL) {
            $mail->setFrom($sender);
        }
        $mail->setSubject($subject)
            ->setBody($body);
        if (!is_array($recipients)) {
            $recipients = [$recipients];
        }
        foreach ($recipients as $rcpt) {
            $mail->addTo($rcpt);
        }
        $mailer = $this->mailer;
        $mailer->send($mail);
    }

    /**
     * Simple static shortcut to send mail internally using PHP's mail() function.
     *
     * WARNING: using static calls is not advised in clean code. This method
     * serves only for scaffolding / super simple projects to maximally simplify caller's code.
     *
     * @param string|array $recipients
     * @param string $subject
     * @param string $body
     * @param string|NULL $sender
     * @return void
     * @throws SendException
     */
    public static function send($recipients, $subject, $body, $sender = NULL)
    {
        (new self(new SendmailMailer))->sendMail($recipients, $subject, $body, $sender);
    }
}
