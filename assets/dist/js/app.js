$(document).ready(function(){
	$("a#sign_out_btn").on("click", function(){
		window.location = baseurl + "?out=1"
	});

	$("a#changepass_btn").on("click", function(){
		$("div#changepass_modal").modal("show");
	});

	$("button#changepassword_submitbtn").on("click", function(){
		var currentpassword = $("input#current_password").val();
		var newpassword = $("input#new_password").val();
		var confirmpwd = $("input#confirm_new_password").val();

		if(newpassword != confirmpwd){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Password does not match.", {
	          type: "danger",
	          width: 300
	        });

	        return;
		}

		var data = {
			"password" : currentpassword
		}

		$.ajax({
			method: "POST",
			data: data,
			url: baseurl+"/userlist/checkpassword",
			success: function(res){
				if(res == "success"){
					var d = {
						"password": newpassword
					}
					dochangepassword(d);
				}else{
					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Incorrect Password.", {
			          type: "danger",
			          width: 300
			        });
				}
			}
		});
	});

	function dochangepassword(data){
		$.ajax({
			method: "POST",
			data: data,
			url: baseurl+"/userlist/updatepassword",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa ffa-check-circle' style='font-size: 20px'></span> &nbsp; Password successfully updated!. Please login again.", {
			          type: "success",
			          width: 300
			        });


					setTimeout(function(){
			        	window.location = baseurl + "?out=1"
					}, 3000);
				}
			}
		});
	}

});