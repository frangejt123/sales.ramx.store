<?php
	$locationimage = "#";

?>

<body>
<div id="container">
	<button class="btn-danger routing-btn lg-btn" data-route-to="/">
		<i class="fa fa-arrow-left"></i> &nbsp; Back
	</button>

	<button id="new_customer" 
			class="btn-warning pull-right create-btn " 
			data-toggle="modal" 
			data-target="#customer_detail_modal">
		<i class="fa fa-plus"></i> &nbsp; New Customer
	</button>

	<div style="clear:both"></div>

		<div class="card">
			<div class="card-header pb-1">
				<div class="row">
					<div class="col-6">
						<h5 class="card-title mt-1">Customer List</h5>	 
					</div>
					<div class="col-6">
					<input type="text" id="search" data-table="customer_tbl" class="form-control" placeholder="Search...">
					</div>
				</div>
			</div>
			<div class="card-body p-0">
				<table class="table table-striped table-hover mb-0" id="customer_tbl">
					<thead>
						<tr>
							<th  class="border-top-0">ID</th>
							<th class="border-top-0">Name</th>
							<th class="border-top-0">Facebook Name</th>
							<th class="border-top-0">Contact Number</th>
							<th class="border-top-0">Delivery Address</th>
						</tr>
					</thead>
					<tbody>
						<?php
					
						if (count($customers) > 0) {
					
							foreach ($customers as $ind => $row) {
								$id = $row["id"];
								
								echo "<tr id='tr_$id' class='routing-btn' data-route-to='/customer/detail/$id'>";
								echo 	'<td>' . sprintf("%04s", $id) . '</td>';
								echo 	'<td>' . $row["name"] . '</td>';
								echo 	'<td>' . $row["facebook_name"]. '</td>';
								echo 	'<td>' . $row["contact_number"] . '</td>';
								echo 	'<td>' . $row["delivery_address"] . '</td>';
								echo '</tr>';
							}
						} else  { ?>
								<tr>
									  <td colspan='5' class='text-center'>No records found</td>
								</tr>
						
						<?php } ?>
					</tbody>
				</table>
			</div>
			<!-- /.box-body -->
		</div>
	</div>
<!-- Modal -->
<div class="modal fade" id="customer_detail_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form class="needs-validation" method="post" id="customer_form" novalidate>
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Add New Customer</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Customer Name</label>
									<input data-model="name" type="text" class="form-control input" required="true" placeholder="Customer Name" id="customer_name" value="">
									<div class="invalid-feedback">
										Please input `Customer Name`.
									</div>
								</div>
								<div class="form-group">
									<label>Facebook Name</label>
									<input data-model="facebook_name" type="text" class="form-control input" required id="facebook_name" value="">
									<div class="invalid-feedback">
										Please input `Facebook Name`.
									</div>
								</div>
								<div class="form-group">
									<label>Contact Number</label>
									<input data-model="contact_number" type="text" class="form-control input" required placeholder="etc. 09123456789" id="cust_contact_number" value="">
									<div class="invalid-feedback">
										Please input `Contact Number`.
									</div>
								</div>
							
								<div class="form-group">
									<label>Delivery Address</label>
									<textarea data-model="delivery_address" class="form-control input" rows="3" required id="cust_delivery_address"></textarea>
									<div class="invalid-feedback">
										Please input `Delivery Address`.
									</div>
								</div>
									
								
							</div><!-- col 6 / left panel -->
							<div class="col-md-6">
								<div class="form-group">
									<label>Customer Location</label>
									<input type='file' id="customer_location" class="custom-file-input" />
									<div id="map_preview">
										<img id="map_img_preview" src="<?php echo $locationimage; ?>" alt="Map of Customer's Location" />
									</div>
								</div>
							</div>
						</div><!-- row -->
						
						
					</div>

				</div><!-- modal body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
				</form>
			</div>
		</div>

	</div>




