<?php

namespace App\Mail;

use Google\Client;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class GmailOAuthTransport extends AbstractTransport
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $refreshToken;
    protected string $email;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        string $email
    ) {
        parent::__construct();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->refreshToken = $refreshToken;
        $this->email = $email;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';

        $mail->setOAuth(
            new OAuth([
                'provider' => new \League\OAuth2\Client\Provider\Google([
                    'clientId' => $this->clientId,
                    'clientSecret' => $this->clientSecret,
                ]),
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'refreshToken' => $this->refreshToken,
                'userName' => $this->email,
            ])
        );

        $mail->setFrom($this->email, config('app.name'));
        
        foreach ($email->getTo() as $to) {
            $mail->addAddress($to->getAddress(), $to->getName() ?? '');
        }

        foreach ($email->getCc() as $cc) {
            $mail->addCC($cc->getAddress(), $cc->getName() ?? '');
        }

        foreach ($email->getBcc() as $bcc) {
            $mail->addBCC($bcc->getAddress(), $bcc->getName() ?? '');
        }

        $mail->Subject = $email->getSubject() ?? '';
        
        if ($email->getHtmlBody()) {
            $mail->isHTML(true);
            $mail->Body = $email->getHtmlBody();
            $mail->AltBody = $email->getTextBody() ?? strip_tags($email->getHtmlBody());
        } else {
            $mail->Body = $email->getTextBody() ?? '';
        }

        $mail->send();
    }

    public function __toString(): string
    {
        return 'gmail-oauth';
    }
}
