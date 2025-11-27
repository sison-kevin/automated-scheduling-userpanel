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
        // Try multiple SMTP configurations to increase chance of success
        $this->mail->clearAddresses();
        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
        $this->mail->clearReplyTos();

        $from = $fromEmail ?: ($this->mail->Username ?: getenv('SMTP_USER') ?: '');
        $name = $fromName ?: 'PetCare System';
        if (empty($from)) {
            $this->lastError = 'From email is not configured.';
            error_log($this->lastError);
            return false;
        }

        $this->mail->setFrom($from, $name);
        $this->mail->addAddress($to);
        $this->mail->Subject = $subject;
        $this->mail->Body = $htmlBody;
        $this->mail->AltBody = $textBody ?? html_entity_decode(strip_tags($htmlBody));

        $debugLog = __DIR__ . '/../../writable/email_debug.log';

        $transports = [
            ['secure' => PHPMailer::ENCRYPTION_STARTTLS, 'port' => (int)(getenv('SMTP_PORT') ?: 587)],
            ['secure' => PHPMailer::ENCRYPTION_SMTPS, 'port' => 465],
        ];

        foreach ($transports as $cfg) {
            try {
                $this->mail->SMTPSecure = $cfg['secure'];
                $this->mail->Port = $cfg['port'];
                // enable debug output capture
                $this->mail->SMTPDebug = 0;
                $this->mail->send();
                file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "Mail sent via port {$cfg['port']} (secure: {$cfg['secure']}) to $to\n", FILE_APPEND);
                return true;
            } catch (Exception $e) {
                $err = $this->mail->ErrorInfo ?? $e->getMessage();
                $this->lastError = 'Send failed (port ' . $cfg['port'] . '): ' . $err;
                file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . $this->lastError . "\n", FILE_APPEND);
                // try next transport
            }
        }

        // If SMTP attempts failed, try MailerSend API if available as a fallback
        $apiKey = getenv('MAILERSEND_API_KEY') ?: getenv('MAILERSEND_KEY');
        if ($apiKey) {
            $payload = [
                'from' => [ 'email' => $from, 'name' => $name ],
                'to' => [[ 'email' => $to ]],
                'subject' => $subject,
                'text' => $textBody ?? strip_tags($htmlBody),
                'html' => $htmlBody,
            ];

            $ch = curl_init('https://api.mailersend.com/v1/email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . "MailerSend response code: $httpCode; body: $res\n", FILE_APPEND);

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }
        }

        // All attempts failed
        error_log($this->lastError ?? 'Send failed: unknown error');
        return false;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
