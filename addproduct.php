<?php
@include 'config.php';

if (isset($_POST['add_product'])) {
    $name = filter_var($_POST['name']);
    $category = filter_var($_POST['category']);
    $details = filter_var($_POST['details']);
    $price = filter_var($_POST['price']);

    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    if (!file_exists('uploaded_img')) {
        mkdir('uploaded_img', 0777, true);
    }

    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $insert_product = $conn->prepare("INSERT INTO products (name, category, details, price, image) VALUES (?, ?, ?, ?, ?)");
        $insert_product->execute([$name, $category, $details, $price, $image]);

        if (move_uploaded_file($image_tmp_name, $image_folder)) {
            echo "<p>Product added successfully!</p>";
        } else {
            echo "<p>Failed to upload image!</p>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <script>
      
        function updateCharCount() {
            const textArea = document.getElementById('details');
            const charCount = document.getElementById('charCount');
            charCount.textContent = `${textArea.value.length}/500 characters`;
        }
    </script>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" required placeholder="Enter product name"><br>
        
        <select name="category" required>
            <option value="" disabled selected>Select category</option>
            <option value="Fruits">Fruits</option>
            <option value="Vegetables">Vegetables</option>
            
        </select><br>
        
        <textarea 
            id="details" 
            name="details" 
            required 
            placeholder="Enter product details (maximum 500 characters)" 
            oninput="updateCharCount()" 
            maxlength="500" 
            style="width: 100%; height: 100px;"></textarea>
        <div id="charCount" style="font-size: 12px; color: gray;">0/500 characters</div><br>
        
        <input type="number" name="price" required placeholder="Enter price"><br>
        <input type="file" name="image" required accept="image/*"><br>
        <input type="submit" name="add_product" value="Add Product">
    </form>
</body>
</html>
