

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

	<div class="grid_container">
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Search" id="search_customer_name">
		</div>

		<div class="box">
			<div class="box-body no-padding">
				<table class="table table-striped table-hover" id="inventory_adjustment_tbl">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Facebook Name</th>
							<th>Contact Number</th>
							<th>Delivery Address</th>
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
</div>

<!-- Modal -->
<div class="modal fade" id="customer_detail_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Add New Customer</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md">
								<div class="form-group">
									<label>Customer Name</label>
									<div style="position: relative; height: 34px;">
										<input type="text" class="form-control" placeholder="Customer Name" id="customer_name" style="position: absolute; z-index: 2; background: transparent;" value="">
									</div>
								</div>
								<div class="form-group">
									<label>Facebook Name</label>
									<input type="text" class="form-control" id="facebook_name" value="">
								</div>
								<div class="form-group">
									<label>Contact Number</label>
									<input type="text" class="form-control" placeholder="etc. 09123456789" id="cust_contact_number" value="">
								</div>
								<div class="form-group">
									<label>Delivery Address</label>
									<textarea class="form-control" rows="2" id="cust_delivery_address"></textarea>
								</div>
								<div class="form-group">
									<label>Customer Location</label>
									<input type='file' id="customer_location" class="custom-file-input" />
									<div id="map_preview">
										<img id="map_img_preview" src="" alt="Map of Customer's Location" />
									</div>
								</div>
							</div><!-- col 6 / left panel -->
						</div><!-- row -->
					</div>

				</div><!-- modal body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="save_customer_detail_btn">Save changes</button>
				</div>
			</div>
		</div>
	</div>




