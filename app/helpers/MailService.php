<?php
declare(strict_types=1);

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mail;
    private ?string $lastError = null;

    public function __construct()
    {
        // Optional: load .env for local development if vlucas/phpdotenv is installed
        try {
            $projectRoot = dirname(__DIR__, 2);
            $envFile = $projectRoot . DIRECTORY_SEPARATOR . '.env';
            if (file_exists($envFile) && class_exists('Dotenv\\Dotenv')) {
                $dotenv = \Dotenv\Dotenv::createImmutable($projectRoot);
                $dotenv->safeLoad();
            }
        } catch (\Throwable $t) {
            error_log('Dotenv load skipped: ' . $t->getMessage());
        }

        $this->mail = new PHPMailer(true);

        try {
            $this->mail->isSMTP();
            $this->mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = getenv('SMTP_USER') ?: '';
            $this->mail->Password = getenv('SMTP_PASS') ?: '';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = (int)(getenv('SMTP_PORT') ?: 587);
            $this->mail->CharSet = 'UTF-8';
            $this->mail->isHTML(true);
            if (!empty($this->mail->Username)) {
                $this->mail->setFrom($this->mail->Username, 'PetCare System');
            }
            $this->mail->SMTPDebug = 0;
        } catch (Exception $e) {
            $this->lastError = 'Mailer initialization failed: ' . $e->getMessage();
            error_log($this->lastError);
        }
    }

    public function sendEmail(string $to, string $subject, string $htmlBody, ?string $textBody = null, ?string $fromEmail = null, ?string $fromName = null): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();
            $this->mail->clearReplyTos();

            $from = $fromEmail ?: ($this->mail->Username ?: getenv('SMTP_USER') ?: '');
            $name = $fromName ?: 'PetCare System';
            if (empty($from)) {
                throw new \RuntimeException('From email is not configured.');
            }

            $this->mail->setFrom($from, $name);
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $htmlBody;
            $this->mail->AltBody = $textBody ?? html_entity_decode(strip_tags($htmlBody));
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            $this->lastError = 'Send failed: ' . ($this->mail->ErrorInfo ?? $e->getMessage());
            error_log($this->lastError);
            return false;
        } catch (\Throwable $t) {
            $this->lastError = 'Unexpected error: ' . $t->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
