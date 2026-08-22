<?php
require 'db.php';
session_start();


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$error_type = 'danger'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['username']); 
    $pass = trim($_POST['password']);

    if (!empty($login_input) && !empty($pass)) {
        try {
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login_input, $login_input]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

          
            if ($userData && password_verify($pass, $userData['password'])) {
                
           
                if ((int)$userData['status'] === 1) {
               
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['email'] = $userData['email'];

                    header("Location: index.php");
                    exit;
                } else {
                 
                    $error = "A fiókod még nincs aktiválva! Kérjük, ellenőrizd az e-mail fiókodat a megerősítő linkért.";
                    $error_type = 'warning'; 
                }
            } else {
                $error = "Hibás felhasználónév, e-mail cím vagy jelszó.";
                $error_type = 'danger';
            }
        } catch (PDOException $e) {
            $error = "Adatbázis hiba történt: " . $e->getMessage();
            $error_type = 'danger';
        }
    } else {
        $error = "Minden mezőt kötelező kitölteni.";
        $error_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link class="style-link" rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                
                <div class="auth-logo" style="text-align: center; margin-bottom: 20px;">
                    <img src="images/logo.png" alt="TechMagic Logo" style="max-width: 150px; height: auto;">
                </div>
                
                <h2 class="auth-title">Üdvözlünk a TechMagic oldalon!</h2>
                <p class="auth-subtitle">Lépj be a fiókodba a vásárláshoz</p>
            </div>

         
            <?php if ($error): ?>
                <div class="alert alert-<?= $error_type ?>"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Felhasználónév vagy E-mail cím</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($login_input ?? '') ?>" required class="form-input" placeholder="felhasznalonev vagy@email.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Jelszó</label>
                    <input type="password" name="password" required class="form-input" placeholder="••••••••">
                </div>
                <button type="submit" class="submit-btn">Bejelentkezés</button>
            </form>

            <div style="margin-top: 12px;">
                <a href="index.php" class="submit-btn" style="display: block; text-align: center; text-decoration: none; background: #6b7280; color: white;">
                    Tovablépés vendégként
                </a>
            </div>

            <div class="auth-footer" style="margin-top: 20px;">
                Nincs még fiókod? <a href="register.php" class="auth-link">Regisztrálj itt</a>
            </div>
        </div>
    </div>

</body>
</html>
