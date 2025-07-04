<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
};

if (isset($_POST['add_to_wishlist'])) {

   $pid = $_POST['pid'];
   $pid = filter_var($pid);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image);

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

   $pid = $_POST['pid'];
   $pid = filter_var($pid);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image);
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty);

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="home-bg">

      <section class="home">

         <div class="content">
            <span>Don't panic, go organice</span>
            <h3>Fresh Groceries Delivered to Your Door</h3>
            <p>Shop for fresh vegetables, fruits, spices, and authentic Indian groceries. Quality products at affordable prices, delivered right to your doorstep.</p>
            <a href="about.php" class="btn">about us</a>
         </div>

      </section>

   </div>

   <section class="home-category">

      <h1 class="title">shop by category</h1>

      <div class="box-container">

         <div class="box">
            <img src="images/cat-1.png" alt="">
            <h3>Fruits</h3>
            <p>Explore a vibrant selection of fresh, juicy, and seasonal fruits.</p>
            <a href="category.php?category=fruits" class="btn">fruits</a>
         </div>

         <div class="box">
            <img src="images/cat-3.png" alt="">
            <h3>Vegitables</h3>
            <p>Pick from a variety of fresh, organic vegetables for healthy meals.</p>
            <a href="category.php?category=vegetables" class="btn">vegetables</a>
         </div>

         <div class="box">
            <img src="images/spices.png" alt="">
            <h3>Spices</h3>
            <p>Discover aromatic spices to elevate the flavors of your cooking.</p>
            <a href="category.php?category=spices" class="btn">spices</a>
         </div>

         <div class="box">
            <img src="images/grains.webp" alt="">
            <h3>Grains</h3>
            <p>Choose premium-quality grains to craft nutritious, hearty meals.</p>
            <a href="category.php?category=grains" class="btn">grains</a>
         </div>

         <div class="box">
            <img src="images/dairy&eggs.avif" alt="">
            <h3>Dairy</h3>
            <p>Indulge in farm-fresh dairy and eggs for a wholesome experience.</p>
            <a href="category.php?category=dairy" class="btn">dairy</a>
         </div>

         <div class="box">
            <img src="images/bakery.webp" alt="">
            <h3>Bakery Items</h3>
            <p>Treat yourself to freshly baked delights for every occasion.</p>
            <a href="category.php?category=bakery items" class="btn">bakery items</a>
         </div>

         <div class="box">
            <img src="images/snaks.png" alt="">
            <h3>Snacks</h3>
            <p>Savor crispy and delicious snacks to satisfy your cravings.</p>
            <a href="category.php?category=snacks" class="btn">snacks</a>
         </div>

         <div class="box">
            <img src="images/oil.webp" alt="">
            <h3>Oils</h3>
            <p>Shop for pure, healthy oils to enhance your cooking experience.</p>
            <a href="category.php?category=oils" class="btn">oils</a>
         </div>


      </div>

   </section>

   <section class="products">

      <h1 class="title">latest products</h1>

      <div class="box-container">

         <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 8");
         $select_products->execute();
         if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <form action="" class="box" method="POST">
                  <div class="price">₹<span><?= $fetch_products['price']; ?></span>/-</div>
                  <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
                  <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                  <div class="name"><?= $fetch_products['name']; ?></div>
                  <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                  <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
                  <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
                  <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
                  <input type="number" min="1" value="1" name="p_qty" class="qty">
                  <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
                  <input type="submit" value="add to cart" class="btn" name="add_to_cart">
               </form>
         <?php
            }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         }
         ?>

      </div>

   </section>
   <?php include 'footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>