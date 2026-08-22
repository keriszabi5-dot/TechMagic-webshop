<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (empty($_SESSION['cart'])) { header("Location: index.php?tab=products"); exit; }

$user_id = $_SESSION['user_id'];
$cart_count = array_sum($_SESSION['cart']);
$error_message = '';
$success = false;
$order_id = 0;


$ids = implode(',', array_keys($_SESSION['cart']));
$cart_items = $pdo->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll(PDO::FETCH_ASSOC);

$total_price = 0;
foreach ($cart_items as $item) {
    $qty = $_SESSION['cart'][$item['id']];
    $total_price += $item['price'] * $qty;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        $pdo->beginTransaction();

      
        foreach ($cart_items as $item) {
            $qty = $_SESSION['cart'][$item['id']];
            if ($item['stock'] < $qty) {
                throw new Exception("Sajnos a(z) '" . $item['name'] . "' termékből nincs elég raktáron (Elérhető: " . $item['stock'] . " db).");
            }
        }

        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->execute([$user_id, $total_price]);
        $order_id = $pdo->lastInsertId();

    
        $insert_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($cart_items as $item) {
            $qty = $_SESSION['cart'][$item['id']];
            
         
            $insert_item->execute([$order_id, $item['id'], $qty, $item['price']]);
            
           
            $update_stock->execute([$qty, $item['id']]);
        }
 
        $pdo->commit();
         
        $_SESSION['cart'] = [];
        $success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fizetés - TechMagic</title>
    <link rel="stylesheet" href="style.css">
    <style>
        html { scrollbar-gutter: stable; }
        body {
            background-image: url('images/main-bg.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            background-attachment: fixed;
            margin: 0;
            font-family: sans-serif;
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        }
        .checkout-wrapper {
            max-width: 500px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(229, 231, 235, 0.8);
            border-radius: 16px;
            padding: 32px;
            backdrop-filter: blur(8px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .message-box {
            padding: 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="logo.png" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <img src="images/logo.png" alt="TechBolt Logo" style="height: 28px; width: auto; border-radius: 50%; vertical-align: middle;">
                    <span>Tech<span style="color: #6366f1;">Magic</span></span>
                </a>
                <div class="nav-tabs" style="margin-left: 24px; display: flex; gap: 16px; padding-top: 6px;">
                    <a href="index.php?tab=home" class="tab-link" style="font-size: 15px; padding: 6px 12px; font-weight: 500;">Főoldal</a>
                    <a href="index.php?tab=products" class="tab-link" style="font-size: 15px; padding: 6px 12px; font-weight: 500;">Termékek</a>
                    <a href="index.php?tab=contact" class="tab-link" style="font-size: 15px; padding: 6px 12px; font-weight: 500;">Elérhetőség</a>
                </div>
            </div>
            <div class="nav-menu">
                <span class="welcome-text">Szia, <b><?= htmlspecialchars($_SESSION['username']) ?></b></span>
                <a href="cart.php" class="cart-btn">
                    <svg class="cart-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span class="cart-count"><?= $success ? 0 : $cart_count ?></span>
                </a>
                <a href="logout.php" class="logout-btn">Kilépés</a>
            </div>
        </div>
    </nav>

   
    <div class="checkout-wrapper">
        <?php if ($success): ?>
            <div class="message-box success">
                🎉 Sikeres rendelés! Köszönjük a vásárlást!
            </div>
            <h2 style="font-size: 20px; font-weight: 800; color: #111827; margin: 0 0 8px 0;">Rendelésszám: #<?= $order_id ?></h2>
            <p style="font-size: 13px; color: #6b7280; line-height: 1.5; margin-bottom: 24px;">
                A fizetendő összeget ($<?= number_format($total_price, 2) ?>) a csomag átvételekor tudod kiegyenlíteni a futárnál.
            </p>
            <a href="index.php?tab=home" class="buy-btn" style="text-decoration: none; padding: 12px 24px; font-size: 13px; display: inline-block;">Vissza a főoldalra</a>
        <?php else: ?>
            <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 12px 0; text-align: left;">Rendelés véglegesítése</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="message-box error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; text-align: left; margin-bottom: 20px;">
                <span style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 4px;">Fizetési mód:</span>
                <b style="font-size: 13px; color: #111827; display: block; margin-bottom: 12px;">📦 Utánvét (Készpénz vagy Kártya a futárnál)</b>
                
                <div style="border-top: 1px solid #e5e7eb; padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; color: #4b5563; font-weight: 500;">Fizetendő végösszeg:</span>
                    <span style="font-size: 18px; font-weight: 800; color: #6366f1;">$<?= number_format($total_price, 2) ?></span>
                </div>
            </div>
            <form action="checkout.php" method="POST">
                <button type="submit" class="buy-btn" style="width: 100%; box-sizing: border-box; text-align: center; padding: 14px; font-size: 13px; font-weight: 600; border: none; cursor: pointer;">
                    Rendelés leadása és fizetés
                </button>
            </form>
            <a href="cart.php" style="display: block; margin-top: 14px; font-size: 12px; color: #6b7280; text-decoration: none;">Vissza a kosárhoz</a>
        <?php endif; ?>
    </div>

</body>
</html>