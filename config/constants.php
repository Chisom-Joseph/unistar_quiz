<?php
define('SITE_URL', 'http://quizapp.test'); // Laragon URL
define('MIN_PASSWORD_LEN', 8);
define('PASSWORD_PATTERN', '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/');
define('MAX_PIC_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_PIC_TYPES', ['image/jpeg', 'image/png']);
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('RATE_LIMIT_ATTEMPTS', 5);
define('RATE_LIMIT_LOCKOUT', 900); // 15 minutes
define('VERIFICATION_EXPIRY', 86400); // 24 hours
define('RESET_EXPIRY', 3600); // 1 hour
define('PAYMENT_AMOUNT', 5000.00); // ₦, editable via admin
define('PAYSTACK_PUBLIC_KEY', 'pk_test_your_public_key'); // Replace
define('PAYSTACK_SECRET_KEY', 'sk_test_your_secret_key'); // Replace
define('PAYSTACK_WEBHOOK_SECRET', 'whsec_your_webhook_secret'); // Replace
// define('SITE_URL', 'http://quizapp.test/'); // Laragon URL
?>