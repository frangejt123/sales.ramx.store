$(document).ready(function(){
	$("button#new_product_btn").on("click", function(){
		$("div#new_product_modal").modal("show");
		var inputs = $("form#newProductForm").find("input");
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			$(this).val("");
		});
		$("select#product_uom").removeClass("emptyField");
	});

	$("button#clear_new_product").on("click", function(){
		var inputs = $("form#newProductForm").find("input");
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			$(this).val("");
		});
		$("select#product_uom").val("").removeClass("emptyField");
		$("select#parent_id").val("");
	});

	$("div#new_product_modal").on('shown.bs.modal', function () {
		var d = {
			product_id: "0"
		}
	 	$.ajax({
		method: "POST",
		data: d,
		url: baseurl+"/product/getParent",
		success: function(res){
				var res = JSON.parse(res);

                var dataProduct = [{"id":"","text":""}];
                $.each(res["product"], function(i, r){
                    dataProduct.push({"id":r["id"],"text":r["description"]});
                });

                var dataUom = [{"id":"","text":""}];
                $.each(res["uom"], function(i, r){
                    dataUom.push({"id":r["id"],"text":r["description"]});
                });


				// $("select#parent_id").html(options);
				// $("select#product_uom").html(uom);

                $('.select2#parent_id').select2({
                   placeholder: "Select Parent",
                   data: dataProduct
                });

                $('.select2#product_uom').select2({
                   placeholder: "Select Unit of Measurement",
                   data: dataUom
                });

			}
		});
	});

	$("input#product_price").on("blur", function(){
		var value = $(this).val();
		if(!isNaN(value)){
			$("input#product_price").val(parseFloat(value).toFixed(2));
		}else{
			$("input#product_price").val(parseFloat("0.00").toFixed(2));
		}
	});

	$("input#product_id").on("blur", function(){
		var value = $(this).val();
		var data = {
			"id": value
		}
		if(value != ""){
			$("input#product_id").removeClass("idExist");
			$.ajax({
				method: "POST",
				data: data,
				url: baseurl+"/product/checkProductExists",
				success: function(res){
					var res = parseInt(res);
					if(res > 0){
						$("input#product_id").addClass("idExist");
						$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Product Code already exist.", {
				          type: "danger",
				          allow_dismiss: false,
				          width: 300
				        });
					}
				}
			});
		}
	});

	$("#newProduct_submitBtn").on("click", function(){
		var product_id = $("input#product_id").val();
		var product_description = $("input#product_description").val();
		var product_uom = $("select#product_uom").val();
		var product_price = $("input#product_price").val();
		var parent_id = $("select#parent_id").val();
		var parent_description = $("select#parent_id option:selected").html();
		var uom_description = $("select#product_uom option:selected").html();

		var data = {
			"id": product_id,
			"description": product_description,
			"uom": product_uom,
			"price": product_price,
			"parent_id": parent_id
		}

		if($("input#product_id").hasClass("idExist")){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Product Code already exist.", {
	          type: "danger",
	          width: 300
	        });
	        return;
		}

		var inputs = $("form#newProductForm").find("input");
		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

		$("select#product_uom").removeClass("emptyField");
		if(product_uom == ""){
			$("select#product_uom").addClass("emptyField");
			empty++;
		}
		if(empty > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.", {
	          type: "danger",
	          width: 300
	        });
			return;
		}

		$.ajax({
			method: "POST",
			data: data,
			url: baseurl+"/product/saveProduct",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var tr = '<tr id="'+data["id"]+'"><td>'+data["id"]+'</td><td>'+data["description"]+'</td><td id="'+data["uom"]+'">'+uom_description+'</td><td>'+parseFloat(data["price"]).toFixed(2)+'</td><td>'
								+"<a href='javascript:void(0)' style='color: #000' data-toggle='tooltip' data-placement='top' title='"+parent_description+"'>"
		                      		+ data["parent_id"]+"</a></td></tr>";

		            $("table#producttable tbody").prepend(tr);
					$('[data-toggle="tooltip"]').tooltip();	
					$("div#new_product_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!", {
			          type: "success",
			          allow_dismiss: false,
			          width: 300
			        });
				}
			}
		})
	});

	$("#updateProduct_submitBtn").on("click", function(){
		var product_id = $("input#detail_product_id").val();
		var product_description = $("input#detail_product_description").val();
		var product_uom = $("select#detail_product_uom").val();
		var product_price = $("input#detail_product_price").val();
		var parent_id = $("select#detail_parent_id").val();
		var parent_description = $("select#detail_parent_id option:selected").html();
		var uom_description = $("select#detail_product_uom option:selected").html();

		var d = {
			"id": product_id,
			"description": product_description,
			"uom": product_uom,
			"price": product_price,
			"parent_id": parent_id
		}

		if($("input#product_id").hasClass("idExist")){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Product Code already exist.", {
	          type: "danger",
	          width: 300
	        });
	        return;
		}

		var inputs = $("form#detailProductForm").find("input");
		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

		$("select#detail_product_uom").removeClass("emptyField");
		if(product_uom == ""){
			$("select#detail_product_uom").addClass("emptyField");
			empty++;
		}
		if(empty > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.", {
	          type: "danger",
	          width: 300
	        });
			return;
		}

		$.ajax({
			method: "POST",
			data: d,
			url: baseurl+"/product/updateProduct",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var td = '<td>'+d["id"]+'</td><td>'+d["description"]+'</td><td id="'+d["uom"]+'">'+uom_description+'</td><td>'+parseInt(d["price"]).toFixed(2)+'</td><td>'
								+"<a href='javascript:void(0)' style='color: #000' data-toggle='tooltip' data-placement='top' title='"+parent_description+"'>"
		                      		+ d["parent_id"]+"</a></td></tr>";

		            $("table#producttable tbody tr#"+d["id"]).html(td);
					$('[data-toggle="tooltip"]').tooltip();
					$("div#product_detail_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!", {
			          type: "success",
			          width: 300
			        });
				}
			}
		});
	});

	/* on row click */
	$("table#producttable tbody").on("click", "tr", function(){
		var tds = $(this).find("td");

		var id = $(tds[0]).html();
		var description = $(tds[1]).html();
		var uomval = $(tds[2]).attr("id");
		var price = $(tds[3]).html();
		var parentval = $(tds[4]).find("a").html();

		var d = {
			product_id: id
		}

		var inputs = $("form#detailProductForm").find("input");
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
		});
		$("select#detail_product_uom").removeClass("emptyField");
		$("div#product_detail_modal").data("id", id);

	 	$.ajax({
		method: "POST",
		data: d,
		url: baseurl+"/product/getParent",
		success: function(res){
				var res = JSON.parse(res);
				$("div#product_detail_modal").data("childcount", res["child"]);

                var dataProduct = [{"id":"","text":"", "selected": false}];
                $.each(res["product"], function(i, r){
                    dataProduct.push({"id":r["id"],"text":r["description"]});
                });

                var dataUom = [{"id":"","text":"", "selected": false}];
                $.each(res["uom"], function(i, r){
                    dataUom.push({"id":r["id"],"text":r["description"]});
                });

                $('.select2#detail_product_uom').select2({
                   placeholder: "Select Unit of Measurement",
                   data: dataUom
                }).val(uomval).trigger("change");

                $('.select2#detail_parent_id').select2({
                   placeholder: "Select Parent",
                   data: dataProduct
                }).val(parentval).trigger("change");

				$("input#detail_product_id").val(id);
				$("input#detail_product_description").val(description);
				$("input#detail_product_price").val(price);

				$("div.progress_mask").hide();
				$("div#product_detail_modal").modal("show");
			},
		beforeSend: function(){
				$("div.progress_mask").show();
			}
		});
	});
	/* on row click */

	$("select#detail_parent_id").change(function(e) {
		var childcount = $("div#product_detail_modal").data("childcount");
		var value = $(this).val();
		if(value == "")
			return;
		if(childcount > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Unable to change parent product because it is used by another data.", {
	          type: "warning",
	          width: 300
	        });
			$("select#detail_parent_id").val("");
		}
	});

	/* populate prouduct list */
	$.ajax({
		method: "POST",
		url: baseurl+"/product/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var tr = "";

			$.each(res, function(ind, row){
				tr += '<tr id="'+row["id"]+'"><td>'+row["id"]+'</td><td>'+row["description"]+'</td><td id="'+row["uom"]+'">'+row["uom_description"]+'</td><td>'+parseFloat(row["price"]).toFixed(2)+'</td><td>'
						+"<a href='javascript:void(0)' style='color: #000' data-toggle='tooltip' data-placement='top' title='"+row["parent_description"]+"'>"
                      		+ row["parent_id"]+"</a></td></tr>";
			});

			$("table#producttable tbody").html(tr);
			$('[data-toggle="tooltip"]').tooltip();
		}
	})

	/*delete record*/
	$("button#delete_product").on("click", function(){
		var childcount = $("div#product_detail_modal").data("childcount");
		if(childcount > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Unable to delete this product because it is used by another data.", {
	          type: "warning",
	          width: 300
	        });
			return;
		}
		$("div#confirm_modal").modal("show");
	});

	$("a#delete_product_btn").on("click", function(){
		var id = $("div#product_detail_modal").data("id");

		var datas = {
			"id" : id
		}

		$.ajax({
			url: baseurl+"/product/delete",
			method: "POST",
			data: datas,
			success: function(data){
	        	var data = JSON.parse(data);
	        	if(data["success"]){
	        		$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.", {
		              type: "success",
		              width: 300
		            });

	        		$("div#product_detail_modal").modal("hide");
	        		$("table#producttable").find("tr#"+id).remove();
	        	}

			}
		});

		$('div#product_detail_modal').on('hide.bs.modal', function () {
	        $("div#confirm_modal").modal("hide");
	        $('html, body').css({
	            overflow: 'hidden',
	            height: '100%'
        	});
      	});
	});

	/* search product */
	$("input#search_product").on("keyup", function() {
	    var value = $(this).val().toLowerCase();
	    $("table#producttable tbody tr").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	    });
	 });
	/* end */

});