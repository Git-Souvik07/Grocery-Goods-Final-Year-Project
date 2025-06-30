<?php
include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE verify_token = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['is_verified'] == 0) {
            $update = $conn->prepare("UPDATE `users` SET is_verified = 1 WHERE verify_token = ?");
            $update->execute([$token]);
            echo "Email verification successful! You can now <a href='login.php'>log in</a>.";
        } else {
            echo "Email is already verified. Please <a href='login.php'>log in</a>.";
        }
    } else {
        echo "Invalid token or user not found.";
    }
} else {
    echo "No token provided.";
}
?>
