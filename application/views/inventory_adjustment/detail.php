<?php
	include_once("layouts/header.php");

	$status_txt = array("", "Pending", "Approved");
	$status_color = array("", "text-success", 'text-primary');
	if(isset($adjustment)) {
	
		$id = $adjustment["id"];
		$status = $adjustment["status"];
		$date = $adjustment["date"];
		$type = $adjustment["type"];
		$remarks = $adjustment["remarks"];
		$prepared_by =  isset($prep_by) ? $prep_by["username"] : "" ;
		$approved_by = isset($app_by) ? $app_by["username"] : "" ;
		$btn_class = "";
	} else {
		$id = "";
		$status = "1";
		$date = "";
		$type= "";
		$remarks = "";
		$prepared_by = "";
		$approved_by = "";
		$btn_class = "hidden";
	}
?>

<div id="container">
	<button class="btn-danger lg-btn routing-btn" data-route-to="/inventory/adjustment">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>


	<button id="save-btn" class="btn-success pull-right lg-btn <?=$status==1 ? '' : 'hidden' ?>">
			<i class="fa fa-save"></i> &nbsp; Save
	</button>
	<span class="pull-right span_seperator"></span>


	<button id="approve-btn" class="btn-primary pull-right lg-btn <?= $btn_class ?>" data-toggle="modal" data-target="#approve-modal">
			<i class="fa fa-thumbs-up"></i> &nbsp; Approve
	</button>
	<span class="pull-right span_seperator"></span>
	<button id="delete-btn" class="btn-danger pull-right lg-btn <?= $btn_class ?>" data-toggle="modal" data-target="#delete-modal">
			<i class="fa fa-trash"></i> &nbsp; Delete
	</button>


	<div style="clear:both"></div>


	<div class="transaction_detail_container">
		<form class="form-inline" id="form" method="post">
			<table id="table_trans_detail">
				<tr>
					<td><label>Inventory Adjustment # : </label> 	
					<input type="text" class="form-control form-readonly" name="id" value="<?php echo $id ?>"" id="adjustment_id" readonly placeholder="#NEW#"></td>
					<td class="sep"></td>
					<td> 
						<label>Type : </label> 
						<select class="form-control" id="type" name="type" value="<?=$type?>" required>
								<option value="1">IN</option>
								<option value="2">OUT</option>
						</select>
					</td>
					<td class="sep">
					</td>
					<td>
						<div class="form-group">
							<label for="date">Date : </label>
							<input type="date" class="form-control" value="<?=$date?>" name="date" id="date" required>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label for="status">Status : </label>
							<span id="status-display" class='<?=$status_color[$status] ?>'><?=$status_txt[$status] ?></span>
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<div class="grid_container">
		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="adjustment-detail-table">
					<thead>
					<tr>
						<th>Product Name</th>
						<th>Quantity</th>
						<th style="width:30px"></th>
					</tr>
					</thead>
					<tbody>
					<?php
					if(isset($details))
						foreach($details as $ind => $row){
					?>		
						<tr class="detail-row" data-tmp-id="<?=$row['id']?>">
							<td>
								<select 
									class="form-control input_product input" 
									data-model="product_id" 
									placeholder="Select Product">
									<option value=""></option>
								<?php
									if(isset($product)) {
										foreach($product as $ind => $prod) {
											$selected = $prod["id"] == $row["product_id"] ? "selected" : "";
											$prod_id = $prod["id"];
											$prod_desc = $prod["description"];
											echo "<option value='$prod_id' $selected>$prod_desc</option>";
										}
									}
								?>
								</select>
							<td><input type="number" class="form-control input_quantity input" data-model="quantity" value='<?=$row["quantity"]?>' ></td>
							<td class="" v-align="middle">
								<button class="btn-info action-btn btn btn-sm btn-round undo hidden" title="Undo" data-action="undo">
									<i class="fa fa-undo"></i>
								</button>
								<button class="btn-danger action-btn btn btn-sm btn-round delete " title="Delete" data-action="delete">
									<i class="fa fa-trash"></i>
								</button>
							</td>
						</tr>

					<?php
						}
					?>
						
					</tbody>
					</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="remarks">Remarks</label>
				<textarea class="form-control" id="remarks" rows="3" ><?= $remarks ?></textarea>
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="prepared_by">Prepared by: 	<span id="prepared_by"><?=$prepared_by ?></span></label>
			</div>
		</div>

		<div class="col-md-3">
			<div class="form-group">
				<label for="prepared_by">Approved by: <span id="approved_by"><?=$approved_by ?></span></label>
			</div>
		</div>
	</div>
</div>

<div id="approve-modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box">
						<span class="fa fa-thumbs-up fa-5x text-primary"></span>
					</div>
					<h4 class="modal-title">APPROVE INVENTORY ADJUSTMENT</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-success" id="approve-adjustment">Yes</button>
				</div>
			</div>
		</div>
	</div>

	<div id="delete-modal" class="modal fade">
		<div class="modal-dialog modal-confirm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="icon-box">
						<span class="fa fa-trash fa-5x text-danger"></span>
					</div>
					<h4 class="modal-title">DELETE INVENTORY ADJUSTMENT</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
					<button type="button" class="btn btn-danger" id="delete-adjustment">Yes</button>
				</div>
			</div>
		</div>
	</div>

<template>
			<tr class="detail-row" data-tmp-id="pre_new">
				<td>
				    <select class="form-control input_product input" data-model="product_id" placeholder="Select Product">
						<option value=""></option>
					<?php
						if(isset($product)) {
							foreach($product as $ind => $prod) {
								$prod_id = $prod["id"];
								$prod_desc = $prod["description"];
								echo "<option value='$prod_id'>$prod_desc</option>";
							}
						}
					?>
					</select>
				<td><input type="number" class="form-control input_quantity input" data-model="quantity" ></td>
				<td class="" v-align="middle">
					<button class="btn-info action-btn btn btn-sm btn-round undo hidden" title="Undo" data-action="undo">
						<i class="fa fa-undo"></i>
					</button>
					<button class="btn-danger action-btn btn btn-sm btn-round delete hidden" title="Delete" data-action="delete">
						<i class="fa fa-trash"></i>
					</button>
				</td>
			</tr>
	</template>

<script type="text/javascript">
	window.rows = <?php echo isset($details) ? json_encode($details) : '[]' ?>;
	window.adjustment = <?php echo isset($adjustment) ? json_encode($adjustment) : 'null' ?>
</script>
<?php
	include_once("layouts/footer.php");
?>

