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
} else {
	$name = $customer["name"];
	$fb_name = $customer["facebook_name"];
	$cn = $customer["contact_number"];
	$cust_location_image = $customer["location_image"];
	$delivery_add = $customer["delivery_address"];
}

$pagetitle = "RAM-X";
$pageicon = "favicon.jpg";
if($store_id == "2"){
	$pagetitle = "RIBSHACK";
	$pageicon = "favicon2.jpg";
}
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
    <title>RAM-X Meatshop | New Order</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>/assets/app/img/favicon.jpg" />
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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/dashboard.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/common.css">

 


   
  </head>
  <body>



  <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
 	 <a class="navbar-brand  mr-auto mr-lg-0" href="<?php echo site_url() . '/order' ?>">
		<img src="<?=base_url()?>/assets/app/img/favicon.png" width="20"  />
		<span class="d-lg-inline-block d-none">RAM-X Meatshop</span>
		<span class=" d-lg-none d-md-inline-block ">New Order</span>
	</a>

	<button class="navbar-toggler p-0 border-0 mr-3 mb-1  d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
		<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-cart4" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
		</svg>
	</button>
	<button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
		<span class="navbar-toggler-icon"></span>
	</button>


  <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item active">
	 	 <a class="nav-link" href="#">New Order <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
	 	 <a class="nav-link" href="<?=site_url()?>/order/purchases">My Purchases</a>
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



<div class="container-fluid ">
  <div class="row">

    <main role="main" class="col-md-8  col-lg-9 px-md-4 order-1">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      
        <div class="btn-toolbar mb-2 mb-md-0">
		
				<div class="dropdown">
					<a class="btn btn-sm btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-filter" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
						</svg>	
						Filter Products
					</a>

					<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
					<?php
							$first = 0;
							foreach($category as $ind => $row){
								$class = 'category_li dropdown-item';
							
								echo '<a href="javascript:void(0)" class="'.$class.'" id="cat_'.$row["id"].'">'.$row["name"].'</a>';
								$first++;
							}
					?>
					</div>
			
			</div>
        </div>
      </div>
	  <input type="text" id="transaction_id_inp" hidden value="<?php echo isset($transaction) ? $transaction["id"] : "" ?>">
						
      <div class="album py-5 ">
    	<div class="container-fluid">

      <div class="row">
	  <?php
		foreach($product as $ind => $row){

			$availableqty = ($row["avail_qty"] != null ? $row["avail_qty"] : 0);
            if ($availableqty > 0) {
                echo '<div class=" col-lg-4 col-xl-3  main_product prodcat_'.$row["category_id"].'">';
                echo '	<div class="card mb-4 shadow-sm product_main">';
                echo '		<div class="product_cont'. ($availableqty == 0 ? " notavailable" : "") .'" id="'.$row["id"].'">';
                echo '			<svg class="bd-placeholder-img card-img-top product_desc" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: Thumbnail">
									<title>Add to Cart</title>
									<rect width="100%" height="100%" fill="#55595c"/>
									<text x="50%" y="50%" fill="#eceeef" dy=".3em"></text>
								</svg>
								<div class="card-img-overlay text-white justify-content-center d-flex">
									<h5 class="card-title text-center mt-5">'.$row["description"].'</h5>
								</div>
								<div class="product_desc"  style="display:none">'.$row["description"].'</div>
								<div class="product_price" hidden>'.$row["price"].'</div>
								';
				echo '		</div>';//product_cont
					
            echo '		<div class="card-body">	
							<div class="card-text">
								<div class=" d-flex pa-3">
									
									<div class="p_price"><b> Price : '.number_format($row["price"], 2).'</b> / '.$row["uom"].''.'</div>
									<div class="product_uom" id="produom_'.$row["id"].'" hidden>'.$row["uom"].'</div>
									
									<div id="qty_'.$row["id"].'" class="availqty_cont ml-sm-auto">
										Avail Qty: <span>'. $availableqty .'</span>
									</div>
								</div>
								<div class="product_qty mt-2">
									<input type="text" class="form-control inpqty" value="1" id="inpqty'.$row["id"].'">
								</div>
							</div>
						</div>';//product_cont
            echo '	</div>';//product_main
            echo '</div>';//col
            }
		}
		?>	
    
      </div>
    </div>
  </div>
	</main>
	<nav id="sidebarMenu" class="col-md-4 col-lg-3 d-md-block bg-light sidebar collapse order-2 overflow-none">
      	<div class="sidebar-sticky pt-3 px-3 d-flex flex-column overflow-none">
			<div>
				<button id="customer_details_main_btn" class="btn btn-primary btn-block">
					<?php echo $name == "" ? "Order Details" : $name; ?>
				</button>
			</div>
			<div class="pt-2 pb-4  mb-auto" style="overflow-x: hidden; overflow-y: auto">
				<div class="mb-auto ">
					<div class="d-flex flex-column pl-2" id="productsummary">
						<?php
						if(isset($transactiondetail)){
							foreach($transactiondetail as $ind => $row){
								echo '<div class="row prodsumrow existing" data-id="'.$row["id"].'" id="'.$row["product_id"].'">';
								echo '<div class=" ml-2 summary_desc mr-auto ">asdf'.$row["description"].'</div>';
								echo '<div class=" summary_qty mr-5">'.$row["quantity"].'</div>';
								echo '<div class="mr-3">';
								echo '<button type="button" class="btn btn-danger delbtn" id="delbtn_'.$row["product_id"].'" ';
								echo '<i class="fa fa-trash"></i>';
								echo '</button>';
								echo '</div>';
								echo '<div style="clear:both"></div>';
								echo '</div>';
							}
						}
						?>
					</div><!-- row wrapper product_summary -->
				</div><!-- slimscroll container -->
			</div>
			<div class="px-2">
				<div class="row total_summary text-center">
						<h5 style="width: 100%">TOTAL: <span id="totalvalue"><?php echo $total == "" ? "0.00" : number_format($total, 2); ?></span></h5>
					</div>
				</div>
				<br/>
				<div class="row px-2 pb-3">
					<button type="button" class="btn btn-success" id="settlebtn" style="height: 50px;width: 98%; font-size: 20px;">
						<i class="fa fa-check"></i>&nbsp; &nbsp; PLACE ORDER
					</button>
				</div>
			</div>
      	</div>
    </nav>


  </div>
</div>
<!-- 
		*
		*		MODALS	
		*
	-->
<div id="confirmmodal" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="icon-box" style="border: 3px solid #5cb85c; color: #5cb85c">
					<i class="fa fa-save"></i>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>

			<h4 class="modal-title">SETTLE TRANSACTION</h4>
			<div class="modal-body">
				<p>Are you sure you want to settle this transaction?.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" id="confirm_noopt">No</button>
				<button type="button" class="btn btn-success" id="confirm_yesopt">Yes</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmcancelmodal" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="icon-box" style="border: 3px solid #c01a25; color: #e53441">
					<i class="fa fa-remove"></i>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<h4 class="modal-title">CANCEL TRANSACTION</h4>
			<div class="modal-body">
				<p>You have unsaved changes. Are you sure you want to cancel transaction?.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" id="cancel_suspend_transaction">No</button>
				<button type="button" class="btn btn-success" id="confirm_cancel_transaction">Yes</button>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="customer_detail_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">ORDER DETAILS</h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Customer Name</label>
								<input type="text" id="customer_id" hidden value="<?php echo $custid; ?>">
								<div style="position: relative; height: 34px;">
									<input type="text" class="form-control" placeholder="Customer Name"  autocomplete="off" id="customer_name" style="position: absolute; z-index: 2; background: transparent;" value="<?php echo $name; ?>">
								</div>
							</div>
							<div class="form-group">
								<label>Facebook Name</label>
								<input type="text" class="form-control" id="facebook_name"  autocomplete="false" value="<?php echo $fb_name; ?>">
							</div>
							<div class="form-group">
								<label>Contact Number</label>
								<input type="text" class="form-control" placeholder="etc. 09123456789" id="cust_contact_number" value="<?php echo $cn; ?>">
							</div>
							<div class="form-group">
								<label>Customer Location</label>
								<input type='file' id="customer_location" class="" />
								<div id="map_preview">
									<img id="map_img_preview" src="<?php echo $locationimage; ?>" alt="Map of Customer's Location" />
								</div>
							</div>
						</div><!-- col 6 / left panel -->
						<div class="col-md-6">
							<div class="form-group">
								<label>Delivery Address</label>
								<textarea class="form-control" rows="2" id="cust_delivery_address"><?php echo $delivery_add; ?></textarea>
							</div>
							<div class="form-group">
								<label>Delivery Date</label>
								<input type="date" class="form-control" readonly id="delivery_date" value="">
							</div>
							<div class="form-group">
								<label>Payment Method</label>
								<select class="form-control" id="payment_method">
									<option value="0" <?php echo $pm == 0 ? 'selected="selected"' : ''; ?>>Cash on Delivery (COD)</option>
									<option value="1" <?php echo $pm == 1 ? 'selected="selected"' : ''; ?>>Bank Transfer - BPI</option>
									<option value="3" <?php echo $pm == 3 ? 'selected="selected"' : ''; ?>>Bank Transfer - Metrobank</option>
									<option value="2" <?php echo $pm == 2 ? 'selected="selected"' : ''; ?>>GCash</option>
									<option value="4" <?php echo $pm == 4 ? 'selected="selected"' : ''; ?>>Check</option>
								</select>
							</div>
							<div class="form-group">
								<label>Payment Confirmation Details</label>
								<textarea class="form-control" rows="1" id="payment_confirmation_detail"><?php echo $pcd; ?></textarea>
							</div>

							<div class="form-group">
								<label>Remarks/Notes</label>
								<textarea class="form-control" rows="3" id="trans_remarks"><?php echo $remarks; ?></textarea>
							</div>
						</div><!-- col 6 / right panel -->
					</div><!-- row -->
				</div>

			</div><!-- modal body -->
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="save_customer_detail_btn">Save changes</button>
			</div>
		</div>
	</div>
</div>

<textarea id="clipboard" area-hidden="true" style="display:none"></textarea>

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>

<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/moment.js"></script>
<script>
	var siteURL = "<?=site_url()?>"
	var baseURL = "<?=base_url()?>"
	var ordernumber = '<?php echo $ordernumber; ?>';
    var cust_location_image = "<?=$cust_location_image?>"
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
<script src="<?php echo base_url(); ?>assets/app//order/order.js"></script>
<script src="<?php echo base_url(); ?>assets/app/order/common.js"></script>
<script src="<?php echo base_url(); ?>assets/app/order/dashboard.js"></script></body>
</html>
