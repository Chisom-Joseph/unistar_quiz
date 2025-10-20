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

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function getSchools() {
        $stmt = $this->pdo->query("SELECT id, name FROM schools ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllFaculties() {
        $stmt = $this->pdo->query("SELECT id, school_id, name FROM faculties ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFaculties($schoolId) {
        $stmt = $this->pdo->prepare("SELECT id, name FROM faculties WHERE school_id = ? ORDER BY name");
        $stmt->execute([$schoolId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDepartments() {
        $stmt = $this->pdo->query("SELECT id, faculty_id, name FROM departments ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartments($facultyId) {
        $stmt = $this->pdo->prepare("SELECT id, name FROM departments WHERE faculty_id = ? ORDER BY name");
        $stmt->execute([$facultyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function register($data) {
        // Validate input
        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['full_name']) ||
            !isset($data['school_id']) || !isset($data['faculty_id']) || !isset($data['department_id']) || !isset($data['level'])) {
            throw new Exception('All required fields must be filled');
        }
        if (!isset($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
            throw new Exception('Passwords do not match');
        }
        if (!preg_match(defined('PASSWORD_PATTERN') ? PASSWORD_PATTERN : '/^.{8,}$/', $data['password'])) {
            throw new Exception('Password must be at least 8 characters and meet complexity requirements');
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        if (!in_array($data['level'], ['100', '200', '300', '400', '500'])) {
            throw new Exception('Invalid level selected');
        }

        // Check email/username uniqueness
        $stmt = Database::query("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?", 
            [$data['email'], $data['username']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Email or username already exists');
        }

        // Validate school, faculty, department
        $stmt = Database::query("SELECT COUNT(*) FROM schools WHERE id = ?", [$data['school_id']]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Invalid school selected');
        }
        $stmt = Database::query("SELECT COUNT(*) FROM faculties WHERE id = ? AND school_id = ?", 
            [$data['faculty_id'], $data['school_id']]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Invalid faculty selected');
        }
        $stmt = Database::query("SELECT COUNT(*) FROM departments WHERE id = ? AND faculty_id = ?", 
            [$data['department_id'], $data['faculty_id']]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception('Invalid department selected');
        }

        // Handle profile picture
        $profilePic = 'default.jpg';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            error_log("Processing profile picture upload: " . print_r($_FILES['profile_pic'], true));
            if ($_FILES['profile_pic']['size'] > MAX_PIC_SIZE) {
                error_log("Profile picture size exceeds limit: {$_FILES['profile_pic']['size']} bytes");
                throw new Exception('Profile picture exceeds size limit');
            }
            if (!in_array($_FILES['profile_pic']['type'], ALLOWED_PIC_TYPES)) {
                error_log("Invalid profile picture type: {$_FILES['profile_pic']['type']}");
                throw new Exception('Invalid profile picture format');
            }
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . uniqid() . '.' . $ext;
            $uploadDir = realpath(__DIR__ . '/../public/uploads') . '/';
            $uploadPath = $uploadDir . $filename;
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    error_log("Failed to create directory: $uploadDir");
                    throw new Exception('Failed to create upload directory');
                }
            }
            if (!is_writable($uploadDir)) {
                error_log("Directory not writable: $uploadDir");
                throw new Exception('Upload directory is not writable');
            }
            if (!file_exists($_FILES['profile_pic']['tmp_name']) || !is_readable($_FILES['profile_pic']['tmp_name'])) {
                error_log("Temporary file missing or not readable: {$_FILES['profile_pic']['tmp_name']}");
                throw new Exception('Temporary file is missing or not readable');
            }
            if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadPath)) {
                error_log("Failed to move uploaded file from {$_FILES['profile_pic']['tmp_name']} to $uploadPath");
                throw new Exception('Failed to upload profile picture');
            }
            $profilePic = $filename;
            error_log("Profile picture uploaded successfully: $uploadPath");
        } elseif (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Profile picture upload error code: {$_FILES['profile_pic']['error']}");
            throw new Exception('Profile picture upload failed with error code: ' . $_FILES['profile_pic']['error']);
        }

        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(16));

        // Use transaction for user insert and token insert
        try {
            $this->pdo->beginTransaction();

            // Insert user
            Database::query("INSERT INTO users (username, email, password_hash, full_name, profile_pic, role, school_id, faculty_id, department_id, level) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                [$data['username'], $data['email'], $password_hash, $data['full_name'], $profilePic, 'user', 
                 $data['school_id'], $data['faculty_id'], $data['department_id'], $data['level']]);
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
        $stmt = Database::query("SELECT id, password_hash, is_active, is_verified, role FROM users WHERE email = ?", [$email]);
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
        $_SESSION['role'] = $user['role'];
        error_log("User logged in: user_id=" . $user['id'] . ", email=$email, role=" . $user['role']);
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
        $stmt = Database::query("
            SELECT u.id, u.username, u.email, u.full_name, u.profile_pic, u.is_active, u.is_verified, u.role,
                   u.school_id, u.faculty_id, u.department_id, u.level,
                   s.name AS school_name, f.name AS faculty_name, d.name AS department_name
            FROM users u
            LEFT JOIN schools s ON u.school_id = s.id
            LEFT JOIN faculties f ON u.faculty_id = f.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE u.id = ?", [$userId]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new Exception('User not found');
        }
        return $user;
    }

    public function updateUser($userId, $data) {
        $fields = [];
        $params = [];
        if (!empty($data['username'])) {
            $fields[] = 'username = ?';
            $params[] = $data['username'];
        }
        if (!empty($data['email'])) {
            $fields[] = 'email = ?';
            $params[] = $data['email'];
        }
        if (!empty($data['full_name'])) {
            $fields[] = 'full_name = ?';
            $params[] = $data['full_name'];
        }
        if (isset($data['school_id']) && $data['school_id'] !== '') {
            $fields[] = 'school_id = ?';
            $params[] = $data['school_id'];
        }
        if (isset($data['faculty_id']) && $data['faculty_id'] !== '') {
            $fields[] = 'faculty_id = ?';
            $params[] = $data['faculty_id'];
        }
        if (isset($data['department_id']) && $data['department_id'] !== '') {
            $fields[] = 'department_id = ?';
            $params[] = $data['department_id'];
        }
        if (isset($data['level']) && $data['level'] !== '') {
            $fields[] = 'level = ?';
            $params[] = $data['level'];
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = ?';
            $params[] = $data['is_active'];
        }
        if (isset($data['is_verified'])) {
            $fields[] = 'is_verified = ?';
            $params[] = $data['is_verified'];
        }
        if (!empty($data['role'])) {
            $fields[] = 'role = ?';
            $params[] = $data['role'];
        }
        if (empty($fields)) {
            return false;
        }
        $params[] = $userId;
        $stmt = $this->pdo->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
        try {
            // Validate uniqueness
            if (!empty($data['username'])) {
                $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmtCheck->execute([$data['username'], $userId]);
                if ($stmtCheck->fetch()) {
                    throw new Exception("Username already taken");
                }
            }
            if (!empty($data['email'])) {
                $stmtCheck = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmtCheck->execute([$data['email'], $userId]);
                if ($stmtCheck->fetch()) {
                    throw new Exception("Email already in use");
                }
            }
            // Validate school, faculty, department
            if (!empty($data['school_id'])) {
                $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM schools WHERE id = ?");
                $stmtCheck->execute([$data['school_id']]);
                if ($stmtCheck->fetchColumn() == 0) {
                    throw new Exception('Invalid school selected');
                }
            }
            if (!empty($data['faculty_id'])) {
                $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM faculties WHERE id = ? AND school_id = ?");
                $stmtCheck->execute([$data['faculty_id'], $data['school_id']]);
                if ($stmtCheck->fetchColumn() == 0) {
                    throw new Exception('Invalid faculty selected');
                }
            }
            if (!empty($data['department_id'])) {
                $stmtCheck = $this->pdo->prepare("SELECT COUNT(*) FROM departments WHERE id = ? AND faculty_id = ?");
                $stmtCheck->execute([$data['department_id'], $data['faculty_id']]);
                if ($stmtCheck->fetchColumn() == 0) {
                    throw new Exception('Invalid department selected');
                }
            }
            $stmt->execute($params);
            error_log("User updated: user_id=$userId");
            return true;
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfile($userId, $username, $email, $full_name, $profile_pic = null) {
        // Validate username and email uniqueness
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->fetch()) {
            throw new Exception("Username already taken");
        }
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            throw new Exception("Email already in use");
        }

        // Handle profile picture upload
        $profile_pic_path = $profile_pic ? $profile_pic : 'default.jpg';
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['size'] > 0) {
            error_log("Processing profile picture update: " . print_r($_FILES['profile_pic'], true));
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $file = $_FILES['profile_pic'];
            if (!in_array($file['type'], $allowed_types)) {
                error_log("Invalid profile picture type: {$file['type']}");
                throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
            }
            if ($file['size'] > $max_size) {
                error_log("Profile picture size exceeds limit: {$file['size']} bytes");
                throw new Exception("File size exceeds 2MB limit.");
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = realpath(__DIR__ . '/../public/uploads') . '/';
            $uploadPath = $uploadDir . $filename;
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    error_log("Failed to create directory: $uploadDir");
                    throw new Exception('Failed to create upload directory');
                }
            }
            if (!is_writable($uploadDir)) {
                error_log("Directory not writable: $uploadDir");
                throw new Exception('Upload directory is not writable');
            }
            if (!file_exists($file['tmp_name']) || !is_readable($file['tmp_name'])) {
                error_log("Temporary file missing or not readable: {$file['tmp_name']}");
                throw new Exception('Temporary file is missing or not readable');
            }
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                error_log("Failed to move uploaded file from {$file['tmp_name']} to $uploadPath");
                throw new Exception("Failed to upload profile picture.");
            }
            $profile_pic_path = $filename;
            error_log("Profile picture updated successfully: $uploadPath");
        }

        // Update user data
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET username = ?, email = ?, full_name = ?, profile_pic = ?
            WHERE id = ?
        ");
        try {
            $stmt->execute([$username, $email, $full_name, $profile_pic_path, $userId]);
            error_log("Profile updated for user_id: $userId");
        } catch (PDOException $e) {
            error_log("Profile update failed: " . $e->getMessage());
            throw new Exception("Profile update failed: " . $e->getMessage());
        }
    }

    public function changePassword($userId, $current_password, $new_password) {
        // Verify current password
        $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            throw new Exception("Current password is incorrect");
        }

        // Validate new password
        if (strlen($new_password) < 8) {
            throw new Exception("New password must be at least 8 characters long");
        }

        // Update password
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        try {
            $stmt->execute([$password_hash, $userId]);
            error_log("Password changed for user_id: $userId");
        } catch (PDOException $e) {
            error_log("Password change failed: " . $e->getMessage());
            throw new Exception("Password change failed: " . $e->getMessage());
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        error_log("User logged out");
    }
}
?>