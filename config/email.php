<?php
// config/email.php
require_once 'classes/Database.php';
require_once 'config/constants.php';

class Email {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function send($recipient, $subject, $body, $userId = null) {
        // Email headers
        $headers = "From: Quiz App <admin@sportfanszone.com>\r\n";
        $headers .= "Reply-To: admin@sportfanszone.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Log notification
        $status = 'sent';
        try {
            // Send email using mail()
            if (!mail($recipient, $subject, $body, $headers)) {
                throw new Exception('Email sending failed');
            }
            error_log("Email sent to $recipient");

            // Log notification if userId is provided
            if ($userId !== null) {
                Database::query("INSERT INTO notifications (user_id, type, content, status) VALUES (?, ?, ?, ?)", 
                    [$userId, 'email', $body, $status]); // Line ~24 (fixed)
            }
            return true;
        } catch (Exception $e) {
            $status = 'failed';
            if ($userId !== null) {
                Database::query("INSERT INTO notifications (user_id, type, content, status) VALUES (?, ?, ?, ?)", 
                    [$userId, 'email', $body, $status]);
            }
            error_log("Email failed for $recipient: " . $e->getMessage());
            throw new Exception('Email could not be sent: ' . $e->getMessage());
        }
    }
}
?>