<?php

@include 'config.php';

function format_rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:index.php');
}

if(isset($_POST['add_bundling'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = mysqli_real_escape_string($conn, $_POST['price']);
   $description = mysqli_real_escape_string($conn, $_POST['description']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_name = mysqli_query($conn, "SELECT bundling_name FROM `bundling_menu` WHERE bundling_name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_name) > 0){
      $message[] = 'nama bundling sudah ada!';
   }else{
      $insert_bundling = mysqli_query($conn, "INSERT INTO `bundling_menu`(bundling_name, description, price, image) VALUES('$name', '$description', '$price', '$image')") or die('query failed');

      if($insert_bundling){
         if($image_size > 2000000){
            $message[] = 'gambar terlalu besar!';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'bundling berhasil ditambahkan!';
         }
      }
   }

}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $select_image = mysqli_query($conn, "SELECT image FROM `bundling_menu` WHERE bundling_id = '$delete_id'") or die('query failed');
   $fetch_image = mysqli_fetch_assoc($select_image);
   unlink('uploaded_img/'.$fetch_image['image']);
   mysqli_query($conn, "DELETE FROM `bundling_menu` WHERE bundling_id = '$delete_id'") or die('query failed');
   header('location:admin_bundling.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bundling</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="add-products">

   <form action="" method="POST" enctype="multipart/form-data">
      <h3>add new bundling</h3>
      <input type="text" class="box" required placeholder="enter bundling name" name="name">
      <input type="number" min="0" class="box" required placeholder="enter bundling price" name="price">
      <textarea name="description" class="box" required placeholder="enter bundling description" cols="30" rows="10"></textarea>
      <input type="file" accept="image/jpg, image/jpeg, image/png" required class="box" name="image">
      <input type="submit" value="add bundling" name="add_bundling" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_bundling = mysqli_query($conn, "SELECT * FROM `bundling_menu`") or die('query failed');
         if(mysqli_num_rows($select_bundling) > 0){
            while($fetch = mysqli_fetch_assoc($select_bundling)){
      ?>
      <div class="box">
         <div class="price"><?php echo format_rupiah($fetch['price']); ?>,-</div>
         <img class="image" src="uploaded_img/<?php echo $fetch['image']; ?>" alt="">
         <div class="name"><?php echo $fetch['name']; ?></div>
         <div class="details"><?php echo $fetch['description']; ?></div> 
         <a href="admin_bundling.php?delete=<?php echo $fetch['bundling_id']; ?>" class="delete-btn" onclick="return confirm('delete this bundling?');">delete</a>

      </div>
      <?php
         }
      }else{
         echo '<p class="empty">tidak ada bundling ditambahkan!</p>';
      }
      ?>
   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>
