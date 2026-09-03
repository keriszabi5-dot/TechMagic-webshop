<?php
require 'db.php';
session_start();

$message = '';
$is_success = false;
$activation_link = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);
    $pass_confirm = trim($_POST['password_confirm']);

    if (empty($user) || empty($email) || empty($pass) || empty($pass_confirm)) {
        $message = "Minden mező kitöltése kötelező.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Érvénytelen e-mail cím formátum.";
    } elseif (strlen($pass) < 6) {
        // SZERVEROLDALI ELLENŐRZÉS: Legalább 6 karakter (több mint 5)
        $message = "A jelszónak legalább 6 karakterből kell állnia.";
    } elseif ($pass !== $pass_confirm) {
        $message = "A két jelszó nem egyezik meg.";
    } else {
        $hashed_password = password_hash($pass, PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(32)); 
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, activation_token, status) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$user, $email, $hashed_password, $token]);
            
            $current_dir = dirname($_SERVER['REQUEST_URI']);
            $activation_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $current_dir . "/activate.php?token=" . $token;
            
            $to = $email;
            $subject = "TechMagic - Regisztracio megerositese";
            $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: TechMagic <noreply@techmagic.hu>\r\n";
            $mail_body = "<html><body><h2>Kedves " . htmlspecialchars($user) . "!</h2><p>Kattints ide a fiókod aktiválásához:</p><p><a href='" . $activation_link . "'>Fiók aktiválása</a></p></body></html>";
            
            @mail($to, $subject, $mail_body, $headers);
            
            $message = "Sikeres regisztráció! Kérjük, aktiváld a fiókodat a továbblépéshez.";
            $is_success = true;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "A felhasználónév vagy az e-mail cím már foglalt.";
            } else {
                $message = "Adatbázis hiba történt: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h2 class="auth-title">Fiók létrehozása</h2>
                <p class="auth-subtitle">Csatlakozz és vásárolj egyszerűen</p>
            </div>

            <?php if ($message): ?>
                <div class="alert <?= $is_success ? 'alert-success' : 'alert-danger' ?>" style="padding: 15px; border-radius: 6px; margin-bottom: 20px; line-height: 1.5;">
                    <div style="font-weight: 600;"><?= $message ?></div>
                    
                    <?php if ($is_success && $activation_link): ?>
                        <div style="margin-top: 15px; text-align: center;">
                            <a href="<?= $activation_link ?>" style="display: inline-block; background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; transition: background 0.2s;">Fiók aktiválása most &rarr;</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$is_success): ?>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Felhasználónév</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user ?? '') ?>" required class="form-input" placeholder="Felhasználónév">
                </div>
                <div class="form-group">
                    <label class="form-label">E-mail cím</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required class="form-input" placeholder="peldany@email.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Jelszó (minimum 6 karakter)</label>
                    <!-- KLIENSOLDALI ELLENŐRZÉS: minlength="6" -->
                    <input type="password" name="password" minlength="6" required class="form-input" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label class="form-label">Jelszó újra</label>
                    <!-- KLIENSOLDALI ELLENŐRZÉS: minlength="6" -->
                    <input type="password" name="password_confirm" minlength="6" required class="form-input" placeholder="••••••••">
                </div>
                <button type="submit" class="submit-btn">Regisztráció</button>
            </form>
            <?php endif; ?>

            <div class="auth-footer">
                Már van fiókod? <a href="login.php" class="auth-link">Lépj be itt</a>
            </div>
        </div>
    </div>
</body>
</html>