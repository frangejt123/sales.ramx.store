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
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/select2/dist/css/select2.css">
	<!-- daterange picker -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
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
	<button id="logout_btn" class="btn-danger pull-left">
		<i class="fa fa-sign-out fa-flip-horizontal"></i> &nbsp;Logout
	</button>
	<span class="span_seperator pull-left"></span>
	<div id="current_logged_user" class="pull-left">
		<span<b><i class="fa fa-user"></i> &nbsp; <?php echo $_SESSION["name"]; ?></b></span>
	</div>

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
				<a class="dropdown-item dd-item text-default" href="#" id="driver_list_btn"><i class="fa fa-user"></i> &nbsp; Driver</a>
			</div>
		</div>
		<span class="span_seperator pull-right"></span>
		<div class="dropdown pull-right">
			<button id="report_btn" class="btn-success" data-toggle="dropdown">
				<i class="fa fa-file-text-o"></i> &nbsp;Report
			</button>
			<div class="dropdown-menu">
				<a class="dropdown-item dd-item rpt_btn" data-filter="from_to" href="javascript:void(0)" id="item_summary_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Item Summary </a>
				<a class="dropdown-item dd-item rpt_btn" data-filter="from_to" href="javascript:void(0)" id="item_summary_detail_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Item Summary  Detail</a>
				<a class="dropdown-item dd-item rpt_btn" data-filter="order_number" href="javascript:void(0)" id="payment_record_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Payment Record</a>
				<a class="dropdown-item dd-item rpt_btn" data-filter="from_to,trx_status,rpt_mop,rpt_paid" href="javascript:void(0)" id="so_list_by_delivery_date_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Sales Order</a>
				<a class="dropdown-item dd-item rpt_btn" data-filter="from_to,trx_status,rpt_mop,rpt_paid" href="javascript:void(0)" id="sales_by_delivery_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Sales by Delivery Date</a>
				<a class="dropdown-item dd-item rpt_btn" data-filter="from_to,trx_status,rpt_mop,trx_date,trx_driver" href="javascript:void(0)" id="sales_by_payment_method_rpt"><i class="fa fa-file-pdf-o"></i> &nbsp; Sales by Payment Method</a>
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
	<div style="clear: both"></div>

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search for Customer Name" id="search_customer_name">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="orderlist_table">
					<thead>
						<tr>
							<!-- 0 --><th class="sortable" id="td_orderid">Order # <i class="fa fa-sort float-right"></th>
							<!-- 1 --><th class="sortable" id="td_orderdate">Order Date <i class="fa fa-sort float-right"></th>
							<!-- 2 --><th class="sortable" id="td_deliverydate">Delivery Date <i class="fa fa-sort float-right"></th>
							<!-- 3 --><th class="sortable" id="td_customername">Customer Name <i class="fa fa-sort float-right"></th>
							<!-- 4 --><th class="sortable" id="td_driver">Driver <i class="fa fa-sort float-right"></th>
							<!-- 5 --><th class="sortable" id="td_paid_status">Paid Status <i class="fa fa-sort float-right"></th>
							<!-- 6 --><th class="sortable" id="td_mop">Payment Method <i class="fa fa-sort float-right"></th>
							<!-- 7 --><th class="sortable" id="td_print_status">Print Status <i class="fa fa-sort float-right"></th>
							<!-- 8 --><th class="sortable" id="td_status">Status <i class="fa fa-sort float-right"></th>
							<!-- 9 --><th hidden></th><!-- filter -->
						</tr>
					</thead>
					<tbody>
					<?php
						$moparray = array("Cash on Delivery", "Bank Transfer - BPI", "GCash", "Bank Transfer - Metrobank");
						$statusarray = array("Pending", "For Delivery", "Complete", "Voided", "Delivered");
						$tdclass = array("text-success", "text-warning", "text-primary", "text-danger", "text-info");
						$order = array();
						$rowcount = 0;
						foreach($transaction as $ind => $row){
							$paidclass = "";
							$paid = "";
							$printed = "";
							$printCls = "";
							$transdate = date("mdY", strtotime($row["datetime"]));
							if($row["paid"] == 1){
								$paid = "Paid";
								$paidclass = "text-success";
							}else{
								$paid = "---";
							}
							if($row["printed"] == 1){
								$printed = "Printed";
								$printCls = "text-success";
							}else if($row["printed"] == 2){
								$printed = "Revised";
								$printCls = "text-warning";
							}else{
								$printed = "---";
							}
							echo '<tr id="tr_'.$row["id"].'">';
								echo '<td class="td_orderid">'.$transdate.'-'.sprintf("%04s", $row["id"]).'</td>';
								echo '<td class="td_orderdate">'.date("m/d/Y H:i:s", strtotime($row["datetime"])).'</td>';
								echo '<td class="td_deliverydate">'.date("m/d/Y", strtotime($row["delivery_date"])).'</td>';
								echo '<td class="td_customername" width="20%">'.$row["name"].'</td>';
								echo '<td class="td_driver">'.($row["driver_name"] == null ? "---" : $row["driver_name"]).'</td>';
								echo '<td class="td_paid_status '.$paidclass.'">'.$paid.'</td>';
								echo '<td class="td_mop">'.$moparray[$row["payment_method"]].'</td>';
								echo '<td class="td_print_status '.$printCls.'">'.$printed.'</td>';
								echo '<td class="td_status '.$tdclass[$row["status"]].'">'.$statusarray[$row["status"]].'</td>';
								echo '<td hidden>'.$row["delivery_date"].'</td>';
							echo '</tr>';

							json_encode($order[$row["id"]] = $transdate.'-'.sprintf("%04s", $row["id"]));
							$rowcount++;
						}

						$orderids = json_encode($order);
					?>
					</tbody>
					<caption><span id="table_rowcount"><?php echo $rowcount; ?></span> Records found</caption>
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
						<label>Order #</label>
						<div style="position: relative; height: 34px;">
							<input type="text" class="form-control order_id_filter" id="order_id_filter" style="position: absolute; z-index: 2; background: transparent;">
							<input type="text" class="form-control orderid_autocomplete_hint" disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
						</div>
					</div>

					<div class="form-group">
						<label>Delivery Date</label>
						<input type="date" class="form-control" id="filter_delivery_date">
					</div>

<!--					<div class="form-group">-->
<!--						<label>Payment Method</label>-->
<!--						<select class="form-control" id="filter_mop">-->
<!--							<option value=""></option>-->
<!--							<option value="0">Cash on Delivery</option>-->
<!--							<option value="1">Bank Transfer - BPI</option>-->
<!--							<option value="3">Bank Transfer - Metrobank</option>-->
<!--							<option value="2">GCash</option>-->
<!--						</select>-->
<!--					</div>-->

					<div class="form-group">
						<label>Mode of Payment</label>
						<select class="form-control select2" id="filter_mop" multiple="multiple" data-placeholder="Select a Mode of Payment" style="width: 100%;">
							<option value=""></option>-->
							<option value="0">Cash on Delivery</option>
							<option value="1">Bank Transfer - BPI</option>
							<option value="3">Bank Transfer - Metrobank</option>
							<option value="2">GCash</option>
						</select>
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

					<div class="form-group">
						<div class="checkbox icheck">
							<label>
								<input type="checkbox" id="filter_revised"> &nbsp; Revised
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
					<div class="form-group order_number filter_param">
						<label>Order #</label>
						<input type="hidden" id="id_value">
						<div style="position: relative; height: 34px;">
							<input type="text" class="form-control order_id_filter" id="order_id_rpt" style="position: absolute; z-index: 2; background: transparent;">
							<input type="text" class="form-control orderid_autocomplete_hint" disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
						</div>
					</div>

					<div class="form-group delivery_date filter_param">
						<label>Delivery Date</label>
						<input type="date" class="form-control" id="rpt_delivery_date">
					</div>

					<div class="form-group from_to filter_param">
						<label>Delivery Date</label>
						<div class="input-group">
							<input type="text" class="form-control pull-right input_daterangepicker" id="delivery_date_from_to">
						</div>
						<!-- /.input group -->
					</div>

					<div class="form-group trx_date filter_param">
						<label>Order Date</label>
						<div class="input-group">
							<input type="text" class="form-control pull-right input_daterangepicker" id="rpt_param_trxdate">
						</div>
						<!-- /.input group -->
					</div>

					<div class="form-group trx_driver filter_param">
						<label>Driver</label>
						<div style="position: relative; height: 34px;">
							<input type="text" class="form-control driver_id_filter" id="driver_id_rpt" style="position: absolute; z-index: 2; background: transparent;">
							<input type="text" class="form-control driver_autocomplete_hint" disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
						</div>
						<!-- /.input group -->
					</div>

					<div class="form-group trx_status filter_param">
						<label>Status</label>
						<select class="form-control" id="rpt_param_status">
							<option value=""></option>
							<option value="0" class="text-success">Pending</option>
							<option value="1" class="text-warning">For Delivery</option>
							<option value="4" class="text-info">Delivered</option>
							<option value="2" class="text-primary">Complete</option>
							<option value="3" class="text-danger">Voided</option>
						</select>
					</div>

					<div class="form-group rpt_mop filter_param">
						<label>Mode of Payment</label>
						<select class="form-control select2" id="rpt_param_mop" multiple="multiple" data-placeholder="Select a Mode of Payment" style="width: 100%;">
							<option value="0">Cash on Delivery</option>
							<option value="1">Bank Transfer - BPI</option>
							<option value="3">Bank Transfer - Metrobank</option>
							<option value="2">GCash</option>
						</select>
					</div>

					<div class="form-group rpt_paid filter_param">
						<label></label>
						<div class="checkbox icheck">
							<label>
								<input type="checkbox" id="rpt_param_paid"> &nbsp; Paid
							</label>
						</div>
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
	<input type="hidden" id="param" name="param" />
	<input type="hidden" id="param_trxdate" name="param_trxdate" />
	<input type="hidden" id="param_status" name="param_status" />
	<input type="hidden" id="param_mop" name="param_mop" />
	<input type="hidden" id="param_paid" name="param_paid" />
	<input type="text" id="param_driver" name="param_driver" />
</form>
<?php
$driverarray = array();
foreach($driverlist as $ind => $row){
	json_encode($driverarray[$row["id"]] = $row["name"]);
}
?>
<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/jquery.autocomplete.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/select2/dist/js/select2.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/moment/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>
<!-- iCheck -->
<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
	var orderids = JSON.parse('<?php print_r($orderids); ?>');
	var driverlist = JSON.parse('<?php echo json_encode($driverarray); ?>');
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
<script src="<?php echo base_url(); ?>assets/app/sortelement.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/orderlist.js"></script>

</body>
</html>
