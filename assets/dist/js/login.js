$(document).ready(function(){
	$("form#loginform").on("submit", function(e){
		e.preventDefault();
		$("div.login-error-container").hide();
		var email = $("input#email").val();
		var password = $("input#password").val();

		var d = {
			"email" : email,
			"password" : password
		}

		$.ajax({
			method: "POST",
			url: baseurl+"index.php/login",
			data: d,
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					window.location = baseurl;
				}else{
					$("div.login-error-container").fadeIn("slow");
				}
			}
		})
	});
});