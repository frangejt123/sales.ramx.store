$(document).ready(function(){

	NProgress.configure({ showSpinner: false });
	var selecteddispatch = $("#selected_dispatch").val();

	$("#cancel_dispatchdetail_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/dispatch";
	});

	$("iframe#detailIframe").attr("src", "");

	$("#dispatchdetail_table tbody").on("click", "tr", function(e){
		NProgress.start();
		var td = $(this).find("td");
		var id = td[0].textContent;

		$("#dispatchdetail_table tr").removeClass("active");
		$(this).addClass("active");

		$("#delete_dispatchdetail_btn").removeClass("disabled");

		$(".undo_delete_div").hide();
		if($(this).hasClass("mark-deleted")){
			$(".undo_delete_div").show();
		}

		$("iframe#detailIframe").attr("src", baseurl + "/main/dispatchdetail/"+btoa(id)).on("load", function () {
			NProgress.done();
		});
	});

	$("#undo_dispatchdetail_btn").on("click", function(){
		$(".undo_delete_div").hide();

		$("#dispatchdetail_table tbody tr.active").removeClass("mark-deleted hasChanges");
	});

	$("#delete_dispatchdetail_btn").on("click", function(){
		var selectedtr = $("#dispatchdetail_table tbody tr.active");
		if($(this).hasClass("disabled"))
			return false;

		if($(selectedtr).hasClass("mark-new")){
			$(selectedtr).remove();
			$("iframe#detailIframe").attr("src", "");
		}else{
			$(".undo_delete_div").show();
			$(selectedtr).addClass("mark-deleted hasChanges");
		}
	});

	$("#process_dispatch_btn").on("click", function(){
		$("span#dispatchnewstatus").text("For Delivery");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #c57e19",
			"color": "#f0ad4e"
		}).html('<i class="fa fa-truck"></i>');
		$("#statusmodal").modal("show");
	});

	$("#complete_dispatch_btn").on("click", function(){
		$("span#dispatchnewstatus").text("Delivered");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #0a6a7a",
			"color": "#17a2b8"
		}).html('<i class="fa fa-truck fa-check"></i>');
		$("#statusmodal").modal("show");
	});

	$("#confirm_change_status").on("click", function(){
		if($("span#dispatchnewstatus").text() == "For Delivery"){
			changestatus(1);
		}else{
			changestatus(4);
		}
	});

	$("#add_dispatchdetail_btn").on("click", function(){
		$("form#transaction_detail_form input#transaction_id, #transaction_id_autocomplete").val("");

		$("iframe#trxdetailIframe").attr("src", "");
		$("#dispatchdetailmodal").modal("show");
	});

	$("#add_trx_to_detail").on("click", function(){
		var trxid = $("form#transaction_detail_form input#transaction_id").val();
		var formatedID = $('#transaction_id_autocomplete').val();
		var customernName = customerlist[trxid];

		if ($("#dispatchdetail_table tbody tr#tr_"+trxid).length > 0) {
			alert("Transaction already added.");
			return;
		}

		$("#dispatchdetail_table tbody tr").removeClass("active");

		var tr = "<tr id='tr_' class='mark-new hasChanges active'>";
			tr += "<td hidden>"+trxid+"</td>";
			tr += "<td>"+formatedID+"</td>";
			tr += "<td>"+customernName+"</td>";

		$("#dispatchdetail_table tbody").prepend(tr);
		$("#dispatchdetail_table tbody").find("tr#tr_"+trxid).trigger("click");

		$("#dispatchdetailmodal").modal("hide");
	});

	$("#save_dispatchdetail_btn").on("click", function(){
		var id = selecteddispatch;
		var type = "new";
		var driver = $("#driver_id").val();
		var dispatch_date = $("#dispatch_date").val();

		var dispatchdetail = $("#dispatchdetail_table tbody").find("tr.hasChanges");

		if(driver_id == ""){
			alert("Please select driver.");
			return;
		}

		if(dispatch_date == ""){
			alert("Please select dispatch date.");
			return;
		}

		if(id != undefined)
			type = "edit";
		else {
			if (dispatchdetail.length == 0) {
				alert("Please add dispatch detail.");
				return;
			}
		}

		var dispatch = {id,driver,dispatch_date,type}
		var detail = [];

		$.each(dispatchdetail, function(ind, row){
			var id = ($(row).attr("id")).replace("tr_", "");
			var detailtype = $(row).hasClass("mark-deleted") ? "delete" : "new";

			var td = $(this).find("td");
			var transaction_id = td[0].textContent;

			var datarow = {id, transaction_id, detailtype};
			detail.push(datarow);
		});

		var data = {dispatch, detail};
		$.ajax({
			method: 'POST',
			data: data,
			url: baseurl+'/dispatch/save',
			success: function(res){
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved!");
					location.reload();
				}
				$("#save_dispatchdetail_btn").removeAttr("disabled");
			},
			error: function(xhr, status, error){
				$("#save_dispatchdetail_btn").removeAttr("disabled");
				NProgress.done();
				alert("Oppss!. Something went wrong!.")
			},
			beforeSend: function(){
				NProgress.start();
				$("#save_dispatchdetail_btn").attr("disabled", "disabled");
			}
		});
	});

	function changestatus(status){
		var data = {"id":selecteddispatch, status};

		$.ajax({
			method: 'POST',
			data: data,
			url: baseurl + '/dispatch/updatestatus',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}
			},
			error: function (xhr, status, error) {
				NProgress.done();
				alert("Oppss! Something went wrong.");
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	}

	driverautocomplete();
	function driverautocomplete(){
		// Initialize ajax autocomplete:
		var driverArray = $.map(driverlist, function (value, key) {
			return {value: value, data: key};
		});
		$('#driver_id_autocomplete').autocomplete({
			lookup: driverArray,
			lookupLimit: 5,
			lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function (suggestion) {
				//$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				var id = suggestion.data;
				$("form#dispatch_detail_form input#driver_id").val(id);
			},
			onHint: function (hint) {
				$('#driver_autocomplete_hint').val(hint);
			},
			onInvalidateSelection: function () {
				$("form#dispatch_detail_form input#driver_id").val("");
			}
		});
	}

	trxautocomplete();
	function trxautocomplete(){
		// Initialize ajax autocomplete:
		var trxArray = $.map(trxlist, function (value, key) {
			return {value: value, data: key};
		});
		$('#transaction_id_autocomplete').autocomplete({
			lookup: trxArray,
			lookupLimit: 5,
			lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function (suggestion) {
				//$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				var id = suggestion.data;
				$("form#transaction_detail_form input#transaction_id").val(id);

				NProgress.start();
				$("iframe#trxdetailIframe").attr("src", baseurl + "/main/dispatchdetail/"+btoa(id)).on("load", function () {
					NProgress.done();
				});
			},
			onHint: function (hint) {
				$('#transaction_autocomplete_hint').val(hint);
			},
			onInvalidateSelection: function () {
				$("form#transaction_detail_form input#transaction_id").val("");
			}
		});
	}

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
