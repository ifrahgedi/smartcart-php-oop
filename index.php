<?php
session_start();
require_once __DIR__ . '/classes/ShoppingCart.php';

$products = [
    1 => new Product(1, 'Laptop Stand', 2500),
    2 => new Product(2, 'Wireless Mouse', 1200),
    3 => new Product(3, 'USB-C Cable', 700),
    4 => new Product(4, 'Laptop Sleeve', 1800),
];

if (!isset($_SESSION['cart']) || !($_SESSION['cart'] instanceof ShoppingCart)) {
    $_SESSION['cart'] = new ShoppingCart();
}
$cart = $_SESSION['cart'];
$message = '';
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'add') {
            $id = (int)($_POST['product_id'] ?? 0);
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            if (isset($products[$id])) {
                $cart->addProduct($products[$id], $qty);
                $message = 'Product added to cart.';
            }
        } elseif ($action === 'update') {
            foreach (($_POST['quantities'] ?? []) as $id => $qty) {
                $cart->updateQuantity((int)$id, (int)$qty);
            }
            $message = 'Cart updated.';
        } elseif ($action === 'remove') {
            $cart->removeProduct((int)($_POST['product_id'] ?? 0));
            $message = 'Item removed.';
        } elseif ($action === 'discount') {
            $cart->setDiscount((float)($_POST['discount'] ?? 0));
            $message = 'Discount applied.';
        } elseif ($action === 'clear') {
            $cart->clear();
            $message = 'Cart cleared.';
        }
        $_SESSION['cart'] = $cart;
    }
} catch (InvalidArgumentException $e) {
    $error = $e->getMessage();
}

function money(float $amount): string { return 'KSh ' . number_format($amount, 2); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCart | PHP OOP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<main class="container">
    <header>
        <div><span class="eyebrow">PHP OOP REFERENCE PROJECT</span><h1>SmartCart</h1></div>
        <p>A database-free shopping cart built with classes, objects, encapsulation and sessions.</p>
    </header>

    <?php if ($message): ?><div class="notice success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="notice error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <section>
        <h2>Available Products</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <article class="card">
                    <div class="product-icon">SC</div>
                    <h3><?= htmlspecialchars($product->getName()) ?></h3>
                    <strong><?= money($product->getPrice()) ?></strong>
                    <form method="post" class="add-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $product->getId() ?>">
                        <input type="number" name="quantity" value="1" min="1" max="20" aria-label="Quantity">
                        <button type="submit">Add to cart</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="cart-section">
        <div class="section-title"><h2>Your Cart</h2><span><?= count($cart->getItems()) ?> product(s)</span></div>
        <?php if (!$cart->getItems()): ?>
            <div class="empty">Your cart is empty. Add a product above.</div>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <div class="table-wrap"><table>
                    <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($cart->getItems() as $id => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item->getProduct()->getName()) ?></td>
                            <td><?= money($item->getProduct()->getPrice()) ?></td>
                            <td><input class="qty" type="number" name="quantities[<?= $id ?>]" value="<?= $item->getQuantity() ?>" min="0" max="20"></td>
                            <td><?= money($item->getTotal()) ?></td>
                            <td><button class="link-btn" form="remove-<?= $id ?>">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
                <button type="submit" class="secondary">Update quantities</button>
            </form>
            <?php foreach ($cart->getItems() as $id => $item): ?>
                <form id="remove-<?= $id ?>" method="post" class="hidden"><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= $id ?>"></form>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="checkout-grid">
        <div class="discount-box">
            <h2>Discount</h2>
            <p>Try a percentage from 0 to 50.</p>
            <form method="post" class="discount-form">
                <input type="hidden" name="action" value="discount">
                <input type="number" name="discount" min="0" max="50" step="1" value="<?= $cart->getDiscountPercent() ?>">
                <button type="submit">Apply</button>
            </form>
        </div>
        <div class="summary">
            <h2>Order Summary</h2>
            <div><span>Subtotal</span><strong><?= money($cart->getSubtotal()) ?></strong></div>
            <div><span>Discount (<?= $cart->getDiscountPercent() ?>%)</span><strong>-<?= money($cart->getDiscountAmount()) ?></strong></div>
            <div class="grand"><span>Total</span><strong><?= money($cart->getTotal()) ?></strong></div>
            <form method="post"><input type="hidden" name="action" value="clear"><button class="danger" type="submit">Clear cart</button></form>
        </div>
    </section>

    
</main>
</body>
</html>
