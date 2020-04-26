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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/orderlist.css">

</head>
<body>

<div id="container">
	<input type="text" id="order_last_id" value="<?php echo $lastid["id"]; ?>" hidden>
	<button id="logout_btn" class="btn-danger">
		<i class="fa fa-sign-out fa-flip-horizontal"></i> &nbsp;Logout
	</button>

	<?php if($_SESSION["access_level"] != 0) { ?>
	<button id="create_order_btn" class="btn-warning pull-right">
		<i class="fa fa-plus"></i> &nbsp; Create Order
	</button>
	<?php } else{ ?>
	<button id="product_list_btn" class="btn-success pull-right">
		<i class="fa fa-cubes"></i> &nbsp; Product List
	</button>
	<span class="pull-right span_seperator"></span>
	<button id="inventory_adjustment_btn" class="btn-secondary pull-right route_btn">
		<i class="fa fa-cogs"></i> &nbsp; Inv. Adjustment
	</button>
	<span class="pull-right span_seperator"></span>
	<button id="customer_btn" class="btn-secondary pull-right route_btn">
		<i class="fa fa-users"></i> &nbsp; Customers
	</button>
	<?php } ?>
	<div style="clear:both"></div>

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search for Customer Name" id="search_customer_name">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="orderlist_table">
					<thead>
					<tr>
						<th>Transaction #</th>
						<th>Transaction Date</th>
						<th>Delivery Date</th>
						<th>Customer Name</th>
						<th>Status</th>
					</tr>
					</thead>
					<tbody>
					<?php
						$statusarray = array("Pending", "For Delivery", "Complete", "Voided");
						$tdclass = array("text-success", "text-warning", "text-primary", "text-danger");
						foreach($transaction as $ind => $row){
							$transdate = date("mdY", strtotime($row["datetime"]));
							echo '<tr id="tr_'.$row["id"].'">';
								echo '<td>'.$transdate.'-'.sprintf("%04s", $row["id"]).'</td>';
								echo '<td>'.date("m/d/Y H:i:s", strtotime($row["datetime"])).'</td>';
								echo '<td>'.date("m/d/Y", strtotime($row["delivery_date"])).'</td>';
								echo '<td>'.$row["name"].'</td>';
								echo '<td class="'.$tdclass[$row["status"]].'">'.$statusarray[$row["status"]].'</td>';
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>

	<div class="modal fade" id="product_list_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">PRODUCT LIST</h4>
				</div>
				<div class="modal-body">
					<div class="form_container container-fluid">
						<div class="row">
							<input type="hidden" class="form-control" id="selected_product">
							<div class="form-group col-xs-6">
								<label>Description</label>
								<input type="text" class="form-control" id="product_description">
							</div>
							<div class="form-group col-xs-6" style="margin-left: 12px;">
								<label>Price</label>
								<input type="text" class="form-control" id="product_price">
							</div>
						</div>
					</div>

					<div class="form_button_container">
						<button type="button" class="btn btn-danger pull-left" id="delete_product_btn">
							<i class="fa fa-trash"></i>  Delete
						</button>
						<button type="button" class="btn btn-danger pull-left" id="undo_delete_btn">
							<i class="fa fa-undo"></i>  Undo
						</button>

						<button type="button" class="btn btn-success pull-right" id="add_new_product_btn">
							<i class="fa fa-plus"></i> Add
						</button>
						<button type="button" class="btn btn-success pull-right" id="update_product_btn">
							<i class="fa fa-pencil"></i> Update
						</button>
						<span class="pull-right span_seperator"></span>
						<button type="button" class="btn btn-secondary pull-right" id="clear_product_btn">
							<i class="fa fa-remove"></i> Clear
						</button>
						<div style="clear:both"></div>
					</div>

					<div id="product_container">
						<div class="box">
							<div class="box-body no-padding">
								<table class="table table-striped table-hover" id="product_table">
									<thead>
									<tr>
										<th>Description</th>
										<th width="20%">Price</th>
										<th width="20%">Qty</th>
									</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="save_product_changes">Save changes</button>
				</div>
			</div>
		</div>
	</div>


</div>

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/orderlist.js"></script>

</body>
</html>
