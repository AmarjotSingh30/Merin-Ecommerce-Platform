<?php 
    include 'db.php';
    session_start();
    $id=$_GET['order_id'];
    $sum=$total=0;
    $per=0;
$qry = mysqli_query($conn, "
    SELECT 
        o.order_id,
        o.customer_name,
        o.email,
        o.phone,
        o.address,
        o.zip_code,
        o.order_amount,
        o.payment_mode,
        o.txn_id,
        o.order_status,
        o.payment_status,
        o.created_at,
        o.updated_at,
        o.is_coupon,
        od.product_amount,
        od.quantity,
        p.name AS product_name,
        p.product_image_1 AS product_image,
        p.product_title,
        p.product_type,
        p.price
    FROM orders AS o
    LEFT JOIN order_details AS od
        ON o.order_id = od.order_id
    LEFT JOIN product AS p
        ON od.product_id = p.product_id
    WHERE o.order_id = '$id'
");
    $row=mysqli_fetch_assoc($qry);
    if (!$row) {
    die("No data found for Order ID: $id");
    }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Track Orders</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <!-- fontawesome -->
    <!-- <link rel="stylesheet" href="assets/css/all.min.css"> -->
    <!-- bootstrap -->
    <!-- <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css"> -->
    <!-- owl carousel -->
    <!-- <link rel="stylesheet" href="assets/css/owl.carousel.css"> -->
    <!-- magnific popup -->
    <!-- <link rel="stylesheet" href="assets/css/magnific-popup.css"> -->
    <!-- animate css -->
    <!-- <link rel="stylesheet" href="assets/css/animate.css"> -->
    <!-- mean menu css -->
    <!-- <link rel="stylesheet" href="assets/css/meanmenu.min.css"> -->
    <!-- main style -->
    <!-- <link rel="stylesheet" href="assets/css/main.css"> -->
    <!-- responsive -->
    <!-- <link rel="stylesheet" href="assets/css/responsive.css">-->
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
	<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
<style type="text/css">
	@import url('https://fonts.googleapis.com/css?family=Open+Sans&display=swap');
body{
    background-color: #eeeeee;
    font-family: 'Open Sans',serif
}
.container{
    margin-top:50px;
    margin-bottom: 50px;
}
.card{
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.10rem;
}
.card-header:first-child{
    border-radius: calc(0.37rem - 1px) calc(0.37rem - 1px) 0 0;
}
.card-header{
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: #fff;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}
.track{
    position: relative;
    background-color: #ddd;
    height: 7px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 60px;
    margin-top: 50px;
}
.track_2{
    position: relative;
    background-color: #ddd;
    height: 7px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 60px;
    margin-top: 50px;
}
.track .step{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}
.track .step.active:before{
    /* background: #ddddd; */
    background:yellow;
}
.track .step::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 100%;
    left: 0;
    top: 18px;
}
.track .step.active .icon{
    background: #dddddd;
    color: black;
}
.track .icon{
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    position: relative;
    border-radius: 100%;
    background: #ddd;
}
.track .step.active .text{
    font-weight: 400;
    color: #000;
}
.track .text{
    display: block;
    margin-top: 7px;
}

.track_3 .step.active2{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}

.track_3 .step.active2::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 50%;
    left: 0;
    top: 18px;
}
.track_3{
    position: relative;
    background-color: #ddd;
    height: 7px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 60px;
    margin-top: 50px;
}
.track_3 .step{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}
.track_3 .step.active:before{
    background: orange;
}
.track_3 .step::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 100%;
    left: 0;
    top: 18px;
}
.track_3 .step.active .icon{
    background: orange;
    color: white;
}
.track_3 .step.active2 .icon{
    background: orange;
    color: white;
}
.track_3 .icon{
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    position: relative;
    border-radius: 100%;
    background: #ddd;
}
.track_3 .step.active .text{
    font-weight: 400;
    color: #000;
}
.track_3 .text{
    display: block;
    margin-top: 7px;
}

.track_5 {
    position: relative;
    background-color: #ddd;
    height: 7px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 60px;
    margin-top: 50px;
}
.track_5 .step{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}
.track_5 .step.active:before{
    background: orange;
}
.track_5 .step::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 100%;
    left: 0;
    top: 18px;
}
.track_5 .step.active .icon{
    background: orange;
    color: white;
}
.track_5 .step.active2 .icon{
    background: orange;
    color: white;
}
.track_5 .icon{
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    position: relative;
    border-radius: 100%;
    background: #ddd;
}
.track_5 .step.active .text{
    font-weight: 400;
    color: #000;
}
.track_5 .text{
    display: block;
    margin-top: 7px;
}

.track_4 .step.active2{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}
.track_4 .step.active2:before{
    background: orange;
}
.track_4 .step.active2::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 50%;
    left: 0;
    top: 18px;
}

.track_4{
    position: relative;
    background-color: #ddd;
    height: 7px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    margin-bottom: 60px;
    margin-top: 50px;
}
.track_4 .step{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}
.track_4 .step.active:before{
    background: orange;
}
.track_4 .step::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 100%;
    left: 0;
    top: 18px;
}
.track_4 .step.active .icon{
    background: orange;
    color: white;
}
.track_4 .step.active2 .icon{
    background: orange;
    color: white;
}
.track_4 .icon{
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    position: relative;
    border-radius: 100%;
    background: #ddd;
}
.track_4 .step.active .text{
    font-weight: 400;
    color: #000;
}
.track_4 .text{
    display: block;
    margin-top: 7px;
}

.track_2 .step{
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    width: 2vh;
    margin-top: -18px;
    text-align: center;
    position: relative;
}
.track_3 .step.active2:before{
    background: orange;
}
.track_2 .step.active:before{
    background: orange;
}
.track_2 .step::before{
    height: 7px;
    position: absolute;
    content: "";
    width: 50%;
    left: 0;
    top: 18px;
}
.track_2 .step.active .icon{
    background: orange;
    color: white;
}
.track_2 .icon{
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    position: relative;
    border-radius: 100%;
    background: #ddd;
}
.track_2 .step.active .text{
    font-weight: 400;
    color: #000;
}
.track_2 .text{
    display: block;
    margin-top: 7px;
}
.itemside{
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    width: 100%;
}
.itemside .aside{
    position: relative;
    -ms-flex-negative: 0;
    flex-shrink: 0;
}
.img-sm{
    width: 80px;
    height: 80px;
    padding: 7px;
}
ul.row, ul.row-sm{
    list-style: none;
    padding: 0;
}
.itemside .info{
    padding-left: 15px;
    padding-right: 7px;
    }
.itemside .title{
    display: block;
    margin-bottom: 5px;
    color: #212529;
}
p{
    margin-top: 0;
    margin-bottom: 1rem;
}
.btn-warning{
    color: #ffffff;
    background-color: #ee5435;
    border-color: #ee5435;
    border-radius: 1px;
}
.btn-warning:hover{
    color: #ffffff;
    background-color: #ff2b00;
    border-color: #ff2b00;
    border-radius: 1px;
}
.main_box{
    background:radial-gradient(#62879b,#374b56);    
    height: 65vh;
}
.h4{
    color: whitesmoke;
    font-family: ebrima;
}
.h6{
    font-weight: 100;
    color: lightgray;
}
.continue{
    width: 100%;
    height: 5vh;
/*  border:1px solid red;*/
}
.image{
    position: absolute;
    left:-40vh;
}
.continue p{
    color: #badcee;
    padding: 2vh;
}
.details{
    width: 100%;
    height: 40vh;
    margin: auto;
    margin-top: 5vh;
    margin-bottom: -10vh;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.shipping{
    width: 33.33%;
    height: 100%;
    border: 1px solid #4e6b7b;
}
.shipping{
    width: 33.33%;
    height: 100%;
    border: 1px solid #4e6b7b;
}
.shipping{
    width: 33.33%;
    height: 100%;
    border: 1px solid #efefef;
}
.shipping i{
    color: #0091e5;
    font-size: 30px;
    padding: 5vh;
}
.shipping h4{
    padding-left: 5vh;
    margin-top: -3vh;
}
.shipping p{
    padding-left: 5vh;
    padding-right: 5vh;
}
.order_box{
    width: 100%;
    height: auto;
    margin: auto;
    margin-top: 15vh;
/*  border: 1px solid red;*/
    display: flex;
/*  align-items: center;*/
    justify-content: space-between;
}
.side_1{
    width: 68%;
    height: auto;
/*  border: 1px solid red;*/
}
.side_2{
    width: 30%;
    height: 50vh;
    border: 2px solid #e5e5e5;
}
.side_1 h3{
    padding-top: 3vh;
    padding-left: 3vh;
}
.side_2 h3{
    padding-top: 4vh;
    padding-left: 4vh;
}
.side_2 hr{
    width: 90%;
    margin: auto;
}
.view_order_info{
    width: 95%;
    height: auto;
    margin: auto;
    margin-top: 3vh;
/*  border: 1px solid lime;*/
    display: flex;
    justify-content: space-between;
}
.order_image{
    width: 10%;
    height: 10vh;
/*  border: 1px solid blue;*/
}
.order_info{
    width: 77%;
    height: auto;
/*  border: 1px solid pink;*/
}
.order_info h4{
    font-weight: 100;
}
.order_price{
    width: 15%;
    height: auto;
/*  border: 1px solid cyan;*/
}
.order_price h3{
    padding:0vh;
    color: #858585;
    font-weight: 100;
}
.subtotal{
    width: 90%;
    height: 4vh;
    margin: auto;
    display: flex;
    justify-content: space-between;
}
.subtotal p{
    color: #858585;
    font-size: 17px;
}
.sub{
    width: auto;
    height: auto;
}
.amount{
    width: auto;
    height: auto;
}
</style>
</head>
<body>
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
	<div class="container">
    <article class="card">
        <header class="card-header">View Orders:</header>
        <div class="card-body">
            <h6>Order ID: <?php echo $row['order_id'] ?></h6>
            <article class="card">
                <div class="card-body row">
                    <div class="col"> <strong>Estimated Delivery time:</strong> <br>29 nov 2019 </div>
                    <div class="col"> <strong>Shipping BY:</strong> <br> BLUEDART, | <i class="fa fa-phone"></i> +1598675986 </div>
                    <div class="col"> <strong>Status:</strong> <br> <?php echo $row['order_status'] ?> </div>
                    <div class="col"> <strong>Tracking #:</strong> <br> <b>TRK</b>00<?php echo rand(100000000,999999999) ?> </div>
                </div>
            </article>
            <?php  
            if($row['order_status'] == 'Pending'){
            // pending Order
            echo "<div class='track'>
                <div class='step'> <span class='icon'> <i class='fa fa-check'></i> </span> <span class='text'>Order confirmed</span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-user'></i> </span> <span class='text'> Picked by courier</span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-truck'></i> </span> <span class='text'> On the way </span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-box'></i> </span> <span class='text'>Ready for pickup</span> </div>
            </div>";
            }else if($row['order_status'] == 'Confirm'){
            // confirm Order
            echo "<div class='track_2'>
                <div class='step active' > <span class='icon'> <i class='fa fa-check'></i> </span> <span class='text'>Order confirmed</span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-user'></i> </span> <span class='text'> Picked by courier</span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-truck'></i> </span> <span class='text'> On the way </span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-box'></i> </span> <span class='text'>Ready for pickup</span> </div>
            </div>";
            }else if($row['order_status'] == 'Packed'){
            // packed Order
            echo "<div class='track_3'>
                <div class='step active' > <span class='icon'> <i class='fa fa-check'></i> </span> <span class='text'>Order confirmed</span> </div>
                <div class='step active2'> <span class='icon'> <i class='fa fa-user'></i> </span> <span class='text'> Picked by courier</span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-truck'></i> </span> <span class='text'> On the way </span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-box'></i> </span> <span class='text'>Ready for pickup</span> </div>
            </div>";
            }else if($row['order_status'] == 'Shipped'){
            // shipped Order
            echo "<div class='track_4'>
                <div class='step active' > <span class='icon'> <i class='fa fa-check'></i> </span> <span class='text'>Order confirmed</span> </div>
                <div class='step active'> <span class='icon'> <i class='fa fa-user'></i> </span> <span class='text'> Picked by courier</span> </div>
                <div class='step active2'> <span class='icon'> <i class='fa fa-truck'></i> </span> <span class='text'> On the way </span> </div>
                <div class='step'> <span class='icon'> <i class='fa fa-box'></i> </span> <span class='text'>Ready for pickup</span> </div>
            </div>";
            }else{
            // delivered Order
            echo "<div class='track_4'>
                <div class='step active' > <span class='icon'> <i class='fa fa-check'></i> </span> <span class='text'>Order confirmed</span> </div>
                <div class='step active'> <span class='icon'> <i class='fa fa-user'></i> </span> <span class='text'> Picked by courier</span> </div>
                <div class='step active'> <span class='icon'> <i class='fa fa-truck'></i> </span> <span class='text'> On the way </span> </div>
                <div class='step active'> <span class='icon'> <i class='fa fa-box'></i> </span> <span class='text'>Ready for pickup</span> </div>
            </div>";
            }
    
            ?>
            <hr>
            <div class="details">
        <div class="shipping">
            <i class="fa fa-map-marker"></i>
            <h4>Shipping</h4>
            <p><b>FruitKha Corporation</b><br>Block-C147 Ashok Vihar CA 457845,<br> Main Road, Ashok Vihar, New Delhi<br>110052<br>New Delhi, Delhi 110052<br>+91 7849854761</p>
        </div>
        <div class="shipping" style="background:#f2f2f2">
            <i class="fa fa-credit-card"></i>
            <h4>Billing Details</h4>
            <p><b><?php echo $row['customer_name'] ?></b><br><?php echo $row['address'] ?><br><?php echo $row['zip_code'] ?><br>+91 <?php echo $row['phone'] ?></p>
        </div>
        <div class="shipping">
            <i class="fa fa-truck"></i>
            <h4>Delivery Method</h4>
            <p><b>Preferred Method:</b><br>India Standard<br>(normally 4-5 business days, unless noted)</p>
        </div>
    </div>
<!-- ///////////////////////order list/////////////////////// -->
    <div class="order_box">
        <div class="side_1">
            <h3>Order List</h3>
            <hr>

            <?php
         $qry_details = mysqli_query($conn,"
            SELECT 
                order_details.*,
                product.*
            FROM product
            JOIN order_details 
                ON order_details.product_id = product.product_id
            WHERE order_details.order_id = '$id'
");


            while($row_details=mysqli_fetch_assoc($qry_details)){?>

                <div class="view_order_info">
                    <div class="order_image">
                        <img src="../images/<?php echo $row_details['product_image_1'] ?>" width="100%" height="100%">
                    </div>
                    <div class="order_info">
                        <h4><?php echo $row_details['name'] ?><br>-<?php echo $row_details['product_title'] ?></h4>
                        <p style="margin-top: -1vh">Qty: <?php echo $row_details['quantity'] ?></p>
                    </div>
                    <div class="order_price">
                        <h3>$<?php echo $row_details['product_amount']*$row_details['quantity'] ?></h3>
                    </div>
                </div>
                <?php
                    $total+=($row_details['product_amount']*$row_details['quantity']); 
                }    
            ?>
        </div>

        <!-- //////////////////////order summary///////////////////// -->
        <div class="side_2">
            <h3>Order Summary</h3>
            <hr>
            <br><br>
            <div class="subtotal">
                <div class="sub">
                    <p>Original Price:</p>
                </div>
                <div class="amount">
                    <p>$<?php echo $total ?></p>
                </div>
            </div>
            <div class="subtotal">
                <div class="sub">
                    <p>Subtotal:</p>
                </div>
                <div class="amount">
                    <p>$<?php
                $qry_coupon_true=mysqli_query($conn,"SELECT is_coupon FROM orders WHERE order_id='$id'");
                    $row_coupon=mysqli_fetch_assoc($qry_coupon_true);
                    if($row_coupon['is_coupon'] == 1){
                        $per=$total/100*20;
                        $total-=$per;
                        echo round($total,2);
                    }else{  
                        echo round($total,2);
                    } 
                    ?></p>
                </div>
            </div>
            <div class="subtotal">
                <div class="sub">
                    <p>Shipping & Handling:</p>
                </div>
                <div class="amount">
                    <p>$<?php echo $ship=round($total/100*2,2) ?></p>
                </div>
            </div>
            <div class="subtotal">
                <div class="sub">
                    <p>Savings:</p>
                </div>
                <div class="amount">
                    <p>-$<?php echo $per; ?></p>
                </div>
            </div>
            <br>
            <hr>
            <br><br>
            <div class="subtotal">
                <div class="sub">
                    <h4 style="font-weight: 100;">Total:</h4>
                </div>
                <div class="amount">
                    <h4 style="font-weight: 100;"><?php echo $total+$ship; ?></h4>
                </div>
            </div>
        </div>
    </div>
            
            <hr>

            <a href="vieworders.php" class="btn btn-warning" data-abc="true"> <i class="fa fa-chevron-left"></i> Back to ViewOrders</a>
        </div>
    </article>
</div>

    <!-- jquery -->
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <!-- bootstrap -->
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <!-- count down -->
    <script src="assets/js/jquery.countdown.js"></script>
    <!-- isotope -->
    <script src="assets/js/jquery.isotope-3.0.6.min.js"></script>
    <!-- waypoints -->
    <script src="assets/js/waypoints.js"></script>
    <!-- owl carousel -->
    <script src="assets/js/owl.carousel.min.js"></script>
    <!-- magnific popup -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <!-- mean menu -->
    <script src="assets/js/jquery.meanmenu.min.js"></script>
    <!-- sticker js -->
    <script src="assets/js/sticker.js"></script>
    <!-- main js -->
    <script src="assets/js/main.js"></script>
</body>
</html>
<!-- orderid
customer name
items count
total amount
status
created/updated
action - view/delete

helpers

price() 
dateformat()


product(id)
{
    
} -->