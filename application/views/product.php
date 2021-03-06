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
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/<?php echo $pageicon; ?>" />
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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/product.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/croppie.css">

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
	<button id="cancel_userlist_btn" class="btn-danger pull-left lg-btn">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<button id="create_product_btn" class="btn-success pull-right">
		<i class="fa fa-cubes"></i> &nbsp;Add Product
	</button>
	<span class="span_seperator pull-right"></span>
	<button id="product_category_btn" class="btn-info pull-right">
		<i class="fa fa-navicon"></i> &nbsp;Product Category
	</button>

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search Product Name" id="search_product">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="productlist_table">
					<thead>
					<tr>
						<th class="sortable">Description <i class="fa fa-sort float-right"></i></th>
						<th>Measurement</th>
						<th class="sortable">Quantity <i class="fa fa-sort float-right"></i></th>
						<th>Price</th>
						<th class="sortable">Category <i class="fa fa-sort float-right"></i></th>
						<th hidden></th>
						<th hidden></th>
						<th hidden></th>
					</tr>
					</thead>
					<tbody>
					<?php

					foreach($product as $ind => $row){
						$cls = "";
						if($row["avail_qty"] == 0)
							$cls=' text-danger';
						echo '<tr data-image="'.$row["prod_img"].'">';
						echo '<td>'.$row["description"].'</td>';
						echo '<td>'.$row["uom"].'</td>';
						echo '<td class="'.$cls.'">'.number_format($row["avail_qty"], 2).'</td>';
						echo '<td>'.number_format($row["price"], 2).'</td>';
						echo '<td>'.$row["category"].'</td>';
						echo '<td hidden>'.$row["id"].'</td>';
						echo '<td hidden>'.$row["category_id"].'</td>';
						echo '<td hidden>'.$row["phase_out"].'</td>';
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>

	<div id="product_modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-cubes"></i> <span>Add Product<span></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" id="product_id" class="not-required">

					<div class="form-group">
						<label>Product Image</label>
						
						<div id="img_preview" class="detail_map_preview mx-auto" >
							<img id="map_img_preview" src=""  />
							<div class='text-center' id="upload-text"><i class="fa fa-camera"></i> Upload Image</div>
						</div>
						<input type='file' id="product_img" class='d-none form-control not-required' />
					</div>
					<div class="form-group">
						<label>Description</label>
						<input type="text" class="form-control" id="description">
					</div>


					<div class="form-group">
						<label>Unit of Measurement</label>
						<input type="text" class="form-control" id="uom">
					</div>

					<div class="form-group">
						<label>Price</label>
						<input type="text" class="form-control" id="price">
					</div>

					<div class="form-group">
						<label>Category</label>
						<select class="form-control" id="category">
							<?php
								foreach($category as $ind => $row){
									echo '<option value="'.$row["id"].'">'.$row["name"].'</option>';
								}
							?>
						</select>
					</div>

					<div class="form-group" id="phase_out_grp">
						<label></label>
						<div class="checkbox icheck">
							<label>
								<input type="checkbox" id="phase_out"> &nbsp; Phase Out
							</label>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="display: inline !important;">
					<button hidden type="button" class="btn btn-danger pull-left" id="delete_product">Delete</button>
					<button type="button" class="btn btn-primary pull-right" id="save_changes">Save changes</button>
					<button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">Close</button>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="delete_product_modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box" style="border: 3px solid #d82121;color: #f74242">
						<i class="fa fa-trash-o"></i>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<h4 class="modal-title">DELETE PRODUCT</h4>
				<div class="modal-body">
					<p>Deleting product <b><span id="product_name"></span></b>. <br />Do you want to continue?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-success" id="confirm_delete_product">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="category_list_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
						<h4 class="modal-title" id="exampleModalLabel">CATEGORY LIST</h4>
				</div>
				<div class="modal-body">
					<div class="form_container container-fluid">
						<input type="hidden" class="form-control" id="selected_category">
						<div class="row">
							<div class="col">
								<div class="form-group">
									<label>Description</label>
									<input type="text" class="form-control" id="category_description">
								</div>
							</div>
						</div>
					</div>

					<div class="form_button_container">
						<button type="button" class="btn btn-danger pull-left" id="delete_category_btn">
							<i class="fa fa-trash"></i>  Delete
						</button>
						<button type="button" class="btn btn-danger pull-left" id="undo_delete_btn">
							<i class="fa fa-undo"></i>  Undo
						</button>

						<button type="button" class="btn btn-success pull-right" id="add_new_category_btn">
							<i class="fa fa-plus"></i> Add
						</button>
						<button type="button" class="btn btn-success pull-right" id="update_category_btn">
							<i class="fa fa-pencil"></i> Update
						</button>
						<span class="pull-right span_seperator"></span>
						<button type="button" class="btn btn-secondary pull-right" id="clear_category_btn">
							<i class="fa fa-remove"></i> Clear
						</button>
						<div style="clear:both"></div>
					</div>

					<div id="category_container">
						<div class="box">
							<div class="box-body no-padding">
								<table class="table table-striped table-hover" id="category_table">
									<thead>
									<tr>
										<th>Description</th>
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
					<button type="button" class="btn btn-primary" id="save_category_changes">Save changes</button>
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

	var baseURL = "<?= base_url() ?>"
	var siteURL = "<?= site_url() ?>"
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
<script src="<?php echo base_url(); ?>assets/app/croppie.js"></script>
<script src="<?php echo base_url(); ?>assets/app/product.js"></script>

</body>
</html>
