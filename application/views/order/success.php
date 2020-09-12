
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>RAM-X Meatshop | Track your Order</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
	  }
	  
	  .form-readonly {
		  background-color: #fff !important;
	  }
    </style>
 
  </head>
  <body class="bg-light">
    <div class="container pb-5">
		<div class="py-5 text-center">
			<a href="<?php echo site_url();?>/order"><img class="d-block mx-auto mb-4" src="<?php echo base_url(); ?>assets/app/img/ramx.png" alt=""  height="72"></a>
			<h2>Thank you for purchasing at RAM-X Meatshop!</h2>
			<p class="lead">You may track your order using the details below, please keep it (take a screenshot) for tracking your order.</p>
		</div>

		<div class="row">
    <div class="col-md-4 order-md-2 mb-4">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Your orders</span>
      </h4>
      <ul class="list-group mb-3">

	  <?php foreach($detail as $row) {
				  echo '
				<li class="list-group-item d-flex justify-content-between lh-condensed">
					<div>
						<h6 class="my-0">'.$row["description"].'</h6>
						<small class="text-muted">'.$row["quantity"].' ' . $row["uom"].'</small>
					</div>
					<span class="text-muted">P'.number_format($row["total_price"],2).'</span>
				</li>
				 
				  ';
		  	}
	  ?>
       
		

	
        <li class="list-group-item d-flex justify-content-between bg-light">
          <div class="text-success">
            <h6 class="my-0">Total Amount</h6>
          </div>
          <span class="text-success">P<?=number_format($transaction['total'], 2)?></span>
        </li>
        <li class="list-group-item d-flex justify-content-between ">
          <div >
            <h6 class="my-0">Payment Method</h6>
          </div>
          <span ><?=($payment_method[$transaction['payment_method']])?></span>
        </li>
      </ul>
    </div>
    <div class="col-md-8 order-md-1">
      <h4 class="mb-3">Order # <?php echo $transaction['order_number'] ?></h4>
      <form>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="firstName">Customer Name</label>
            <input type="text" class="form-control form-readonly" readonly  value="<?=$transaction['name']?>" >
          </div>
          <div class="col-md-6 mb-3">
            <label for="lastName">Facebook Name</label>
            <input type="text" class="form-control form-readonly" readonly value="<?=$transaction['facebook_name']?>" >
          </div>
        </div>

        <div class="mb-3">
          <label for="username">Contact Number</label>
            <input type="text" class="form-control form-readonly" readonly value="<?=$transaction['contact_number']?> ">
        
        </div>

        <div class="mb-3">
          <label for="email">Delivery Date</label>
          <input type="text" class="form-control form-readonly" readonly value="<?= date('m/d/Y', strtotime( $transaction['delivery_date']))?> ">
        </div>

        <div class="mb-3">
		  <label for="address">Delivery Address</label>
		  <textarea class="form-control form-readonly" readonly><?=$transaction['delivery_address']?></textarea>
        </div>	

        <div class="mb-3">
		  <label for="address">Remarks</label>
		  <textarea class="form-control form-readonly" readonly><?=$transaction['remarks']?></textarea>
        </div>	
      </form>
    </div>
	</div>
	<hr class="mb-4">
	<div>
		<div class="row">
			<div class="col-sm-12">
			<h4 class="mb-3">Order Tracking</h4>
			<form class="needs-validation" novalidate">
				<div class="row">
				<div class="col-md-6 mb-3">
					<label for="customerName">Customer Name</label>
					<input type="text" class="form-control" id="customerName" placeholder="" value="<?php echo $transaction['name'] ?>" required>
					<div class="invalid-feedback">
					Valid customer name is required.
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<label for="transactionId">Transaction Number</label>
					<input type="text" class="form-control" id="transactionId" placeholder="" value="<?php echo $transaction['order_number'] ?>" required>
					<div class="invalid-feedback">
					Valid transaction number is required.
					</div>
				</div>
				</div>

				<hr class="mb-4">
				<button class="btn btn-primary btn-lg btn-block" id="submitBtn" type="submit">Submit</button>
				<button class="btn btn-primary btn-lg btn-block d-none" id="loadingBtn" type="button" disabled>
					<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
					Loading...
				</button>
			</form>
			</div>
		</div>

		<div class="row">
			<div class="col">
				<div class="card mt-5">
					<div class="card-body">
						<p class="lead font-weight-normal" id="result"></p>
					</div>
				</div>
			
			</div>
		</div>

	</div>
</div>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
	</script>
	   <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/track.js"></script>
	</body>
</html>
