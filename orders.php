<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- Font Awesome & Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <!-- Display Feedback Messages -->
   <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-info text-center">
         <?= $_SESSION['message']; unset($_SESSION['message']); ?>
      </div>
   <?php endif; ?>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
      $select_orders->execute([$user_id]);
      if ($select_orders->rowCount() > 0) {
         while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) { 
   ?>
  
   <div class="box">
      <p> Placed on: <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Name: <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Number: <span>+91 <?= $fetch_orders['number']; ?></span> </p>
      <p> Email: <span><?= $fetch_orders['email']; ?></span> </p>
      <p> Address: <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Payment Method: <span><?= $fetch_orders['method']; ?></span> </p>
      <p> Your Orders: <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Total Price: <span>₹<?= $fetch_orders['total_price']; ?>/-</span> </p>
      <p> Payment Status: 
         <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') { echo 'red'; } else { echo 'green'; } ?>">
         <?= $fetch_orders['payment_status']; ?>
         </span> 
      </p>

      <?php if ($fetch_orders['payment_status'] == 'pending') { ?>
         <form action="cancle_order.php" method="post">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <button type="submit" name="cancel_order" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel Order</button>
         </form>
      <?php } ?>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">No orders placed yet!</p>';
      }
   ?>

   </div>

</section>

<?php include 'footer.php'; ?>

<!-- Custom JS -->
<script src="js/script.js"></script>

</body>
</html>
