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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/dispatch.css">

</head>
<body>

<div id="container">
	<button id="cancel_dispatch_btn" class="btn-danger pull-left lg-btn">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<span class="span_seperator pull-right"></span>
	<button id="create_dispatch_btn" class="btn-success pull-right btn-lg">
		<i class="fa fa-user-plus"></i> &nbsp;New Dispatch
	</button>

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search for Name" id="search_driver">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="dispatch_table">
					<thead>
					<tr>
						<th hidden></th>
						<th>Datetime</th>
						<th>Driver</th>
						<th>Dispatch Date</th>
						<th>Status</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$statusarray = ["Pending", "For Delivery", "Delivered"];
					$statusclass = ["text-success", "text-warning", "text-info"];
					foreach($dispatchlist as $ind => $row){
						$st = $row["status"];
						echo '<tr>';
						echo '<td hidden>'.$row["id"].'</td>';
						echo '<td>'.date("m/d/Y H:i:s", strtotime($row["datetime"])).'</td>';
						echo '<td>'.$row["driver_name"].'</td>';
						echo '<td>'.date("m/d/Y", strtotime($row["dispatch_date"])).'</td>';
						echo '<td class="'.$statusclass[$st].'">'.$statusarray[$st].'</td>';
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
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
<script src="<?php echo base_url(); ?>assets/app/dispatch.js"></script>

</body>
</html>
