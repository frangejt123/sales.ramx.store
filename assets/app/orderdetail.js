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
				$(".grid_container #date_printed span").html("<i class='fa fa-print'></i> &nbsp;Printed &mdash; "+res["param"]["date_printed"]);

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
					$("#voiddetailmodal p#voidreason").text("Reason: "+res["void_reason"]);
					$("#void_order_btn").hide();
					$("#process_order_btn").hide();
					$("#update_order_btn").hide();
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
					$("#update_order_btn").hide();
					$("#process_order_btn").hide();
					$("#void_order_btn").hide();
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
		window.location = baseurl + "/main/ut/"+btoa(orderid);
	});

	$("#paid_order_btn").on("click", function(){
		$("#tag_as_paid_modal").modal("show");
	});

	$("#unpaid_order_btn").on("click", function(){
		$("#unpaid_modal").modal("show");
	});

	$("#confirm_tag_as_paid").on("click", function(){
		var payment_method = $("#mode_of_payment").val();
		var payment_confirmation_detail = $("#payment_confirmation_detail").val();

		$.ajax({
			method: 'POST',
			data: {"id":selectedorder, "paid":"1", payment_method, payment_confirmation_detail},
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
