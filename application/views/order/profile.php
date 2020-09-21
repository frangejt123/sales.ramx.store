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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/croppie.css">
 
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
      <li class="nav-item ">
	 	 <a class="nav-link" href="<?=site_url()?>/order/purchases">My Purchases</a>
      </li>
      <li class="nav-item dropdown active">
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

<div class="container pb-5 mt-3 ">
	
 <div class="d-flex">     
 	<button class="btn btn-primary ml-auto" id="save-btn">Save </button>
 </div>
	<hr />
  
	<div class="row">
   
    <div class="col-md-7 ">
      <h4 class="mb-3">My Profile</h4>
      <form class="needs-validation" novalidate id="customer_form" method="post" data-mode='edit'>
				<button type="submit" class="d-none"></button>
				<div class="form-group ">
						<label >Name  </label> 	
							<input type="text" class="form-control input" data-resource='customer' data-model="name" name="name" value="<?php echo $customer['name'] ?>" required id="customer_name" >
							<div class="invalid-feedback">
								Please input `Customer Name`.
							</div>
				</div>

				<div class="form-group ">
					<label>Facebook Name  </label> 	
					<input type="text" class="form-control input"  required data-resource='customer' data-model="facebook_name" name="facebook_name" value="<?php echo $customer['facebook_name'] ?>"" id="fb_name" >
					<div class="invalid-feedback">
						Please input `Facebook Name`.
					</div>
				</div>

				<div class="form-group">
					<label>Contact Number   </label> 	
					<input type="text" class="form-control input" required  data-resource='customer' data-model="contact_number" name="contact_number" value="<?php echo $customer['contact_number'] ?>" id="contact_number" >
					<div class="invalid-feedback">
						Please input `Contact Number`.
					</div>
				</div>

				<div class="form-group ">
				<label >City</label>
					<select class="form-control input" id="city" required  data-resource='customer' data-model="city_id">
						<option value=""></option>
						<?php 
						  foreach($city as $c) {
							  $selected = $c["id"] == $customer['city_id'] ? 'selected="selected"' : "";
							  echo '<option value="'.$c["id"].'" '.$selected.' >'.$c['name'].'</option>';
						  }
						  ?>
					</select>
						<div class="invalid-feedback">
							Please select `City`.
						</div>
				</div>
				
				<div class="form-group ">
					<label >Delivery Address :  </label> 	
						<textarea data-resource='customer' data-model="delivery_address" required class="form-control input" rows="3" required id="cust_delivery_address"><?= $customer['delivery_address'] ?>
						</textarea>
						<div class="invalid-feedback">
							Please input `Delivery Address`.
						</div>
				</div>

				<div class="form-group">
					<label>Customer Location</label>
					<input type='file' id="customer_location" class='input' data-resource='customer' data-model='customer_location' />
					<div id="map_preview" class="detail_map_preview w-100" style="height: 22rem">
						<img id="map_img_preview" src="<?= base_url() .'assets/location_image/'. $customer["location_image"] ?>" alt="Map of Customer's Location" />
					</div>
				</div>
				</form>
			</div>

			<div class="col-md-5 mt-5 mt-md-0">
			<h4 class="mb-3">User Info</h4>
			<div class="form-group ">
						<label >Username  </label> 	
							<input type="text" class="form-control input" data-resource='user' data-model="username" name="username" value="<?php echo $user['username'] ?>" required id="username" >
							<div class="invalid-feedback">
								Please input `Username`.
							</div>
				</div>

				<div class="form-group ">
					<label>Password  </label> 	
					<input type="password" class="form-control input"  required data-resource='user' data-model="password" name="password"  id="password" >
					<div class="invalid-feedback">
					  Password did not match
					</div>
				</div>

				<div class="form-group">
					<label>Confirm Password   </label> 	
					<input type="password" class="form-control input" required data-resource='user'  data-model="cpassword" name="cpassword"  id="cpassword" >
					<div class="invalid-feedback">
						Please re-type your password
					</div>
				</div>
	</div>

	</div>
	
	<hr class="mb-4">

</div>
<script>
	//$.widget.bridge('uibutton', $.ui.button);
	var siteURL = '<?php echo site_url(); ?>';
	window.customer = <?=json_encode($customer)?>;
	window.user = <?=json_encode($user)?>
	</script>
	   <script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
	   <script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/order/common.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/croppie.js"></script>
	   <script src="<?php echo base_url(); ?>assets/app/sweetalert2.js"></script>		
		<script src="<?php echo base_url(); ?>assets/app/order/profile.js"></script>
	</body>
</html>
