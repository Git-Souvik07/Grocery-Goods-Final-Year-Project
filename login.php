<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email) ;
   $pass = md5($_POST['pass']);
   $pass = filter_var($pass);

   $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $rowCount = $stmt->rowCount();  

   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if($rowCount > 0){

      if($row['user_type'] == 'admin'){
         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');
      }elseif($row['user_type'] == 'user'){
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
      }else{
         $message[] = 'no user found!';
      }

   }else{
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

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
         margin: 10px;
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

   <form action="" method="POST">
      <h3>Login now</h3>
      <input type="email" name="email" class="box" placeholder="enter your email" required>

      <div class="password-field">
         <input type="password" name="pass" id="pass" class="box" placeholder="enter your password" required>
         <i class="fas fa-eye toggle-pass" onclick="togglePassword('pass', this)"></i>
      </div>

      <input type="submit" value="login now" class="btn" name="submit">
      <p>don't have an account? <a href="register.php">Register now</a></p>
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
