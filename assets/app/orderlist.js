$(document).ready(function(){

	$('.select2').select2()
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
					window.location = baseurl + "/login";
				}
			}
		});
	});

	$(".rpt_btn").on("click", function(){
		var title = $(this).html();
		var id = $(this).attr("id");
		var param = $(this).attr("data-filter");
		$("#id_value").val("");
		$("#report_param_modal #reportModalLabel").html(title);
		$("#report_param_modal").find(".filter_param").hide();
		$("#report_param_modal").find("."+param).show();

		$("#report_param_modal").data("rpt_name", id).modal("show");
	});

	$("#print_report").on("click", function(){
		var deliverydate = $("#rpt_delivery_date").val();
		var order_id = $("#id_value").val();
		var rpt_name = ($("#report_param_modal").data("rpt_name")).replace('_rpt','');

		var param = {
			"delivery_date": ["item_summary", "item_summary_detail"],
			"order_number": ["payment_record"]
		}

		var inputvalue = "";
		if(param["delivery_date"].includes(rpt_name)){
			if(deliverydate == "")
				return;
			inputvalue = deliverydate;
		}

		if(param["order_number"].includes(rpt_name)){
			if(order_id == "")
				return;
			inputvalue = order_id;
		}

		$("form#report_data input#param").val(inputvalue);
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

	$("#confirm_filter").on("click", function(){
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
				moptd = (rows[i].cells[5].textContent).toUpperCase();
			else
				moparray = "";
			if(deliverydate != "")
				deliverydatetd = rows[i].cells[8].textContent;
			if(paid != "")
				paidtd = (rows[i].cells[4].textContent).toUpperCase();
			if(printed != "")
				printedtd = (rows[i].cells[6].textContent).toUpperCase();
			if(revised != "")
				revisedtd = (rows[i].cells[6].textContent).toUpperCase();
			if(orderid != "")
				orderidtd = (rows[i].cells[0].textContent).toUpperCase();
			if(status != "")
				statustd = (rows[i].cells[7].textContent).toUpperCase();

			if((moparray.includes(moptd))
				&& (status == statustd)
				&& (deliverydate == deliverydatetd)
				&& (paid == paidtd)
				&& (printed == printedtd)
				&& (revised == revisedtd)
				&& (orderid == orderidtd)
			){
				rows[i].style.display = "";
				$(rows[i]).addClass("filtered");
				rowcount++;
			} else {
				rows[i].style.display = "none";
			}

			if((deliverydate == "") && (status == "") && (paid == "") && (printed == "")
				&& (revised == "") && (orderid == "") && (moparray.length == 0)){
				rows[i].style.display = "";
			}
			$("#table_rowcount").html(rowcount);
		}

		if((deliverydate == "") && (status == "") && (paid == "") && (printed == "")
			&& (revised == "") && (orderid == "") && (moparray.length == 0)){
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
		$("#filter_mop").val([]).trigger('change');

		$("#confirm_filter").trigger("click");
	});

	$("#filter_printed").on('ifChecked', function(event){
		$("#filter_revised").iCheck("uncheck");
	});

	$("#filter_revised").on('ifChecked', function(event){
		$("#filter_printed").iCheck("uncheck");
	});

	$(".sortable").click(function(e) {
		let ind = e.currentTarget.cellIndex;
		let tbl = $(e.currentTarget.offsetParent).attr("id");
		let isSortUp = $(e.currentTarget).find("i").hasClass("fa-sort-up");

		$(".sortable i").removeClass("fa-sort-up fa-sort-down");
		$(".sortable i").addClass("fa-sort");

		$(e.currentTarget).find("i").removeClass("fa-sort");

		if(isSortUp) {
			$(e.currentTarget).find("i").addClass("fa-sort-down");
		} else {
			$(e.currentTarget).find("i").addClass("fa-sort-up");
		}

		sortTable(ind, tbl);
	});

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

	function sortTable(n, tableId) {
		var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
		table = document.getElementById(tableId);
		switching = true;
		// Set the sorting direction to ascending:
		dir =  "asc";
		/* Make a loop that will continue until
		no switching has been done: */
		while (switching) {
			// Start by saying: no switching is done:
			switching = false;
			rows = table.rows;
			/* Loop through all table rows (except the
			first, which contains table headers): */
			for (i = 1; i < (rows.length - 1); i++) {
				// Start by saying there should be no switching:
				shouldSwitch = false;
				/* Get the two elements you want to compare,
				one from current row and one from the next: */
				x = rows[i].getElementsByTagName("TD")[n];
				y = rows[i + 1].getElementsByTagName("TD")[n];
				//check if the two rows should switch place:
				/* Check if the two rows should switch place,
				based on the direction, asc or desc: */
				if (dir == "asc") {
					if(!isNaN(x.innerHTML)){
						if (parseFloat(x.innerHTML) > parseFloat(y.innerHTML)) {
							// If so, mark as a switch and break the loop:
							shouldSwitch = true;
							break;
						}
					}else{
						if(x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()){
							// If so, mark as a switch and break the loop:
							shouldSwitch = true;
							break;
						}
					}
				} else if (dir == "desc") {
					if(!isNaN(x.innerHTML)){
						if (parseFloat(x.innerHTML) < parseFloat(y.innerHTML)) {
							// If so, mark as a switch and break the loop:
							shouldSwitch = true;
							break;
						}
					}else{
						if(x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()){
							// If so, mark as a switch and break the loop:
							shouldSwitch = true;
							break;
						}
					}
				}
			}
			if (shouldSwitch) {
				/* If a switch has been marked, make the switch
				and mark that a switch has been done: */
				rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
				switching = true;
				// Each time a switch is done, increase this count by 1:
				switchcount ++;
			} else {
				/* If no switching has been done AND the direction is "asc",
				set the direction to "desc" and run the while loop again. */
				if (switchcount == 0 && dir == "asc") {
					dir = "desc";
					switching = true;
				}
			}
		}
	}
});
