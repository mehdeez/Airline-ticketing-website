<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error loading profile");
}

// Handle profile updates
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Validate current password if changing password
    if (!empty($new_password)) {
        if (!password_verify($current_password, $user['password_hash'])) {
            $errors[] = "Current password is incorrect";
        } elseif (!isStrongPassword($new_password)) {
            $errors[] = "New password must be 8+ chars with 1 uppercase & 1 symbol";
        }
    }
    
    if (empty($errors)) {
        try {
            // Update query
            $query = "UPDATE users SET full_name = ?, phone = ?";
            $params = [$full_name, $phone];
            
            // Add password update if changed
            if (!empty($new_password)) {
                $query .= ", password_hash = ?";
                $params[] = password_hash($new_password, PASSWORD_BCRYPT);
            }
            
            $query .= " WHERE user_id = ?";
            $params[] = $_SESSION['user_id'];
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            $success = "Profile updated successfully!";
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        } catch(PDOException $e) {
            $errors[] = "Update failed. Please try later.";
        }
    }
}
?>

<div class="profile-container" style="max-width: 800px; margin: 2rem auto;">
    <h2 style="color: var(--primary-purple);">My Profile</h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-section" style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <div class="profile-sidebar" style="background: var(--light-purple); padding: 1.5rem; border-radius: 8px;">
            <div style="text-align: center;">
                <div style="width: 100px; height: 100px; background: var(--secondary-purple); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <h3><?= htmlspecialchars($user['full_name']) ?></h3>
                <p>@<?= htmlspecialchars($user['username']) ?></p>
            </div>
            
            <hr style="border-color: rgba(94, 42, 132, 0.2);">
            
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 0.5rem;">
                    <i class="fas fa-envelope" style="color: var(--primary-purple);"></i> 
                    <?= htmlspecialchars($user['email']) ?>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <i class="fas fa-phone" style="color: var(--primary-purple);"></i> 
                    <?= $user['phone'] ? htmlspecialchars($user['phone']) : 'Not set' ?>
                </li>
                <li>
                    <i class="fas fa-calendar-alt" style="color: var(--primary-purple);"></i> 
                    Member since <?= date('M Y', strtotime($user['created_at'])) ?>
                </li>
            </ul>
        </div>
        
        <div class="profile-content">
            <form method="POST">
                <h3 style="color: var(--secondary-purple);">Edit Profile</h3>
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" 
                           value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>
                
                <h4 style="color: var(--secondary-purple); margin-top: 2rem;">Change Password</h4>
                
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control">
                    <small class="text-muted">Leave blank to keep current password</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
            
            <div style="margin-top: 3rem; padding-top: 1rem; border-top: 1px solid var(--light-purple);">
                <a href="logout.php" class="btn btn-danger" 
                   style="background: #dc3545; border-color: #dc3545;">Logout</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>