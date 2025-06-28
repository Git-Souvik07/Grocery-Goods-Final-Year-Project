<?php

@include 'config.php';

session_start();

if (isset($_POST['cancel_order'])) {
    $order_id = filter_var($_POST['order_id']);

    $delete_query = $conn->prepare("DELETE FROM `orders` WHERE `id` = ? AND `user_id` = ?");
    $delete_query->execute([$order_id, $_SESSION['user_id']]);


    if ($delete_query->rowCount() > 0) {
        $_SESSION['message'] = "Order cancel and delete successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete the order. Please try again.";
    }


    header('location:orders.php');
    exit;
}

?>
