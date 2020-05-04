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
	<!-- iCheck -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">
	<!-- Slimscroll -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/slimscroll.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/orderlist.css">

</head>
<body>

<div id="container">
	<input type="text" id="order_last_id" value="<?php echo isset($lastid["id"]) ? $lastid["id"] : ""; ?>" hidden>
	<button id="logout_btn" class="btn-danger">
		<i class="fa fa-sign-out fa-flip-horizontal"></i> &nbsp;Logout
	</button>

	<?php if($_SESSION["access_level"] != 0) { ?>
	<button id="create_order_btn" class="btn-dark pull-right">
		<i class="fa fa-plus"></i> &nbsp; Create Order
	</button>
	<?php } else{ ?>
		<div class="dropdown pull-right">
			<button id="dropdown_btn" class="btn-secondary" data-toggle="dropdown">
				<i class="fa fa-bars"></i>
			</button>
			<div class="dropdown-menu">
				<a class="dropdown-item dd-item text-success" href="#" id="product_list_btn"><i class="fa fa-cubes"></i> &nbsp; Product List</a>
				<a class="dropdown-item dd-item text-warning" href="#" id="customer_btn"><i class="fa fa-users"></i> &nbsp; Cutomer List</a>
				<a class="dropdown-item dd-item text-info" href="#" id="inventory_adjustment_btn"><i class="fa fa-cogs"></i> &nbsp; Inv. Adjustment</a>
				<a class="dropdown-item dd-item text-default" href="#" id="user_list_btn"><i class="fa fa-user-secret"></i> &nbsp; User List</a>
			</div>
		</div>
		<span class="span_seperator pull-right"></span>
		<div class="dropdown pull-right">
			<button id="report_btn" class="btn-success" data-toggle="dropdown">
				<i class="fa fa-file-text-o"></i> &nbsp;Report
			</button>
			<div class="dropdown-menu">
				<a class="dropdown-item dd-item rpt_btn" href="javascript:void(0)" id="item_summary_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Item Summary </a>
				<a class="dropdown-item dd-item rpt_btn" href="javascript:void(0)" id="item_summary_detail_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Item Summary  Detail</a>
			</div>
		</div>
	<?php } ?>

	<span class="span_seperator pull-right"></span>
	<button id="filter_btn" class="btn-warning pull-right">
		<i class="fa fa-filter"></i> &nbsp;Filter By
	</button>
	<span class="span_seperator pull-right"></span>
	<button id="clear_filter_btn" class="btn-default pull-right">
		<i class="fa fa-remove"></i> &nbsp;Clear Filter
	</button>

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
						<th>Paid Status</th>
						<th>Print Status</th>
						<th>Status</th>
						<th hidden></th><!-- filter -->
					</tr>
					</thead>
					<tbody>
					<?php
						$statusarray = array("Pending", "For Delivery", "Complete", "Voided");
						$tdclass = array("text-success", "text-warning", "text-primary", "text-danger");
						foreach($transaction as $ind => $row){
							$paidclass = "";
							$paid = "";
							$printed = "";
							$printCls = "";
							$transdate = date("mdY", strtotime($row["datetime"]));
							if($row["paid"] == 1){
								$paid = "Paid";
								$paidclass = "text-success";
							}
							if($row["printed"] == 1){
								$printed = "Printed";
								$printCls = "text-success";
							}
							echo '<tr id="tr_'.$row["id"].'">';
								echo '<td>'.$transdate.'-'.sprintf("%04s", $row["id"]).'</td>';
								echo '<td>'.date("m/d/Y H:i:s", strtotime($row["datetime"])).'</td>';
								echo '<td>'.date("m/d/Y", strtotime($row["delivery_date"])).'</td>';
								echo '<td width="25%">'.$row["name"].'</td>';
								echo '<td class="'.$paidclass.'">'.$paid.'</td>';
								echo '<td class="'.$printCls.'">'.$printed.'</td>';
								echo '<td class="'.$tdclass[$row["status"]].'">'.$statusarray[$row["status"]].'</td>';
								echo '<td hidden>'.$row["delivery_date"].'</td>';
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>

	<div class="modal fade" id="filter_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">FILTER ORDER</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Delivery Date</label>
						<input type="date" class="form-control" id="filter_delivery_date">
					</div>

					<div class="form-group">
						<label>Status</label>
						<select class="form-control" id="filter_status">
							<option value=""></option>
							<option value="0" class="text-success">Pending</option>
							<option value="1" class="text-warning">For Delivery</option>
							<option value="4" class="text-info">Delivered</option>
							<option value="2" class="text-primary">Complete</option>
							<option value="3" class="text-danger">Voided</option>
						</select>
					</div>

					<div class="form-group">
						<label></label>
						<div class="checkbox icheck">
							<label>
							<input type="checkbox" id="filter_paid"> &nbsp; Paid
							</label>
						</div>
					</div>

					<div class="form-group">
						<div class="checkbox icheck">
							<label>
								<input type="checkbox" id="filter_printed"> &nbsp; Printed
							</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="confirm_filter">Filter</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="report_param_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="reportModalLabel"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Delivery Date</label>
						<input type="date" class="form-control" id="rpt_delivery_date">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="print_report">Print</button>
				</div>
			</div>
		</div>
	</div>

</div>
<form id="report_data" method="post" action="" target="new_window">
	<input type="hidden" id="dlvrydate" name="deliverydate" />
</form>
<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>
<!-- iCheck -->
<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
	$(function () {
		$('input').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%' /* optional */
		});
	});
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/orderlist.js"></script>

</body>
</html>
