$(document).ready(function(){

	$('.select2').select2()
	$('.input_daterangepicker').daterangepicker();
	NProgress.configure({ showSpinner: false });
	$("input#search_customer_name").on("keyup", function(){
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("search_customer_name");
		filter = input.value.toUpperCase();
		table = document.getElementById("orderlist_table");
		var isfiltered = $("#clear_filter_btn").is(":visible");
		if(isfiltered)
			tr = $(table).find("tr.filtered");
		else
			tr = $(table).find("tr");

		// Loop through all table rows, and hide those who don't match the search query
		var rowcount = 0;
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[3];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						rowcount++;
				} else {
					tr[i].style.display = "none";
				}
			}
		}
		$("#table_rowcount").html(rowcount);
	});

	$("button#create_order_btn").on("click", function(){
		NProgress.start();
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

	$("table#orderlist_table tbody").on("click", "tr", function(){
		var id = $(this).attr("id").split("_")[1];
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

		var param_mop = $("#param_status").val();
		var param_status = $("#param_mop").val();
		var param_trxdate = $("#param_trxdate").val();

		$("form#report_data input#param_status").val("");
		$("form#report_data input#param_mop").val("");
		$("form#report_data input#param_paid").val("");
		$("form#report_data input#param_trxdate").val("");
		$("form#report_data input#param").val("");

		// var param = {
		// 	"delivery_date": [],
		// 	"order_number": ["payment_record"],
		// 	"from_to": ["sales_by_delivery", "item_summary", "item_summary_detail", "sales_by_payment_method", "so_list_by_delivery_date"]
		// }

		// var inputvalue = "";
		// if(param["delivery_date"].includes(rpt_name)){
		// 	if(deliverydate == "")
		// 		return;
		// 	inputvalue = deliverydate;
		// }
		//
		// if(param["order_number"].includes(rpt_name)){
		// 	if(order_id == "")
		// 		return;
		// 	inputvalue = order_id;
		// }
		//
		// if(param["from_to"].includes(rpt_name)){
		// 	if(from_to == "")
		// 		return;
		// 	inputvalue = from_to;
		// }

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
			$("form#report_data input#param_mop").val($("#param_mop").val());
		}
		if(param_status != ""){
			$("form#report_data input#param_status").val($("#param_status").val());
		}
		if($("#param_paid").prop('checked')){
			$("form#report_data input#param_paid").val("1");
		}

		if($("#param_trxdate").val() != ""){
			$("form#report_data input#param_trxdate").val(param_trxdate);
		}

		$('#report_data').attr("action", baseurl+"/report/"+rpt_name);

		window.open('', 'new_window');
		document.getElementById('report_data').submit();


		$("#report_param_modal").modal("hide");
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
					var paidCls = row["paid"] == "1" ? "text-success" : "";
					var rowPaid = row["paid"] == "1" ? "Paid" : "";
					var printCls = row["printed"] == "1" ? "text-success" : "";
					var rowPrint = row["printed"] == "1" ? "Paid" : "";
					var tr = "<tr id='tr_" + row["id"] + "'>";
					tr += "<td>" + row["formatid"] + "</td>";
					tr += "<td>" + row["datetime"] + "</td>";
					tr += "<td>" + row["delivery_date"] + "</td>";
					tr += "<td width='25%'>" + row["name"] + "</td>";
					tr += "<td>" + row["driver_name"] + "</td>";
					tr += "<td class='"+paidCls+"'>" + rowPaid + "</td>";
					tr += "<td>" + moparr[row["payment_method"]] + "</td>";
					tr += "<td class='"+printCls+"'>" + rowPrint + "</td>";
					tr += "<td class='" + tdclass[row["status"]] + "'>" + statusarr[row["status"]] + "</td>";
					tr += "<td hidden>"+row["delivery_date"]+"</td>";
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
		$("#filter_mop").val([]).trigger('change');

		$("#confirm_filter").trigger("click");
	});

	$("#filter_printed").on('ifChecked', function(event){
		$("#filter_revised").iCheck("uncheck");
	});

	$("#filter_revised").on('ifChecked', function(event){
		$("#filter_printed").iCheck("uncheck");
	});

	var inverse = false;
	if(localStorage["inverse"] != undefined){
		inverse = (localStorage["inverse"] == 'true');
		sortable(parseInt(localStorage["thIndex"]));
	}

	$("th.sortable").on("click", function(){
		var th = $(this);
		let isSortUp = $(th).find("i").hasClass("fa-sort-up");

		$(".sortable i").removeClass("fa-sort-up fa-sort-down");
		$(".sortable i").addClass("fa-sort");

		if(isSortUp) {
			$(th).find("i").addClass("fa-sort-down");
		} else {
			$(th).find("i").addClass("fa-sort-up");
		}
		sortable(th.index());
	});

	function sortable(thIndex){
		$('table#orderlist_table').find('td').filter(function(){
			return $(this).index() === thIndex;
		}).sortElements(function(a, b){
			if($.text([a]) > $.text([b])){
				return inverse ? -1 : 1
			}
			else{
				return inverse ? 1 : -1;
			}
		}, function(){
			// parentNode is the element we want to move
			return this.parentNode;

		});

		localStorage["inverse"] = inverse;
		inverse = !inverse;
		localStorage["thIndex"] = thIndex;
	}

	function filterlist(){
		var rows = document.querySelector("#orderlist_table tbody").rows;

		var moparray = [];
		$("#filter_mop option:selected").each(function() {
			moparray.push((this.text).toUpperCase());
		});
		var deliverydate = $("#filter_delivery_date").val();
		var status = ($("#filter_status option:selected").text()).toUpperCase();
		var paid = $("#filter_paid").prop('checked') ? "PAID" : "";
		var printed = $("#filter_printed").prop('checked') ? "PRINTED" : "";
		var revised = $("#filter_revised").prop('checked') ? "REVISED" : "";
		var orderid = $("#order_id_filter").val();

		var statustd = "";
		var deliverydatetd = "";
		var paidtd = "";
		var printedtd = "";
		var revisedtd = "";
		var orderidtd = "";
		var moptd = "";
		var rowcount = 0;
		for (var i = 0; i < rows.length; i++) {
			if(moparray.length > 0)
				moptd = (rows[i].cells[6].textContent).toUpperCase();
			else
				moparray = "";
			if(deliverydate != "")
				deliverydatetd = rows[i].cells[9].textContent;
			if(paid != "")
				paidtd = (rows[i].cells[5].textContent).toUpperCase();
			if(printed != "")
				printedtd = (rows[i].cells[7].textContent).toUpperCase();
			if(revised != "")
				revisedtd = (rows[i].cells[7].textContent).toUpperCase();
			if(orderid != "")
				orderidtd = (rows[i].cells[0].textContent).toUpperCase();
			if(status != "")
				statustd = (rows[i].cells[8].textContent).toUpperCase();

			if((moparray.includes(moptd))
				&& (status == statustd)
				&& (deliverydate == deliverydatetd)
				&& (paid == paidtd)
				&& (printed == printedtd)
				&& (revised == revisedtd)
				&& (orderid == orderidtd)
			){
				rows[i].style.display = "";
				$(rows[i]).find("td").removeClass("display");
				$(rows[i]).addClass("filtered");
				rowcount++;
			} else {
				rows[i].style.display = "none";
				$(rows[i]).find("td").addClass("display");
			}

			if((deliverydate == "") && (status == "") && (paid == "") && (printed == "")
				&& (revised == "") && (orderid == "") && (moparray.length == 0)){
				rows[i].style.display = "";
				$(rows[i]).find("td").removeClass("display");
			}
			$("#table_rowcount").html(rowcount);
		}

		if((deliverydate == "") && (status == "") && (paid == "") && (printed == "")
			&& (revised == "") && (orderid == "") && (moparray.length == 0)){
			$("#clear_filter_btn").hide();
		}else{
			$("#clear_filter_btn").show();
		}

		var orderfilters = {};
		orderfilters["orderid"] = orderid;
		orderfilters["deliverydate"] = deliverydate;
		orderfilters["status"] = $("#filter_status").val();
		orderfilters["paid"] = paid;
		orderfilters["printed"] = printed;
		orderfilters["revised"] = revised;
		orderfilters["mop"] = $("#filter_mop").val();

		localStorage["filter"] = JSON.stringify(orderfilters);

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
