<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
    <title><?=$this->config->item('branch') ?>| Track your Order</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/common.css">
 
  </head>
  <body class="bg-light">
    <div class="container">
  <div class="py-5 text-center">
    <a href="<?php echo site_url();?>/order"><img class="d-block mx-auto mb-4" src="<?php echo base_url(); ?>assets/app/img/ramx.png" alt=""  height="72"></a>
    <h2>Track your Order</h2>
  </div>

  <div class="row">

    <div class="col">
      <h4 class="mb-3">Order Details</h4>
      <form class="needs-validation" novalidate">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="customerName">Customer Name</label>
            <input type="text" class="form-control" id="customerName" placeholder="" value="" required>
            <div class="invalid-feedback">
              Valid customer name is required.
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="transactionId">Transaction Number</label>
            <input type="text" class="form-control" id="transactionId" placeholder="" value="" required>
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
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var baseurl = '<?php echo base_url(); ?>'+'index.php';
	</script>
	   <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/order/track.js"></script>
	</body>
</html>
