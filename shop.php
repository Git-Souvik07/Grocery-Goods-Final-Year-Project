<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

$category = $_GET['category'] ?? '';

if (isset($_POST['add_to_wishlist'])) {
   $pid = filter_var($_POST['pid']);
   $p_name = filter_var($_POST['p_name']);
   $p_price = filter_var($_POST['p_price']);
   $p_image = filter_var($_POST['p_image']);

   $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
   $check_wishlist_numbers->execute([$p_name, $user_id]);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if ($check_wishlist_numbers->rowCount() > 0) {
      $message[] = 'already added to wishlist!';
   } elseif ($check_cart_numbers->rowCount() > 0) {
      $message[] = 'already added to cart!';
   } else {
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'added to wishlist!';
   }
}


if (isset($_POST['add_to_cart'])) {
   $pid = filter_var($_POST['pid']);
   $p_name = filter_var($_POST['p_name']);
   $p_price = filter_var($_POST['p_price']);
   $p_image = filter_var($_POST['p_image']);
   $p_qty = filter_var($_POST['p_qty']);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if ($check_cart_numbers->rowCount() > 0) {
      $message[] = 'already added to cart!';
   } else {
      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if ($check_wishlist_numbers->rowCount() > 0) {
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'added to cart!';
   }
}


if ($category) {
   $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
   $select_products->execute([$category]);
} else {
   $select_products = $conn->prepare("SELECT * FROM `products`");
   $select_products->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Shop</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

   <link rel="stylesheet" href="css/style.css" />
</head>

<body>
   <?php include 'header.php'; ?>

   <div class="shop-container">
      <section class="p-category">
         <h1>Categories</h1>
         <a href="shop.php" class="<?= ($category == '') ? 'active' : '' ?>">All</a>
         <a href="shop.php?category=fruits" class="<?= ($category == 'fruits') ? 'active' : '' ?>">Fruits</a>
         <a href="shop.php?category=vegetables" class="<?= ($category == 'vegetables') ? 'active' : '' ?>">Vegetables</a>
         <a href="shop.php?category=spices" class="<?= ($category == 'spices') ? 'active' : '' ?>">Spices</a>
         <a href="shop.php?category=grains" class="<?= ($category == 'grains') ? 'active' : '' ?>">Grains</a>
         <a href="shop.php?category=dairy" class="<?= ($category == 'dairy') ? 'active' : '' ?>">Dairy</a>
         <a href="shop.php?category=bakery items" class="<?= ($category == 'bakery items') ? 'active' : '' ?>">Bakery Items</a>
         <a href="shop.php?category=snacks" class="<?= ($category == 'snacks') ? 'active' : '' ?>">Snacks</a>
         <a href="shop.php?category=oils" class="<?= ($category == 'oils') ? 'active' : '' ?>">Oils</a>
      </section>

      <section class="products">
         <div>
            <a href="home.php" class="back-btn">
               <i class="fas fa-arrow-left"></i> Back to Home
            </a>
            <a href="cart.php" class="back-btn">
               <i class="fas fa-arrow-right"></i> Go to Cart
            </a>
         </div>

       
         <a href="#" id="goTopBtn" class="go-top-btn">
            <i class="fas fa-arrow-up"></i>
         </a>

         <h1 class="title">
            <?= $category ? ucfirst(str_replace('_', ' ', $category)) : 'Latest Products' ?>
         </h1>

         <div class="box-container">
            <?php
            if ($select_products->rowCount() > 0) {
               while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                  <form action="" class="box" method="POST">
                     <div class="price">₹<span><?= $fetch_products['price']; ?></span>/-</div>
                     <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
                     <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="<?= htmlspecialchars($fetch_products['name']); ?>" />
                     <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
                     <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>" />
                     <input type="hidden" name="p_name" value="<?= htmlspecialchars($fetch_products['name']); ?>" />
                     <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>" />
                     <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>" />
                     <input type="number" min="1" value="1" name="p_qty" class="qty" />
                     <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist" />
                     <input type="submit" value="add to cart" class="btn" name="add_to_cart" />
                  </form>
            <?php
               }
            } else {
               echo '<p class="empty">No products found in this category!</p>';
            }
            ?>
         </div>
      </section>
   </div>

   <?php include 'footer.php'; ?>

   <script src="js/script.js"></script>

   <script>
    
      document.addEventListener("DOMContentLoaded", function () {
         const goTopBtn = document.getElementById("goTopBtn");

         window.addEventListener("scroll", function () {
            goTopBtn.style.display = window.scrollY > 20 ? "flex" : "none";
         });

         goTopBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: "smooth" });
         });
      });
   </script>
</body>

</html>
