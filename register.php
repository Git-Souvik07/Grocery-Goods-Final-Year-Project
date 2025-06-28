<?php

include 'config.php';

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name);
   $email = $_POST['email'];
   $email = filter_var($email);
   $pass = md5($_POST['pass']);
   $pass = filter_var($pass);
   $cpass = md5($_POST['cpass']);
   $cpass = filter_var($cpass);

   $image = $_FILES['image']['name'];
   $image = filter_var($image);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select->execute([$email]);

   if($select->rowCount() > 0){
      $message[] = 'user email already exists!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image) VALUES(?,?,?,?)");
         $insert->execute([$name, $email, $pass, $image]);

         if($insert){
            if($image_size > 2000000){
               $message[] = 'image size is too large!';
            }else{
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'registered successfully!';
               header('location:login.php');
            }
         }

      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

  
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

   
   <link rel="stylesheet" href="css/components.css">

   <style>
      .password-field {
         position: relative;
      }

      .password-field .toggle-pass {
         position: absolute;
         top: 50%;
         right: 15px;
         transform: translateY(-50%);
         cursor: pointer;
         color: #666;
      }

      .message {
         background-color: #f2f2f2;
         padding: 10px;
         margin: 10px 0;
         border-left: 5px solid #ff6600;
         position: relative;
      }

      .message i {
         position: absolute;
         top: 10px;
         right: 10px;
         cursor: pointer;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<section class="form-container">

   <form action="" enctype="multipart/form-data" method="POST">
      <h3>Register now</h3>
      <input type="text" name="name" class="box" placeholder="enter your name" required>
      <input type="email" name="email" class="box" placeholder="enter your email" required>

      <div class="password-field">
         <input type="password" name="pass" id="pass" class="box" placeholder="enter your password" required>
         <i class="fas fa-eye toggle-pass" onclick="togglePassword('pass', this)"></i>
      </div>

      <div class="password-field">
         <input type="password" name="cpass" id="cpass" class="box" placeholder="confirm your password" required>
         <i class="fas fa-eye toggle-pass" onclick="togglePassword('cpass', this)"></i>
      </div>

      <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="register now" class="btn" name="submit">
      <p>already have an account? <a href="login.php">Login now</a></p>
   </form>

</section>

<script>
function togglePassword(fieldId, icon) {
   const field = document.getElementById(fieldId);
   if (field.type === "password") {
      field.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
   } else {
      field.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
   }
}
</script>

</body>
</html>
