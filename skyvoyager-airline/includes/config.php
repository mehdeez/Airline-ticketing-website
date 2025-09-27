<?php
// Unique error-handling connection
$host = "localhost";
$dbname = "skyvoyager_db";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // Check if tables exist (extra validation)
    $tablesExist = $conn->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    if (!$tablesExist) {
        header("Location: install.php");
        exit();
    }
} catch(PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    die("<div style='background: var(--light-purple); padding: 20px; border-radius: 5px;'>
        <h3 style='color: var(--primary-purple);'>⚠️ Database Connection Error</h3>
        <p>Please check if:</p>
        <ol>
            <li>MySQL server is running</li>
            <li>Database 'skyvoyager_db' exists</li>
            <li>Credentials in <code>includes/config.php</code> are correct</li>
        </ol>
        <a href='install.php' style='color: var(--accent);'>Run installation</a>
        </div>");
}

// =============================================
function isAdmin() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    if($_SESSION['username'] == 'admin'){
        return true;
    }

    try {
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return (bool)$stmt->fetchColumn();
    } catch(PDOException $e) {
        error_log("Admin check failed: " . $e->getMessage());
        return false;
    }
}
?>