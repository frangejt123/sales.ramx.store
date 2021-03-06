<?php
	
	if(isset($customer)) {	
		$id = $customer["id"];
		$name = $customer["name"];
		$fb_name = $customer["facebook_name"];
		$contact_number = $customer["contact_number"];
		$delivery_address = $customer["delivery_address"];
		$location_image = $customer["location_image"];
		$status_class = array("success", "warning", "primary", "danger", "info");
		$city_id = $customer["city_id"];
	}

	if(isset($transactions)) {
		$hasTransactions = count($transactions) > 0;
	}

	if(isset($user)) {
		$username = $user["username"];
	}

	$paid = ["Unpaid", "Paid"];
	$paid_class = ["danger", "success"];
?>

<div id="container" class="mt-5">
	<button class="btn-danger lg-btn routing-btn" data-route-to="/customer">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<button id="save-btn" class="btn-success pull-right lg-btn">
			<i class="fa fa-save"></i> &nbsp; Save
	</button>
	<button id="create_user_btn" class="btn-primary pull-right lg-btn mr-2" style="width: auto">
			<i class="fa fa-user"></i> &nbsp; Set Default User
	</button>
	<span class="pull-right span_seperator"></span>
	<span class="pull-right span_seperator"></span>
	<button id="delete-btn" class="btn-danger pull-right lg-btn <?= $hasTransactions ? 'd-none' : ''?>">
			<i class="fa fa-trash"></i> &nbsp; Delete
	</button>


	<div style="clear:both"></div>


	<div class="container-fluid">
		<form class="needs-validation" novalidate id="customer_form" method="post" data-mode='edit'>
			<button type="submit" class="d-none"></button>
		<div class="row">
			<div class="col-6">
				<div class="form-group row">
					<label class="col-sm-3 col-form-label">Customer ID :  </label> 	
					<div class="col-sm-9">
						<input type="text" class="form-control form-readonly" name="id" value="<?php echo $id ?>"" id="customer_id" readonly placeholder="#NEW#">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label">Name :  </label> 	
					<div class="col-sm-9">
						<input type="text" class="form-control input" data-model="name" name="name" value="<?php echo $name ?>" required id="customer_name" >
						<div class="invalid-feedback">
							Please input `Customer Name`.
						</div>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label">Facebook Name :  </label> 	
					<div class="col-sm-9">
						<input type="text" class="form-control input"  required data-model="facebook_name" name="facebook_name" value="<?php echo $fb_name ?>"" id="fb_name" >
						<div class="invalid-feedback">
							Please input `Facebook Name`.
						</div>
					</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label">Contact Number :  </label> 	
					<div class="col-sm-9">
						<input type="text" class="form-control input" required  data-model="contact_number" name="contact_number" value="<?php echo $contact_number ?>"" id="contact_number" >
						<div class="invalid-feedback">
							Please input `Contact Number`.
						</div>
					</div>
				</div>

				<div class="form-group row">
				<label class="col-sm-3 col-form-label">City</label>
				<div class="col-sm-9">
					<select class="form-control input" id="city" required  data-model="city_id">
						<option value=""></option>
						<?php 
						  foreach($city as $c) {
							  $selected = $c["id"] == $city_id ? 'selected="selected"' : "";
							  echo '<option value="'.$c["id"].'" '.$selected.' >'.$c['name'].'</option>';
						  }
						  ?>
					</select>
					<div class="invalid-feedback">
							Please select `City`.
						</div>
						</div>
				</div>

				<div class="form-group row">
					<label class="col-sm-3 col-form-label">Delivery Address :  </label> 	
					<div class="col-sm-9">
						<textarea data-model="delivery_address" required class="form-control input" rows="3" required id="cust_delivery_address"><?= $delivery_address ?>
						</textarea>
						<div class="invalid-feedback">
							Please input `Delivery Address`.
						</div>
					</div>
				</div>
			</div>

			<div class="col-6">
				<div class="form-group">
					<label>Customer Location</label>
					<input type='file' id="customer_location" />
					<div id="map_preview" class="detail_map_preview" style="height: 300px">
						<img id="map_img_preview" src="<?= $location_image ?>" alt="Map of Customer's Location" />
					</div>
				</div>
			</div>
		</div>

		</form>
	</div>

		<div class="card">
			<div class="card-header pb-1">
				<div class="row">
					<div class="col-6">
						<h5 class="card-title mt-1">Transaction History</h5>	 
					</div>
					<div class="col-6">
					<input type="text" id="search" class="form-control" data-table="transaction_tbl" placeholder="Search...">
					</div>
				</div>
			</div>
			<div class="card-body p-0">
				<table class="table table-striped table-hover mb-0" id="transaction_tbl">
					<thead>
					<tr>
						<th class="border-top-0">Trans #</th>
						<th class="border-top-0">Datetime</th>
						<th class="border-top-0">Delivery Date</th>
						<th class="border-top-0">Payment Method</th>
						<th class="border-top-0 text-center">Total</th>
						<th class="border-top-0 text-center">Paid</th>
						<th class="border-top-0 text-center">Status</th>
					</tr>
					</thead>
					<tbody>
					<?php
					if(isset($transactions) && count($transactions) > 0)
						foreach($transactions as $ind => $row){
					?>		
						<tr class="transaction-row routing-btn" data-id="<?=$row['id']?>" data-route-to="/main/orderdetail/<?=base64_encode($row['id']) ?>">
							<td><?= $row["id"] ?></td>
							<td><?= date('m/d/Y H:i:s', strtotime($row["datetime"])) ?></td>
							<td><?= date('m/d/Y', strtotime($row["delivery_date"])) ?></td>
							<td><?= $payment_method[$row["payment_method"]] ?></td>
							<td class="text-right"><?= number_format($row["total"], 2) ?></td>
							<td class="text-center"><span class="badge badge-pill badge-<?= $paid_class[$row['paid']] ?>"><?= $paid[$row["paid"]] ?></span></td>
							<td class="text-center"><span class="badge badge-pill badge-<?= $status_class[$row['status']] ?>"><?= $status[$row["status"]] ?></span></td>
						</tr>

					<?php
							} else  { ?>
								<tr>
									  <td colspan='6' class='text-center'>No records found</td>
								</tr>
						
						<?php } ?>
			
						
					</tbody>
					</table>
			</div>
			<!-- /.box-body -->
		</div>
</div>



</div>
</div>

<div id="create_user" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-user-plus"></i> Set Default User</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Username</label>
						<input type="text" class="form-control" id="username" value="<?=$username ?? null ?>">
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
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-warning" id="default-user">Set Default</button>
					<button type="button" class="btn btn-primary" id="save_user">Save changes</button>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript">
	var siteURL = "<?= site_url() ?>"
	window.form = <?php echo isset($customer) ? json_encode($customer) : 'null' ?>
</script>
