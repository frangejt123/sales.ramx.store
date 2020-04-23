<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
	 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	 <!-- Font Awesome -->
	 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
	 <!-- Ionicons -->
	 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css">
	 <!-- Ionicons -->
	 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/app/app.css">
	
</head>
<body>

<div id="container">
	<input type="text" class="form-control" placeholder="Date From" id="date_from" style="width: 200px;float:left">
	<input type="text" class="form-control" placeholder="Date To" id="date_to" style="width: 200px;float:left;">
	<button type="button" class="btn btn-success" id="generatesales" style="float:left">
	        <i class="fa fa-check">&nbsp; &nbsp; Submit</i>
	</button>

	<div style="clear:both"></div>
	<br />
	<div class="salescont" style="font-size: 100px;text-align:center">

	</div>
</div>

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
  var baseurl = '<?php echo base_url(); ?>'+'index.php';
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$("#generatesales").on("click", function(){
			var datefrom = $("#date_from").val();
			var dateto = $("#date_to").val();
			$.ajax({
	        	method: 'POST', 
	        	data: {"datefrom":datefrom,"dateto":dateto},
	        	url: baseurl+'/sales/generate',
	        	success: function(res){ 
	        		$(".salescont").html("P "+res);
		        },
		        error: function(xhr, status, error){
		        	alert("Oppss!. Something went wrong!.")
		        }
	    	});
		});
	});
</script>

</body>
</html>