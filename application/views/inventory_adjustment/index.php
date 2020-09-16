


	<div id="app"></div>


	<div id="container" class="mt-5">

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
								<th class="border-top-0 sortable">Inv. Adj. # <i class="fa fa-sort float-right"></i></th>
								<th class="border-top-0 sortable">Date <i class="fa fa-sort float-right"></i></th>
								<th class="border-top-0 sortable">Type <i class="fa fa-sort float-right"></i></th>
								<th class="border-top-0 sortable">Status <i class="fa fa-sort float-right"></i></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
	</div>

	


