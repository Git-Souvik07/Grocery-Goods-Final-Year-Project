<?php
@include 'config.php';

session_start();

if (isset($_POST['submit'])) {
    // Sanitize inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = $_POST['pass'];

    // Prepare query to find user
    $sql = "SELECT * FROM `users` WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    // Check if user exists
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if (password_verify($pass, $row['password'])) {
            // Check if email is verified
            if ($row['is_verified'] == 1) {
                // Set session based on user type
                if ($row['user_type'] == 'admin') {
                    $_SESSION['admin_id'] = $row['id'];
                    header('location:admin_page.php');
                    exit();
                } elseif ($row['user_type'] == 'user') {
                    $_SESSION['user_id'] = $row['id'];
                    header('location:home.php');
                    exit();
                } else {
                    $message[] = 'No user found with this role!';
                }
            } else {
                $message[] = 'Please verify your email address to complete your registration. A confirmation email has been sent to your inbox. Once verified, you will be able to log in successfully';
            }
        } else {
            $message[] = 'Incorrect password!';
        }
    } else {
        $message[] = 'Incorrect email or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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
if (isset($message)) {
    foreach ($message as $msg) {
        echo "<div class='message'><span>$msg</span><i class='fas fa-times' onclick='this.parentElement.remove();'></i></div>";
    }
}
?>
<section class="form-container">
    <form action="" method="POST">
        <h3>Login Now</h3>
        <input type="email" name="email" class="box" placeholder="Enter your email" required>
        <div class="password-field">
            <input type="password" name="pass" id="pass" class="box" placeholder="Enter your password" required>
            <i class="fas fa-eye toggle-pass" onclick="togglePassword('pass', this)"></i>
        </div>
        <input type="submit" value="Login Now" class="btn" name="submit">
        <p>Don't have an account? <a href="register.php">Register now</a></p>
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
