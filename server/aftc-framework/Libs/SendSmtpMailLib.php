<?php

namespace AFTC\Libs;

use AFTC\Config\Config;
use AFTC\Utils\AFTCUtils;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class SendSmtpMailLib
 * @package AFTC\Libs
 */
class SendSmtpMailLib
{
    /**
     * @var string The error message
     */
    private string $errorMessage = "";

    /**
     * Send an email using SMTP
     *
     * @param string $to The recipient's email address
     * @param string $subject The email subject
     * @param string $message The email message
     * @return bool True if the email was sent successfully, false otherwise
     */
    public function send(string $to, string $subject, string $message): bool
    {
        // Handle config overrides
        if (Config::$sendAllEmailsToDev) {
            $to = Config::$emailDev;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = Config::$smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = Config::$emailApiUsername;
            $mail->Password = Config::$emailApiPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = Config::$smtpPort;

            $mail->setFrom(Config::$emailFrom, Config::$emailFromName);
            $mail->addAddress($to);
            $mail->addReplyTo(Config::$emailReplyTo);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return true;
        } catch (\Exception $e) {
            AFTCUtils::writeToLog("Email was not sent: " . $mail->ErrorInfo);
            $this->errorMessage = $mail->ErrorInfo;
            return false;
        }
    }

    /**
     * Get the error message
     *
     * @return string The error message
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}