<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\EmailQueue;

/**
 * EmailService
 * Handles email sending using PHPMailer
 */
class EmailService
{
    private EmailQueue $emailQueue;
    private array $config;

    public function __construct()
    {
        $this->emailQueue = new EmailQueue();
        $this->config = $this->loadConfig();
    }

    /**
     * Load email configuration
     */
    private function loadConfig(): array
    {
        return [
            'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
            'smtp_port' => getenv('SMTP_PORT') ?: 587,
            'smtp_user' => getenv('SMTP_USER') ?: '',
            'smtp_pass' => getenv('SMTP_PASS') ?: '',
            'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'noreply@livepetal.com',
            'from_name' => getenv('MAIL_FROM_NAME') ?: 'LivePetal',
            'encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls'
        ];
    }

    /**
     * Process email queue
     */
    public function processQueue(int $batchSize = 50): array
    {
        $emails = $this->emailQueue->getPending($batchSize);
        $results = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0
        ];

        foreach ($emails as $email) {
            $results['processed']++;

            if ($this->sendEmail($email)) {
                $this->emailQueue->markAsSent($email['id']);
                $results['sent']++;
            } else {
                $error = $this->getLastError();
                $this->emailQueue->incrementAttempts($email['id'], $error);

                // Mark as failed if max attempts reached
                if ($email['attempts'] + 1 >= $email['max_attempts']) {
                    $this->emailQueue->markAsFailed($email['id'], $error);
                }

                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send individual email
     */
    private function sendEmail(array $emailData): bool
    {
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_user'];
            $mail->Password = $this->config['smtp_pass'];
            $mail->SMTPSecure = $this->config['encryption'];
            $mail->Port = $this->config['smtp_port'];

            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($emailData['recipient_email'], $emailData['recipient_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $emailData['subject'];

            // Render template
            $body = $this->renderTemplate(
                $emailData['template'],
                $emailData['body'],
                json_decode($emailData['template_data'], true) ?? []
            );

            $mail->Body = $body;
            $mail->AltBody = strip_tags($emailData['body']);

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    private ?string $lastError = null;

    private function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Render email template
     */
    private function renderTemplate(string $template, string $message, array $data): string
    {
        $templatePath = __DIR__ . "/../templates/emails/{$template}.php";

        // Use default template if specific one doesn't exist
        if (!file_exists($templatePath)) {
            $templatePath = __DIR__ . "/../templates/emails/default_notification.php";
        }

        // If template file exists, use it
        if (file_exists($templatePath)) {
            ob_start();
            extract($data);
            $notificationMessage = $message;
            include $templatePath;
            return ob_get_clean();
        }

        // Fallback to simple HTML
        return $this->getDefaultTemplate($message, $data);
    }

    /**
     * Get default HTML template
     */
    private function getDefaultTemplate(string $message, array $data): string
    {
        $actionButton = '';
        if (!empty($data['action_url'])) {
            $actionButton = '<a href="' . htmlspecialchars($data['action_url']) . '" 
                              style="display: inline-block; padding: 12px 24px; background: #4F46E5; 
                              color: white; text-decoration: none; border-radius: 6px; margin-top: 20px;">
                              View Details
                            </a>';
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 8px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                            <h1 style="margin: 0; color: white; font-size: 24px;">LivePetal</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <div style="color: #333; font-size: 16px; line-height: 1.6;">
                                {$message}
                            </div>
                            {$actionButton}
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0; color: #6c757d; font-size: 14px;">
                                &copy; 2024 LivePetal. All rights reserved.
                            </p>
                            <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 12px;">
                                This is an automated notification. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Send test email
     */
    public function sendTestEmail(string $toEmail, string $toName = 'Test User'): bool
    {
        $emailId = $this->emailQueue->create([
            'recipient_email' => $toEmail,
            'recipient_name' => $toName,
            'subject' => 'Test Email - LivePetal Notification System',
            'body' => 'This is a test email to verify your notification system is working correctly.',
            'template' => 'default_notification',
            'template_data' => json_encode([]),
            'priority' => 'normal',
            'status' => 'pending',
            'attempts' => 0,
            'max_attempts' => 3
        ]);

        if (!$emailId) {
            return false;
        }

        $email = $this->emailQueue->find($emailId);
        if ($this->sendEmail($email)) {
            $this->emailQueue->markAsSent($emailId);
            return true;
        }

        return false;
    }

    /**
     * Get queue statistics
     */
    public function getQueueStats(): array
    {
        return $this->emailQueue->getStats();
    }
}
