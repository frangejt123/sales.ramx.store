$("document").ready(function(){

	NProgress.configure({ showSpinner: false });
	$("input.inpqty").val("1");

	var deviceheight = document.documentElement.clientHeight;
	$("body").css({
		height: deviceheight
	});

	//var whight = $(window).height();
	$('#left_panel').slimScroll({
		height: deviceheight - 100 + "px"
	});

	$('#productsummary').slimScroll({
		height: deviceheight - 300 + "px"
	});

	$("div#container").on("click", ".product_cont", function(){
		var id = $(this).attr("id");
		var description = $(".product_main #"+id).find("div.product_desc").html();
		var price = $(".product_main #"+id).find("div.product_price").html();
		var qty = $("input#inpqty"+id).val();

		var products = $("#productsummary").find(".row#"+id);

		$exist = false;
		if(products.length > 0) {
			$exist = true;
		}

		var html = '<div class="row prodsumrow new haschanges" id="'+id+'">'
					+ '<div class="col-lg-7 summary_desc left_floater">'
					+ description
					+ '</div>'
					+ '<div class="col-lg-2 summary_qty left_floater">'
					+ qty
					+ '</div>'
					+ '<div class="col-lg-2 right_floater">'
					+ '<button type="button" class="btn btn-danger delbtn" id="delbtn_'+id+'" style="height: 50px;width: 50px;">'
						+ '<i class="fa fa-trash"></i>'
					+ '</button>'
					+ '</div>'
					+ '<div style="clear:both"></div>'
					+ '</div>';

		if(!$exist)
			$("#productsummary").prepend(html);
		else{
			var exstqty = $("#productsummary .row#"+id).find(".summary_qty").html();
			var currentqty = parseFloat(exstqty) + parseFloat(qty);
			$("#productsummary .row#"+id).find(".summary_qty").html(currentqty);
			if($("#productsummary .row#"+id).hasClass("existing"))
				$("#productsummary .row#"+id).addClass("edited haschanges");
		}

		changeprice();
	});

	$("div#container").on("click", ".delbtn", function(){
		var id = $(this).attr("id").split("_")[1];
		var row = $("div#container").find(".prodsumrow#"+id);

		if($(row).hasClass("existing")){
			$(row).hide().addClass("deleted haschanges").attr("id",id+"_deleted").removeClass("edited");
		}else{
			$(row).remove();
		}
		changeprice();
	});

	function changeprice(){
		var products = $("#productsummary").find(".prodsumrow").not(".deleted");

		var total = 0;
		$.each(products, function(ind, row){
			var id = $(row).attr("id");
			var qty = $(row).find(".summary_qty").html();
			var price = $(".product_main #"+id).find("div.product_price").html();
			    total += (parseFloat(qty) * parseFloat(price));
		});

		$("#totalvalue").html(parseFloat(total).toFixed(2));
	}

	$("#settlebtn").on("click", function(){
	  	  var total = parseFloat($("#totalvalue").html());

	      var products = $("#productsummary").find(".row");
	      if(products.length < 1){
	      	alert("Please add product");
			return;
		  }

	      if($("input#customer_name").val() == ""){
	      	alert("Please add order detail.");
	      	return;
		  }

		  $("#confirmmodal").modal("show");
	  });

	$("#confirm_noopt").on("click", function(){
	  	$("#confirmmodal").modal("hide");
	});

	$("#confirm_yesopt").on("click", function(){
	  	var detail = [];

	  	var total = parseFloat($("#totalvalue").html()).toFixed(2);
		var customer_id = $("input#customer_id").val();
		var delivery_address = ucwords($("#cust_delivery_address").val());
		var delivery_date = $("input#delivery_date").val();
		var payment_method = $("#payment_method").val();
		var payment_confirmation_detail = ucwords($("#payment_confirmation_detail").val());
		var contact_number = $("input#cust_contact_number").val();
		var remarks = $("#trans_remarks").val();

		var transaction_id = $("#transaction_id_inp").val();
		var haschanges = 0;
		if(transaction_id != ""){
			haschanges = 1;
		}
	  	var transdata = {transaction_id, customer_id,total,delivery_address,delivery_date,remarks,payment_method,payment_confirmation_detail,haschanges,"status":0};
	  	var product = $("#productsummary").find(".row.haschanges");

	  	$.each(product, function(ind, row){
	  		var product_id = $(row).attr("id");
	  		var id = $(row).attr("data-id") != undefined ? $(row).attr("data-id") : "";
	  		var quantity = parseFloat($(row).find(".summary_qty").html());

	  		var status;
	  		if($(row).hasClass("new"))
	  			status = "new";
	  		else if($(row).hasClass("edited"))
				status = "edited";
	  		else
				status = "deleted";

	  		var datarow = {product_id, quantity, id, status};
	  		detail.push(datarow);
	  	});

	  	var data = {"trans":transdata,"detail":detail};
		if(newcustomer !== undefined){
			data["newcustomer"] = newcustomer;
		}else{
			data["customerdetail"] = {customer_id,contact_number,delivery_address};
		}
	  	$.ajax({
        	method: 'POST',
        	data: data,
        	url: baseurl+'/main/settle',
        	success: function(res){
        		var res = JSON.parse(res);
				NProgress.done();
          		if(res["success"]){
					alert("Transaction Successfully Settled!");
					location.reload();
          		}
	        },
	        error: function(xhr, status, error){
        		
				NProgress.done();
	        	alert("Oppss!. Something went wrong!.")
	        },
			beforeSend: function(){
				NProgress.start();
			}
	    });
	  });

	$("#customer_name").trigger("changed");
	$("#customer_details_main_btn").on("click", function(){
		$("#customer_detail_modal").modal("show");
		setTimeout(function(){
			$("#customer_name").focus();
		},500);
	});

	$("#save_customer_detail_btn").on("click", function(){
		//$("#customer_detail_modal").modal("hide");
		var cname = ucwords($("input#customer_name").val());
		var cnumber = $("input#cust_contact_number").val();
		var address = ucwords($("#cust_delivery_address").val());
		var cid = $("input#customer_id").val();

		if(cname == ""){
			alert("Customer name cannot be empty");
			return;
		}

		if(customerdetail[cid] == undefined){
			$("#savecustomerdetailmodal").modal("show");
			$("span#new_customer_name").text(cname);
		}else{
			$("#customer_details_main_btn").text(cname);
			$("#customer_detail_modal").modal("hide");
		}

		if(customerdetail["temp_id"] !== undefined){
			customerdetail["temp_id"]["contact_number"] = cnumber;
			customerdetail["temp_id"]["delivery_address"] = address;
		}
	});

	$("#confirm_save_new_customer").on("click", function(){
		var cname = ucwords($("input#customer_name").val());
		var cnumber = $("input#cust_contact_number").val();
		var address = ucwords($("#cust_delivery_address").val());
		$("#savecustomerdetailmodal").modal("hide");
	  	namelist["temp_id"] = cname;

		customerdetail["temp_id"] = {
			"name":cname,
			"contact_number":cnumber,
			"delivery_address":address
		};

		newcustomer = customerdetail["temp_id"];
		$("#customer_detail_modal").modal("hide");
		$("#customer_details_main_btn").text(cname);
	  	//reinitialize selection
		inputautocomplete();
	});

	$("button#cancel_order_btn").on("click", function(){
			var products = $("#productsummary").find(".row.haschanges");
			if(products.length > 0){
				$("#confirmcancelmodal").modal("show");
			}else{
				NProgress.start();
				if($("#transaction_id_inp").val() == "")
					window.location = baseurl;
				else
					window.location = baseurl + "/main/orderdetail/"+btoa($("#transaction_id_inp").val());
			}
	  });

	$("#confirm_cancel_transaction").on("click", function(){
		if($("#transaction_id_inp").val() == "")
			window.location = baseurl;
		else
			window.location = baseurl + "/main/orderdetail/"+btoa($("#transaction_id_inp").val());
	});

	$("#cancel_save_new_customer").on("click", function(){
		$("#savecustomerdetailmodal").modal("hide");
	});

	$("#cancel_suspend_transaction").on("click", function(){
		$("#confirmcancelmodal").modal("hide");
	});

	inputautocomplete();
	function inputautocomplete() {
		  // Initialize ajax autocomplete:
		  var customernameArray = $.map(namelist, function (value, key) {
			  return {value: value, data: key};
		  });
		  $('#customer_name').autocomplete({
			  lookup: customernameArray,
			  lookupLimit: 5,
			  lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
				  var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
				  return re.test(suggestion.value);
			  },
			  onSelect: function (suggestion) {
				  //$('#selction-ajax').html('You selected: ' + suggestion.value + ', ' + suggestion.data);
				  var id = suggestion.data;
				  var contactnumber = customerdetail[id]["contact_number"];
				  var deliveryaddress = customerdetail[id]["delivery_address"];

				  $("#cust_contact_number").val(contactnumber);
				  $("#cust_delivery_address").val(deliveryaddress);
				  $("#customer_id").val(id);
			  },
			  onHint: function (hint) {
				  $('#name_autocomplete_hint').val(hint);
			  },
			  onInvalidateSelection: function () {
				  $("#cust_contact_number").val("");
				  $("#cust_delivery_address").val("");
				  $("#customer_id").val("");
			  }
		  });
	}
	function ucwords (str) {
		return (str + '')
			.replace(/^(.)|\s+(.)/g, function ($1) {
				return $1.toUpperCase()
			})
	}

});
