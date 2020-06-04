$(document).ready(function(){

	NProgress.configure({ showSpinner: false });

	var croppieimg = "";
	var imghaschanges;
	var croppie;
	var croppieready = false;

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

	$("#reconcile_order_btn").on("click", function(){
		$("#reconcile_order").modal("show");
	});

	$("#confirm_reconcile").on("click", function(){
		$.ajax({
			method: 'POST',
			data: {"id":selectedorder, "reconcile":"1"},
			url: baseurl + '/main/updateorder',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				alert("Changes successfully saved.");
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
		$("#driver_name_grp, #date_delivered_grp").hide();
		$("span#ordernewstatus").text("Pending");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #388638",
			"color": "#5cb85c"
		}).html('<i class="fa fa-star-half-o"></i>');

		$("#statusmodal").modal("show");
	});

	$("#process_order_btn").on("click", function(){
		$("#date_delivered_grp").hide();
		$("#driver_name_grp").show();
		$("span#ordernewstatus").text("For Delivery");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #c57e19",
			"color": "#f0ad4e"
		}).html('<i class="fa fa-truck"></i>');

		$("#statusmodal").modal("show");
	});

	$("#delivered_order_btn").on("click", function(){
		$("#driver_name_grp").hide();
		$("#date_delivered_grp").show();
		$("span#ordernewstatus").text("Delivered");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #0a6a7a",
			"color": "#17a2b8"
		}).html('<i class="fa fa-truck fa-flip-horizontal"></i>');

		$("#statusmodal").modal("show");
	});

	$("#complete_order_btn").on("click", function(){
		$("#driver_name_grp, #date_delivered_grp").hide();
		$("span#ordernewstatus").text("Complete");
		$("#statusmodal").find(".icon-box").css({
			"border": "3px solid #104675",
			"color": "#286090"
		}).html('<i class="fa fa-check"></i>');

		$("#statusmodal").modal("show");
	});

	$("#confirm_change_status").on("click", function(){
		if($("span#ordernewstatus").text() == "For Delivery"){
			if($("#driver_id").val() == "")
				return;
			changeorderstatus(1);
		}else if($("span#ordernewstatus").text() == "Pending"){
			changeorderstatus(0);
		}else if($("span#ordernewstatus").text() == "Delivered"){
			if($("#date_delivered").val() == "")
				return;
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

		if($(this).hasClass("disabled"))
			return;

		setTimeout(function(){
			if(imghaschanges) {
				$('#payment_img_preview').croppie("result", {
					type: "base64",
					format: "jpeg"
				}).then(function (img) {
					croppieimg = img;
				});
			}
		}, 500);

		NProgress.start();
		$("#confirm_tag_as_paid").addClass("disabled");
		setTimeout(function(){
			var payment_img = croppieimg;
			$.ajax({
				method: 'POST',
				data: {"transaction_id":selectedorder, payment_method, payment_confirmation_detail, amount, balance, payment_img},
				url: baseurl + '/main/insertpayment',
				success: function (res) {
					var res = JSON.parse(res);
					alert("Changes successfully saved!");
					$("#confirm_tag_as_paid").removeClass("disabled");
					NProgress.done();
					location.reload();
				},
				error: function (xhr, status, error) {
					NProgress.done();
					$("#confirm_tag_as_paid").removeClass("disabled");
					alert("Oppss! Something went wrong.");
				},
				beforeSend: function(){
					$("#confirm_tag_as_paid").addClass("disabled");
				}
			});

		}, 800);
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
		var amount = td[3].innerText;
		var mop = td[6].innerText;
		var pcd = td[4].innerText;
		var imagename = td[7].innerText;

		var bsurl = baseurl.replace("index.php", "");

		$("#update_mode_of_payment").val(mop);
		$("#update_paid_amount").val(parseFloat(amount));
		$("#update_payment_confirmation_detail").val(pcd);
		$("img#payment_img_preview_update").attr('src', bsurl+"assets/payment_image/"+imagename+".jpeg");
		$("#update_payment_modal").modal("show").data("payment_id", id);
	});

	$("#confirm_updatepayment").on("click", function(){
		var id = $("#update_payment_modal").data("payment_id");
		var td = $("#paymenthistory_table tr#tr_"+id).find("td");
		var transaction_id = selectedorder;
		var oldamount = td[2].innerText;
		var oldimgname = td[6].innerText;
		var amount = $("#update_paid_amount").val();
		var payment_method = $("#update_mode_of_payment").val();
		var moptext = $("#update_mode_of_payment option:selected").text();
		var payment_confirmation_detail = $("#update_payment_confirmation_detail").val();

		var balanceval = $("input#balance").val();
		var oldbalance = (parseFloat(balanceval) + parseFloat(oldamount))
		var newbalance = oldbalance - parseFloat(amount);
		$("input#balance").val(newbalance);

		setTimeout(function(){
			if(imghaschanges) {
				$('#payment_img_preview_update').croppie("result", {
					type: "base64",
					format: "jpeg"
				}).then(function (img) {
					croppieimg = img;
				});
			}
		}, 500);

		NProgress.start();
		setTimeout(function() {
			var payment_img = croppieimg;
			$.ajax({
				method: 'POST',
				data: {id, amount, payment_method, payment_confirmation_detail, newbalance, transaction_id, payment_img, oldimgname},
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
				}
			});
		}, 800);
	});

	$("#tag_as_paid_modal").on("shown.bs.modal", function(){
		setTimeout(function(){
			if (!croppieready) {
				croppie = $('#payment_img_preview').croppie({
					"viewport": {
						width: ($(".payment_image").width()+2)+"px",
						height: 330,
						type: 'square'
					},
					enforceBoundary: false,
				});
				croppieready = true;
			}
		},600);
	});

	$("#payment_proof").change(function(){
		var imgfile = $(this).val();
		var extension = imgfile.replace(/^.*\./, '');
		if (extension == imgfile)
			extension = '';
		else
			extension = extension.toLowerCase();

		var currentimgsrc = $("#payment_img_preview").attr("src");

		if(extension !== "jpg" && extension !== "jpeg"){
			alert("Please upload JPEG / JPG file only.");
			$(this).val("");
			$("#payment_img_preview").attr("src", currentimgsrc);
			return;
		}

		$("#payment_img_preview").css({
			width: $("#payment_preview").width() + "px",
		});

		readURL(this, "");
	});

	$("#payment_proof_update").change(function(){
		var imgfile = $(this).val();
		var extension = imgfile.replace(/^.*\./, '');
		if (extension == imgfile)
			extension = '';
		else
			extension = extension.toLowerCase();

		var currentimgsrc = $("#payment_img_preview_update").attr("src");

		if(extension !== "jpg" && extension !== "jpeg"){
			alert("Please upload JPEG / JPG file only.");
			$(this).val("");
			$("#payment_img_preview_update").attr("src", currentimgsrc);
			return;
		}

		$("#payment_img_preview_update").css({
			width: $("#payment_preview_update").width() + "px",
		});

		readURL(this, "_update");
	});

	function readURL(input, update) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#payment_img_preview'+update).attr('src', e.target.result);
					setTimeout(function() {
						if (!croppieready) {
							croppie = $('#payment_img_preview'+update).croppie({
								"viewport": {
									width: ($(".payment_image"+update).width()+2)+"px",
									height: 330,
									type: 'square'
								},
								enforceBoundary: false,
							});
							croppieready = true;
						}
						croppie.croppie('bind', {
							url: e.target.result
						});
					}, 500);
				imghaschanges = true;
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	function changeorderstatus(status){
		var driver_id = $("#driver_id").val();
		var date_delivered = $("#date_delivered").val();
		var data = {"id":selectedorder, status};
		if(status == "1")
			data["driver_id"] = driver_id;
		if(status == "4")
			data["date_delivered"] = date_delivered;

		$.ajax({
			method: 'POST',
			data: data,
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

	inputautocomplete();
	function inputautocomplete() {
		// Initialize ajax autocomplete:
		var driverArray = $.map(driverlist, function (value, key) {
			return {value: value, data: key};
		});
		$('#driver_filter').autocomplete({
			lookup: driverArray,
			lookupLimit: 5,
			lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function (suggestion) {
				//$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				var id = suggestion.data;
				$("#driver_id").val(id);
			},
			onHint: function (hint) {
				$('#driver_autocomplete_hint').val(hint);
			},
			onInvalidateSelection: function () {
				$("#driver_id").val("");
			}
		});
	}

});
