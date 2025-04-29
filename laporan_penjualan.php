<?php

@include 'config.php';

function format_rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:index.php');
   exit;
}

// Total semua pemasukan
$total_sales = 0;
$sales_query = mysqli_query($conn, "SELECT o.total_price FROM sales_report sr JOIN orders o ON sr.order_id = o.id") or die('Query total sales gagal');
while($sales = mysqli_fetch_assoc($sales_query)){
    $total_sales += $sales['total_price'];
}

// Data semua laporan
$report_query = mysqli_query($conn, "SELECT sr.sales_date, o.total_products, o.total_price 
                                     FROM sales_report sr 
                                     JOIN orders o ON sr.order_id = o.id 
                                     ORDER BY sr.sales_date DESC") or die('Query laporan gagal');

?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Laporan Penjualan</title>

   <!-- font awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php @include 'admin_header.php'; ?>

<section class="dashboard">

   <h1 class="title">Laporan Penjualan</h1>

   <div class="box-container">

      <div class="box">
         <h3><?php echo format_rupiah($total_sales); ?>,-</h3>
         <p>Total Penjualan</p>
      </div>

      <div class="box">
         <?php
            $total_orders = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM sales_report"));
         ?>
         <h3><?php echo $total_orders; ?></h3>
         <p>Jumlah Pesanan Tercatat</p>
      </div>

   </div>


   <div class="box-container">
      <?php if(mysqli_num_rows($report_query) > 0): ?>
         <?php while($row = mysqli_fetch_assoc($report_query)): ?>
            <div class="box report-box">
               <p><strong>Tanggal:</strong> <?= htmlspecialchars($row['sales_date']) ?></p>
               <p><strong>Produk:</strong> <?= htmlspecialchars($row['total_products']) ?></p>
               <p><strong>Total Harga:</strong> <?= format_rupiah($row['total_price']) ?></p>
            </div>
         <?php endwhile; ?>
      <?php else: ?>
         <div class="box">
            <p>Belum ada laporan penjualan.</p>
         </div>
      <?php endif; ?>
   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>
