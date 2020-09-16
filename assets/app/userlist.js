$(document).ready(function(){

	NProgress.configure({ showSpinner: false });
	$("input#search_user").on("keyup", function(){
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("search_user");
		filter = input.value.toUpperCase();
		table = document.getElementById("userlist_table");
		tr = table.getElementsByTagName("tr");

		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[1];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
	});

	$("button#create_user_btn").on("click", function(){
		$("#create_user").modal("show");
	});

	$("#cancel_userlist_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl;
	});

	$("#userlist_table").on("click", "tr", function(){
		var td = $(this).find("td");

		let access_lvl = $(td[2]).data('access-level');
		$("#detail_username").val(td[0].textContent);
		$("#detail_name").val(td[1].textContent);
		$("#detail_access_level").val(access_lvl);
		$("#old_password").val(td[3].textContent);
		$("#userid").val(td[4].textContent);
		
		$("#user_detail").modal("show");
	});

	$(".show_password").on("click", function(){
		var id = $(this).attr("id");
		showhidepassword(id);
	});

	$("#save_user").on("click", function(){
		var input = $("#create_user").find("input");
		var empty = 0;
		var password = $("#password").val();
		var confirmpassword = $("#confirmpassword").val();
		$.each(input, function(ind, row){
			if($(row).val() == ""){
				empty++;
			}
		});

		if(empty > 0){
			alert("Please fill in empty fields.");
			return;
		}

		if(password != confirmpassword){
			alert("Password did not match");
			return;
		}

		var username = $("#username").val();
		var name = $("#name").val();
		var access_level = $("select#access_level").val();

		$.ajax({
			method: 'POST',
			data: {username, name, password, access_level},
			url: baseurl + '/user/adduser',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}else{
					alert(res["error_msg"]);
				}
				$("#save_user").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#save_user").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#save_user").attr("disabled", "disabled");
			}
		});
	});

	function showhidepassword(id){
		var x = document.getElementById(id.split("_")[1]);
		$("#"+id+" i").removeClass("fa-eye fa-eye-slash");
		if (x.type === "password") {
			x.type = "text";
			$("#"+id+" i").addClass("fa-eye-slash");
		} else {
			x.type = "password";
			$("#"+id+" i").addClass("fa-eye");
		}
	}

	$("#change_pass_btn").on("click", function(){
		$("#change_password_container").toggle();
	});

	$("#update_user").on("click", function(){
		var input = $("#user_detail").find("input").not(".not_required");
		var empty = 0;
		var oldpasswordtxt = $("#detailPassword").val();
		var oldpassword = $("#old_password").val();
		var password = $("#newpassword").val();
		var cnfrmpassword = $("#cfrmnewPassword").val();
		var id = $("#userid").val();
		$.each(input, function(ind, row){
			if($(row).val() == ""){
				empty++;
			}
		});

		if(empty > 0){
			alert("Please fill in empty fields.");
			return;
		}

		var username = $("#detail_username").val();
		var name = $("#detail_name").val();
		var access_level = $("select#detail_access_level").val();

		var data = {id, username, name, access_level};
		var error = 0;
		if(password != ""){
			if(password != cnfrmpassword){
				alert("Password did not match");
				error++;
			}else{
				data["password"] = password;
			}

			if( $.md5(oldpasswordtxt) != oldpassword){
				alert("Old password did not match");
				error++;
			}
		}

		if(error > 0)
			return;


		$.ajax({
			method: 'POST',
			data: data,
			url: baseurl + '/user/update',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}else{
					alert(res["error_msg"]);
				}
				$("#update_user").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#update_user").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#update_user").attr("disabled", "disabled");
			}
		});
	});

	$("#delete_user_btn").on("click", function(){
		var name = $("#detail_name").val();
		$("span#user_name").text(name);
		$("#delete_user_modal").modal("show");
	});

	$("#confirm_delete_user").on("click", function(){
		var id = $("#userid").val();
		$.ajax({
			method: 'POST',
			data: {id},
			url: baseurl + '/user/delete',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}
				$("#confirm_delete_user").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#confirm_delete_user").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#confirm_delete_user").attr("disabled", "disabled");
			}
		});
	});

	function validate(s) {
		var rgx = /^[0-9]*\.?[0-9]*$/;
		return s.match(rgx);
	}

	function ucwords (str) {
		return (str + '')
			.replace(/^(.)|\s+(.)/g, function ($1) {
				return $1.toUpperCase()
			})
	}

	$("#logout").on("click", function(){
		NProgress.start();
		$.ajax({
			method: 'POST',
			url: baseurl + '/login/logout',
			success: function (res) {
				if(res == "success"){
					localStorage.removeItem("filter");
					localStorage.removeItem("inverse");
					localStorage.removeItem("thIndex");
				
					localStorage.removeItem("searchvalue");
					localStorage.removeItem("filter");
					localStorage.removeItem("store_id");
					window.location = baseurl + "/login";
				}
			}
		});
	});
});
