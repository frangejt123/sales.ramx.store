<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$btnvoid = '<span class="pull-right span_seperator"></span><button id="void_order_btn" class="btn-warning pull-right">'.
				'<i class="fa fa-trash"></i> &nbsp; Void Order</button>';

/*$btnprint = '<span class="pull-right span_seperator"></span><button id="print_order_btn" class="btn-secondary pull-right">'.
				'<i class="fa fa-print"></i> &nbsp; Print</button>';*/

$btnpaid = '<span class="pull-right span_seperator"></span><button id="paid_order_btn" class="btn-dark pull-right">'.
				'<i class="fa fa-credit-card"></i> &nbsp; <span>Paid</span></button>';

$btnunpaid = '<span class="pull-right span_seperator"></span><button id="unpaid_order_btn" class="btn-danger pull-right">'.
		'<i class="fa fa-credit-card"></i> &nbsp; <span>Unpaid</span></button>';

$btnupdate = '<span class="pull-right span_seperator"></span><button id="update_order_btn" class="btn-info pull-right">'.
				'<i class="fa fa-pencil"></i> &nbsp; Update</button>';

$btnreconcile = '<span class="pull-right span_seperator"></span>'.
				'<button id="reconcile_order_btn" class="btn-primary pull-right">'.
					'<i class="fa fa-balance-scale"></i> &nbsp; Reconcile</button>';

$printbtn = '';
if($_SESSION["access_level"] == 0) //admin
	$printbtn = '<a class="dropdown-item dd-item text-success" href="#" id="print_order_btn"><i class="fa fa-print"></i> &nbsp; Print</a>';

$btnmoreaction = '<span class="pull-right span_seperator"></span>'.
					'<div class="dropdown pull-right detail_action">'.
					'<button class="btn-secondary" data-toggle="dropdown" id="dropdown_btn"><i class="fa fa-bars"></i></button>'.
					'<div class="dropdown-menu">'.
						'<a class="dropdown-item dd-item text-primary" href="#" id="order_history_btn"><i class="fa fa-clock-o"></i> &nbsp; Order History</a>'.
						'<a class="dropdown-item dd-item text-warning" href="#" id="payment_history_btn"><i class="fa fa-dollar"></i> &nbsp; Payment History</a>'.
						$printbtn.
					'</div>'.
					'</div>';

$paidClass = "visible";

$btnarray = [ //for status dropdown btn
		"btn"=> [
				'<i class="fa fa-star-half-o"></i>',
				'<i class="fa fa-truck"></i>',
				'<i class="fa fa-check"></i>',
				'', //void
				'<i class="fa fa-truck fa-flip-horizontal"></i>'
		],
		"text"=> [
				'Pending',
				'For Delivery',
				'Complete',
				'', //void
				'Delivered'
		],
		"class"=> [
				'btn-success',
				'btn-warning',
				'btn-primary',
				'', //void
				'btn-info'
		]
];

$dropdownmenu = '';
if($transaction["status"] == 0)//pending
	$dropdownmenu .= '<a class="dropdown-item dd-item text-warning" href="#" id="process_order_btn"><i class="fa fa-truck"></i> &nbsp; For Delivery</a>';

if($transaction["status"] == 1){//for delivery
	$dropdownmenu .= '<a class="dropdown-item dd-item text-success" href="#" id="pending_order_btn"><i class="fa fa-star-half-o"></i> &nbsp; Pending</a>'.
						'<a class="dropdown-item dd-item text-info" href="#" id="delivered_order_btn"><i class="fa fa-truck fa-flip-horizontal"></i> &nbsp; Delivered</a>';
}

if($transaction["status"] == 4) {//delivered
	$dropdownmenu .= '<a class="dropdown-item dd-item text-primary" href="#" id="complete_order_btn"><i class="fa fa-check"></i> &nbsp; Completed</a>';
}

$btnstatus = '<span class="pull-right span_seperator"></span>'.
				'<div class="dropdown pull-right">'.
					'<button id="status_btn" class="'.$btnarray["class"][$transaction["status"]] .'" data-toggle="dropdown">'.
						$btnarray["btn"][$transaction["status"]].' &nbsp; '.$btnarray["text"][$transaction["status"]].
					'</button>'.
					'<div class="dropdown-menu">'.
						$dropdownmenu.
					'</div>'.
				'</div>';

$delivery_date = date("m/d/Y", strtotime($transaction["delivery_date"]));
$date_delivered = $transaction["date_delivered"];
if(!is_null($date_delivered))
	$date_delivered = date("m/d/Y", strtotime($transaction["date_delivered"]));

$pagetitle = "RAM-X";
$pageicon = "favicon.jpg";
if($store_id == "2"){
	$pagetitle = "RIBSHACK";
	$pageicon = "favicon2.jpg";

	$btnstatus = "";
}

function getUser($row) {
	if($row["user_type"] == 'User') {
		return $row["user_name"];
	} else {
		return 'Customer Encoded';
	}
}

function getSalesAgent($transaction) {
	if(empty($transaction["sales_agent"])  && empty($transaction["user_id"]) ) {
		return 'CUSTOMER ENCODED';
	} else {
		return $transaction["sales_agent"];
	}
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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/croppie.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/orderlist.css">

</head>
<body>
<header>
  <!-- Fixed navbar -->
  <nav class="navbar navbar-expand navbar-dark fixed-top bg-dark">
    <a class="navbar-brand" href="#">
		<img src="<?=base_url()?>assets/app/img/favicon.png" width="20"  />
		<?=$this->config->item('branch') ?>
	</a>

    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#">	<span<b><i class="fa fa-user"></i> &nbsp; <?php echo $_SESSION["name"]; ?></b></span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#">|</a>
        </li>
        <li class="nav-item">
          <a class="nav-link " id="logout" href="#" tabindex="-1" aria-disabled="true">Logout</a>
        </li>
      </ul>
     
    </div>
  </nav>
</header>
<div id="container" class='mt-5'>

	<input type="text" hidden id="selected_order" value="<?php echo $transaction["id"]; ?>">
	<?php
		if($transaction["paid"] == "0")
			$paidClass = "hidden";

		if(!isset($nobutton)){
	?>

	<button id="cancel_orderlist_btn" class="btn-danger pull-left">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<?php
		if($_SESSION["access_level"] == 1) { // sales agent
			echo $btnmoreaction;//$btnprint;

			if($transaction["paid"] == "0") {

				echo $btnpaid;

				if (($transaction["status"] == 0)) {// && $_SESSION["id"] == $transaction["user_id"]
					echo $btnvoid;
					echo $btnupdate;
				}//if logged-in agent = agent created the order && order is not paid
			}


		}// access level = 1;

		if($_SESSION["access_level"] == 0) { //admin
			echo $btnmoreaction;//$btnprint;

			if ($transaction["status"] != "3") {

				if ($transaction["paid"] == "0") {
					if ($transaction["status"] == 0)
						echo $btnvoid;
					echo $btnpaid;
				}//if not paid
				else{
					echo $btnunpaid;
				}

				echo $btnstatus;
			}//if not void

			if ($transaction["status"] == "0" && $transaction["paid"] == "0") {
				echo $btnupdate;
			}//if status is Pending
		}

		if($transaction["status"] == "4" && $transaction["paid"] == "1" && $transaction["reconcile"] == "0"){
			echo $btnreconcile;
		}

		}//if !isset(no button)

		$total_payment = 0;
		foreach($paymenthistory as $ind => $row){
			$total_payment += $row["amount"];
		}
		$balance = $transaction["total"] - $total_payment;

		$transdate = date("mdY", strtotime($transaction["datetime"]));
		$ordernumber = $transdate.'-'.sprintf("%04s", $transaction["id"]);
	?>

	<input type="text" hidden id="balance" value="<?php echo $balance; ?>">
	<div style="clear:both"></div>

	<div class="void_notif">
		ORDER VOIDED BY <span><?php echo $transaction["void_user"]; ?></span>
	</div>

	<div class="transaction_detail_container">
		<table id="table_trans_detail">
			<tr>

				<td>Order # : <?php echo $ordernumber; ?></td>
				<td class="sep"></td>
				<td>Datetime : <?php echo date("m/d/Y H:i:s", strtotime($transaction["datetime"])); ?></td>
			</tr>
			<tr>
				<td>Customer Name : <?php echo $transaction["name"]; ?></td>
				<td class="sep"></td>
				<td>Driver : <?php echo $transaction["driver_name"]; ?></td>
			</tr>
			<tr>
				<td>Facebook Name : <?php echo $transaction["facebook_name"]; ?></td>
				<td class="sep"></td>
				<?php
					$paymentmethodarray = array("Cash on Delivery (COD)", "Bank Transfer - BPI", "GCash", "Bank Transfer - Metrobank", "Check");
				?>
				<td>
					Payment Method : <?php echo $paymentmethodarray[$transaction["payment_method"]]; ?>
				</td>
			</tr>
			<tr>
				<td>Contact Number : <?php echo $transaction["contact_number"]; ?></td>
				<td class="sep"></td>
				<td>Payment Confirmation Details : <?php echo $transaction["payment_confirmation_detail"]; ?></td>
			</tr>
			<tr>
				<td width="50%">Delivery Date : <?php echo $delivery_date;
					if(!is_null($date_delivered))
						echo ' &mdash;&mdash; Date Delivered : '.$date_delivered; ?>
				</td>
				<td class="sep"></td>
				<td>Sales Agent : <?php echo getSalesAgent($transaction) ; ?></td>
			</tr>
			<tr>
				<td>City : <?php echo $transaction["city"]; ?></td>
				<td class="sep"></td>
				<td>Remarks : <?php echo $transaction["remarks"]; ?></td>
			</tr>
			<tr>
				<td>Delivery Address : <?php echo $transaction["delivery_address"]; ?></td>
			</tr>
		</table>
	</div>

	<div class="grid_container">
		<div id="date_printed" class="pull-left" style="padding-top: 15px;">
				<?php if($transaction["printed"] == 1){ ?>
					<span class="text-success">
							<i class="fa fa-print"></i> &nbsp;Printed &mdash; <?php echo date("m/d/Y H:i:s", strtotime($transaction["date_printed"])); ?>
					</span>
				<?php
				}else if($transaction["printed"] == 2){//revised ?>
					<span class="text-warning">
						<i class="fa fa-undo"></i> &nbsp;Revised &mdash; <?php echo date("m/d/Y H:i:s", strtotime($transaction["date_revised"])); ?>
					</span>
				<?php }else if($transaction["printed"] == 3){//updated ?>
					<span class="text-info">
						<i class="fa fa-pencil"></i> &nbsp;Updated &mdash; <?php echo date("m/d/Y H:i:s", strtotime($transaction["date_revised"])); ?>
					</span>
				<?php } ?>
		</div>
		<?php if($transaction["reconcile"] == 1){ ?>
		<div style="clear:both"></div>
		<div id="reconciled_label" class="pull-left" style="padding-top: 15px;">
				<span class="text-primary">
					<i class="fa fa-balance-scale"></i> &nbsp;Reconciled
				</span>
		</div>
		<?php } ?>
		<div class="detail_grand_total pull-right">
			TOTAL : <?php echo number_format($transaction["total"], 2); ?>
		</div>
		<div style="clear:both"></div>
		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="orderdata_table">
					<thead>
					<tr>
						<th>Product Name</th>
						<th>UoM</th>
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
						echo '<td>'.$row["uom"].'</td>';
						echo '<td>'.$row["quantity"].'</td>';
						echo '<td>'.number_format(($row["total_price"] / $row["quantity"]), 2).'</td>';
						echo '<td>'.number_format($row["total_price"], 2).'</td>';
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
					<button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
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
					<div class="form-group" id="driver_name_grp">
						<label>Select Driver</label>
						<input type="hidden" id="driver_id">
						<div style="position: relative; height: 34px;">
							<input type="text" class="form-control" id="driver_filter" style="position: absolute; z-index: 2; background: transparent;">
							<input type="text" class="form-control" id="driver_autocomplete_hint" disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
						</div>
					</div>

					<div class="form-group" id="date_delivered_grp">
						<label>Date Delivered</label>
						<input type="date" class="form-control" id="date_delivered" value="<?php echo $transaction["delivery_date"]; ?>">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" id="cancel_change_status">No</button>
					<button type="button" class="btn btn-success" id="confirm_change_status">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div id="tag_as_paid_modal" class="modal fade">
		<div class="modal-dialog modal-fixed-width modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #104675;color: #286090">
						<i class="fa fa-dollar"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">TAG AS PAID</h4>
				<div class="modal-body" style="text-align: left">
					<div class="row">
						<div class="col-5">
							<div class="form-group">
								<label>Mode of Payment</label>
								<select class="form-control sel_mode_of_payment" id="mode_of_payment">
									<?php $pm = $transaction["payment_method"]; ?>
									<option value="0" <?php echo $pm == 0 ? 'selected="selected"' : ''; ?>>Cash On Delivery (COD)</option>
									<option value="1" <?php echo $pm == 1 ? 'selected="selected"' : ''; ?>>Bank Transfer - BPI</option>
									<option value="3" <?php echo $pm == 3 ? 'selected="selected"' : ''; ?>>Bank Transfer - Metrobank</option>
									<option value="2" <?php echo $pm == 2 ? 'selected="selected"' : ''; ?>>GCash</option>
									<option value="4" <?php echo $pm == 4 ? 'selected="selected"' : ''; ?>>Check</option>
								</select>
							</div>

							<div class="form-group">
								<label>Amount</label>
								<input type="text" class="form-control" id="paid_amount" value="<?php echo $balance; ?>">
							</div>

							<div class="form-group">
								<label>Payment Confirmation Detail</label>
								<textarea class="form-control" rows="5" id="payment_confirmation_detail"><?php echo $pm = $transaction["payment_confirmation_detail"]; ?></textarea>
							</div>


							<div id="payment_check_detail">

								<div class="form-group">
									<label>Bank Name</label>
									<input type="text" class="form-control" id="chk_bank_name" value="">
								</div>
								<div class="form-group">
									<label>Account #</label>
									<input type="text" class="form-control" id="chk_acc_num" value="">
								</div>
								<div class="form-group">
									<label>Account Name</label>
									<input type="text" class="form-control" id="chk_acc_name" value="">
								</div>
								<div class="form-group">
									<label>Check #</label>
									<input type="text" class="form-control" id="chk_num" value="">
								</div>

							</div><!-- check detail -->


						</div><!-- left -->
						<div class="col-7">
							<div class="form-group">
								<input type='file' id="payment_proof" />
								<div id="payment_preview" class="payment_image">
									<img id="payment_img_preview" src="" alt="Proof of Payment" />
								</div>
							</div>
						</div><!-- right -->
					</div>
				</div>
				<div style="clear:both"></div>
				<div class="modal-footer" style="padding-top: 30px;padding-bottom: 0px;">
					<button type="button" class="btn btn-info" id="cancel_tag_as_paid">Cancel</button>
					<button type="button" class="btn btn-success" id="confirm_tag_as_paid">Save</button>
				</div>
			</div>
		</div>
	</div>

	<div id="unpaid_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #d82121;color: #f74242">
						<i class="fa fa-dollar"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">TAG AS UNPAID</h4>
				<div class="modal-body">
					<p>This order will be tag as UNPAID. <br />Do you want to continue?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" id="cancel_unpaid">No</button>
					<button type="button" class="btn btn-success" id="confirm_unpaid">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div id="reconcile_order" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #0e55b5;color: #2574e0">
						<i class="fa fa-balance-scale"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">TAG AS RECONCILED</h4>
				<div class="modal-body">
					<p>This order will be tag as RECONCILED. <br />Do you want to continue?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-success" id="confirm_reconcile">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="payment_history_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">PAYMENT HISTORY</h4>
				</div>
				<div class="modal-body">
					<div class="box">
						<div class="box-body no-padding">
							<table class="table table-striped table-hover" id="paymenthistory_table">
								<thead>
								<tr>
									<th>Date</th>
									<th>User</th>
									<th>Payment Method</th>
									<th>Amount</th>
									<th>Payment Confirmation Detail</th>
									<th></th>
									<th hidden></th>
									<th hidden></th>
								</tr>
								</thead>
								<tbody>
								<?php
								foreach($paymenthistory as $ind => $row){
									$actbtn = '<button id="edit_'.$row["id"].'" type="button" class="btn btn-secondary grid-btn edit_payment">
													<i class="fa fa-pencil"></i>
												</button> &nbsp; 
												<button id="delete_'.$row["id"].'" type="button" class="btn btn-danger grid-btn delete_payment">
													<i class="fa fa-trash-o"></i>
												</button>';
									if($_SESSION["access_level"] == 1)
										$actbtn = "";
									echo '<tr id="tr_'.$row["id"].'" class="payment_history_tr">';
									echo '<td>'.date("m/d/Y", strtotime($row["payment_date"])).'</td>';
									echo '<td>'.$row["user_name"].'</td>';
									echo '<td>'.$paymentmethodarray[$row["payment_method"]].'</td>';
									echo '<td>'.number_format($row["amount"], 2).'</td>';
									echo '<td>'.$row["payment_confirmation_detail"].'</td>';
									echo '<td width="120px">'.$actbtn.'</td>';
									echo '<td hidden>'.$row["payment_method"].'</td>';
									echo '<td hidden>'.$row["image_name"].'</td>';
									echo '</tr>';
								}
								?>
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-remove"></i> &nbsp;Close
					</button>
				</div>
			</div>
		</div>
	</div><!-- modal -->
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

	<div id="update_payment_modal" class="modal fade">
		<div class="modal-dialog modal-fixed-width modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #104675;color: #286090">
						<i class="fa fa-dollar"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">UPDATE PAYMENT</h4>
				<div class="modal-body" style="text-align: left">
					<div class="row">
						<div class="col-5">
							<div class="form-group">
								<label>Mode of Payment</label>
								<select class="form-control sel_mode_of_payment" id="update_mode_of_payment">
									<option value="0">Cash On Delivery (COD)</option>
									<option value="1">Bank Transfer - BPI</option>
									<option value="3">Bank Transfer - Metrobank</option>
									<option value="2">GCash</option>
									<option value="4">Check</option>
								</select>
							</div>
							<div class="form-group">
								<label>Amount</label>
								<input type="text" class="form-control" id="update_paid_amount" value="">
							</div>
							<div class="form-group">
								<label>Payment Confirmation Detail</label>
								<textarea class="form-control" rows="2" id="update_payment_confirmation_detail"></textarea>
							</div>

							<div id="payment_check_detail_update">

								<div class="form-group">
									<label>Bank Name</label>
									<input type="text" class="form-control" id="update_bank" value="">
								</div>
								<div class="form-group">
									<label>Account #</label>
									<input type="text" class="form-control" id="update_accnt_num" value="">
								</div>
								<div class="form-group">
									<label>Account Name</label>
									<input type="text" class="form-control" id="update_accnt_name" value="">
								</div>
								<div class="form-group">
									<label>Check #</label>
									<input type="text" class="form-control" id="update_check_num" value="">
								</div>

							</div><!-- check detail -->

						</div>
						<div class="col-7">
							<div class="form-group">
								<input type='file' id="payment_proof_update" />
								<div id="payment_preview_update" class="payment_image_update">
									<img id="payment_img_preview_update" src="" alt="Proof of Payment" />
								</div>
							</div>
						</div><!-- right -->
					</div><!-- left -->
				</div>
				<div class="modal-footer" style="padding-top: 30px;padding-bottom: 0px;">
					<button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-success" id="confirm_updatepayment">Update</button>
				</div>
			</div>
		</div>
	</div>

	<div id="delete_payment_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #d82121;color: #f74242">
						<i class="fa fa-trash-o"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">DELETE PAYEMENT</h4>
				<div class="modal-body">
					<p>Are you sure you want to delete this payment?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-success" id="confirm_delete_payment">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="order_history_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">ORDER HISTORY</h4>
				</div>
				<div class="modal-body">
					<div class="box">
						<div class="box-body no-padding">
							<table class="table table-striped table-hover" id="paymenthistory_table">
								<thead>
								<tr>
									<th>Date</th>
									<th>User</th>
									<th>Action</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<?php
								foreach($orderhistory as $ind => $row){
									echo '<tr id="tr_'.$row["id"].'" class="order_history_tr">';
									echo '<td>'.date("m/d/Y H:i:s", strtotime($row["created_at"])).'</td>';
									echo '<td>'. getUser($row).'</td>';
									echo '<td>'.ucfirst($row["event"]).'</td>';
									echo '<td width="120px"><button id="historydetail_'.$row["id"].'" type="button" class="btn btn-secondary grid-btn view_history_detail">
													Details
												</button>
											</td>';
									echo '</tr>';
								}
								?>
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fa fa-remove"></i> &nbsp;Close
					</button>
				</div>
			</div>
		</div>
	</div><!-- modal -->

	<div class="modal fade" id="history_detail_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">ORDER HISTORY DETAILS</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-6">
								<div class="card">
									<div class="card-header">
										OLD VALUES
									</div>
									<?php
										foreach($orderhistory as $ind => $row){
									?>
									<div class="card-body history-table" id="table_old_<?php echo $row["id"]; ?>">
										<h5 class="card-title">TRANSACTION</h5>
										<table class="table table-striped table-hover" style="font-size: 10px;font-family: Courier">
											<thead>
											<tr>
												<th>Field Name</th>
												<th>Value</th>
											</tr>
											</thead>
											<tbody>
											<?php
													$rows = json_decode($row["old_values"], true);

													if(isset($rows["transaction"]))
														foreach($rows["transaction"] as $tind => $trow){
															if($tind == "total")
																$trow = number_format($trow, 2);
															if($tind == "delivery_date")
																$trow = date("m/d/Y", strtotime($trow));
															echo '<tr id="tr_'.$row["id"].'">';
															echo '<td>'.strtoupper($tind).'</td>';
															echo '<td>'.$trow.'</td>';
															echo '</tr>';
														}
											?>
											</tbody>
										</table>
									</div>
									<div class="card-body history-table" id="table_detail_old_<?php echo $row["id"]; ?>">
										<h5 class="card-title">TRANSACTION DETAIL</h5>
										<table class="table table-striped table-hover" style="font-size: 10px;font-family: Courier">
											<thead>
											<tr>
												<th>Name</th>
												<th>Quantity</th>
												<th>Price</th>
											</tr>
											</thead>
											<tbody>
											<?php
											$rows = json_decode($row["old_values"], true);

											if(isset($rows["transaction_detail"]))
												foreach($rows["transaction_detail"] as $tind => $trow){
													echo '<tr>';
													echo '<td>'.$trow["name"].'</td>';
													echo '<td>'.$trow["quantity"].'</td>';
													echo '<td>'.number_format($trow["total_price"], 2).'</td>';
													echo '</tr>';
												}
											?>
											</tbody>
										</table>
									</div>
									<?php
										}
									?>
								</div>
							</div><!-- col 6 / left panel -->
							<div class="col-md-6">
								<div class="card">
									<div class="card-header">
										NEW VALUES
									</div>
									<?php
										foreach($orderhistory as $ind => $row){
									?>
									<div class="card-body history-table" id="table_new_<?php echo $row["id"]; ?>">
										<h5 class="card-title">TRANSACTION</h5>
										<table class="table table-striped table-hover" style="font-size: 10px;font-family: Courier">
											<thead>
											<tr>
												<th>Field Name</th>
												<th>Value</th>
											</tr>
											</thead>
											<tbody>
											<?php
												$rows = json_decode($row["new_values"], true);

												if(isset($rows["transaction"]))
													foreach($rows["transaction"] as $tind => $trow){
														if($tind == "total")
															$trow = number_format($trow, 2);
														if($tind == "delivery_date")
															$trow = date("m/d/Y", strtotime($trow));
														echo '<tr id="tr_'.$row["id"].'">';
														echo '<td>'.strtoupper($tind).'</td>';
														echo '<td>'.$trow.'</td>';
														echo '</tr>';
													}
											?>
											</tbody>
										</table>
									</div>
									<div class="card-body history-table" id="table_detail_new_<?php echo $row["id"]; ?>">
										<h5 class="card-title">TRANSACTION DETAIL</h5>
										<table class="table table-striped table-hover" style="font-size: 10px;font-family: Courier">
											<thead>
											<tr>
												<th>Name</th>
												<th>Quantity</th>
												<th>Price</th>
											</tr>
											</thead>
											<tbody>
											<?php
											$rows = json_decode($row["new_values"], true);

											if(isset($rows["transaction_detail"]))
												foreach($rows["transaction_detail"] as $tind => $trow){
													echo '<tr>';
													echo '<td>'.$trow["name"].'</td>';
													echo '<td>'.$trow["quantity"].'</td>';
													echo '<td>'.number_format($trow["total_price"], 2).'</td>';
													echo '</tr>';
												}
											?>
											</tbody>
										</table>
									</div>
									<?php } ?>
								</div>
							</div><!-- col 6 / right panel -->
						</div><!-- row -->
					</div>

				</div><!-- modal body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

</div>

<div id="paid_stamp" class="<?php echo $paidClass; ?>">
	<span class="stamp is-draft">Paid</span>
</div>

<form id="report_data" method="post" action="<?php echo base_url(); ?>index.php/report" target="new_window">
	<input type="hidden" id="trans_id" name="id" />
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
<script src="<?php echo base_url(); ?>assets/app/croppie.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
	var transmop = '<?php echo $transaction["payment_method"]; ?>';
	var transpcd = '<?php echo preg_replace("/\r\n|\r|\n/",'<br/>', $transaction["payment_confirmation_detail"]); ?>';
	var driverlist = JSON.parse('<?php echo json_encode($driverarray); ?>');
	var checkdetail = JSON.parse('<?php echo json_encode($checkdetail); ?>');
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<?php
	if(!isset($nobutton)){
?>
<script src="<?php echo base_url(); ?>assets/app/orderdetail.js"></script>
<?php } ?>

</body>
</html>
