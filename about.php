<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">

   <div class="row">

      <div class="box">
         <img src="images/about-img-1.png" alt="">
         <h3>why choose us?</h3>
      
<p><strong>1. Freshness Guaranteed:</strong> We source the freshest produce and high-quality goods daily to ensure you get the best for your family.</p>
<p><strong>2. Wide Variety of Products:</strong> From everyday essentials to exotic ingredients, our shelves are stocked with everything you need.</p>
<p><strong>3. Affordable Prices:</strong> Enjoy competitive pricing with frequent discounts and deals to make shopping cost-effective.</p>
<p><strong>4. Convenient Shopping:</strong> Shop online or in-store with ease, and take advantage of home delivery or curbside pickup services.</p>
<p><strong>5. Sustainability Focus:</strong> We prioritize eco-friendly packaging and support local farmers to promote sustainability.</p>
<p><strong>6. Exceptional Customer Service:</strong> Our friendly team is always ready to assist you, ensuring a seamless shopping experience.</p>
<p><strong>7. Loyalty Rewards:</strong> Earn points with every purchase and redeem them for exciting offers and exclusive benefits.</p>
<p>Choose us to simplify your grocery shopping and experience quality like never before!</p>

         <a href="contact.php" class="btn">contact us</a>
      </div>

      <div class="box">
         <img src="images/about-img-2.png" alt="">
         <h3>what we provide?</h3>
         <p><p><strong>1. Fresh Produce:</strong> Handpicked fruits and vegetables to ensure quality and freshness.</p>
<p><strong>2. Grains and Pulses:</strong> A wide variety of rice, wheat, lentils, and beans to stock your pantry.</p>
<p><strong>3. Cooking Essentials:</strong> Oils, spices, and condiments for delicious meals.</p>
<p><strong>4. Packaged Goods:</strong> Snacks, ready-to-eat meals, and canned goods for convenience.</p>
<p><strong>5. Baking Supplies:</strong> Flour, sugar, and other baking essentials for your culinary adventures.</p>
<p><strong>6. Organic Options:</strong> Certified organic grains, spices, and staples for a healthier choice.</p>
<p>We aim to provide high-quality grocery goods that meet all your cooking and household needs!</p></p>
         <a href="shop.php" class="btn">our shop</a>
      </div>

   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>