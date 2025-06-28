<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}


function sanitizeInput($input) {
    return filter_var($input);
}


if (isset($_POST['add_to_wishlist'])) {
    $pid = sanitizeInput($_POST['pid']);
    $p_name = sanitizeInput($_POST['p_name']);
    $p_price = sanitizeInput($_POST['p_price']);
    $p_image = sanitizeInput($_POST['p_image']);

    try {
        $checkWishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $checkWishlist->execute([$p_name, $user_id]);

        $checkCart = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $checkCart->execute([$p_name, $user_id]);

        if ($checkWishlist->rowCount() > 0) {
            $message[] = 'Already added to wishlist!';
        } elseif ($checkCart->rowCount() > 0) {
            $message[] = 'Already added to cart!';
        } else {
            $insertWishlist = $conn->prepare("INSERT INTO `wishlist` (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)");
            $insertWishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
            $message[] = 'Added to wishlist!';
        }
    } catch (PDOException $e) {
        $message[] = 'Error: ' . htmlspecialchars($e->getMessage());
    }
}


if (isset($_POST['add_to_cart'])) {
    $pid = sanitizeInput($_POST['pid']);
    $p_name = sanitizeInput($_POST['p_name']);
    $p_price = sanitizeInput($_POST['p_price']);
    $p_image = sanitizeInput($_POST['p_image']);
    $p_qty = sanitizeInput($_POST['p_qty']);

    try {
        $checkCart = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $checkCart->execute([$p_name, $user_id]);

        if ($checkCart->rowCount() > 0) {
            $message[] = 'Already added to cart!';
        } else {
            $checkWishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
            $checkWishlist->execute([$p_name, $user_id]);

            if ($checkWishlist->rowCount() > 0) {
                $deleteWishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
                $deleteWishlist->execute([$p_name, $user_id]);
            }

            $insertCart = $conn->prepare("INSERT INTO `cart` (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
            $insertCart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
            $message[] = 'Added to cart!';
        }
    } catch (PDOException $e) {
        $message[] = 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick View</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>

<section class="quick-view">
    <h1 class="title">Quick View</h1>

   
    <button onclick="goBack()" class="back-btn"><i class="fas fa-arrow-left"></i> Back</button>

    <?php
 
    $pid = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT);
    if ($pid) {
        try {
            $selectProduct = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
            $selectProduct->execute([$pid]);

            if ($selectProduct->rowCount() > 0) {
                while ($product = $selectProduct->fetch(PDO::FETCH_ASSOC)) {
                    
                    $details = preg_split('/(?<=\.)\s*(?!\d+\/\d+)/', $product['details']);
                    ?>
                    <form action="" class="box" method="POST">
                        <div class="price">₹<span><?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?></span>/-</div>
                        <img src="uploaded_img/<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image">
                        <div class="name"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <ul class="details">
                            <?php foreach ($details as $point): ?>
                                <?php if (!empty(trim($point))): ?>
                                    <li><?= html_entity_decode(htmlspecialchars(trim($point), ENT_QUOTES, 'UTF-8')); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <input type="hidden" name="pid" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="p_name" value="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="p_price" value="<?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="p_image" value="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="number" min="1" value="1" name="p_qty" class="qty" required>
                        <input type="submit" value="Add to Wishlist" class="option-btn" name="add_to_wishlist">
                        <input type="submit" value="Add to Cart" class="btn" name="add_to_cart">
                    </form>
                    <?php
                }
            } else {
                echo '<p class="empty">No products found!</p>';
            }
        } catch (PDOException $e) {
            echo '<p class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    } else {
        echo '<p class="error">Invalid Product ID!</p>';
    }
    ?>
</section>

<?php include 'footer.php'; ?>

<script>
    function goBack() {
        const referrer = document.referrer;
        if (referrer.includes('search')) {
            window.location.href = "search_page.php"; 
        }
        else if (referrer.includes('shop')) {
            window.location.href = "shop.php";
         } 
         else if (referrer.includes ('cart')){
            window.location.href ="cart.php";
         }
         else if(referrer.includes('wishlist')){
            window.location.href="wishlist.php";
         }

         else {
            window.location.href = "home.php"; 
        }
    }
</script>

<script src="js/script.js"></script>
</body>
</html>
