

<body>

	<div id="app"></div>


	<div id="container">

		<button class="btn-danger routing-btn lg-btn" data-route-to="/">
			<i class="fa fa-arrow-left"></i> &nbsp; Back
		</button>

		<button id="create_inventory_adj" 
				class="btn-warning pull-right create-btn routing-btn" 
				data-route-to="/inventory/adjustment/new">
			<i class="fa fa-plus"></i> &nbsp; Create Inventory Adj.
		</button>

		<div style="clear:both"></div>

			<div class="card">
			<div class="card-header pb-1">
				<div class="row">
					<div class="col-6">
						<h5 class="card-title mt-1">Inventory Adjustments</h5>	 
					</div>
					<div class="col-6">
					<input type="text" id="search" data-table="inventory_adjustment_tbl" class="form-control" placeholder="Search...">
					</div>
				</div>
			</div>
				<div class="card-body p-0">
					<table class="table table-striped table-hover mb-0" id="inventory_adjustment_tbl">
						<thead>
							<tr>
								<th class="border-top-0">Inv. Adj. #</th>
								<th class="border-top-0">Date</th>
								<th class="border-top-0">Type</th>
								<th class="border-top-0">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$status = array("", "Pending", "Approved");
							$type = ["", "IN", "OUT"];
							$status_color = array("", "text-success", "text-primary");
							if (count($adjustments) > 0) {
						
								foreach ($adjustments as $ind => $row) {
									$id = $row["id"];
									
									echo "<tr id='tr_$id' class='routing-btn' data-route-to='/inventory/adjustment/detail/$id'>";
									echo '<td>' . sprintf("%04s", $id) . '</td>';
									echo '<td>' . date("m/d/Y", strtotime($row["date"])) . '</td>';
									echo '<td>' . $type[$row['type']] . '</td>';
									echo '<td class='. $status_color[$row["status"]] .'>' . $status[$row["status"]] . '</td>';
									echo '</tr>';
								}
							} else  { ?>
									<tr>
									  	<td colspan='4' class='text-center'>No records found</td>
									</tr>
							
							<?php } ?>
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
	</div>

	


