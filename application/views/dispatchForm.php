<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$pagetitle = "RAM-X";
$pageicon = "favicon.jpg";
if($store_id == "2"){
	$pagetitle = "RIBSHACK";
	$pageicon = "favicon2.jpg";
}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $pagetitle; ?></title>
	<meta name="viewport" content=" user-scalable=0"/>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>/assets/app/img/<?php echo $pageicon; ?>" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css">
	<!-- Slimscroll -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/slimscroll.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/croppie.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/dispatch.css">
	<script>
		function resizeIframe(obj) {
			obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
		}
	</script>
</head>
<body>

<div id="container">
	<button id="cancel_dispatchdetail_btn" class="btn-danger pull-left lg-btn">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<button id="save_dispatchdetail_btn" class="btn-info pull-right lg-btn">
		<i class="fa fa-save"></i> &nbsp; Save Changes
	</button>

	<div style="clear:both"></div>
	<br />
	<div class="container-fluid">
		<form class="" id="dispatch_detail_form" method="post">
			<div class="form-group row">
				<input type="hidden" id="driver_id" value="" />
				<label class="col-sm-1 col-form-label">Driver Name: </label>
				<div class="col-sm-4">
					<div class="form-group trx_driver filter_param">
						<div style="position: relative; height: 34px;">
							<input type="text" class="form-control driver_id" id="driver_id_autocomplete" style="position: absolute; z-index: 2; background: transparent;" value="">
							<input type="text" class="form-control driver_autocomplete_hint" disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
						</div>
						<!-- /.input group -->
					</div>
				</div>
				<label class="col-sm-2 col-form-label" style="text-align: right">Dispatch Date : </label>
				<div class="col-sm-3">
					<input type="date" class="form-control" id="dispatch_date" value="" />
				</div>
			</div>
		</form>
	</div>

	<div class="row">
		<div class="col-3"><!-- left -->
			<div class="grid_container">
				<div class="box">
					<div class="box-body pt-2 border">
						<div class="undo_delete_div" style="display: none;">
							<span class="span_seperator pull-left"></span>
							<button id="undo_dispatchdetail_btn" class="btn-info pull-left sm-btn">
								<i class="fa fa-undo"></i>
							</button>
						</div>

						<span class="span_seperator pull-right"></span>
						<button id="delete_dispatchdetail_btn" class="btn-danger pull-right sm-btn disabled">
							<i class="fa fa-trash"></i>
						</button>
						<span class="span_seperator pull-right"></span>
						<button id="add_dispatchdetail_btn" class="btn-success pull-right sm-btn">
							<i class="fa fa-plus"></i>
						</button>
						<div style="clear:both"></div>

						<table class="table table-striped table-hover" id="dispatchdetail_table">
							<thead>
							<tr>
								<th hidden></th>
								<th>Transaction ID</th>
								<th>Customer</th>
							</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
			</div>
		</div><!-- left -->


		<div class="col-9"><!-- right -->
			<iframe src="" frameborder="0" scrolling="no" onload="resizeIframe(this)" width="100%" id="detailIframe"></iframe>
		</div><!-- right -->
	</div><!-- row -->

</div>

<div id="statusmodal" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="icon-box"></div>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<h4 class="modal-title">UPDATE DISPATCH STATUS</h4>
			<div class="modal-body">
				<p>Update status to <b>"<span id="dispatchnewstatus"></span>"</b>?<br />
					This action cannot be undone.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" >No</button>
				<button type="button" class="btn btn-success" id="confirm_change_status">Yes</button>
			</div>
		</div>
	</div>
</div>

<div id="dispatchdetailmodal" class="modal fade">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<div class="icon-box"></div>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<form class="" id="transaction_detail_form" method="post">
					<input type="hidden" id="transaction_id" value="" />
					<label class="col-form-label">Transaction #: </label>
					<div class="form-group trx_driver filter_param">
						<div style="position: relative; height: 34px;">
							<input type="text" class="form-control transaction_id" id="transaction_id_autocomplete" style="position: absolute; z-index: 2; background: transparent;" value="">
							<input type="text" class="form-control transaction_autocomplete_hint" disabled style="color: #CCC; position: absolute; background: transparent; z-index: 1;">
						</div>
						<!-- /.input group -->
					</div>
				</form>
				<br />
				<iframe src="" frameborder="0" scrolling="no" onload="resizeIframe(this)" width="100%" id="trxdetailIframe"></iframe>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal" >Cancel</button>
				<button type="button" class="btn btn-success" id="add_trx_to_detail">Add</button>
			</div>
		</div>
	</div>
</div>
<?php
$driverarray = array();
foreach($driverlist as $ind => $row){
	json_encode($driverarray[$row["id"]] = $row["name"]);
}
$trxids = array();
$custids = array();
foreach($trx as $ind => $row){
	$transdate = date("mdY", strtotime($row["datetime"]));
	$formatID = $transdate.'-'.sprintf("%04s", $row["id"]);
	json_encode($trxids[$row["id"]] = $formatID);
	json_encode($custids[$row["id"]] = $row["name"]);
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
	var driverlist = JSON.parse('<?php echo json_encode($driverarray); ?>');
	var trxlist = JSON.parse('<?php echo json_encode($trxids); ?>');
	var customerlist = JSON.parse('<?php echo json_encode($custids); ?>');
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/nprogress.js"></script>
<script src="<?php echo base_url(); ?>assets/app/dispatchdetail.js"></script>

</body>
</html>
