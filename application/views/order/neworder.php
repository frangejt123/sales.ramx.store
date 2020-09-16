<?php

defined('BASEPATH') OR exit('No direct script access allowed');


$name = "";
$cn = "";
$delivery_dt = "";
$delivery_add = "";
$pm = "";
$pcd = "";
$remarks = "";
$total = "";
$custid = "";
$locationimage = "#";
$fb_name = "";
$ordernumber = "";

if(isset($transaction)){
	$name = $transaction["name"];
	$custid = $transaction["customer_id"];
	$cn = $transaction["contact_number"];
	$delivery_dt = $transaction["delivery_date"];
	$delivery_add = $transaction["delivery_address"];
	$pm = $transaction["payment_method"];
	$pcd = $transaction["payment_confirmation_detail"];
	$remarks = $transaction["remarks"];
	$total = $transaction["total"];
	$locationimage = $transaction["cust_location_image"];
	$fb_name = $transaction["facebook_name"];
	$orderid = sprintf("%04s", $transaction["id"]);
	$tnxdt =  date("mdY", strtotime($transaction["datetime"]));
	$ordernumber = $tnxdt."-".$orderid;
}

$pagetitle = "RAM-X";
$pageicon = "favicon.jpg";
if($store_id == "2"){
	$pagetitle = "RIBSHACK";
	$pageicon = "favicon2.jpg";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<title><?php echo $pagetitle; ?></title>
	<meta name="viewport" content=" user-scalable=0"/>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/<?php echo $pageicon; ?>" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css">
	<!-- Slimscroll -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/slimscroll.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/croppie.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/app.css">

</head>

<body>

<div class="d-flex" id="wrapper">

	<div class="container" style="max-width: 100% !important; padding: 0px !important;">
		<div class="content">
			<div class="row"><!-- button row -->
				<div class="col-3 pl-0 pr-0"><!-- left column -->
					<div class="bg-light pt-2 pl-4">
						<input type="text" id="transaction_id_inp" hidden value="<?php echo isset($transaction) ? $transaction["id"] : "" ?>">
						<button id="cancel_order_btn" class="btn-danger">
							<i class="fa fa-arrow-left"></i> &nbsp; Back
						</button>
					
					</div>
				</div><!-- left column -->
				<div class="col-6 pl-0 pr-0">
					<div class="bg-light pt-2 pl-4 w-100 h-100">
						<div class="row">
							<div class="col-12">
								<button id="copy_details" class="btn-secondary pull-right" data-toggle="tooltip" data-placement="bottom" title="Copy details to clipboard">
									<i class="fa fa-copy"></i>
								</button>
							</div>
						</div><!-- row -->
					</div>
				</div><!-- center column -->
				<div class="col-3 pl-0 pr-0">
					<div class="bg-light pt-2 pr-4">
						<div class="row">
							<div class="col-1">
							</div>
							<div class="col-11">
								<button id="customer_details_main_btn" class="btn-primary">
									<?php echo $name == "" ? "Order Details" : $name; ?>
								</button>
							</div>
						</div><!-- row -->
					</div>
				</div><!-- right column -->
			</div><!-- button row -->
		</div>
		<div class="row"><!-- main row -->
			<div class="col-2 pl-0 pr-0"><!-- left column -->
				<div class="bg-light border-right">
					<div class="pb-4">
						<div class="mb-auto slimscrollcont2">
							<ul class="list-group">
								<?php
									$first = 0;
									foreach($category as $ind => $row){
										$class = 'category_li list-group-item list-group-item-action rounded-0 border-right-0 pl-lg-4 ';
										if($first < 1)
											$class .= ' active';
										echo '<a href="javascript:void(0)" class="'.$class.'" id="cat_'.$row["id"].'">'.$row["name"].'</a>';
										$first++;
									}
								?></ul>
						</div>
					</div>
				</div>
			</div><!-- left column -->
		
		</div><!-- main row -->
	</div>

</div>
<!-- /#wrapper -->






<textarea id="clipboard" area-hidden="true"></textarea>

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/jquery.autocomplete.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>

<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
	var namelist = <?php print_r($namelist); ?>;
	var customerdetail = <?php print_r($customerdetail); ?>;
	var newcustomer;
	var ordernumber = '<?php echo $ordernumber; ?>';

	$(document).ready(function(){
		$('a#click-a').click(function(){
			$('.nav').toggleClass('nav-view');
		});
	});
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/croppie.js"></script>
<script src="<?php echo base_url(); ?>assets/app/order.js"></script>

</body>
</html>
