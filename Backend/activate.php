<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';
session_start();

$message = '';
$is_success = false;

if (isset($_GET['token']) && !empty(trim($_GET['token']))) {
    $token = trim($_GET['token']);

    try {
     
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE activation_token = ? AND status = 0");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
          
            $update = $pdo->prepare("UPDATE users SET status = 1, activation_token = NULL WHERE id = ?");
            $update->execute([$user['id']]);

            $message = "Kedves <b>" . htmlspecialchars($user['username']) . "</b>! A fiókod sikeresen aktiválásra került.";
            $is_success = true;
        } else {
         
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE activation_token IS NULL AND status = 1");
         
            $message = "Érvénytelen, lejárt vagy már felhasznált aktivációs link.";
        }
    } catch (PDOException $e) {
        $message = "Adatbázis hiba történt: " . $e->getMessage();
    }
} else {
    $message = "Hiányzó vagy üres aktivációs kód a böngésző címsorában.";
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Fiók aktiválása</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: #ffffff; padding: 32px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; box-sizing: border-box; }
        .alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 15px; line-height: 1.5; }
        .success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .danger { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
        .btn { display: block; background: #6366f1; color: white; padding: 12px; border-radius: 6px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color: #111827; margin-top: 0;">Fiók aktiválása</h2>
        
        <div class="alert <?= $is_success ? 'success' : 'danger' ?>">
            <?= $message ?>
        </div>

        <?php if ($is_success): ?>
            <a href="login.php" class="btn">Tovább a bejelentkezéshez &rarr;</a>
        <?php else: ?>
            <a href="register.php" style="color: #6366f1; text-decoration: underline;">Vissza a regisztrációhoz</a>
        <?php endif; ?>
    </div>
</body>
</html>