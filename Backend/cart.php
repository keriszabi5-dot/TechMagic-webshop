<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }


if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

$cart_items = [];
$total_price = 0;
$cart_count = array_sum($_SESSION['cart']);

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $cart_items = $pdo->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosár - TechMagic</title>
    <link rel="stylesheet" href="style.css">
    <style>
      
        body {
            background-color: #f8fafc; 
            margin: 0;
            font-family: sans-serif;
        }
        
    
        .cart-wrapper {
            max-width: 550px;
            margin: 60px auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .cart-header-block {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 12px;
        }
        
        .cart-main-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        
 
        .cart-scroll-area {
            max-height: 320px;
            overflow-y: auto;
            padding-right: 4px;
        }
        
      
        .cart-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border: 1px solid #f3f4f6;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        
        .cart-row-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
    
        .cart-img-box {
            width: 48px;
            height: 48px;
            object-fit: contain;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 2px;
        }
        
        .cart-item-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .cart-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        
        .cart-item-meta {
            font-size: 11px;
            color: #6b7280;
        }
        
        .cart-row-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        
        .cart-subtotal {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            min-width: 65px;
            text-align: right;
        }
        
        .cart-remove-link {
            color: #ef4444;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            padding: 0 4px;
            line-height: 1;
        }
        
        .cart-remove-link:hover {
            color: #b91c1c;
        }
        
        /* Összegző szekció */
        .cart-total-bar {
            border-top: 1px solid #f3f4f6;
            margin-top: 20px;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-total-label {
            font-size: 13px;
            font-weight: 500;
            color: #4b5563;
        }
        
        .cart-total-price {
            font-size: 20px;
            font-weight: 800;
            color: #6366f1; 
        }
        
        .cart-empty-text {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            padding: 24px 0;
        }
    </style>
</head>
<body>

   
    <nav class="navbar">
        <div class="nav-container">
            <div style="display: flex; align-items: center;">
               
                <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <img src="images/logo.png" alt="TechMagic Logo" style="height: 28px; width: auto; border-radius: 50%; vertical-align: middle;">
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
                
                <a href="cart.php" class="cart-btn active">
                    <svg class="cart-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span class="cart-count"><?= $cart_count ?></span>
                </a>

                <a href="logout.php" class="logout-btn">Kilépés</a>
            </div>
        </div>
    </nav>

 
    <div class="cart-wrapper">
        <div class="cart-header-block">
            <h2 class="cart-main-title">Kosarad tartalma</h2>
            <a href="index.php?tab=products" style="text-decoration: none; font-size: 12px; color: #6366f1; font-weight: 500;">&larr; Vissza a termékekhez</a>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="cart-empty-text">
                <p style="margin-bottom: 16px;">A kosarad még üres.</p>
                <a href="index.php?tab=products" class="buy-btn" style="text-decoration: none; padding: 8px 16px;">Böngészés indítása</a>
            </div>
        <?php else: ?>
            <div class="cart-scroll-area">
                <?php foreach ($cart_items as $item): 
                    $qty = $_SESSION['cart'][$item['id']];
                    $subtotal = $item['price'] * $qty;
                    $total_price += $subtotal;
                ?>
                    <div class="cart-row">
                    
                        <div class="cart-row-left">
                            <?php if (!empty($item['image'])): ?>
                                <img src="images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-img-box">
                            <?php else: ?>
                                <div class="cart-img-box" style="display: flex; align-items: center; justify-content: center; font-size: 9px; color: #6366f1;">No img</div>
                            <?php endif; ?>
                            
                            <div class="cart-item-info">
                                <h4 class="cart-item-title"><?= htmlspecialchars($item['name']) ?></h4>
                                <span class="cart-item-meta">$<?= number_format($item['price'], 2) ?> &times; <?= $qty ?></span>
                            </div>
                        </div>

                        
                        <div class="cart-row-right">
                            <span class="cart-subtotal">$<?= number_format($subtotal, 2) ?></span>
                            <a href="cart.php?remove=<?= $item['id'] ?>" class="cart-remove-link" title="Törlés">&times;</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

   
            <div class="cart-total-bar">
                <span class="cart-total-label">Fizetendő végösszeg:</span>
                <span class="cart-total-price">$<?= number_format($total_price, 2) ?></span>
            </div>

           <a href="checkout.php" class="buy-btn" style="display: block; text-decoration: none; box-sizing: border-box; text-align: center; margin-top: 16px; padding: 12px; font-size: 13px; font-weight: 600;">Tovább a fizetéshez</a>
        <?php endif; ?>
    </div>

</html>
