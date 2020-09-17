<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>RAM-X Meatshop |  Login</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
    <!-- Bootstrap core CSS -->
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/app/img/favicon.jpg" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/order/login.css">
	<!-- Font Awesome -->

  </head>
  <body class="text-center">
    <form class="form-signin">
		<img class="mb-4 img-fluid" src="<?=base_url()?>assets/app/img/ramx.png"  >
		<h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
		<label for="inputEmail" class="sr-only">Email address</label>
		<input type="text" id="username" class="form-control" placeholder="Username" required autofocus>
		<label for="inputPassword" class="sr-only">Password</label>
		<input type="password" id="password" class="form-control" placeholder="Password" required>
		<p class='text-danger d-none error-message'>Incorrect login details, please try again.</p>
		<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		<p class="mt-5 mb-3 text-muted">&copy; 2020</p>
	</form>
	<script>
		const siteUrl = "<?=site_url()?>"
	</script>
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/app/order/login.js"></script>

</body>
</html>
