<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Unique password strength validation
function isStrongPassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/', $password);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username)) $errors[] = "Username is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (!isStrongPassword($password)) $errors[] = "Password must be 8+ chars with 1 uppercase & 1 symbol";
    if ($password !== $confirm_password) $errors[] = "Passwords don't match";

    if (empty($errors)) {
        try {
            // Check if username/email exists
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username or email already exists";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, full_name, phone) 
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $full_name, $phone]);
                
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: profile.php");
                exit();
            }
        } catch(PDOException $e) {
            $errors[] = "System error. Please try later.";
        }
    }
}
?>

<div class="form-container" style="max-width: 500px; margin: 2rem auto;">
    <h2 style="color: var(--primary-purple);">Create Account</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Username*</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Email*</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Full Name*</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" class="form-control">
        </div>
        
        <div class="form-group">
            <label>Password*</label>
            <input type="password" name="password" class="form-control" required>
            <small class="text-muted">8+ chars with 1 uppercase & 1 symbol</small>
        </div>
        
        <div class="form-group">
            <label>Confirm Password*</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
        <p style="margin-top: 1rem;">Already have an account? <a href="login.php" style="color: var(--secondary-purple);">Login here</a></p>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>