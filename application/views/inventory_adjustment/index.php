

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

		<div class="grid_container">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search" id="search_customer_name">
			</div>

			<div class="box">
				<div class="box-body no-padding">
					<table class="table table-striped table-hover" id="inventory_adjustment_tbl">
						<thead>
							<tr>
								<th>Inv. Adj. #</th>
								<th>Date</th>
								<th>Type</th>
								<th>Status</th>
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
	</div>

	


