$(document).ready(function(){
	var burl = baseurl.replace("/index.php", "");

	NProgress.configure({ showSpinner: false });

	if(typeof (localStorage["store_id"]) !== 'undefined') {
		localStorage["store_id"] = localStorage["store_id"];
		switchpage(localStorage["store_id"]);
	}else{
		localStorage["store_id"] = "1";
	}

	var ordertable = $('#orderlist_table').DataTable({
		"processing": true,
		"serverSide": true,
		"pageLength": 20,
		"bLengthChange": false,
		"order": [],
		'serverMethod': 'post',
		'stateSave': true,
		'ajax': {
			'url': baseurl + "/main/orderlist",
			'data': function(d){
				d.order_number = $("#order_id_filter").val();
				d.delivery_date = $("#filter_delivery_date").val();
				d.payment_methods =  $("#filter_mop").val();
				d.status = $("#filter_status").val();
				d.paid = $("#filter_paid").prop('checked') ? "1" : "";
				d.printed = $("#filter_printed_status").val();
				d.store_id = localStorage["store_id"];
				d.city_id = $("#city").val();
			}
		},
		"columns": [
			{"data": "id"},
			{"data": "datetime"},
			{"data": "delivery_date"},
			{"data": "name"},
			{"data": "driver_name"},
			{"data": "paid"},
			{"data": "payment_method"},
			{"data": "printed"},
			{"data": "status"},
			{"class": "hide_column", "data": "frm_delivery_date"},
			{"class": "hide_column","data": "transaction_id"}
		],
		"createdRow": function( row, data, dataIndex, cells) {
			$(row).attr("id", "tr_"+data["transaction_id"]);
			$(cells[8]).addClass(data["status_class"]);
		}
	});

	$('#orderlist_table').on('preXhr.dt', function ( e, settings, json, xhr ) {
		$('.dataTables_processing').hide();
		$("#page_mask").show();
	});

	$('#orderlist_table').on('xhr.dt', function ( e, settings, json, xhr ) {
		$('.dataTables_processing').hide();
		$("#page_mask").hide();
	});

	$('.dataTables_processing').hide();

	$("#page_mask").css({"width": $(document).width(), "height":$(document).height()});

	$('.select2').select2()
	$('.input_daterangepicker').daterangepicker();

	$("input#search_table").on("change", function(){
		localStorage["searchvalue"] = $(this).val();
		ordertable.search($(this).val()).draw();
	});

	if(typeof (localStorage["searchvalue"]) !== 'undefined') {
		$("input#search_table").val(localStorage["searchvalue"]);
	}

	$("button#create_order_btn").on("click", function(){
		NProgress.start();

		// $("form#form_store_id input#input_store_id").val(localStorage["store_id"]);
		// $('#form_store_id').attr("action", baseurl + "/main/pos");
		// document.getElementById('form_store_id').submit();

		window.location = baseurl + "/main/pos";
	});

	$("#inventory_adjustment_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/inventory/adjustment";
	});

	$("#customer_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/customer";
	});

	$("#user_list_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/user";
	});

	$("#driver_list_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/driver";
	});

	$("#product_list_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/product";
	});

	$("#rgc_dispatch").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/dispatch";
	});

	$("table#orderlist_table tbody").on("click", "tr", function(){
		var id = $(this).attr("id").split("_")[1];
		//var id = "889";
		NProgress.start();
		window.location = baseurl + "/main/orderdetail/"+btoa(id);
	});

	$("#logout_btn").on("click", function(){
		NProgress.start();
		$.ajax({
			method: 'POST',
			url: baseurl + '/login/logout',
			success: function (res) {
				if(res == "success"){
					localStorage.removeItem("filter");
					localStorage.removeItem("inverse");
					localStorage.removeItem("thIndex");
					ordertable.state.clear();
					localStorage.removeItem("searchvalue");
					localStorage.removeItem("filter");
					localStorage.removeItem("store_id");
					window.location = baseurl + "/login";
				}
			}
		});
	});

	$(".rpt_btn").on("click", function(){
		var title = $(this).html();
		var id = $(this).attr("id");
		var param = $(this).attr("data-filter").split(",");

		$("select").val("");
		$(".select2").select2().val("").trigger("change")
		$("input").val("");
		$("input[type='checkbox']").iCheck("uncheck");

		$("#id_value").val("");
		$("#report_param_modal #reportModalLabel").html(title);
		$("#report_param_modal").find(".filter_param").hide();
		$.each(param, function(ind, row){
			$("#report_param_modal").find("."+row).show();
		});
		$("#report_param_modal").data("rpt_name", id).modal("show");
	});

	$("#print_report").on("click", function(){
		var deliverydate = $("#rpt_delivery_date").val();
		var order_id = $("#id_value").val();
		var from_to = $("#delivery_date_from_to").val();
		var rpt_name = ($("#report_param_modal").data("rpt_name")).replace('_rpt','');

		var param_mop = $("#rpt_param_mop").val();
		var param_status = $("#rpt_param_status").val();
		var param_trxdate = $("#rpt_param_trxdate").val();
		var param_paymentdate = $("#rpt_param_paymentdate").val();

		if(from_to != ""){
			$("form#report_data input#param").val(from_to);
		}

		if(order_id != ""){
			$("form#report_data input#param").val(order_id);
		}

		if(deliverydate != ""){
			$("form#report_data input#param").val(deliverydate);
		}

		if(param_mop != ""){
			$("form#report_data input#param_mop").val(param_mop);
		}
		if(param_status != ""){
			$("form#report_data input#param_status").val(param_status);
		}

		if($("#rpt_param_paid").prop('checked')){
			$("form#report_data input#param_paid").val("1");
		}

		if($("#rpt_param_unpaid").prop('checked')){
			$("form#report_data input#param_unpaid").val("1");
		}

		if(param_trxdate != ""){
			$("form#report_data input#param_trxdate").val(param_trxdate);
		}

		if(param_paymentdate != ""){
			$("form#report_data input#param_paymentdate").val(param_paymentdate);
		}

		$('#report_data').attr("action", baseurl+"/report/"+rpt_name);

		window.open('', 'new_window');
		document.getElementById('report_data').submit();


		$("#report_param_modal").modal("hide");

		$("form#report_data input#param_status").val("");
		$("form#report_data input#param_mop").val("");
		$("form#report_data input#param_paid").val("");
		$("form#report_data input#param_unpaid").val("");
		$("form#report_data input#param_trxdate").val("");
		$("form#report_data input#param").val("");
	});

	/* AJAX LONG pollING */

	setTimeout(function(){
		longpoll();
	}, 1000);

	function longpoll() {
		var lastid = $("#order_last_id").val();
		$.ajax({
			method: 'POST',
			data: {lastid},
			url: baseurl + '/main/neworder',
			success: function (res) {
				var res = JSON.parse(res);
				var moparr = ["Cash on Delivery", "Bank Transfer - BPI", "GCash", "Bank Transfer - Metrobank"];
				var statusarr = ["Pending", "For Delivery", "Completed"];
				var tdclass = ["text-success", "text-warning", "text-primary"];
				$.each(res["orders"], function (ind, row) {
					//var paidCls = row["paid"] == "1" ? "text-success" : "";
					var rowPaid = row["paid"] == "1" ? "Paid" : "";
					var printCls = row["printed"] == "1" ? "text-success" : "";
					var rowPrint = row["printed"] == "1" ? "Paid" : "";
					var tr = "<tr id='tr_" + row["id"] + "'>";
					tr += "<td>" + row["formatid"] + "</td>";
					tr += "<td>" + row["datetime"] + "</td>";
					tr += "<td>" + row["delivery_date"] + "</td>";
					tr += "<td>" + row["name"] + "</td>";
					tr += "<td>" + (row["driver_name"] != "null" ? "---" : row["driver_name"]) + "</td>";
					tr += "<td>" + rowPaid + "</td>";
					tr += "<td>" + moparr[row["payment_method"]] + "</td>";
					tr += "<td>" + rowPrint + "</td>";
					tr += "<td class='" + tdclass[row["status"]] + "'>" + statusarr[row["status"]] + "</td>";
					tr += "<td hidden>"+row["delivery_date"]+"</td>";
					tr += "<td hidden>"+row["id"]+"</td>";
					tr += "</tr>";


					$("table#orderlist_table tbody").prepend(tr);
				});

				$("#order_last_id").val(res["lastid"]);
				longpoll();
			},
			error: function (xhr, status, error) {
				longpoll();
			}
		});
	}
	/* AJAX LONG pollING */

	/*$("#product_list_btn").on("click", function(){
		$.ajax({
			method: 'POST',
			url: baseurl + '/main/productlist',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				var tr = "";
				$.each(res, function (ind, row) {
					var avail_qty = row["avail_qty"] == null ? "0.00" : row["avail_qty"];
					tr += "<tr id='tr_" + row["id"] + "'>";
					tr += "<td class='pdesc'>" + row["description"] + "</td>";
					tr += "<td class='pprice'>" + parseFloat(row["price"]).toFixed(2) + "</td>";
					tr += "<td>" + parseFloat(avail_qty).toFixed(2) + "</td>";
					tr += "</tr>";
				});

				$("table#product_table tbody").html(tr);

				$("#product_list_modal").modal("show");
			},
			error: function (xhr, status, error) {
				NProgress.done();
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});*/

	var deviceheight = document.documentElement.clientHeight;
	$('#product_container').slimScroll({
		height: deviceheight - 350 + "px"
	});

	$("#add_new_product_btn").on("click", function(){
		var uid = Math.floor(Math.random() * 26) + Date.now();
		var description = $("#product_description").val();
		var price = $("#product_price").val();

		var tr = "<tr id='tr_"+uid+"' class='new_prod text-primary haschanges'>";
			tr += "<td class='pdesc'>"+ucwords(description)+"</td>";
			tr += "<td class='pprice'>"+parseFloat(price).toFixed(2)+"</td>";
			tr += "</tr>";

		if(!validate(price))
			return;

		if("" == description || "" == price)
			return;

		$("#selected_product").val("");
		$("#product_description").val("");
		$("#product_price").val("");
		$("#product_table tbody").prepend(tr);
	});

	$("table#product_table tbody").on("click", "tr", function(){
		var id = $(this).attr("id");
		var description = $(this).find("td.pdesc").text();
		var price = $(this).find("td.pprice").text();

		$("#selected_product").val(id);
		$("#product_description").val(description);
		$("#product_price").val(price);

		$("#add_new_product_btn").hide();
		$("#update_product_btn, #delete_product_btn").show();

		if($(this).hasClass("remove_prod")){
			$("#delete_product_btn").hide();
			$("#undo_delete_btn").show();
		}

	});

	$("#clear_product_btn").on("click", function(){
		$("#product_description").val("");
		$("#product_price").val("");
		$("#selected_product").val("");

		$("#add_new_product_btn").show();
		$("#update_product_btn, #delete_product_btn").hide();
	});

	$("#update_product_btn").on("click", function(){
		var description = $("#product_description").val();
		var price = $("#product_price").val();
		var id = $("#selected_product").val();
		var tr = $("table#product_table tbody tr#"+id);

		if(!validate(price))
			return;

		if(tr.find("td.pdesc").text() == description && tr.find("td.pprice").text() == price)
			return;

		$("#selected_product").val("");
		$("#product_description").val("");
		$("#product_price").val("");
		tr.find("td.pdesc").text(description);
		tr.find("td.pprice").text(price);

		$(this).hide();
		$("#delete_product_btn").hide();
		$("#add_new_product_btn").show();
		$("#undo_delete_btn").hide();
		if(!tr.hasClass("text-primary")) {
			tr.addClass("update_prod text-success haschanges");
			tr.removeClass("remove_prod text-danger");
		}
	});

	$("#delete_product_btn").on("click", function() {
		var description = $("#product_description").val();
		var price = $("#product_price").val();
		var id = $("#selected_product").val();
		var tr = $("table#product_table tbody tr#" + id);

		if(!validate(price))
			return;

		$("#selected_product").val("");
		$("#product_price").val("");
		$("#product_description").val("");
		$(this).hide();
		$("#update_product_btn").hide();
		$("#add_new_product_btn").show();

		if (!tr.hasClass("text-primary")){
			tr.addClass("remove_prod text-danger haschanges");
			tr.removeClass("update_prod text-success");
		}else{
			tr.remove();
		}
	});

	$("#undo_delete_btn").on("click", function(){
		var description = $("#product_description").val();
		var price = $("#product_price").val();
		var id = $("#selected_product").val();
		var tr = $("table#product_table tbody tr#" + id);

		$(this).hide();
		if (!tr.hasClass("text-primary")){
			$("#delete_product_btn").show();
			tr.removeClass("remove_prod text-danger text-success update_prod haschanges");
		}
	});

	$("#save_product_changes").on("click", function(){
		var trdata = $("table#product_table tbody").find("tr.haschanges");
		var data = [];
		$.each(trdata, function(ind, row){
			var status;
			var id = $(row).attr("id").split("_")[1];
			var description = $(row).find("td.pdesc").text();
			var price = $(row).find("td.pprice").text();
			if($(row).hasClass("new_prod"))
				status = "new";
			else if($(row).hasClass("update_prod"))
				status = "edit";
			else
				status = "delete";

			var json = {"id":id,description,price,status};
			data.push(json);
		});

		$.ajax({
			method: 'POST',
			url: baseurl + '/main/saveproductchanges',
			data: {data: data},
			success: function (res) {
				NProgress.done();
				if(res == "success"){
					alert("Change save successfully");
					$("#product_list_modal").modal("hide");
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

	$("#filter_btn").on("click", function(){
		$("#filter_modal").modal("show");
	});

	/* access local storage filters */
	if(typeof (localStorage["filter"]) !== 'undefined') {
		var orderfilters = JSON.parse(localStorage["filter"]);
		if (orderfilters) {
			$("#filter_revised").iCheck("uncheck");
			$("#filter_revised").iCheck("uncheck");
			$("#filter_revised").iCheck("uncheck");
			$("#filter_delivery_date").val(orderfilters["deliverydate"]);
			$("#filter_status").val(orderfilters["status"]);
			if (orderfilters["paid"] == "PAID")
				$("#filter_paid").iCheck("check");
			if (orderfilters["printed"] == "PRINTED")
				$("#filter_printed").iCheck("check");
			if (orderfilters["revised"] == "REVISED")
				$("#filter_revised").iCheck("check");
			$("#filter_printed_status").val(orderfilters["filter_printed_status"]);
			$("#order_id_filter").val(orderfilters["orderid"]);
			$("#filter_mop").select2().val(orderfilters["mop"]).trigger("change");
			filterlist();
		}
	}

	$("#confirm_filter").on("click", function(){
		filterlist();
	});

	$("#clear_filter_btn").on("click", function(){
		$("#filter_delivery_date").val("");
		$("#filter_status").val("");
		$("#filter_paid").iCheck('uncheck');
		$("#filter_printed").iCheck('uncheck');
		$("#filter_revised").iCheck('uncheck');
		$("#order_id_filter").val("");
		$("#filter_printed_status").val("");
		$("#filter_mop").val([]).trigger('change');

		$("#confirm_filter").trigger("click");
	});

	$("#filter_printed").on('ifChecked', function(event){
		$("#filter_revised").iCheck("uncheck");
	});

	$("#filter_revised").on('ifChecked', function(event){
		$("#filter_printed").iCheck("uncheck");
	});

	function filterlist(){

		//var rows = document.querySelector("#orderlist_table tbody").rows;

		// var moparray = $("#filter_mop").find(':selected').map(function() {
		// 	return $( this ).text();
		// }).get().join("|");

		var deliverydate = $("#filter_delivery_date").val();
		var status = $("#filter_status").val();
		var paid = $("#filter_paid").prop('checked') ? "1" : "";
		var printed = $("#filter_printed").prop('checked') ? $("#filter_printed_status").val("1") : "";
		var revised = $("#filter_revised").prop('checked') ? $("#filter_printed_status").val("2") : "";
		var orderid = $("#order_id_filter").val();
		var city_id = $("select#city").val();

		// var printed_status = "";
		// if(printed != "" || revised != ""){
		// 	printed_status = printed == "PRINTED" ? "PRINTED" : "REVISED";
		// 	printed_status = printed_status != "" ? printed_status : "";
		// }
		//
		// ordertable.column(0).search(orderid).draw();
		// ordertable.column(5).search(paid).draw();
		// ordertable.column(6).search(moparray, true, false).draw();
		// ordertable.column(7).search(printed_status).draw();
		// ordertable.column(8).search(status).draw();
		// ordertable.column(9).search(deliverydate).draw();

		ordertable.ajax.reload();

		var orderfilters = {};
		orderfilters["orderid"] = orderid;
		orderfilters["deliverydate"] = deliverydate;
		orderfilters["status"] = $("#filter_status").val();
		orderfilters["paid"] = paid;
		orderfilters["printed"] = printed;
		orderfilters["revised"] = revised;
		orderfilters["filter_printed_status"] = $("#filter_printed_status").val();
		orderfilters["mop"] = $("#filter_mop").val();
		orderfilters["city_id"] = city_id;

		localStorage["filter"] = JSON.stringify(orderfilters);

		if((deliverydate == "") && (status == "") && (paid == "") && (printed == "")
			&& (revised == "") && (orderid == "") && $("#filter_mop").val() == ""){
			$(".clear_filter_div").hide();
		}else{
			$(".clear_filter_div").show();
		}

		$("#filter_modal").modal("hide");
	}

	inputautocomplete();
	function inputautocomplete() {
		// Initialize ajax autocomplete:
		var orderidArray = $.map(orderids, function (value, key) {
			return {value: value, data: key};
		});
		$('.order_id_filter').autocomplete({
			lookup: orderidArray,
			lookupLimit: 5,
			lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function (suggestion) {
				//$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				var id = suggestion.data;
				$("#id_value").val(id);
			},
			onHint: function (hint) {
				$('.orderid_autocomplete_hint').val(hint);
			},
			onInvalidateSelection: function () {
				$("#id_value").val("");
			}
		});
	}

	driverautocomplete();
	function driverautocomplete(){
		// Initialize ajax autocomplete:
		var driverArray = $.map(driverlist, function (value, key) {
			return {value: value, data: key};
		});
		$('#driver_id_rpt').autocomplete({
			lookup: driverArray,
			lookupLimit: 5,
			lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function (suggestion) {
				//$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				var id = suggestion.data;
				$("form#report_data input#param_driver").val(id);
			},
			onHint: function (hint) {
				$('#driver_autocomplete_hint').val(hint);
			},
			onInvalidateSelection: function () {
				$("form#report_data input#param_driver").val("");
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

	/* SWITCH */
	$("#switch_ribshack").on("click", function(){
		localStorage.setItem("store_id", "2");
		switchpage("2");
	});

	/* SWITCH */
	$("#switch_ramx").on("click", function(){
		localStorage.setItem("store_id", "1");
		switchpage("1");
	});

	function switchpage(store_id){
		$.ajax({
			url: baseurl + "/main/changeStoreid",
			data: {store_id},
			type: "POST",
			success: function(){
				ordertable.ajax.reload();
				NProgress.done();
			},
			error: function (xhr, status, error) {
				NProgress.done();
			},
			beforeSend: function(){
				NProgress.start();
			}
		});


		if(store_id == "1"){
			$(".switch_ramx_div").hide();
			$(".switch_rgc_div").show();
			document.title = "RAM-X";
			$('link[rel="shortcut icon"]').attr('href',  burl + "/assets/app/img/favicon.jpg")
		}else{
			$(".switch_rgc_div").hide();
			$(".switch_ramx_div").show();
			document.title = "RIBSHACK";
			$('link[rel="shortcut icon"]').attr('href',  burl + "/assets/app/img/favicon2.jpg")
		}
	}
});
