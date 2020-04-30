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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/userlist.css">

</head>
<body>

<div id="container">
	<button id="cancel_userlist_btn" class="btn-danger pull-left lg-btn">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<span class="span_seperator pull-right"></span>
	<button id="create_user_btn" class="btn-success pull-right">
		<i class="fa fa-user-plus"></i> &nbsp;Add User
	</button>

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search for Name" id="search_user">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="userlist_table">
					<thead>
					<tr>
						<th>Username</th>
						<th>Name</th>
						<th>Access Level</th>
						<th hidden></th>
					</tr>
					</thead>
					<tbody>
						<?php
							$access_level = ["Admin", "Sales Agent"];
							foreach($userlist as $ind => $row){
								echo '<tr>';
								echo '<td>'.$row["username"].'</td>';
								echo '<td>'.$row["name"].'</td>';
								echo '<td>'.$access_level[$row["access_level"]].'</td>';
								echo '<td hidden>'.$row["password"].'</td>';
								echo '<td hidden>'.$row["id"].'</td>';
								echo '</tr>';
							}
						?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>


	<div id="create_user" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-user-plus"></i> Add User</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Username</label>
						<input type="text" class="form-control" id="username">
					</div>

					<div class="form-group">
						<label>Password</label>
						<div class="input-group">
							<input type="password" class="form-control" id="password">
							<div class="input-group-append">
								<button class="btn btn-outline-secondary show_password" id="show_password" type="button">
									<i class="fa fa-eye"></i>
								</button>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>Confirm Password</label>
						<div class="input-group">
							<input type="password" class="form-control" id="confirmpassword">
							<div class="input-group-append">
								<button class="btn btn-outline-secondary show_password" id="show_confirmpassword" type="button">
									<i class="fa fa-eye"></i>
								</button>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" id="name">
					</div>

					<div class="form-group">
						<label>Access Level</label>
						<select class="form-control" id="access_level">
							<option value="0">Admin</option>
							<option value="1">Sales Agent</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="save_user">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<div id="user_detail" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-user-plus"></i> Add User</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" class="form-control" id="old_password">
					<input type="hidden" class="form-control" id="userid">
					<div class="form-group">
						<label>Username</label>
						<input type="text" class="form-control" id="detail_username">
					</div>

					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" id="detail_name">
					</div>

					<div class="form-group">
						<label>Access Level</label>
						<select class="form-control" id="detail_access_level">
							<option value="0">Admin</option>
							<option value="1">Sales Agent</option>
						</select>
					</div>

					<div id="change_password_container">
						<div class="form-group">
							<label>Old Password</label>
							<div class="input-group">
								<input type="password" class="form-control not_required" id="detailPassword">
								<div class="input-group-append">
									<button class="btn btn-outline-secondary show_password" id="show_detailPassword" type="button">
										<i class="fa fa-eye"></i>
									</button>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label>New Password</label>
							<div class="input-group">
								<input type="password" class="form-control not_required" id="newpassword">
								<div class="input-group-append">
									<button class="btn btn-outline-secondary show_password" id="show_newpassword" type="button">
										<i class="fa fa-eye"></i>
									</button>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label>Confirm New Password</label>
							<div class="input-group">
								<input type="password" class="form-control not_required" id="cfrmnewPassword">
								<div class="input-group-append">
									<button class="btn btn-outline-secondary show_password" id="show_cfrmnewPassword" type="button">
										<i class="fa fa-eye"></i>
									</button>
								</div>
							</div>
						</div>
					</div><!-- change_password_container -->
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left" id="delete_user_btn">Delete</button>
					<button type="button" class="btn btn-warning pull-left" id="change_pass_btn">Change Password</button>
					<button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary pull-right" id="update_user">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<div id="delete_user_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #d82121;color: #f74242">
						<i class="fa fa-trash-o"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">DELETE USER</h4>
				<div class="modal-body">
					<p>Deleting user <b><span id="user_name"></span></b>. <br />Do you want to continue?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-success" id="confirm_delete_user">Yes</button>
				</div>
			</div>
		</div>
	</div>

</div>

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/popper.js"></script>
<script src="<?php echo base_url(); ?>assets/app/jquerymd5.js"></script>
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
<script src="<?php echo base_url(); ?>assets/app/userlist.js"></script>

</body>
</html>
