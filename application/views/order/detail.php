<?php
$editable = true;
if(array_key_exists("pending", $tracking["history"])) {
	$check_out_active = 'active';
	$check_out_date = $tracking["history"]["pending"]["date"];
}

if(array_key_exists("process", $tracking["history"])) {
	$editable = false;
	$packing_active = 'active';
	$packing_date = $tracking["history"]["process"]["date"];
} else {
	$packing_active = '';
	$packing_date = '';
}

if(array_key_exists("to_receive", $tracking["history"])) {
	$to_receive_active = 'active';
	$to_receive_date = $tracking["history"]["to_receive"]["date"];
} else {
	$to_receive_active = '';
	$to_receive_date = '';
}

if(array_key_exists("delivered", $tracking["history"])) {
	$delivered_active = 'active';
	$delivered_date = $tracking["history"]["delivered"]["date"];
} else {
	$delivered_active = '';
	$delivered_date = '';
}

if(array_key_exists("completed", $tracking["history"])) {
	$completed_active = 'active';
	$completed_date = $tracking["history"]["completed"]["date"];
} else {
	$completed_active = '';
	$completed_date = '';
}


?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
    <title>RAM-X Meatshop | Track your Order</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/common.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/detail.css">
 
  </head>
  <body class="bg-light">
	  
  <nav class="navbar navbar-expand-lg  font-smaller fixed-top navbar-dark bg-dark">
 	 <a class="navbar-brand  mr-auto mr-lg-0" href="<?php echo site_url() . '/order' ?>">
		<img src="<?=base_url()?>assets/app/img/favicon.png" width="20"  />
		<span class="d-lg-inline-block d-none">RAM-X Meatshop</span>
		<span class=" d-lg-none d-md-inline-block ">My Purchases</span>
	</a>
	
	<button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
		<span class="navbar-toggler-icon"></span>
	</button>


  <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item ">
	 	 <a class="nav-link" href="<?=site_url()?>/order/new">New Order <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
	 	 <a class="nav-link" href="<?=site_url()?>/order/purchases">My Purchases</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  		<path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
		</svg>
			<?=$username?></a>
        <div class="dropdown-menu dropdown-menu-left" style="left: -5rem !important" aria-labelledby="dropdown01">
          <a class="dropdown-item" href="<?=site_url()?>/order/profile">Profile</a>
          <a class="dropdown-item" href="#" id="logout">Logout</a>
        </div>
      </li>
    </ul>
  </div>
</nav>

    <div class="container pb-5 ">
		<?php if(isset($_GET['success'])) { ?>
			<div class="pt-5 text-center" >
					<a href="<?php echo site_url();?>/order"><img class="d-block mx-auto mb-4" src="<?php echo base_url(); ?>assets/app/img/ramx.png" alt=""  height="72"></a>
					<h2>Thank you for placing an order with us, <?=$transaction['name']?>! </h2>
					<p class="lead">This is your order confirmation. Please take note of your order number. </p>
					
			</div>
		<?php } ?>
	<div class="row mb-3">
		<div class="col">
			<div class="card  mt-5" role="alert">
				<div class="card-body  d-flex">
					<div style='color: <?=$tracking["status"]["color"]?>'><b><?=$tracking['status']['status']?></b></div> 
					<div class='mx-5'>|</div>
					<div><?=$tracking['status']["message"]?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="md-stepper-horizontal blue">
    <div class="md-step <?=$check_out_active?> done">
      	<div class="md-step-circle ">
		  	<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-cart-check-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zM4 14a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm7 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm.354-7.646a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
			</svg>
		</div>
	  <div class="md-step-title">Checkout</div>
	  <div class="md-step-optional"><?=$check_out_date?></div>
      <div class="md-step-bar-left"></div>
      <div class="md-step-bar-right"></div>
    </div>
    <div class="md-step <?=$packing_active?>">
      <div class="md-step-circle">
	  	<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-seam" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
		</svg>
	  </div>
      <div class="md-step-title">Packing</div>
      <div class="md-step-optional"><?=$packing_date?></div>
      <div class="md-step-bar-left"></div>
      <div class="md-step-bar-right"></div>
    </div>
	<div class="md-step <?=$to_receive_active?>">
      <div class="md-step-circle">
	  	<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-truck" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
		</svg>
	  </div>
	  <div class="md-step-title">To Receive</div>
	  <div class="md-step-optional"><?=$to_receive_date?></div>
      <div class="md-step-bar-left"></div>
      <div class="md-step-bar-right"></div>
    </div>
	<div class="md-step <?=$delivered_active?>">
      <div class="md-step-circle">
		  <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
			<path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
		</svg>
	</div>
	  <div class="md-step-title">Delivered</div>
	  <div class="md-step-optional"><?=$delivered_date?></div>
      <div class="md-step-bar-left"></div>
      <div class="md-step-bar-right"></div>
    </div>
	<div class="md-step <?=$completed_active?>">
      <div class="md-step-circle">
			<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-star-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				<path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
		</svg>
	  </div>
	  <div class="md-step-title">Completed</div>
	  <div class="md-step-optional"><?=$completed_date?></div>
      <div class="md-step-bar-left"></div>
      <div class="md-step-bar-right"></div>
    </div>
  </div>


	<div class="row">
    <div class="col-md-4 order-md-2 mb-4">
      <h4 class="d-flex justify-content-between align-items-center mb-3">
		<span class="text-muted">Your orders</span>
		<?php if($editable) {?>
			<a href="#" style="font-size: 1rem;" id="edit-order" data-id='<?=$transaction["id"]?>'> <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
			</svg> Edit</a>
		<?php } ?>
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
	  	<p class="text-muted" style='font-size: .75rem'>You may settle it through GCash Payment, Bank Online Transfer (available thru BPI and/or Metrobank), or COD.</p>
    	<p class="text-muted" style='font-size: .75rem' >For Cash on Delivery (COD), we encourage you to pay the exact amount. If unable, please reply with the amount that you will pay so we can prepare your change. </p>
			
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
		  <label for="address">City</label>
		  <textarea class="form-control form-readonly" readonly><?=$transaction['city']?></textarea>
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
	
	<p class='text-muted' style='font-size: .75rem'>Delivery Info: Please expect to receive your orders from 9AM to 6PM (NO specific time slot). For orders confirmed before 10PM, you will receive it the following day. The delivery team will contact your number if they're on the way. 
Thank you very much </p>
</div>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var siteURL = '<?php echo site_url(); ?>';
	</script>
	   <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
	   <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/order/common.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/order/track.js"></script>
	</body>
</html>
