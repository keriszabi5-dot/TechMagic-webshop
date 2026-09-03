<?php
require 'db.php';
session_start();

if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

$active_tab = $_GET['tab'] ?? 'home';
if (!in_array($active_tab, ['home', 'products', 'contact'])) {
    $active_tab = 'home';
}

if (isset($_GET['add'])) {
    $product_id = (int)$_GET['add'];
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
    
    header("Location: index.php?tab=" . urlencode($active_tab));
    exit;
}

$cart_count = array_sum($_SESSION['cart']);
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

$weekly_offers = array_slice($products, 0, 2);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechBolt - TechMagic</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <img src="images/logo.png" alt="TechBolt Logo" style="height: 28px; width: auto; border-radius: 50%; vertical-align: middle;">
                    <span>Tech<span style="color: #6366f1;">Magic</span></span>
                </a>
                
                <div class="nav-tabs" style="margin-left: 24px; display: flex; gap: 16px; padding-top: 6px;">
                    <a href="index.php?tab=home" id="link-home" class="tab-link <?= $active_tab === 'home' ? 'active' : '' ?>" style="font-size: 15px; padding: 6px 12px; font-weight: 500;" onclick="switchTab(event, 'home')">Főoldal</a>
                    <a href="index.php?tab=products" id="link-products" class="tab-link <?= $active_tab === 'products' ? 'active' : '' ?>" style="font-size: 15px; padding: 6px 12px; font-weight: 500;" onclick="switchTab(event, 'products')">Termékek</a>
                    <a href="index.php?tab=contact" id="link-contact" class="tab-link <?= $active_tab === 'contact' ? 'active' : '' ?>" style="font-size: 15px; padding: 6px 12px; font-weight: 500;" onclick="switchTab(event, 'contact')">Elérhetőség</a>
                </div>
            </div>
            
            <div class="nav-menu">
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="welcome-text">Szia, <b><?= htmlspecialchars($_SESSION['username']) ?></b></span>
                    
                    <?php if (isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1): ?>
                        <a href="admin.php" style="color: #6366f1; font-weight: 700; text-decoration: none; margin-right: 8px; border: 1px solid #6366f1; padding: 4px 8px; border-radius: 6px; background: rgba(99, 102, 241, 0.05);">🛠️ Admin panel</a>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <span class="welcome-text">Szia, <b>Vendég</b></span>
                <?php endif; ?>
                
                <a href="cart.php" class="cart-btn">
                    <svg class="cart-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span class="cart-count"><?= $cart_count ?></span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="logout-btn">Kilépés</a>
                <?php else: ?>
                    <a href="register.php" class="logout-btn" style="background: #10b981; color: white; margin-right: 8px;">Regisztráció</a>
                    <a href="login.php" class="logout-btn" style="background: #6366f1; color: white;">Belépés</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
  
    <main class="main-content">
        <div id="home" class="tab-content <?= $active_tab === 'home' ? 'active' : '' ?>">
            <div style="background: #ffffff; border: 1px solid #f3f4f6; border-radius: 12px; padding: 32px; text-align: center; max-width: 600px; margin: 0 auto 32px auto;">
                <h2 style="font-size: 25px; font-weight: 900; margin-bottom: 8px; color: #111827;">Üdvözlünk a TechMagic oldalon!</h2>
                <p style="font-size: 18px; color: #6b7280; line-height: 1.6; margin-bottom: 20px;">
                    Fedezd fel a legújabb technológiai kiegészítőket és prémium perifériákat egy helyen. Használd a fenti menüt a termékek böngészéséhez!
                </p>
                <a href="index.php?tab=products" class="buy-btn" style="padding: 10px 20px; font-size: 20px;" onclick="switchTab(event, 'products')">Termékek megtekintése</a>
            </div>

            <div style="max-width: 800px; margin: 0 auto; background: #ffffff; border: 1px solid #f3f4f6; border-radius: 12px; padding: 24px;">
                <h2 style="font-size: 35px; font-weight: 900; text-align: center; margin-bottom: 24px; color: #111827; position: relative; padding-bottom: 8px;">
                    🔥 Heti ajánlataink
                    <span style="display: block; width: 50px; height: 3px; background: #6366f1; margin: 8px auto 0 auto; border-radius: 2px;"></span>
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <?php if (!empty($weekly_offers)): ?>
                        <?php foreach ($weekly_offers as $product): ?>
                            <?php 
                                $image_file = '';
                                if (!empty($product['image'])) {
                                    $image_file = $product['image'];
                                } else {
                                    $product_name_lower = mb_strtolower($product['name']);
                                    if (strpos($product_name_lower, 'keyboard') !== false || strpos($product_name_lower, 'billentyű') !== false) {
                                        $image_file = 'keyboard.jpg';
                                    } elseif (strpos($product_name_lower, 'mouse') !== false || strpos($product_name_lower, 'egér') !== false) {
                                        $image_file = 'mouse.png';
                                    } elseif (strpos($product_name_lower, 'headset') !== false || strpos($product_name_lower, 'fejhallgató') !== false) {
                                        $image_file = 'headset.jpg';
                                    }
                                }
                            ?>
                            
                            <div class="product-card" style="display: flex; flex-direction: column; justify-content: space-between; cursor: pointer; border: 1px solid #e5e7eb; transition: transform 0.2s, border-color 0.2s;" onclick="switchTab(event, 'products')">
                                <div>
                                    <div class="product-image-container" style="width: 100%; height: 160px; overflow: hidden; margin-bottom: 12px; display: flex; align-items: center; justify-content: center;">
                                        <?php if (!empty($image_file)): ?>
                                            <img src="images/<?= htmlspecialchars($image_file) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        <?php else: ?>
                                            <span style="color: #6366f1; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">TechBolt Device</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="product-name" style="color: #6366f1;"><?= htmlspecialchars($product['name']) ?></h3>
                                    <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                                    
                                    <p style="font-size: 12px; margin-bottom: 8px;">
                                        <?php if ((int)$product['stock'] > 0): ?>
                                            <span style="color: #10b981; font-weight: 600;">Készleten: <?= (int)$product['stock'] ?> db</span>
                                        <?php else: ?>
                                            <span style="color: #ef4444; font-weight: 600;">Nincs raktáron</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="product-footer" style="margin-top: 12px;">
                                    <span class="product-price" style="font-size: 20px; font-weight: 800; color: #10b981;">$<?= number_format($product['price'], 2) ?></span>
                                    <span style="font-size: 13px; color: #6366f1; font-weight: 600; text-decoration: underline;">Részletek →</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #6b7280; grid-column: 1 / -1;">Jelenleg nincs elérhető akciós termék.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="products" class="tab-content <?= $active_tab === 'products' ? 'active' : '' ?>">
            <h2 style="font-size: 50px; font-weight: 900; margin-bottom: 16px; color: #374151;">Kiemelt termékeink</h2>
            <div class="grid">
                <?php foreach ($products as $product): ?>
                    <?php 
                        $image_file = '';
                        if (!empty($product['image'])) {
                            $image_file = $product['image'];
                        } else {
                            $product_name_lower = mb_strtolower($product['name']);
                            if (strpos($product_name_lower, 'keyboard') !== false || strpos($product_name_lower, 'billentyű') !== false) {
                                $image_file = 'keyboard.jpg';
                            } elseif (strpos($product_name_lower, 'mouse') !== false || strpos($product_name_lower, 'egér') !== false) {
                                $image_file = 'mouse.png';
                            } elseif (strpos($product_name_lower, 'headset') !== false || strpos($product_name_lower, 'fejhallgató') !== false) {
                                $image_file = 'headset.jpg';
                            }
                        }
                    ?>
                    <div class="product-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="product-image-container" style="width: 100%; height: 160px; overflow: hidden; margin-bottom: 12px; display: flex; align-items: center; justify-content: center;">
                                <?php if (!empty($image_file)): ?>
                                    <img src="images/<?= htmlspecialchars($image_file) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                <?php else: ?>
                                    <span style="color: #6366f1; font-size: 11px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">TechBolt Device</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                            
                            <p style="font-size: 12px; margin-bottom: 8px;">
                                <?php if ((int)$product['stock'] > 0): ?>
                                    <span style="color: #10b981; font-weight: 600;">Készleten: <?= (int)$product['stock'] ?> db</span>
                                <?php else: ?>
                                    <span style="color: #ef4444; font-weight: 600;">Átmenetileg nem elérhető</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="product-footer" style="margin-top: 12px;">
                            <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
                            
                            <?php if ((int)$product['stock'] > 0): ?>
                                <a href="index.php?add=<?= $product['id'] ?>&tab=products" class="buy-btn add-to-cart-btn">+ Kosárba</a>
                            <?php else: ?>
                                <span style="background: #f3f4f6; color: #9ca3af; font-size: 12px; padding: 6px 12px; border-radius: 8px; font-weight: 700;">Elfogyott</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="contact" class="tab-content <?= $active_tab === 'contact' ? 'active' : '' ?>">
            <div class="contact-card">
                <h3 class="contact-title">Kapcsolat & Elérhetőség</h3>
                <div class="contact-item"><span>Ügyfélszolgálat:</span> <b>keriszabi5@gmail.com</b></div>
                <div class="contact-item"><span>Telefon:</span> <b>+36 1 234 5678</b></div>
                <div class="contact-item"><span>Címünk:</span> <b>1061 Budapest, Andrássy út 1.</b></div>
                <div class="contact-item"><span>Nyitvatartás:</span> <b>H-P: 09:00 - 18:00</b></div>
            </div>
        </div>
    </main>

    <script>
    function switchTab(event, tabId) {
        if (event) event.preventDefault();
        
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.classList.remove('active'));
        
        const links = document.querySelectorAll('.tab-link');
        links.forEach(link => link.classList.remove('active'));
        
        document.getElementById(tabId).classList.add('active');
        const linkElement = document.getElementById('link-' + tabId);
        if (linkElement) linkElement.classList.add('active');
        
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
        window.history.pushState({ path: newUrl }, '', newUrl);
        
        const cartButtons = document.querySelectorAll('.add-to-cart-btn');
        cartButtons.forEach(btn => {
            const url = new URL(btn.href);
            url.searchParams.set('tab', tabId);
            btn.href = url.toString();
        });
    }
    </script>
</body>
</html>