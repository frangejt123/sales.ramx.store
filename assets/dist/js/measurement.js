$(document).ready(function(){
	/* populate measurement list */
	$.ajax({
		method: "POST",
		url: baseurl+"/uom/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var tr = "";

			$.each(res, function(ind, row){
				tr += '<tr id="'+row["id"]+'"><td>'+row["abbr"]+'</td><td>'+row["description"]+'</td></tr>';
			});

			$("table#measurementtable tbody").html(tr);
		}
	})


	$("button#new_uom_btn").on("click", function(){
		$("div#new_uom_modal").modal("show");
		// var inputs = $("form#newProductForm").find("input");
		// $.each(inputs, function(ind, row){
		// 	$(this).removeClass("emptyField");
		// 	$(this).val("");
		// });
		// $("select#product_uom").removeClass("emptyField");
	});

	$("#newUom_submitBtn").on("click", function(){
		var uomabbr = $("input#uom_abbr").val();
		var uomdescription = $("input#uom_description").val();

		var data = {
			"abbr": uomabbr,
			"description": uomdescription,
		}

		var inputs = $("form#newUomForm").find("input");
		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

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
			url: baseurl+"/uom/insert",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var tr = '<tr id="'+res["id"]+'"><td>'+uomabbr+'</td><td>'+uomdescription+'</td></tr>';

		            $("table#measurementtable tbody").prepend(tr);
					$("div#new_uom_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!", {
			          type: "success",
			          allow_dismiss: false,
			          width: 300
			        });
				}
			}
		})
	});

	/* on row click */
	$("table#measurementtable tbody").on("click", "tr", function(){
		var tds = $(this).find("td");
		var id = $(this).attr("id");

		var abbr = $(tds[0]).html();
		var description = $(tds[1]).html();

		$("input#detail_uom_abbr").val(abbr);
		$("input#detail_uom_description").val(description);
		$("div#uom_detail_modal").data("id", id);
		$("div#uom_detail_modal").modal("show");
	});

	$("#updateUom_submitBtn").on("click", function(){
		var id = $("div#uom_detail_modal").data("id");
		var abbr = $("input#detail_uom_abbr").val();
		var description = $("input#detail_uom_description").val();

		var d = {
			"id": id,
			"abbr": abbr,
			"description": description
		}

		var inputs = $("form#detailUomForm").find("input");
		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

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
			url: baseurl+"/uom/update",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var td = '<td>'+abbr+'</td><td>'+description+'</td>';

		            $("table#measurementtable tbody tr#"+id).html(td);
					$("div#uom_detail_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!", {
			          type: "success",
			          width: 300
			        });
				}
			}
		});
	});

	/*delete record*/
	$("button#delete_uom").on("click", function(){
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

	$("a#confirm_delete_uom_btn").on("click", function(){
		var id = $("div#uom_detail_modal").data("id");

		var datas = {
			"id" : id
		}

		$.ajax({
			url: baseurl+"/uom/delete",
			method: "POST",
			data: datas,
			success: function(data){
	        	var data = JSON.parse(data);
	        	if(data["success"]){
	        		$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.", {
		              type: "success",
		              width: 300
		            });

	        		$("div#uom_detail_modal").modal("hide");
	        		$("table#measurementtable").find("tr#"+id).remove();
	        	}

			}
		});

		$('div#uom_detail_modal').on('hide.bs.modal', function () {
	        $("div#confirm_modal").modal("hide");
	        $('html, body').css({
	            overflow: 'hidden',
	            height: '100%'
        	});
      	});
	});

});