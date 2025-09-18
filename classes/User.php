<?php
// classes/User.php
require_once 'classes/Database.php';
require_once 'config/constants.php';
require_once 'config/email.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function register($data) {
        // Validate input
        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['full_name'])) {
            throw new Exception('All required fields must be filled');
        }
        if (!isset($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
            throw new Exception('Passwords do not match');
        }
        if (!preg_match(PASSWORD_PATTERN, $data['password'])) {
            throw new Exception('Password does not meet requirements');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Check email/username uniqueness
        $stmt = Database::query("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?", 
            [$data['email'], $data['username']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Email or username already exists');
        }

        // Handle profile picture
        $profilePic = 'default.jpg';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            if ($_FILES['profile_pic']['size'] > MAX_PIC_SIZE) {
                throw new Exception('Profile picture exceeds size limit');
            }
            if (!in_array($_FILES['profile_pic']['type'], ALLOWED_PIC_TYPES)) {
                throw new Exception('Invalid profile picture format');
            }
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $uploadPath = 'public/images/' . $filename;
            if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to upload profile picture');
            }
            $profilePic = $filename;
        }

        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(16));

        // Use transaction for user insert and token insert
        try {
            $this->pdo->beginTransaction();

            // Insert user
            Database::query("INSERT INTO users (username, email, password_hash, full_name, profile_pic, role) 
                VALUES (?, ?, ?, ?, ?, ?)", 
                [$data['username'], $data['email'], $password_hash, $data['full_name'], $profilePic, 'user']);
            $userId = $this->pdo->lastInsertId();
            if (!$userId) {
                $this->pdo->rollBack();
                throw new Exception('Failed to create user - no ID returned');
            }

            // Save verification token
            $expires = date('Y-m-d H:i:s', time() + VERIFICATION_EXPIRY);
            Database::query("INSERT INTO reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)", 
                [$userId, $token, $expires]);

            $this->pdo->commit();

            // Send verification email
            $email = new Email();
            $verifyUrl = SITE_URL . "?page=verify&token=" . $token;
            $message = "<h1>Verify Your Account</h1><p>Click <a href='$verifyUrl'>here</a> to verify your email.</p>";
            $email->send($data['email'], 'Verify Your Quiz App Account', $message, $userId);

            return $userId;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            throw new Exception('Email and password are required');
        }
        $stmt = Database::query("SELECT id, password_hash, is_active, is_verified FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new Exception('Invalid email or password');
        }
        if (!password_verify($password, $user['password_hash'])) {
            throw new Exception('Invalid email or password');
        }
        if (!$user['is_active']) {
            throw new Exception('Account is not active');
        }
        if (!$user['is_verified']) {
            throw new Exception('Account not verified');
        }
        $_SESSION['user_id'] = $user['id'];
        error_log("User logged in: user_id=" . $user['id'] . ", email=$email");
        return $user['id'];
    }

    public function requestReset($email) {
        $stmt = Database::query("SELECT id FROM users WHERE email = ?", [$email]);
        $row = $stmt->fetch();
        if (!$row) {
            return; // Silent fail per PRD
        }
        $userId = $row['id'];
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + RESET_EXPIRY);
        Database::query("INSERT INTO reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)", 
            [$userId, $token, $expires]);

        $resetUrl = SITE_URL . "?page=reset&token=" . $token;
        $message = "<h1>Reset Your Password</h1><p>Click <a href='$resetUrl'>here</a> to reset your password. This link expires in 1 hour.</p>";
        $email = new Email();
        $email->send($email, 'Password Reset Request', $message, $userId);
    }

    public function getUserData($userId) {
        $stmt = Database::query("SELECT id, username, email, full_name, profile_pic, is_active, is_verified, role 
                                 FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new Exception('User not found');
        }
        return $user;
    }
}
?>