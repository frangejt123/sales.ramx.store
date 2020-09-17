<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>RAM-X</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css">
	<!-- Slimscroll -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/slimscroll.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/nprogress.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/driverlist.css">

</head>
<body>

<div id="container">
	<button id="cancel_driver_btn" class="btn-danger pull-left lg-btn">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<span class="span_seperator pull-right"></span>
	<button id="create_driver_btn" class="btn-success pull-right">
		<i class="fa fa-user-plus"></i> &nbsp;Add Driver
	</button>

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search for Name" id="search_driver">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="driverlist_table">
					<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($driverlist as $ind => $row){
						echo '<tr>';
						echo '<td>'.$row["id"].'</td>';
						echo '<td>'.$row["name"].'</td>';
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>


	<div id="create_driver" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-user-plus"></i> Add Driver</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" id="name">
					</div>
				</div>
				<div class="modal-footer" style="display:inline">
					<button type="button" class="btn btn-danger pull-left" id="delete_driver_btn">Delete</button>
					<button type="button" class="btn btn-primary pull-right" id="save_driver">Save changes</button>
					<button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div id="delete_driver_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #d82121;color: #f74242">
						<i class="fa fa-trash-o"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">DELETE DRIVER</h4>
				<div class="modal-body">
					<p>Do you delete <b><span id="driver_name"></span></b> as driver?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-success" id="confirm_delete_driver">Yes</button>
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
<script src="<?php echo base_url(); ?>assets/app/driverlist.js"></script>

</body>
</html>
