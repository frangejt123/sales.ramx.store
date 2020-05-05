$(document).ready(function(){

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
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[3];
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

	$("#product_list_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl + "/product";
	});

	$("table#orderlist_table").on("click", "tr", function(){
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
					window.location = baseurl + "/login";
				}
			}
		});
	});

	$(".rpt_btn").on("click", function(){
		var title = $(this).html();
		var id = $(this).attr("id");
		$("#report_param_modal #reportModalLabel").html(title);
		$("#report_param_modal").data("rpt_name", id).modal("show");
	});

	$("#print_report").on("click", function(){
		var deliverydate = $("#rpt_delivery_date").val();

		if(deliverydate == "")
			return;

		$("form#report_data input#dlvrydate").val(deliverydate);
		var rpt_name = ($("#report_param_modal").data("rpt_name")).replace('_rpt','');

		$('#report_data').attr("action", baseurl+"/report/"+rpt_name);

		window.open('', 'new_window');
		document.getElementById('report_data').submit();
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
					tr += "<td class='"+paidCls+"'>" + rowPaid + "</td>";
					tr += "<td class='"+printCls+"'>" + rowPrint + "</td>";
					tr += "<td class='" + tdclass[row["status"]] + "'>" + statusarr[row["status"]] + "</td>";
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

	$("#confirm_filter").on("click", function(){
		var rows = document.querySelector("#orderlist_table tbody").rows;

		var deliverydate = $("#filter_delivery_date").val();
		var status = ($("#filter_status option:selected").text()).toUpperCase();
		var paid = $("#filter_paid").prop('checked') ? "PAID" : "";
		var printed = $("#filter_printed").prop('checked') ? "PRINTED" : "";
		var revised = $("#filter_revised").prop('checked') ? "REVISED" : "";
		var orderid = $("#order_id_filter").val();
		var mop = ($("#filter_mop option:selected").text()).toUpperCase();

		for (var i = 0; i < rows.length; i++) {
			$(rows[i]).removeClass("filtered");
			var deliverydatetd = rows[i].cells[8].textContent;
			var statustd = (rows[i].cells[7].textContent).toUpperCase();
			var paidtd = (rows[i].cells[4].textContent).toUpperCase();
			var printedtd = (rows[i].cells[6].textContent).toUpperCase();
			var revisedtd = (rows[i].cells[6].textContent).toUpperCase();
			var orderidtd = (rows[i].cells[0].textContent).toUpperCase();
			var moptd = (rows[i].cells[5].textContent).toUpperCase();
			var filterarray = {
				"delivery_date": deliverydatetd,
				"status": statustd,
				"paid": paidtd,
				"printed": printedtd,
				"revised": revisedtd,
				"orderid": orderidtd,
				"mop": moptd
			}
			
			if(filterarray["delivery_date"].includes(deliverydate)
				&& (filterarray["status"].includes(status))
				&& (filterarray["paid"].includes(paid))
				&& filterarray["printed"].includes(printed)
				&& filterarray["revised"].includes(revised)
				&& filterarray["orderid"].includes(orderid)
				&& filterarray["mop"].includes(mop)){
				$(rows[i]).addClass("filtered");
				rows[i].style.display = "";
			}else{
				rows[i].style.display = "none";
			}
		}

		if((deliverydate == "") && (status == "") && (paid == "") && (printed == "")
			&& (revised == "") && (orderid == "") && (mop == "")){
			$("#clear_filter_btn").hide();
		}else{
			$("#clear_filter_btn").show();
		}

		$("#filter_modal").modal("hide");
	});

	$("#clear_filter_btn").on("click", function(){
		$("#filter_delivery_date").val("");
		$("#filter_status").val("");
		$("#filter_paid").iCheck('uncheck');
		$("#filter_printed").iCheck('uncheck');
		$("#filter_revised").iCheck('uncheck');
		$("#order_id_filter").val("");
		$("#filter_mop").val("");

		$("#confirm_filter").trigger("click");
	});

	$("#filter_printed").on('ifChecked', function(event){
		$("#filter_revised").iCheck("uncheck");
	});

	$("#filter_revised").on('ifChecked', function(event){
		$("#filter_printed").iCheck("uncheck");
	});

	inputautocomplete();
	function inputautocomplete() {
		// Initialize ajax autocomplete:
		var orderidArray = $.map(orderids, function (value, key) {
			return {value: value, data: key};
		});
		$('#order_id_filter').autocomplete({
			lookup: orderidArray,
			lookupLimit: 5,
			lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				return re.test(suggestion.value);
			},
			onSelect: function (suggestion) {
				//$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				var id = suggestion.data;
			},
			onHint: function (hint) {
				$('#orderid_autocomplete_hint').val(hint);
			},
			onInvalidateSelection: function () {
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
