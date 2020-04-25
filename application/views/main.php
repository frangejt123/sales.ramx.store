<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>RAM-X</title>
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
	 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/app.css">
	
</head>
<body>
<div id="container">
		<?php
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
			}
		?>
		<input type="text" id="transaction_id_inp" hidden value="<?php echo isset($transaction) ? $transaction["id"] : "" ?>">
		<button id="cancel_order_btn" class="btn-danger">
			<i class="fa fa-arrow-left"></i> &nbsp; Back
		</button>
		
		<button id="customer_details_main_btn" class="btn-primary pull-right">
			<?php echo $name == "" ? "Order Details" : $name; ?>
		</button>
		<span class="pull-right span_seperator"></span>
		<button id="copy_details" class="btn-secondary pull-right" data-toggle="tooltip" data-placement="bottom" title="Copy details to clipboard">
			<i class="fa fa-copy"></i>
		</button>

		<!--<input type="text" class="form-control" id="table_number" placeholder="Table Number" value="0">-->

		<!-- <button type="button" class="btn btn-warning" id="reprint_transaction"
		style="font-size: 15px;font-family: tahoma, sans-serif; float: right;margin-top: -35px;margin-right: 65px">
			<i class="fa fa-refresh">&nbsp; &nbsp; RE-PRINT</i>
		</button> -->

	<div class="row">
	        <div class="col-lg-9 left_floater">
				<div id="left_panel">
					<?php
						$tdcounter = 1;
						foreach($product as $ind => $row){

							if($tdcounter == 1){
								echo '<br/><div class="row">';
							}
							$availableqty = ($row["avail_qty"] != null ? $row["avail_qty"] : 0);
							echo '<div class="col-lg-3 right_floater">';
							echo '<div class="product_main">';
							echo '<div class="product_cont'. ($availableqty == 0 ? " notavailable" : "") .'" id="'.$row["id"].'">';
							echo '<div class="product_desc">'.$row["description"].'</div>';
							echo '<div class="product_price">'.number_format($row["price"], 2).'</div>';
							echo '</div>';//product_cont
							echo '<div id="qty_'.$row["id"].'" class="availqty_cont">Avail Qty: <span>'.
									$availableqty
									.'</span></div>';//product_cont
							echo '<div class="product_qty">';
							echo '<input type="text" class="form-control inpqty" value="1" id="inpqty'.$row["id"].'">';
							echo '</div>';//product_qty
							echo '</div>';//product_main
							echo '</div>';//col

							$tdcounter++;

							if($tdcounter == 5){
								echo '</div>';//row
								echo '<div style="clear:both"></div>';
								$tdcounter=1;
							}

						}
					?>
					<br />
				</div>
			</div><!-- col left -->

			<div class="col-lg-3 right_floater">
				<div class="row" id="productsummary">
					<?php
						if(isset($transactiondetail)){
							foreach($transactiondetail as $ind => $row){
								echo '<div class="row prodsumrow existing" data-id="'.$row["id"].'" id="'.$row["product_id"].'">';
								echo '<div class="col-lg-7 summary_desc left_floater">'.$row["description"].'</div>';
								echo '<div class="col-lg-2 summary_qty left_floater">'.$row["quantity"].'</div>';
								echo '<div class="col-lg-2 right_floater">';
								echo '<button type="button" class="btn btn-danger delbtn" id="delbtn_'.$row["product_id"].'" style="height: 50px;width: 50px;">';
								echo '<i class="fa fa-trash"></i>';
								echo '</button>';
								echo '</div>';
								echo '<div style="clear:both"></div>';
								echo '</div>';
							}
						}
					?>
				</div><!-- row wrapper product_summary -->

				<br/><br/>

				<div class="row total_summary">
					<div class="col-lg-8 left_floater" style="text-align: right">
						TOTAL
					</div>

					<div class="col-lg-4 right_floater" id="totalvalue" style="text-align: left">
						<?php echo $total == "" ? "0.00" : number_format($total, 2); ?>
					</div>
					<div style="clear:both"></div>
				</div>
				<br/>
				<div class="row">
					<button type="button" class="btn btn-success" id="settlebtn" style="height: 50px;width: 98%; font-size: 20px;">
						<i class="fa fa-check"></i>&nbsp; &nbsp; PLACE ORDER
					</button>
				</div>
			</div><!-- col right -->
		<div style="clear: both"></div>
    </div><!-- row -->

    <div id="confirmmodal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
			  <div class="modal-header">
			    <div class="icon-box" style="border: 3px solid #5cb85c; color: #5cb85c">
			      <i class="fa fa-save"></i>
			    </div>        
			    <h4 class="modal-title">SETTLE TRANSACTION</h4>  
			            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			  </div>
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

	<div id="savecustomerdetailmodal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #5cb85c; color: #5cb85c">
						<i class="fa fa-save"></i>
					</div>
					<h4 class="modal-title">ADD NEW CUSTOMER</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<p><b><span id="new_customer_name"></span></b> is not on the customer list. Would you like to add his/her detail?.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" id="cancel_save_new_customer">No</button>
					<button type="button" class="btn btn-success" id="confirm_save_new_customer">Yes</button>
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
					<h4 class="modal-title">CANCEL TRANSACTION</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
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
										<input type="text" class="form-control" placeholder="Customer Name" id="customer_name" style="position: absolute; z-index: 2; background: transparent;" value="<?php echo $name; ?>">
										<input type="text" class="form-control" id="name_autocomplete_hint"  disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
									</div>
								</div>
								<div class="form-group">
									<label>Facebook Name</label>
									<input type="text" class="form-control" id="facebook_name" value="<?php echo $fb_name; ?>">
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
									<input type="date" class="form-control" id="delivery_date" value="<?php echo $delivery_dt; ?>">
								</div>
								<div class="form-group">
									<label>Payment Method</label>
									<select class="form-control" id="payment_method">
										<option value="0" <?php echo $pm == 0 ? 'selected="selected"' : ''; ?>>Cash on Delivery (COD)</option>
										<option value="1" <?php echo $pm == 1 ? 'selected="selected"' : ''; ?>>Bank Transfer</option>
										<option value="2" <?php echo $pm == 2 ? 'selected="selected"' : ''; ?>>GCash</option>
									</select>
								</div>
								<div class="form-group">
									<label>Payment Confirmation Details</label>
									<textarea class="form-control" rows="1" id="payment_confirmation_detail"><?php echo $pcd; ?></textarea>
								</div>

								<div class="form-group">
									<label>Remarks</label>
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
	
	<textarea id="clipboard" area-hidden="true"></textarea>
</div>

<!-- jQuery 3 -->
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
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/croppie.js"></script>
<script src="<?php echo base_url(); ?>assets/app/app.js"></script>

</body>
</html>
