<?php  
  include "db.php";
  date_default_timezone_set('ASIA/KOLKATA');
  $id=$_GET['delete'];
  $qry=mysqli_query($conn,"UPDATE product SET is_deleted=1 WHERE product_id='$id'");
  if($qry){
  	echo "<script>
  		window.location.href='view_products.php';
  	</script>";
  }else{
  	echo "<script>alert('ERROR!')</script>";
  }
?>