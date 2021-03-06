var croppieimg = "";
var imghaschanges;
var croppie;
var croppieready = false;

$(document).ready(function(){


	$("#upload-text").click(function() {
		$("#product_img").trigger('click')
	});

	$("#product_img").change(function(){
		var imgfile = $(this).val();
		var extension = imgfile.replace(/^.*\./, '');
		if (extension == imgfile)
			extension = '';
		else
			extension = extension.toLowerCase();

		var currentimgsrc = $("#img_preview").attr("src");

		if(extension !== "jpg" && extension !== "jpeg"){
			alert("Please upload JPEG / JPG file only.");
			$(this).val("");
			$("#img_preview").attr("src", currentimgsrc);
			return;
		}
		
		$("#map_img_preview").css({
			width: $("#img_preview").width() + "px",
		});
		readURL(this);
	});

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#map_img_preview').attr('src', e.target.result);
				setTimeout(function() {
					if (!croppieready) {
						croppie = $('#map_img_preview').croppie({
							"viewport": {
								width: $(".detail_map_preview").width() + "px",
								height: '250px',
								type: 'square'
							}
						});
						croppieready = true;
					}

					croppie.croppie('bind', {
						url: e.target.result
					});

				}, 500);
				setTimeout(function(){
					$('#map_img_preview').croppie("result", {
						type: "base64",
						format: "jpeg"
					}).then(function(img) {
						croppieimg = img;
					});
				},700);
				imghaschanges = true;
			}
			reader.readAsDataURL(input.files[0]);
		}
	}


	NProgress.configure({ showSpinner: false });
	$("input#search_product").on("keyup", function(){
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("search_product");
		filter = input.value.toUpperCase();
		table = document.getElementById("productlist_table");
		tr = table.getElementsByTagName("tr");

		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
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

	$("button#create_product_btn").on("click", function(){
		$("input#product_id").val("");
		$("#phase_out_grp").hide();

	
		$("#product_modal .modal-header span").text("Add Product");
		$("#delete_product").attr("hidden", "hidden");
		$("#product_modal").modal("show");
		
	});

	$("#product_modal").on("hidden.bs.modal", function() {
		clearForm();
	});

	function clearForm() {
		$("#product_modal .form-control").val("");
		$("#prod_img").trigger('change');
		$("#map_img_preview").attr("src", '');
		$(".croppie-container img").attr("src", "");
		croppieimg = "";
		imghaschanges = false;
	}

	$("#cancel_userlist_btn").on("click", function(){
		NProgress.start();
		window.location = siteURL;
	});

	$("#productlist_table").on("click", "tr", function(e){
		var td = $(this).find("td");
		var tr = e.currentTarget;
		var img = $(tr).data('image');

		$("#product_modal .modal-header span").text("Update Product");
		$("#phase_out_grp").show();

		$("#description").val(td[0].textContent);
		$("#uom").val(td[1].textContent);
		$("#price").val(td[3].textContent);
		$("#category").val(td[6].textContent);
		if(td[7].textContent == "1"){
			$("#phase_out").iCheck("check");
		}else{
			$("#phase_out").iCheck("uncheck");
		}


		if(img) {
			$("#map_img_preview").attr("src", baseURL + "assets/prod_img/" + img);
		}
		$("input#product_id").val(td[5].textContent);
		$("#delete_product").removeAttr("hidden");
		$("#product_modal").modal("show");
	});

	$("#save_changes").on("click", function(){
		var input = $("#product_modal").find("input").not(".not-required");
		var product_id = $("#product_id").val();
		var empty = 0;
		$.each(input, function(ind, row){
			if($(row).val() == ""){
				empty++;
			}
		});

		if(empty > 0){
			alert("Please fill in empty fields.");
			return;
		}

		var description = $("#description").val();
		var uom = ($("#uom").val()).toUpperCase();
		var price = parseFloat($("#price").val().replace(",", ""));
		var category_id = $("select#category").val();
		var url = siteURL + '/product/addProduct';
		var phase_out = $("#phase_out").prop('checked') ? "1" : "0";

	   if(imghaschanges) {
		   var prod_img = croppieimg;
	   }




		var store_id = localStorage["store_id"];
		var data = {description, uom, price, category_id, phase_out, store_id, prod_img};
		if(product_id != "") {//update
			data["id"] = product_id;
			url = siteURL + '/product/update';
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
				$("#save_user").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#save_user").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#save_user").attr("disabled", "disabled");
			}
		});
	});

	$("#delete_product").on("click", function(){
		var name = $("#description").val();
		$("span#product_name").text(name);
		$("#delete_product_modal").modal("show");
	});

	$("#confirm_delete_product").on("click", function(){
		var id = $("#product_id").val();
		$.ajax({
			method: 'POST',
			data: {id},
			url: siteURL + '/product/delete',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					alert("Changes successfully saved.");
					location.reload();
				}
				$("#confirm_delete_product").removeAttr("disabled");
			},
			error: function (xhr, status, error) {
				NProgress.done();
				$("#confirm_delete_product").removeAttr("disabled");
			},
			beforeSend: function(){
				NProgress.start();
				$("#confirm_delete_product").attr("disabled", "disabled");
			}
		});
	});

	/* CATEGORY */
	var deviceheight = document.documentElement.clientHeight;
	$('#category_container').slimScroll({
		height: deviceheight - 400 + "px"
	});

	$("#product_category_btn").on("click", function(){
		$.ajax({
			method: 'POST',
			data: {"store_id": localStorage["store_id"]},
			url: siteURL + '/category',
			success: function (res) {
				var res = JSON.parse(res);
				NProgress.done();
				var tr = "";
				$.each(res, function (ind, row) {
					tr += "<tr id='tr_" + row["id"] + "'>";
					tr += "<td class='catdesc'>" + row["name"] + "</td>";
					tr += "</tr>";
				});

				$("table#category_table tbody").html(tr);

				$("#category_list_modal").modal("show");
			},
			error: function (xhr, status, error) {
				NProgress.done();
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	});

	$("#add_new_category_btn").on("click", function(){
		var uid = Math.floor(Math.random() * 26) + Date.now();
		var description = $("#category_description").val();

		var tr = "<tr id='tr_"+uid+"' class='new_category text-primary haschanges'>";
		tr += "<td class='catdesc'>"+ucwords(description)+"</td>";
		tr += "</tr>";

		if("" == description)
			return;

		$("#selected_category").val("");
		$("#category_description").val("");
		$("#category_table tbody").prepend(tr);
	});

	$("table#category_table tbody").on("click", "tr", function(){
		var id = $(this).attr("id");
		var description = $(this).find("td.catdesc").text();

		$("#selected_category").val(id);
		$("#category_description").val(description);

		$("#add_new_category_btn").hide();
		$("#update_category_btn, #delete_category_btn").show();

		if($(this).hasClass("remove_category")){
			$("#delete_category_btn").hide();
			$("#undo_delete_btn").show();
		}

	});

	$("#update_category_btn").on("click", function(){
		var description = $("#category_description").val();
		var id = $("#selected_category").val();
		var tr = $("table#category_table tbody tr#"+id);

		if(tr.find("td.catdesc").text() == description)
			return;

		$("#selected_category").val("");
		$("#product_description").val("");
		tr.find("td.catdesc").text(description);

		$(this).hide();
		$("#delete_category_btn").hide();
		$("#add_new_category_btn").show();
		$("#undo_delete_btn").hide();
		if(!tr.hasClass("text-primary")) {
			tr.addClass("update_category text-success haschanges");
			tr.removeClass("remove_category text-danger");
		}
	});

	$("#delete_category_btn").on("click", function() {
		var description = $("#category_description").val();
		var id = $("#selected_category").val();
		var tr = $("table#category_table tbody tr#" + id);


		$("#selected_category").val("");
		$("#category_description").val("");
		$(this).hide();
		$("#update_category_btn").hide();
		$("#add_new_category_btn").show();

		if (!tr.hasClass("text-primary")){
			tr.addClass("remove_category text-danger haschanges");
			tr.removeClass("update_category text-success");
		}else{
			tr.remove();
		}
	});

	$("#undo_delete_btn").on("click", function(){
		var description = $("#category_description").val();
		var id = $("#selected_category").val();
		var tr = $("table#category_table tbody tr#" + id);

		$(this).hide();
		if (!tr.hasClass("text-primary")){
			$("#delete_category_btn").show();
			tr.removeClass("remove_category text-danger text-success update_category haschanges");
		}
	});

	$("#clear_category_btn").on("click", function(){
		$("#category_description").val("");
		$("#selected_category").val("");

		$("#add_new_category_btn").show();
		$("#update_category_btn, #delete_category_btn, #undo_delete_btn").hide();
	});

	$("#save_category_changes").on("click", function(){
		var trdata = $("table#category_table tbody").find("tr.haschanges");
		var data = [];
		$.each(trdata, function(ind, row){
			var status;
			var id = $(row).attr("id").split("_")[1];
			var name = $(row).find("td.catdesc").text();
			if($(row).hasClass("new_category"))
				status = "new";
			else if($(row).hasClass("update_category"))
				status = "edit";
			else
				status = "delete";

			var store_id = localStorage["store_id"];

			var json = {"id":id,name,status,store_id};
			data.push(json);
		});

		$.ajax({
			method: 'POST',
			url: siteURL + '/category/savechanges',
			data: {data: data},
			success: function (res) {
				NProgress.done();
				if(res == "success"){
					alert("Change save successfully");
					location.reload();
					//$("#category_list_modal").modal("hide");
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

	
	$("#logout").on("click", function(){
		NProgress.start();
		$.ajax({
			method: 'POST',
			url: siteURL + '/login/logout',
			success: function (res) {
				if(res == "success"){
					localStorage.removeItem("filter");
					localStorage.removeItem("inverse");
					localStorage.removeItem("thIndex");
				
					localStorage.removeItem("searchvalue");
					localStorage.removeItem("filter");
					localStorage.removeItem("store_id");
					window.location = siteURL + "/login";
				}
			}
		});
	});

});
