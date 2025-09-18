<?php
// PayStack integration via cURL
class Payment {
    private $secretKey;

    public function __construct() {
        $this->secretKey = PAYSTACK_SECRET_KEY;
    }

    // Initialize transaction
    public function initialize($email, $amount) {
        $url = 'https://api.paystack.co/transaction/initialize';
        $data = [
            'email' => $email,
            'amount' => $amount * 100,  // Kobo
            'currency' => 'NGN',
            'callback_url' => 'https://yourdomain.com/payment/callback',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($result['status']) {
            return $result['data']['authorization_url'];  // Redirect to this
        }
        throw new Exception('Payment init failed: ' . $result['message']);
    }

    // Verify transaction (called on callback)
    public function verify($reference) {
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['status'] && $result['data']['status'] === 'success';
    }

    // Webhook handler (POST to /webhook.php)
    public static function handleWebhook() {
        $input = file_get_contents('php://input');
        $event = json_decode($input, true);

        // Verify signature
        $hash = hash_hmac('sha512', $input, PAYSTACK_WEBHOOK_SECRET);
        if (strcmp($hash, $_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) === 0) {
            if ($event['event'] === 'charge.success') {
                $userId = // Extract from metadata or reference
                // Update payment status to success, activate user
                Database::query("UPDATE payments SET status = 'success' WHERE transaction_id = ?", [$event['data']['reference']]);
                // Also activate user: UPDATE users SET is_active=1 WHERE id=?
            }
        }
    }
}
?>