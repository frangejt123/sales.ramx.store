$(document).ready(function(){

	NProgress.configure({ showSpinner: false });
	$("button#login_btn").on("click", function(){
		var username = $("#username").val();
		var password = $("#password").val();

		$("#error_container").hide();
		$.ajax({
			method: 'POST',
			data: {username, password},
			url: baseurl + '/login/auth',
			success: function (res) {
				NProgress.done();
				if(res == 1){
					window.location = baseurl + '/main/';
				}else{
					$("#error_container").fadeIn();
				}
			},
			error: function (xhr, status, error) {
				NProgress.done();
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});

	$(document).on("keyup", function(e){
		if(e.which == 13){
			$("button#login_btn").trigger("click");
		}
	});
});
