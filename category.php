<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}


if (isset($_POST['add_to_wishlist'])) {
    $pid = filter_var($_POST['pid']);
    $p_name = filter_var($_POST['p_name']);
    $p_price = filter_var($_POST['p_price']);
    $p_image = filter_var($_POST['p_image']);

    $check_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
    $check_wishlist->execute([$p_name, $user_id]);

    if ($check_wishlist->rowCount() > 0) {
        $message[] = 'Already added to wishlist!';
    } else {
        $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
        $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
        $message[] = 'Added to wishlist!';
    }
}


if (isset($_POST['add_to_cart'])) {
    $pid = filter_var($_POST['pid']);
    $p_name = filter_var($_POST['p_name']);
    $p_price = filter_var($_POST['p_price']);
    $p_image = filter_var($_POST['p_image']);
    $p_qty = filter_var($_POST['p_qty']);

    $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart->execute([$p_name, $user_id]);

    if ($check_cart->rowCount() > 0) {
        $message[] = 'Already added to cart!';
    } else {
        $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
        $delete_wishlist->execute([$p_name, $user_id]);

        $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
        $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
        $message[] = 'Added to cart!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<section class="products">
    <h1 class="title">Products in <?= htmlspecialchars($_GET['category'] ?? 'Unknown Category'); ?></h1>

    <div class="box-container">
        <?php
        $category_name = filter_var($_GET['category'] ?? '');

        if (empty($category_name)) {
            echo '<p class="empty">Invalid category!</p>';
        } else {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
            $select_products->execute([$category_name]);

            if ($select_products->rowCount() > 0) {
                while ($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <form action="" class="box" method="POST">
                        <div class="price">₹<span><?= htmlspecialchars($product['price']); ?></span>/-</div>
                        <a href="view_page.php?pid=<?= $product['id']; ?>" class="fas fa-eye"></a>
                        <img src="uploaded_img/<?= htmlspecialchars($product['image']); ?>" alt="Product Image">
                        <div class="name"><?= htmlspecialchars($product['name']); ?></div>
                        <input type="hidden" name="pid" value="<?= $product['id']; ?>">
                        <input type="hidden" name="p_name" value="<?= htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="p_price" value="<?= htmlspecialchars($product['price']); ?>">
                        <input type="hidden" name="p_image" value="<?= htmlspecialchars($product['image']); ?>">
                        <input type="number" min="1" value="1" name="p_qty" class="qty">
                        <input type="submit" value="Add to Wishlist" class="option-btn" name="add_to_wishlist">
                        <input type="submit" value="Add to Cart" class="btn" name="add_to_cart">
                    </form>
                    <?php
                }
            } else {
                echo '<p class="empty">No products available in this category!</p>';
            }
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>
