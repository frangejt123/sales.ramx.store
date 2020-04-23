$(document).ready(function(){
	/* populate userlist list */
	$.ajax({
		method: "POST",
		url: baseurl+"/userlist/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var tr = "";
			$.each(res, function(ind, row){
				var style = "";
				var cls = "";
				if(row["access_level"] == 0){
					style = " style='color: #ed8533'";
					cls = " class='admintr'";
				}

				tr += '<tr id="'+row["id"]+'" '+style+cls+'>'
						+ '<td>'+row["firstname"]+'</td>'
						+ '<td>'+row["lastname"]+'</td>'
						+ '<td class="useremailtd">'+row["email"]+'</td>'
						+ '<td>'+row["branch_name"]+'</td>'
						+ '<td style="display:none">'+row["branch_id"]+'</td></tr>';
			});
			$("table#userlisttable tbody").html(tr);
		}
	});

	$("button#new_userlist_btn").on("click", function(){
		var inputs = $("form#newUserForm").find("input");
		$("select#userlist_access_lvl").val("").trigger("change");
		$.each(inputs, function(ind, row){
			$(this).val("");
		});

		$("div#new_userlist_modal").modal("show");
	});

	var acclvlopt = [{"id":"","text":"", "selected": false}, {"id":"0","text":"Administrator"}, {"id":"1","text":"System User"}];
	$("select#userlist_access_lvl, select#detail_userlist_access_lvl").select2({
        placeholder: "Select Access Level",
		width: "100%",
		data: acclvlopt
	});

	$("#newUser_submitBtn").on("click", function(){
		var firstname = $("input#userlist_firstname").val();
		var lastname = $("input#userlist_lastname").val();
		var email = $("input#userlist_email").val();
		var branch = $("select#userlist_branch").val();
		var access_level = $("select#userlist_access_lvl").val();

		var data = {
			"firstname": firstname,
			"lastname": lastname,
			"email": email,
			"branch_id": branch,
			"access_level": access_level
		}

		var branchopt = $("select#userlist_branch").select2('data');
		var selectedbranchtxt = branchopt[0].text;

		var inputs = $("form#newUserForm").find("input");

		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

		$("div#userbranchform").find("span.select2-container--default").removeClass("emptyField");
		if(branch == ""){
			$("div#userbranchform").find("span.select2-container--default").addClass("emptyField");
			empty++;
		}

		$("div#accesslvlform").find("span.select2-container--default").removeClass("emptyField");
		if(access_level == ""){
			$("div#accesslvlform").find("span.select2-container--default").addClass("emptyField");
			empty++;
		}

		if(empty > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.", {
	          type: "danger",
	          width: 300
	        });
			return;
		}


		if (!validateEmail(email)) {
			$("input#userlist_email").addClass("emptyField");
		    $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Incorrect email format.", {
	          type: "danger",
	          width: 300
	        });s
			return;
		}


		var emaillist = $("table#userlisttable tbody tr td.useremailtd");
		var emailexist = 0;
		$.each(emaillist, function(ind, row){
			if($(row).html() == email){
		        emailexist++;
			}
		});

		if(emailexist > 0){
			$("input#userlist_email").addClass("emptyField");
		    $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Email already taken.", {
	          type: "danger",
	          width: 300
	        });

	        return;
		}

		$.ajax({
			method: "POST",
			data: data,
			url: baseurl+"/userlist/insert",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var style = "";
					if(access_level == 0){
						style = " style='color: #ed8533'";
					}
					var tr = '<tr id="'+res["id"]+'" '+style+'>'
								+ '<td>'+res["firstname"]+'</td>'
								+ '<td>'+res["lastname"]+'</td>'
								+ '<td class="useremailtd">'+email+'</td>'
								+ '<td>'+selectedbranchtxt+'</td>'
								+ '<td style="display:none">'+branch+'</td></tr>';

		            $("table#userlisttable tbody").prepend(tr);
					$("div#new_userlist_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!", {
			          type: "success",
			          allow_dismiss: false,
			          width: 300
			        });
				}
			}
		});
	});

	function validateEmail(email) {
	    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(String(email).toLowerCase());
	}

	/* on row click */
	$("table#userlisttable tbody").on("click", "tr", function(){
		var tds = $(this).find("td");
		var id = $(this).attr("id");

		var accesslevel = $(this).hasClass("admintr") ? 0 : 1;

		var fname = $(tds[0]).html();
		var lname = $(tds[1]).html();
		var email = $(tds[2]).html();
		var branch = $(tds[3]).html();

		$("input#detail_userlist_firstname").val(fname);
		$("input#detail_userlist_lastname").val(lname);
		$("input#detail_userlist_email").val(email);
		$("select#detail_userlist_access_lvl").val(accesslevel).trigger("change");
		$("div#userlist_detail_modal").data("id", id);
		$("div#userlist_detail_modal").data("selectedbranch", $(tds[4]).html());
		$("div#userlist_detail_modal").modal("show");
	});

	$('.select2#detail_user_branch, .select2#userlist_branch').select2({
	 	width: "100%"
	});
	$('div#userlist_detail_modal, div#new_userlist_modal').on('shown.bs.modal', function () {
		var selectedbranch = $("div#userlist_detail_modal").data("selectedbranch");

        $.ajax({
            method: "POST",
            url: baseurl+"/branch/getAll",
            success: function(res){
                var res = JSON.parse(res);
                var data = [{"id":"","text":"", "selected": false}];
                $.each(res, function(i, r){
                    data.push({"id":r["id"],"text":r["branch_name"]});
                });


                 $('.select2#detail_user_branch, .select2#userlist_branch').select2({
                    placeholder: "Select a branch",
                    data: data,
                    width: "100%"
                 }).val(selectedbranch).trigger("change");
            }
        });

  	});

	$("#updateUser_submitBtn").on("click", function(){
		var id = $("div#userlist_detail_modal").data("id");
		var firstname = $("input#detail_userlist_firstname").val();
		var lastname = $("input#detail_userlist_lastname").val();
		var email = $("input#detail_userlist_email").val();
		var branch = $("select#detail_user_branch").val();
		var access_level = $("select#detail_userlist_access_lvl").val();

		var d = {
			"id": id,
			"firstname": firstname,
			"lastname": lastname,
			"email": email,
			"branch_id": branch,
			"access_level": access_level
		}

		var inputs = $("form#detailUserForm").find("input");
		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

		if(empty > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.", {
	          type: "danger",
	          width: 300
	        });
			return;
		}

		if (!validateEmail(email)) {
			$("input#userlist_email").addClass("emptyField");
		    $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Incorrect email format.", {
	          type: "danger",
	          width: 300
	        });s
			return;
		}

		var emaillist = $("table#userlisttable tbody tr td.useremailtd");
		var emailexist = 0;
		var currentemail = $("table#userlisttable tbody tr#"+id+" td.useremailtd");
		$.each(emaillist, function(ind, row){
			if($(row).html() != $(currentemail).html())
				if($(row).html() == email){
			        emailexist++;
				}
		});

		if(emailexist > 0){
			$("input#detail_userlist_email").addClass("emptyField");
		    $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Email already taken.", {
	          type: "danger",
	          width: 300
	        });

	        return;
		}

		var branchopt = $("select#detail_user_branch").select2('data');
		var selectedbranchtxt = branchopt[0].text;

		$.ajax({
			method: "POST",
			data: d,
			url: baseurl+"/userlist/update",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var style = "";
					$("table#userlisttable tbody tr#"+id).removeClass("admintr");
					$("table#userlisttable tbody tr#"+id).attr({"style":""});
					if(access_level == 0){
						$("table#userlisttable tbody tr#"+id).addClass("admintr");
					$("table#userlisttable tbody tr#"+id).attr({"style":"color:#ed8533"});
					}

					var td = '<td>'+res["firstname"]+'</td>'
								+ '<td>'+res["lastname"]+'</td>'
								+ '<td class="useremailtd">'+email+'</td>'
								+ '<td>'+selectedbranchtxt+'</td>'
								+ '<td style="display:none">'+branch+'</td></tr>';

		            $("table#userlisttable tbody tr#"+id).html(td);
					$("div#userlist_detail_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!", {
			          type: "success",
			          width: 300
			        });
				}
			}
		});
	});

	/*delete record*/
	$("button#delete_userlist").on("click", function(){
		$("div#confirm_modal").modal("show");
	});

	$("a#confirm_delete_userlist_btn").on("click", function(){
		var id = $("div#userlist_detail_modal").data("id");

		var datas = {
			"id" : id
		}

		$.ajax({
			url: baseurl+"/userlist/delete",
			method: "POST",
			data: datas,
			success: function(data){
	        	var data = JSON.parse(data);
	        	if(data["success"]){
	        		$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.", {
		              type: "success",
		              width: 300
		            });

	        		$("div#userlist_detail_modal").modal("hide");
	        		$("table#userlisttable").find("tr#"+id).remove();
	        	}

			}
		});

		$('div#userlist_detail_modal').on('hide.bs.modal', function () {
	        $("div#confirm_modal").modal("hide");
	        $('html, body').css({
	            overflow: 'hidden',
	            height: '100%'
        	});
      	});
	});

});