$(document).ready(function(){

	NProgress.configure({ showSpinner: false });
	var selectedorder = $("#selected_order").val();

	$("button#cancel_orderlist_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl;
	});

	$(".void_notif").on("click", function(){
		$("#voiddetailmodal").modal("show");
	});

	$("#close_void_detail").on("click", function(){
		$("#voiddetailmodal").modal("hide");
	});
	
	$("#print_order_btn").on("click", function(){
		$("form#report_data input#trans_id").val(selectedorder)

		$.ajax({
			method: 'POST',
			data: {"id":selectedorder, "printed":"1", "print_date": "now"},
			url: baseurl + '/main/updateorder',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				$(".grid_container #date_printed").html("<span class='text-primary'><i class='fa fa-print'></i> &nbsp;Printed &mdash; "+res["param"]["date_printed"]+"</span>");

				window.open('', 'new_window');
				document.getElementById('report_data').submit();
			},
			error: function (xhr, status, error) {
				NProgress.done();
				alert("Oppss! Something went wrong.");
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});

	/* AJAX LONG POLLING. CHECK FOR CHANGES */
	poll();
	function poll(){
		$.ajax({
			method: 'POST',
			data: {"id":selectedorder},
			url: baseurl + '/main/checkchanges',
			success: function (res) {
				var res = JSON.parse(res);
				updateChanges(selectedorder)
				if(res["status"] == "3"){
					$(".transaction_detail_container, .detail_grand_total, table#orderdata_table tbody").addClass("voided");
					$(".void_notif").show();
					$(".void_notif span").html(res["void_user"]);
					$("#voiddetailmodal p#voidreason").text("Reason: "+res["void_reason"]);
					$("#void_order_btn").hide();
					$("#process_order_btn").hide();
					$("#update_order_btn").hide();
					$("#unpaid_order_btn").hide();
					$("#paid_order_btn").hide();
					$(".dropdown").not(".detail_action").hide();
					$("#statusmodal").modal("hide");
				}else{
					alert("Order have been modified. Press OK to reload data.");
					location.reload();
				}
			},
			error: function (xhr, status, error) {
				poll();
			}
		});
	}
	/* AJAX LONG POOLING. CHECK FOR CHANGES */

	function updateChanges(id){
		$.ajax({
			method: 'POST',
			data: {id},
			url: baseurl + '/main/updatechanges',
			success: function (res) {

			},
			error: function (xhr, status, error) {

			}
		});
	}

	$("#void_order_btn").on("click", function(){
		$("#void_detail_modal").modal("show");
	});

	$("select#void_reason_sel").on("change", function(){
		var selected = $(this).val();
		if(selected == 2){
			$("textarea#other_void_reason").removeAttr("disabled");
		}else{
			$("textarea#other_void_reason").val("");
			$("textarea#other_void_reason").attr("disabled", "disabled");
		}
	});

	$("#confirm_void_order").on("click", function(){
		var void_reason = $("select#void_reason_sel").val();
		var other = $("textarea#other_void_reason").val();
		if(void_reason == 2){
			if(other == ""){
				alert("Please specify reason");
				return;
			}
		}

		$.ajax({
			method: 'POST',
			data: {"id":selectedorder, void_reason, other},
			url: baseurl + '/main/voidorder',
			success: function (res) {
				var res = JSON.parse(res);
				if(res["success"]){
					$(".transaction_detail_container, .detail_grand_total, table#orderdata_table tbody").addClass("voided");
					$(".void_notif").show();
					$(".void_notif span").html(res["param"]["void_user"]);
					$("#update_order_btn").hide();
					$("#process_order_btn").hide();
					$("#unpaid_order_btn").hide();
					$("#paid_order_btn").hide();
					$("#void_order_btn").hide();
					$(".dropdown").not(".detail_action").hide();
					$("#void_detail_modal").modal("hide");
					$("#statusmodal").modal("hide");
				}
			},
			error: function (xhr, status, error) {
				alert("Oppss! Something went wrong.");
			}
		});
	});

	$("#pending_order_btn").on("click", function(){
		$("span#ordernewstatus").text("Pending");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #388638",
			"color": "#5cb85c"
		}).html('<i class="fa fa-star-half-o"></i>');

		$("#statusmodal").modal("show");
	});

	$("#process_order_btn").on("click", function(){
		$("span#ordernewstatus").text("For Delivery");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #c57e19",
			"color": "#f0ad4e"
		}).html('<i class="fa fa-truck"></i>');

		$("#statusmodal").modal("show");
	});

	$("#delivered_order_btn").on("click", function(){
		$("span#ordernewstatus").text("Delivered");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #0a6a7a",
			"color": "#17a2b8"
		}).html('<i class="fa fa-truck fa-flip-horizontal"></i>');

		$("#statusmodal").modal("show");
	});

	$("#complete_order_btn").on("click", function(){
		$("span#ordernewstatus").text("Complete");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #104675",
			"color": "#286090"
		}).html('<i class="fa fa-check"></i>');

		$("#statusmodal").modal("show");
	});

	$("#confirm_change_status").on("click", function(){
		if($("span#ordernewstatus").text() == "For Delivery"){
			changeorderstatus(1);
		}else if($("span#ordernewstatus").text() == "Pending"){
			changeorderstatus(0);
		}else if($("span#ordernewstatus").text() == "Delivered"){
			changeorderstatus(4);
		}else{
			changeorderstatus(2);
		}
	});

	$("#cancel_change_status").on("click", function(){
		$("#statusmodal").modal("hide");
	});

	$("#update_order_btn").on("click", function(){
		var orderid = $("#selected_order").val();
		NProgress.start();
		window.location = baseurl + "/main/ut/"+btoa(orderid);
	});

	$("#paid_order_btn").on("click", function(){
		$("select#mode_of_payment").val(transmop);
		$("#payment_confirmation_detail").val(transpcd);
		$("#paid_amount").val($("#balance").val());
		$("#tag_as_paid_modal").modal("show");
	});

	$("#unpaid_order_btn").on("click", function(){
		$("#unpaid_modal").modal("show");
	});

	$("#confirm_tag_as_paid").on("click", function(){
		var amount = $("#paid_amount").val();
		var payment_method = $("#mode_of_payment").val();
		var payment_confirmation_detail = $("#payment_confirmation_detail").val();

		var balance = $("#balance").val();

		$.ajax({
			method: 'POST',
			data: {"transaction_id":selectedorder, payment_method, payment_confirmation_detail, amount, balance},
			url: baseurl + '/main/insertpayment',
			success: function (res) {
				var res = JSON.parse(res);
				alert("Changes successfully saved!");
				NProgress.done();
				location.reload();
			},
			error: function (xhr, status, error) {
				NProgress.done();
				alert("Oppss! Something went wrong.");
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});

	$("#confirm_unpaid").on("click", function(){
		$.ajax({
			method: 'POST',
			data: {"id":selectedorder, "paid":"0"},
			url: baseurl + '/main/updateorder',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				location.reload();
			},
			error: function (xhr, status, error) {
				NProgress.done();
				alert("Oppss! Something went wrong.");
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});

	$("#cancel_tag_as_paid").on("click", function(){
		$("#tag_as_paid_modal").modal("hide");
	});

	$("#cancel_unpaid").on("click", function(){
		$("#unpaid_modal").modal("hide");
	});

	$("#payment_history_btn").on("click", function(){
		$("#payment_history_modal").modal("show");
	});

	$("#order_history_btn").on("click", function(){
		$("#order_history_modal").modal("show");
	});

	$(".payment_history_tr").on("mouseover", function(){
		$(".grid-btn").hide();
		$(this).find(".grid-btn").show();
	});

	$(".order_history_tr").on("mouseover", function(){
		$(".grid-btn").hide();
		$(this).find(".grid-btn").show();
	});

	$(".view_history_detail").on("click", function(){
		var id = $(this).attr("id").split("_")[1];
		$("div.history-table").hide();
		$("div#table_old_"+id+", div#table_new_"+id+", div#table_detail_old_"+id+", div#table_detail_new_"+id).show();
		$("#history_detail_modal").modal("show");
	});

	$(".delete_payment").on("click", function(){
		var id = $(this).attr("id").split("_")[1];
		var amount = $("#paymenthistory_table tr#tr_"+id).find("td")[2];
		$("#delete_payment_modal").modal("show").data({"payment_id": id, "amount":amount.innerText});
	});

	$("#confirm_delete_payment").on("click", function(){
		var id = $("#delete_payment_modal").data("payment_id");
		var amount = $("#delete_payment_modal").data("amount");
		var transaction_id = selectedorder;
		$.ajax({
			method: 'POST',
			data: {id, transaction_id},
			url: baseurl + '/main/deletepayment',
			success: function (res) {
				var res = JSON.parse(res);
				$("#delete_payment_modal").modal("hide");
				alert("Changes successfully saved");
				location.reload();
				NProgress.done();
			},
			error: function (xhr, status, error) {
				NProgress.done();
				alert("Oppss! Something went wrong.");
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});

	$(".edit_payment").on("click", function(){
		var id = $(this).attr("id").split("_")[1];
		var td = $("#paymenthistory_table tr#tr_"+id).find("td");
		var amount = td[2].innerText;
		var mop = td[5].innerText;
		var pcd = td[3].innerText;
		$("#update_mode_of_payment").val(mop);
		$("#update_paid_amount").val(parseFloat(amount));
		$("#update_payment_confirmation_detail").val(pcd);
		$("#update_payment_modal").modal("show").data("payment_id", id);
	});

	$("#confirm_updatepayment").on("click", function(){
		var id = $("#update_payment_modal").data("payment_id");
		var td = $("#paymenthistory_table tr#tr_"+id).find("td");
		var transaction_id = selectedorder;
		var oldamount = td[2].innerText;
		var amount = $("#update_paid_amount").val();
		var payment_method = $("#update_mode_of_payment").val();
		var moptext = $("#update_mode_of_payment option:selected").text();
		var payment_confirmation_detail = $("#update_payment_confirmation_detail").val();

		var balanceval = $("input#balance").val();
		var oldbalance = (parseFloat(balanceval) + parseFloat(oldamount))
		var newbalance = oldbalance - parseFloat(amount);
		$("input#balance").val(newbalance);

		$.ajax({
			method: 'POST',
			data: {id, amount, payment_method, payment_confirmation_detail, newbalance, transaction_id},
			url: baseurl + '/main/updatepayment',
			success: function (res) {
				var res = JSON.parse(res);
				$("#update_payment_modal").modal("hide");
				alert("Changes successfully saved");
				location.reload();
				NProgress.done();
			},
			error: function (xhr, status, error) {
				NProgress.done();
				alert("Oppss! Something went wrong.");
			},
			beforeSend: function(){
				NProgress.start();
			}
		});

	});

	function changeorderstatus(status){
		$.ajax({
			method: 'POST',
			data: {"id":selectedorder, status},
			url: baseurl + '/main/updateorder',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				location.reload();
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
});
