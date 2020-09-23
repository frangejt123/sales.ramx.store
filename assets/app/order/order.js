$("document").ready(function(){

	//set date
	let tommorow_date = moment().add(1, 'days');
	let tommorow_day = tommorow_date.format('dddd');
	if(tommorow_day == 'Sunday') {
		delivery_date = tommorow_date.add(1, 'days').format('YYYY-MM-DD');
	} else {
		delivery_date = tommorow_date.format('YYYY-MM-DD');
	}

	$('#delivery_date').val(delivery_date)

	NProgress.configure({ showSpinner: false });
	$("input.inpqty").val("1");
	var croppieimg = "";
	var imghaschanges;
	var croppie;
	var croppieready = false;
	var total = 0;
	var new_transaction_id = "";

	$('[data-toggle="tooltip"]').tooltip()

	var deviceheight = document.documentElement.clientHeight;
	$("body").css({
		height: deviceheight
	});

	var whight = $(window).height();
	$('#left_panel').slimScroll({
		height: (whight - 100) + "px"
	});

	$('.slimscrollcont').slimScroll({
		height:  (whight - 240) +"px"
	});

	$('.slimscrollcont2').slimScroll({
		height:  (whight - 90) +"px"
	});

	var inventorydata = {};

	$(".product_cont").click(function(){
		var id = $(this).attr("id");
		var description = $(".product_main #"+id).find("div.product_desc").html();
		var price = $(".product_main #"+id).find("div.product_price").html();
		var qty = parseFloat($("input#inpqty"+id).val());
		var availqty = parseFloat($(".product_main #qty_"+id+" span").html());

		var products = $("#productsummary").find(".row#"+id);

		$exist = false;
		if(products.length > 0) {
			$exist = true;
		}

		var new_qty = availqty - qty;
		if(new_qty < 0 || qty <= 0)
			return;

		var html = '<div class="row prodsumrow new haschanges d-flex flex-row mb-2" data-price="'+price+'" id="'+id+'">'
			+ '<div class=" summary_desc mr-auto">'
			+		 description
			+ '</div>'
			+ '<div class=" summary_qty mr-5 ">'
			+ qty
			+ '</div>'
			+ '<div class=" mr-3 ">'
			+ '<button type="button" class="btn btn-danger delbtn" id="delbtn_'+id+'">'
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

		if(id in inventorydata) {
			inventorydata[id]["qty"] -= qty;
		}else{
			var qty = (qty * -1);
			inventorydata[id] = {id, qty};
		}

		$(".product_main #qty_"+id+" span").text(new_qty);
		if(new_qty <= 0){
			$(".product_cont#"+id).addClass("notavailable");
		}
		changeprice();
	});

	$("div#productsummary").on("click", ".delbtn", function(){
		var id = $(this).attr("id").split("_")[1];
		var row = $("div#productsummary").find(".prodsumrow#"+id);
	
		var availqty = parseFloat($(".product_main #qty_"+id+" span").html());
		var inputqty = parseFloat($("div.prodsumrow#"+id+" div.summary_qty").text());


		$(".product_main #qty_"+id+" span").text(availqty + inputqty);
		$(".product_cont#"+id).removeClass("notavailable");
		if(id in inventorydata) {
			inventorydata[id]["qty"] += inputqty;
		}else{
			var qty = inputqty;
			inventorydata[id] = {id, qty};
		}

		if($(row).hasClass("existing")){
			$(row).hide().addClass("deleted haschanges").attr("id",id+"_deleted").removeClass("edited");
		}else{
			$(row).remove();
		}

		changeprice();
	});

	function changeprice(){
		var products = $("#productsummary").find(".prodsumrow").not(".deleted");
		total = 0;
		$.each(products, function(ind, row){
			var id = $(row).attr("id");
			var qty = $(row).find(".summary_qty").html();
			// var price = $(".product_main #"+id).find("div.product_price").html();
			var price = $(row).data('price');
			total += (parseFloat(qty) * parseFloat(price));
		});

		$("#totalvalue").html(toCurrency(total));
		total = total;
	}

	$("#settlebtn").on("click", function(){
		//var total = parseFloat($("#totalvalue").html());

		var products = $("#productsummary").find(".row").not(".deleted");

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
	changeprice();

	$("#confirm_yesopt").on("click", function(){
		var detail = [];

		var customer_id = $("input#customer_id").val();
		var customer_name = $("input#customer_name").val();
		var facebook_name = $("input#facebook_name").val();
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


		var store_id = localStorage["store_id"];
		var transdata = {transaction_id,customer_name,facebook_name,customer_id,total,delivery_address,delivery_date,remarks,payment_method,payment_confirmation_detail,haschanges,"status":0,store_id};
		var product = $("#productsummary").find(".row.haschanges");

		$.each(product, function(ind, row){
			var product_id = ($(row).attr("id")).replace("_deleted", "");
			var id = $(row).attr("data-id") != undefined ? $(row).attr("data-id") : "";
			var quantity = parseFloat($(row).find(".summary_qty").html());
			var price = $(".product_main #"+product_id).find(".product_price").html();

			var status;
			if($(row).hasClass("new"))
				status = "new";
			else if($(row).hasClass("edited"))
				status = "edited";
			else
				status = "deleted";

			var datarow = {product_id, quantity, id, price, status};
			detail.push(datarow);
		});

		var locationimg = croppieimg;

		var data = {"trans":transdata, "detail": detail, locationimg, inventorydata};

	
			data["customerdetail"] = {customer_id,contact_number,delivery_address};
		

		$.ajax({
			method: 'POST',
			data: data,
			url: siteURL+'/order/settle',
			success: function(res){
			
				NProgress.done();
				
				res = JSON.parse(res);

				if(res["success"]){
					// alert("Transaction Successfully Settled!");
					//new_transaction_id = res["transaction_id"];

					//clear prod summary
					$("#productsummary").html("");


					window.location = siteURL + "/order/detail/"+btoa(res["id"])+"?success=true";
					//$("#confirmmodal").modal("hide");
					//$("#productsummary").find(".row").addClass("saved");
					//location.reload();
				}else{
					var product = res["product"] != undefined ? res["product"] : "";
					alert(res["error"] + "\n" + product);
				}
				$("#confirm_yesopt").removeAttr("disabled");
			},
			error: function(xhr, status, error){
				console.log(xhr, status, error)
				$("#confirm_yesopt").removeAttr("disabled");
				NProgress.done();
				alert("Oppss!. Something went wrong!.")
			},
			beforeSend: function(){
				NProgress.start();
				$("#confirm_yesopt").attr("disabled", "disabled");
			}
		});
	});

	$("#new_order_btn").on("click", function(){
		var products = $("#productsummary").find(".row.haschanges");
		var saved = 0;
		$.each(products, function(i, r){
			if($(r).hasClass("saved"))
				saved++;
		});

		if(products.length > 0){
			$("#confirmcancelmodal").modal("show").data("mode", "new");
		}else{
			window.location = siteURL + "/main/pos";
		}
	});

	$("#customer_name").trigger("changed");

	$("#customer_details_main_btn").on("click", function(){
		$("#customer_detail_modal").modal("show");
		setTimeout(function(){
			$("#customer_name").focus();
		},500);
	});

	$("#customer_detail_modal").on("shown.bs.modal", function(){
		if (!croppieready) {
			croppie = $('#map_img_preview').croppie({
				"viewport": {
					width: $("#map_preview").width() + "px",
					height: '200px',
					type: 'square'
				}
			});
			croppieready = true;
		}
	});

	$("#save_customer_detail_btn").on("click", function(){
		//$("#customer_detail_modal").modal("hide");
		var cname = ucwords($("input#customer_name").val());
		var cnumber = $("input#cust_contact_number").val();
		var address = ucwords($("#cust_delivery_address").val());
		var delivery_date =  $("input#delivery_date").val();
		var delivery_address =  $("#cust_delivery_address").val();
		var cid = $("input#customer_id").val();

		if(cname == ""){
			alert("Customer name cannot be empty");
			return;
		}

		if(delivery_address == ""){
			alert("Delivery address cannot be empty");
			return;
		}

		if(delivery_date == ""){
			alert("Delivery date cannot be empty");
			return;
		}

		if(cnumber == ""){
			alert("Contact number cannot be empty");
			return;
		}

		if(imghaschanges){
			$('#map_img_preview').croppie("result", {
				type: "base64",
				format: "jpeg"
			}).then(function(img) {
				croppieimg = img;
			});
		}

		// if(customerdetail[cid] == undefined){
		// 	$("#savecustomerdetailmodal").modal("show");
		// 	$("span#new_customer_name").text(cname);
		// }else{
			$("#customer_details_main_btn").text(cname);
			$("#customer_detail_modal").modal("hide");
		// }

		// if(customerdetail["temp_id"] !== undefined){
		// 	customerdetail["temp_id"]["contact_number"] = cnumber;
		// 	customerdetail["temp_id"]["delivery_address"] = address;
		// }
	});

	

	$("button#cancel_order_btn").on("click", function(){
		var products = $("#productsummary").find(".row.haschanges");
		var url = siteURL;

		if($("#transaction_id_inp").val() != "")
			url = siteURL + "/main/orderdetail/"+btoa($("#transaction_id_inp").val());

		if(products.length > 0){
			$("#confirmcancelmodal").modal("show").data("mode", "back");
		}else{
			NProgress.start();
			window.location = url;
		}
	});

	$("#confirm_cancel_transaction").on("click", function(){
		 if($("#confirmcancelmodal").data("mode") == "new")
				window.location = siteURL+"/main/pos";
		 else{
			if($("#transaction_id_inp").val() == "")
				window.location = siteURL;
			else
				window.location = siteURL + "/main/orderdetail/"+btoa($("#transaction_id_inp").val());
		}
	});



	$("#cancel_suspend_transaction").on("click", function(){
		$("#confirmcancelmodal").modal("hide");
	});

	$("#copy_details").on("click", function(){
		copytoclipboard();
	});

	setTimeout(function(){
		$("a.active").trigger("click");
	}, 100);

	$(".category_li").on("click", function(){
		var id = $(this).attr("id").split("_")[1];
		$(".category_li").removeClass("active");
		$(".main_product").hide();
		$(".main_product.prodcat_"+id).show();

		$(this).addClass("active");
	});

	
	function ucwords (str) {
		return (str + '')
			.replace(/^(.)|\s+(.)/g, function ($1) {
				return $1.toUpperCase()
			})
	}

	$("#customer_location").change(function(){
		var imgfile = $(this).val();
		var extension = imgfile.replace(/^.*\./, '');
		if (extension == imgfile)
			extension = '';
		else
			extension = extension.toLowerCase();

		var currentimgsrc = $("#map_preview").attr("src");

		if(extension !== "jpg" && extension !== "jpeg"){
			alert("Please upload JPEG / JPG file only.");
			$(this).val("");
			$("#map_preview").attr("src", currentimgsrc);
			return;
		}

		$("#map_img_preview").css({
			width: $("#map_preview").width() + "px",
		});
		readURL(this);
	});

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#map_img_preview').attr('src', e.target.result);
				if (!croppieready) {
					croppie = $('#map_img_preview').croppie({
						"viewport": {
							width: $("#map_preview").width() + "px",
							height: '200px',
							type: 'square'
						}
					});
					croppieready = true;
				}
				croppie.croppie('bind', {
					url: e.target.result
				});
				imghaschanges = true;
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	function toCurrency(value) {
		if (isNaN(value)) {
			return "--";
		} else {
			return new Intl.NumberFormat("en-PH", {
				style: "currency",
				currency: "PHP"
			}).format(value);
		}
	}

	function copytoclipboard(){
		/*Name:
		Address:
		Delivery Date:
		Contact Number:
		Mode of Payment:
		Orders:
		Remarks:*/

		var modeofpaymentarray = ["Cash on Delivery", "Bank Transfer  - BPI", "GCash", "Bank Transfer  - Metrobank"];
		var name = $("#customer_name").val();
		var cust_address = $("#cust_delivery_address").val();
		var deliver_date = $("#delivery_date").val();
		var contact_number = $("#cust_contact_number").val();
		var mop = modeofpaymentarray[$("#payment_method").val()];
		var remarks = $("#trans_remarks").val();
		var fbname = $("#facebook_name").val();

		var products = $("#productsummary").find(".prodsumrow").not(".deleted");
		var ordershtml = "";
		var ordertotal = 0;
		$.each(products, function(ind, row){
			var id = $(row).attr("id");
			var pdesc = $(row).find(".summary_desc").html();
			var qty = $(row).find(".summary_qty").html();
			var price = $(".product_main #"+id).find("div.product_price").html();
			var uom = $(".product_uom#produom_"+id).text();
			ordershtml += "\n"+qty +uom+" - "+pdesc+" @ "+toCurrency(price * qty);
			ordertotal += price * qty;
		});

		var clipboardtext = "Name: "+name+"\n"
			+"Facebook Name: "+fbname+"\n"
			+"Order #: "+ordernumber+"\n"
			+"Address: "+cust_address+"\n"
			+"Delivery Date: "+deliver_date+"\n"
			+"Contact #: "+contact_number+"\n"
			+"Mode of Payment: "+mop+"\n"
			+"Orders: "+ordershtml+"\n"
			+"Total: "+toCurrency(ordertotal)+"\n"
			+"Remarks: "+remarks;

		$("textarea#clipboard").val(clipboardtext);

		var copyText = document.getElementById("clipboard");

		/* Select the text field */
		copyText.select();
		copyText.setSelectionRange(0, 99999); /*For mobile devices*/

		/* Copy the text inside the text field */
		document.execCommand("copy");

		/* Alert the copied text */
		alert("Details copied to clipboard");
	}


	if(cust_location_image !== ""){
		if (!croppieready) {
			croppie = $('#map_img_preview').croppie({
				"viewport": {
					width: $("#map_preview").width() + "px",
					height: '200px',
					type: 'square'
				}
			});
			croppieready = true;
		}
		imghaschanges = true;
		croppie.croppie('bind', {
			url: baseURL+"/assets/location_image/"+cust_location_image
		});
	}else{
		$(".cr-boundary").remove();
		$(".cr-slider-wrap").remove();
		croppieready = false;
		imghaschanges = false;
	}

	// $("#customer_details_main_btn").trigger('click')



});


window.addEventListener("beforeunload", function (e) {

	var products = $("#productsummary").find(".row.haschanges");
	if(products.length > 0) {
		var confirmationMessage = "\o/";
  
		(e || window.event).returnValue = confirmationMessage; //Gecko + IE
		return confirmationMessage;        
	}
	                    //Webkit, Safari, Chrome
});
