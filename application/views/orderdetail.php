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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/orderlist.css">

</head>
<body>

<div id="container">
	<input type="text" hidden id="selected_order" value="<?php echo $transaction["id"]; ?>">
	<button id="cancel_orderlist_btn" class="btn-danger">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<?php $paidClass = "visible"; if($_SESSION["access_level"] != 0){
		if ($transaction["status"] == 0 && $_SESSION["id"] == $transaction["user_id"]) {
	?>
		<button id="void_order_btn" class="btn-warning pull-right">
			<i class="fa fa-trash"></i> &nbsp; Void Order
		</button>
	<?php } } else {  ?>
		<button id="print_order_btn" class="btn-secondary pull-right">
			<i class="fa fa-print"></i> &nbsp; Print
		</button>

		<?php if($transaction["status"] == 0){ ?>
		<span class="pull-right span_seperator"></span>
		<button id="process_order_btn" class="btn-warning pull-right">
			<i class="fa fa-truck"></i> &nbsp; For Delivery
		</button>

		<?php if($transaction["paid"] != "1"){  $paidClass = "hidden"; ?>
			<span class="pull-right span_seperator paid_sep"></span>
			<button id="paid_order_btn" class="btn-dark pull-right">
				<i class="fa fa-credit-card"></i> &nbsp; Paid
			</button>

		<?php } }else if($transaction["status"] == 1){ ?>
		<span class="pull-right span_seperator"></span>
		<button id="complete_order_btn" class="btn-primary pull-right">
			<i class="fa fa-check"></i> &nbsp; Complete
		</button>
		<span class="pull-right span_seperator"></span>
		<button id="pending_order_btn" class="btn-success pull-right">
			<i class="fa fa-undo"></i> &nbsp; Pending
		</button>
	<?php } } ?>
	
	<?php
		if(($transaction["status"] == 0) && ($_SESSION["id"] == $transaction["user_id"] || $_SESSION["access_level"] == 0)){
	?>
		<span class="pull-right span_seperator"></span>
		<button id="update_order_btn" class="btn-info pull-right">
			<i class="fa fa-pencil"></i> &nbsp; Update
		</button>
	<?php } ?>

	<div style="clear:both"></div>

	<div class="void_notif">
		ORDER VOIDED BY SALES AGENT.
	</div>

	<div class="transaction_detail_container">
		<table id="table_trans_detail">
			<tr>
				<td>Customer Name : <?php echo $transaction["name"]; ?></td>
				<td class="sep"></td>
				<td>Date : <?php echo date("m/d/Y H:i:s", strtotime($transaction["datetime"])); ?></td>
			</tr>
			<tr>
				<td>Facebook Name : <?php echo $transaction["facebook_name"]; ?></td>
				<td class="sep"></td>
				<?php
					$paymentmethodarray = array("Cash on Delivery (COD)", "Bank Transfer", "GCash");
				?>
				<td>Payment Method : <?php echo $paymentmethodarray[$transaction["payment_method"]]; ?></td>
			</tr>
			<tr>
				<td>Contact Number : <?php echo $transaction["contact_number"]; ?></td>
				<td class="sep"></td>
				<td>Payment Confirmation Details : <?php echo $transaction["payment_confirmation_detail"]; ?></td>
			</tr>
			<tr>
				<td width="50%">Delivery Date : <?php echo date("m/d/Y", strtotime($transaction["delivery_date"])); ?></td>
				<td class="sep"></td>
				<td>Sales Agent : <?php echo $transaction["sales_agent"]; ?></td>
			</tr>
			<tr>
				<td>Delivery Address : <?php echo $transaction["delivery_address"]; ?></td>
				<td class="sep"></td>
				<td>Remarks : <?php echo $transaction["remarks"]; ?></td>
			</tr>
		</table>
	</div>

	<div class="grid_container">
		<div class="detail_grand_total pull-right">
			TOTAL : <?php echo number_format($transaction["total"], 2); ?>
		</div>
		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="orderdata_table">
					<thead>
					<tr>
						<th>Product Name</th>
						<th>Quantity</th>
						<th>Price</th>
						<th>Line Total</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($transactiondetail as $ind => $row){
						echo '<tr id="tr_'.$row["id"].'">';
						echo '<td>'.$row["description"].'</td>';
						echo '<td>'.$row["quantity"].'</td>';
						echo '<td>'.number_format($row["price"], 2).'</td>';
						echo '<td>'.number_format(($row["price"] * $row["quantity"]), 2).'</td>';
						echo '</tr>';
					}
					?>
					</tbody>
					</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>

	<div id="voiddetailmodal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #c01a25; color: #e53441">
						<i class="fa fa-trash"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">ORDED VOIDED</h4>
				<div class="modal-body">
					<p id="voidreason">Reason: <?php echo $transaction["void_reason"]; ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="close_void_detail">OK</button>
				</div>
			</div>
		</div>
	</div>

	<div id="statusmodal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box"></div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">UPDATE ORDER STATUS</h4>
				<div class="modal-body">
					<p>Update order status to <b>"<span id="ordernewstatus"></span>"</b>?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" id="cancel_change_status">No</button>
					<button type="button" class="btn btn-success" id="confirm_change_status">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div id="tag_as_paid_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #104675;color: #286090">
						<i class="fa fa-dollar"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">TAG AS PAID</h4>
				<div class="modal-body">
					<p>This order will be tag as PAID. <br />Do you want o continue?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" id="cancel_tag_as_paid">No</button>
					<button type="button" class="btn btn-success" id="confirm_tag_as_paid">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="void_detail_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">VOID ORDER</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Reason</label>
						<select class="form-control" id="void_reason_sel">
							<option value="0">Customer Cancel Order</option>
							<option value="1">Wrong Input</option>
							<option value="2">Other Reason</option>
						</select>
					</div>

					<div class="form-group">
						<label>Please specify</label>
						<textarea class="form-control" rows="3" id="other_void_reason" disabled></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-remove"></i> &nbsp;Close
					</button>
					<button type="button" class="btn btn-warning" id="confirm_void_order">
						<i class="fa fa-trash"></i> &nbsp;Void
					</button>
				</div>
			</div>
		</div>
	</div><!-- modal -->
</div>

<div id="paid_stamp" class="<?php echo $paidClass; ?>">
	<span class="stamp is-draft">Paid</span>
</div>

<form id="report_data" method="post" action="<?php echo base_url(); ?>index.php/report" target="new_window">
	<input type="hidden" id="trans_id" name="id" />
</form>

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/orderdetail.js"></script>

</body>
</html>
