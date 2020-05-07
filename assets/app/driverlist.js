$(document).ready(function(){

	NProgress.configure({ showSpinner: false });
	$("input#search_driver").on("keyup", function(){
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("search_driver");
		filter = input.value.toUpperCase();
		table = document.getElementById("driverlist_table");
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

	$("button#create_driver_btn").on("click", function(){
		$("#name").val("");
		$("#delete_driver_btn").hide();
		$("#create_driver").modal("show").data("mode", "insert");
	});

	$("#cancel_driver_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl;
	});

	$("#driverlist_table").on("click", "tr", function(){
		var td = $(this).find("td");
		$("#name").val(td[1].textContent);
		var id = td[0].textContent;
		$("#delete_driver_btn").show();
		$("#create_driver").modal("show").data({"mode": "update", "id":id});
	});

	$("#save_driver").on("click", function(){
		var name = $("#name").val();

		if(name == ""){
			alert("Please fill in empty fields.");
			return;
		}

		var data = {name};
		var url = baseurl + '/driver/adddriver';
		if($("#create_driver").modal("show").data("mode") == "update"){
			url = baseurl + '/driver/update';
			data["id"] = $("#create_driver").modal("show").data("id");
		}

		$.ajax({
			method: 'POST',
			data: data,
			url: url,
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}else{
					alert(res["error_msg"]);
				}
				$("#save_driver").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#save_driver").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#save_driver").attr("disabled", "disabled");
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

	$("#update_driver").on("click", function(){
		var input = $("#driver_detail").find("input").not(".not_required");
		var empty = 0;
		var oldpasswordtxt = $("#detailPassword").val();
		var oldpassword = $("#old_password").val();
		var password = $("#newpassword").val();
		var cnfrmpassword = $("#cfrmnewPassword").val();
		var id = $("#driverid").val();
		$.each(input, function(ind, row){
			if($(row).val() == ""){
				empty++;
			}
		});

		if(empty > 0){
			alert("Please fill in empty fields.");
			return;
		}

		var drivername = $("#detail_drivername").val();
		var name = $("#detail_name").val();
		var access_level = $("select#detail_access_level").val();

		var data = {id, drivername, name, access_level};
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
			url: baseurl + '/driver/update',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}else{
					alert(res["error_msg"]);
				}
				$("#update_driver").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#update_driver").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#update_driver").attr("disabled", "disabled");
			}
		});
	});

	$("#delete_driver_btn").on("click", function(){
		var name = $("#name").val();
		$("span#driver_name").text(name);
		$("#delete_driver_modal").modal("show");
	});

	$("#confirm_delete_driver").on("click", function(){
		var id = $("#create_driver").modal("show").data("id");
		$.ajax({
			method: 'POST',
			data: {id},
			url: baseurl + '/driver/delete',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}
				$("#confirm_delete_driver").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#confirm_delete_driver").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#confirm_delete_driver").attr("disabled", "disabled");
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
});
