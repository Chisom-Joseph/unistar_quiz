<?php
// classes/Payment.php
require_once 'classes/Database.php';
require_once 'config/constants.php';

class Payment {
    private $pdo;
    private $secretKey;

    public function __construct() {
        $this->pdo = Database::getInstance();
        $this->secretKey = PAYSTACK_SECRET_KEY;
    }

    public function initialize($email, $amount) {
        $url = 'https://api.paystack.co/transaction/initialize';
        $fields = [
            'email' => $email,
            'amount' => $amount * 100, // PayStack expects amount in kobo
            'callback_url' => SITE_URL . '?page=payment_callback',
            'metadata' => ['user_id' => $this->getUserIdByEmail($email)]
        ];

        $response = $this->makeRequest($url, $fields);
        if ($response['status'] && isset($response['data']['authorization_url'])) {
            // Save payment record
            $this->pdo->query(
                "INSERT INTO payments (user_id, amount, status, transaction_id) VALUES (?, ?, ?, ?)",
                [$fields['metadata']['user_id'], $amount, 'pending', $response['data']['reference']]
            );
            return $response['data']['authorization_url'];
        }
        throw new Exception('Failed to initialize payment: ' . ($response['message'] ?? 'Unknown error'));
    }

    public function verify($reference) {
        $url = "https://api.paystack.co/transaction/verify/$reference";
        $response = $this->makeRequest($url, [], 'GET');

        if ($response['status'] && $response['data']['status'] === 'success') {
            $this->pdo->query(
                "UPDATE payments SET status = ? WHERE transaction_id = ?",
                ['success', $reference]
            );
            return true;
        }
        $this->pdo->query(
            "UPDATE payments SET status = ? WHERE transaction_id = ?",
            ['failed', $reference]
        );
        return false;
    }

    public static function handleWebhook() {
        $input = file_get_contents('php://input');
        $event = json_decode($input, true);

        // Verify webhook signature
        $signature = hash_hmac('sha512', $input, PAYSTACK_WEBHOOK_SECRET);
        if (!hash_equals($signature, $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '')) {
            http_response_code(400);
            exit;
        }

        $pdo = Database::getInstance();
        if ($event['event'] === 'charge.success') {
            $reference = $event['data']['reference'];
            $pdo->query(
                "UPDATE payments SET status = ? WHERE transaction_id = ?",
                ['success', $reference]
            );
        }
    }

    private function makeRequest($url, $fields = [], $method = 'POST') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        throw new Exception('PayStack API request failed: HTTP ' . $httpCode);
    }

    private function getUserIdByEmail($email) {
        $stmt = $this->pdo->query("SELECT id FROM users WHERE email = ?", [$email]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new Exception('User not found for email: ' . $email);
        }
        return $row['id'];
    }
}
?>