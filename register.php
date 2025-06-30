<?php
include 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoload for PHPMailer
require 'vendor/autoload.php';

if (isset($_POST['submit'])) {

    // Sanitize and validate inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
    }
    $pass = password_hash($_POST['pass'], PASSWORD_BCRYPT);
    $cpass = $_POST['cpass'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    $image_type = mime_content_type($image_tmp_name);

    // Validate image type and size
    if (!in_array($image_type, ['image/jpeg', 'image/png'])) {
        $message[] = 'Invalid image format! Only JPG and PNG are allowed.';
    } elseif ($_FILES['image']['size'] > 2000000) {
        $message[] = 'Image size is too large!';
    } else {
        move_uploaded_file($image_tmp_name, $image_folder);
    }

    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select->execute([$email]);

    if ($select->rowCount() > 0) {
        $message[] = 'User email already exists!';
    } else {
        if (!password_verify($cpass, $pass)) {
            $message[] = 'Confirm password does not match!';
        } else {
            $token = bin2hex(random_bytes(16));

            $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image, verify_token, is_verified) VALUES(?,?,?,?,?,?)");
            $insert->execute([$name, $email, $pass, $image, $token, 0]);

            if ($insert) {
                if (sendEmail($email, $name, $token)) {
                    $message[] = 'Registered successfully! Please check your email for verification.';
                    header('location:login.php');
                    exit();
                } else {
                    $message[] = 'Registration successful, but failed to send verification email.';
                }
            } else {
                $message[] = 'Registration failed!';
            }
        }
    }
}

// Function to send verification email
function sendEmail($to_email, $to_name, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'souvikdashpl@gmail.com'; // Replace with your Gmail address
        $mail->Password = 'mwcn iqwf axyg hqbj'; // Replace with your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email settings
        $mail->setFrom('souvikdashpl@gmail.com', 'Grocery-Goods-Final-Year-Project');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body = "
<html>
<head>
   <style>
      body {
         font-family: Arial, sans-serif;
         background-color: #f9f9f9;
         color: #333;
         line-height: 1.6;
      }
      .container {
         max-width: 600px;
         margin: 20px auto;
         background: #fff;
         padding: 20px;
         border-radius: 8px;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }
      .btn {
         display: inline-block;
         margin-top: 20px;
         padding: 10px 20px;
         background-color: #28a745;
         color: #fff;
         text-decoration: none;
         border-radius: 5px;
         font-size: 16px;
      }
      .btn:hover {
         background-color: #218838;
      }
   </style>
</head>
<body>
   <div class='container'>
      <h2>Hello, $to_name!</h2>
      <p>Thank you for registering on our platform. Please click the button below to verify your email address and complete your registration:</p>
      <p><a href='http://localhost/Grocery-Goods-Final-Year-Project/verify.php?token=$token' class='btn'>Verify Email</a></p>
      <p>Best regards,<br>Grocery Goods Team</p>
   </div>
</body>
</html>
";

        $mail->AltBody = "Hello, $to_name!\nPlease click the link below to verify your email address:\nhttp://localhost/Grocery-Goods-Final-Year-Project/verify.php?token=$token";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

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
if (isset($message)) {
    foreach ($message as $msg) {
        echo "<div class='message'><span>$msg</span><i class='fas fa-times' onclick='this.parentElement.remove();'></i></div>";
    }
}
?>

<section class="form-container">

    <form action="" enctype="multipart/form-data" method="POST">
        <h3>Register Now</h3>
        <input type="text" name="name" class="box" placeholder="Enter your name" required>
        <input type="email" name="email" class="box" placeholder="Enter your email" required>

        <div class="password-field">
            <input type="password" name="pass" id="pass" class="box" placeholder="Enter your password" required>
            <i class="fas fa-eye toggle-pass" onclick="togglePassword('pass', this)"></i>
        </div>

        <div class="password-field">
            <input type="password" name="cpass" id="cpass" class="box" placeholder="Confirm your password" required>
            <i class="fas fa-eye toggle-pass" onclick="togglePassword('cpass', this)"></i>
        </div>

        <input type="file" name="image" class="box" required accept="image/jpeg, image/png">
        <input type="submit" name="submit" value="Register Now" class="btn">
        <p>Already have an account? <a href="login.php">Login now</a></p>
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
