<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            
            // Update last login
            $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?")
                 ->execute([$user['user_id']]);
            
            header("Location: profile.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } catch(PDOException $e) {
        $error = "Login failed. Please try later.";
    }
}
?>

<div class="form-container" style="max-width: 500px; margin: 2rem auto;">
    <h2 style="color: var(--primary-purple);">Login to SkyVoyager</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
        <p style="margin-top: 1rem;">New user? <a href="register.php" style="color: var(--secondary-purple);">Create account</a></p>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>