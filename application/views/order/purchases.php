<?php

defined('BASEPATH') OR exit('No direct script access allowed');

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
    <title>RAM-X Meatshop | My Purchases</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>/assets/app/img/favicon.jpg" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">

	<!-- Slimscroll -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/slimscroll.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/purchases.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/common.css">
   
  </head>
  <body class='bg-light'>
   

  <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
 	 <a class="navbar-brand  mr-auto mr-lg-0" href="<?php echo site_url() . '/order' ?>">
		<img src="<?=base_url()?>/assets/app/img/favicon.png" width="20"  />
			<span class="d-lg-inline-block d-none">RAM-X Meatshop</span>
			<span class=" d-lg-none d-md-inline-block ">My Purchases</span>
	</a>
	
	<button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
		<span class="navbar-toggler-icon"></span>
	</button>


  <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item ">
	 	 <a class="nav-link" href="<?=site_url()?>/order/new">New Order <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
	 	 <a class="nav-link" href="#">My Purchases</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Settings</a>
        <div class="dropdown-menu dropdown-menu-left" style="left: -5rem !important" aria-labelledby="dropdown01">
          <a class="dropdown-item" href="#">Profile</a>
          <a class="dropdown-item" href="#" id="logout">Logout</a>
        </div>
      </li>
    </ul>
  </div>
</nav>

<div class="nav-scroller bg-white shadow-sm " id="purchasesNav">
	<div class="container">
		<nav class="nav   nav-tabs nav-underline" role="tablist">
			<a class="nav-link-tab active" data-page='all' data-toggle="tab" href="#all" >All</a>
			<a class="nav-link-tab " data-page='packing' data-toggle="tab" href="#packing" >Packing</a>
			<a class="nav-link-tab"  data-page='topay' data-toggle="tab" href="#topay">
			To Pay
		
			</a>
			<a class="nav-link-tab"  data-page='toreceive' data-toggle="tab" href="#toreceive">To Receive</a>
			<a class="nav-link-tab"  data-page='delivered' data-toggle="tab" href="#delivered">Delivered</a>
			<a class="nav-link-tab"  data-page='completed' data-toggle="tab" href="#completed" >Completed</a>
			<a class="nav-link-tab"  data-page='cancelled' data-toggle="tab" href="#cancelled" >Cancelled</a>
		
		</nav>
	</div>
</div>
	



	<div class="container pt-3" style="margin-top: 2.75rem">

		<main role="main" id="main">
		
			<div class="tab-content">
			<div class="tab-pane fade active show" id="all" >
<?php

foreach($purchases as $purchase) {
	
echo '<div class="card mb-3 order-card" data-id="'.$purchase["id"].'">
		<div class="card-header d-flex">
			<span class="mr-auto order_number" >Order # '. $purchase["order_number"] . '</span>
			<span class=" d-none d-lg-block">'.$purchase["tracking"]["message"].'</span>
			<span class=" border-left ml-3 pl-3" style="color: '.$purchase["tracking"]["color"].' !important"> '.$purchase["tracking"]["status"].' </span></div>
		<div class="card-body px-0 ">
			<ul class="list-group mb-3">
		';

	foreach($purchase["detail"] as  $key => $row) {
        if ($key <= 2) {
            echo '
				  <li class="list-group-item d-flex border-top-0   border-bottom justify-content lh-condensed border-left-0 border-right-0">
						<div class="mr-3">
							<svg class="bd-placeholder-img " width="75" height="75" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: Thumbnail">
								<title>Add to Cart</title>
								<rect width="100%" height="100%" fill="#55595c"/>
								<text x="50%" y="50%" fill="#eceeef" dy=".3em"></text>
							</svg>
						</div>
					  <div class="mr-auto">
						  <h6 class="my-0">'.$row["description"].'</h6>
						  <small class="text-muted">'.$row["quantity"].' ' . $row["uom"].'</small>
					  </div>
					  <span class="text-muted">P'.number_format($row["total_price"], 2).'</span>
				  </li>
				
					';
        }
	}

	$more_detail  = $purchase["detail_count"] - ($key + 1);

	if($more_detail > 0 ) {
		echo '<a class=" list-group-item  text-muted font-smaller p-1 text-center border-left-0 border-right-0" href="#">View ' . $more_detail.' more item/s</a>';
	}

?>	 
  
	  
		  <li class="list-group-item d-flex  border-left-0 border-right-0">
			<div class="text-success ml-auto">
			  <h6 class="my-0">Total Amount</h6>
			</div>
			<span class="text-success ml-5">P<?=number_format($purchase['total'], 2)?></span>
		  </li>
		  <li class="list-group-item d-flex border-left-0 border-right-0">
			<div class="ml-auto" >
			  <h6 class="my-0">Payment Method</h6>
			</div>
			<span class="ml-5" ><?=($payment_method[$purchase['payment_method']])?></span>
		  </li>
		  <li class="list-group-item d-lg-none border-left-0 border-right-0 border-bottom-0">
		   	<span><?=$purchase["tracking"]["message"]?></span>
		  </li>
		</ul>
<?php 		
echo '		</div>
	</div>';
	}

?>
				
				</div>	
				<div class="tab-pane fade" id="packing">
				</div>
				<div class="tab-pane fade" id="topay">
				</div>
				<div class="tab-pane fade" id="toreceive">
				</div>
				<div class="tab-pane fade" id="delivered">
				</div>
				<div class="tab-pane fade" id="completed">
				</div>
				<div class="tab-pane fade" id="cancelled">
				</div>
			</div>
		</main>
		<div class="d-flex flex-row  justify-content-center mb-5 h-100" >
			<div id="loader" class="spinner-border text-primary d-none" role="status">
				<span class="sr-only">Loading...</span>
			</div>
			<div id="empty" class="d-none mt-5">
				<div class=" d-flex flex-column my-auto">
					<h3 class="text-muted mx-auto">No Results Found</h3>
					<img src="<?=base_url()?>/assets/app/img/empty.png" class='img-fluid mx-auto' width="150" />
				</div>
			</div>
		</div>
	</div>

<template>
	<div class="card">
		<div class="card-title">
		</div>

	</div>
</template>

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>

<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/moment.js"></script>
<script>
	var siteURL = "<?=site_url()?>"
	var baseURL = "<?=base_url()?>"
	var payment_method = <?=json_encode($payment_method)?>
	
	$(document).ready(function(){
		$('a#click-a').click(function(){
			$('.nav').toggleClass('nav-view');
		});
	});
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/order/common.js"></script>
<script src="<?php echo base_url(); ?>assets/app/order/purchases.js"></script></body>
</html>
