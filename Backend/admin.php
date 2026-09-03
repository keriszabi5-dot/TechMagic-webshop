<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if (!empty($name) && $price >= 0 && $stock >= 0) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $stock, $product_id]);
            $message = "Termék sikeresen frissítve!";
        } catch (PDOException $e) {
            $message = "Hiba történt a mentés során: " . $e->getMessage();
        }
    } else {
        $message = "Minden mezőt kötelező helyesen kitölteni!";
    }
}

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminisztráció - TechMagic</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: sans-serif; background: #f1f5f9; margin: 0; padding: 20px; }
        .admin-container { max-width: 900px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .product-edit-row { border-bottom: 1px solid #e2e8f0; padding: 16px 0; }
        .edit-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)) 80px; gap: 10px; align-items: end; }
        .form-control { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; box-sizing: border-box; }
        .save-btn { background: #10b981; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .save-btn:hover { background: #059669; }
        .alert-info { background: #e0f2fe; color: #0369a1; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>🛠️ TechMagic - Termékek Kezelése</h2>
            <a href="index.php" class="buy-btn" style="text-decoration: none;">Vissza a főoldalra</a>
        </div>

        <?php if ($message): ?>
            <div class="alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <h3>Termék lista szerkesztése</h3>
        
        <?php foreach ($products as $product): ?>
            <div class="product-edit-row">
                <form method="POST" class="edit-form">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div>
                        <label style="font-size: 11px; color: #64748b;">Terméknév</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="form-control">
                    </div>
                    
                    <div>
                        <label style="font-size: 11px; color: #64748b;">Leírás</label>
                        <input type="text" name="description" value="<?= htmlspecialchars($product['description']) ?>" class="form-control">
                    </div>
                    
                    <div>
                        <label style="font-size: 11px; color: #64748b;">Ár ($)</label>
                        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required class="form-control">
                    </div>
                    
                    <div>
                        <label style="font-size: 11px; color: #64748b;">Készlet (db)</label>
                        <input type="number" name="stock" value="<?= $product['stock'] ?>" required class="form-control">
                    </div>
                    
                    <button type="submit" name="update_product" class="save-btn">Mentés</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>