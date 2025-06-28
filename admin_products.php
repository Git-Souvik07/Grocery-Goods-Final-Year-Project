<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

if (isset($_POST['add_product'])) {
    try {
        $name = filter_var($_POST['name']);
        $price = filter_var($_POST['price']);
        $category = filter_var($_POST['category']);
        $details = filter_var($_POST['details']);

        $image = $_FILES['image']['name'];
        $image = filter_var($image);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploaded_img/' . $image;

        $allowed_file_types = ['image/jpg', 'image/jpeg', 'image/png'];
        $file_type = mime_content_type($image_tmp_name);

        if (!in_array($file_type, $allowed_file_types)) {
            $message[] = 'Invalid image type. Only JPG, JPEG, and PNG are allowed.';
        } elseif ($image_size > 2000000) {
            $message[] = 'Image size is too large! Please upload images under 2MB.';
        } else {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
            $select_products->execute([$name]);

            if ($select_products->rowCount() > 0) {
                $message[] = 'Product name already exists!';
            } else {
                $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image) VALUES(?,?,?,?,?)");
                $insert_products->execute([$name, $category, $details, $price, $image]);

                if ($insert_products) {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message[] = 'New product added successfully!';
                } else {
                    $message[] = 'Failed to add product!';
                }
            }
        }
    } catch (PDOException $e) {
        $message[] = 'Error: ' . $e->getMessage();
    }
}

if (isset($_GET['delete'])) {
    try {
        $delete_id = $_GET['delete'];
        $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
        $select_delete_image->execute([$delete_id]);
        $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);

        if (isset($fetch_delete_image['image']) && file_exists('uploaded_img/' . $fetch_delete_image['image'])) {
            unlink('uploaded_img/' . $fetch_delete_image['image']);
        }

        $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
        $delete_products->execute([$delete_id]);

        $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
        $delete_wishlist->execute([$delete_id]);

        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
        $delete_cart->execute([$delete_id]);

        header('location:admin_products.php');
    } catch (PDOException $e) {
        $message[] = 'Error: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'; ?>

    <section class="add-products">
        <h1 class="title">Add New Product</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="flex">
                <div class="inputBox">
                    <input type="text" name="name" class="box" required placeholder="Enter product name">
                    <select name="category" class="box" required>
                        <option value="" selected disabled>Select category</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Dairy">Dairy</option>
                        <option value="Spices">Spices</option>
                        <option value="Grains">Grains</option>
                        <option value="Snacks">Snacks</option>
                        <option value="Bakery Items">Bakery Items</option>
                        <option value="Oils">Oils</option>
                    </select>
                </div>
                <div class="inputBox">
                    <input type="number" min="0" name="price" class="box" required placeholder="Enter product price">
                    <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
                </div>
            </div>
            <textarea name="details" class="box" required placeholder="Enter product details" cols="30" rows="10"></textarea>
            <input type="submit" class="btn" value="Add Product" name="add_product">
        </form>
    </section>

    <section class="show-products">
        <h1 class="title">Products Added</h1>
        <div class="box-container">
            <?php
            $show_products = $conn->prepare("SELECT * FROM `products`");
            $show_products->execute();

            if ($show_products->rowCount() > 0) {
                while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="box">
                        <div class="price">₹<?= $fetch_products['price']; ?>/-</div>
                        <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                        <div class="name"><?= $fetch_products['name']; ?></div>
                        <div class="cat"><?= $fetch_products['category']; ?></div>
                        <div class="details"><?= $fetch_products['details']; ?></div>
                        <div class="flex-btn">
                            <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
                            <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">No products added yet!</p>';
            }
            ?>
        </div>
    </section>

    <script src="js/script.js"></script>
</body>

</html>
