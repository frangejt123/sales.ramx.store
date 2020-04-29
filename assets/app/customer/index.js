// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
	'use strict';
	window.addEventListener('load', function() {
	  // Fetch all the forms we want to apply custom Bootstrap validation styles to
	  var forms = document.getElementsByClassName('needs-validation');
	  // Loop over them and prevent submission
	  var validation = Array.prototype.filter.call(forms, function(form) {
		form.addEventListener('submit', function(event) {
		  if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
		  } 
		  form.classList.add('was-validated');
		}, false);

	
	  });
	}, false);
  })();

hasError = false;

function routeTo(e) {
	NProgress.start();
	let to = $(e.currentTarget).data("route-to");
	window.location = baseurl + to;
}
function showMessage(message) {
	Swal.fire({
		position: 'top-end',
		icon: 'success',
		title: message,
		showConfirmButton: false,
		timer: 1500
	  })

}

function clearForm() {
	$(".input").val("");
	form = {};
}

function pad (str, max) {
	str = str.toString();
	return str.length < max ? pad("0" + str, max) : str;
}

function addRow() {

	if(form && $("#customer_tbl tbody").length > 0) {
		
		$("#customer_tbl tbody").prepend(`
				<tr id="tr_${form.id}" class="routing-btn  table-success" data-route-to="/customer/detail/${form.id}">
					<td>${pad(form.id, 4)}</td>
					<td>${form.name}</td>
					<td>${form.facebook_name}</td>
					<td>${form.contact_number}</td>
					<td>${form.delivery_address}</td>
				</tr>
		`);

		setTimeout(() => {
			$(".table-success").removeClass("table-success");
		}, 3000)
	}
}

if(!window.form) {
	window.form = {};
}

$(document).ready(() => {
	/*
	 *	ROUTING BUTTONS
	 */
	$(".routing-btn").click(routeTo);

	$(".input").change(e => {
		let model = $(e.currentTarget).data("model");
		form[model] = $(e.currentTarget).val();
	}); 


	$("input#search").on("keyup", function(e){
	
		let tbl = $(this).data("table");

		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = $(e.currentTarget);
		
		if(input.val() == "") {
			$(`#${tbl} tbody tr`).css("display", "");
			return;
		}

		filter = input.val().toUpperCase();
		table = document.getElementById(tbl);
		tr = $(`#${tbl} tbody tr`);

	
		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			let tds = tr[i].getElementsByTagName("td");
			let exists = true;
			for(let j = 0; j < tds.length; j++) {
				if(tds[j]) {
					txtValue = tds[j].textContent || tds[j].innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						exists = true;
						console.log("td break", j)
						break;
					} else {
						exists = false;
					}
					console.log(txtValue, filter, exists);
				}
			}
			if(exists) {
				tr[i].style.display = "";
			} else {
				tr[i].style.display = "none";
			}
		}
	});


	var croppieimg = "";
	var imghaschanges;
	var croppie;
	var croppieready = false;

	$("#customer_detail_modal").on("shown.bs.modal", function(){
		
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
	});

	$("#save-btn").click(() => {
		$("#customer_form button").trigger("click");
	});

	$("#customer_form").submit(e => {
		e.preventDefault();
		
		
		if(!$(e.currentTarget)[0].checkValidity()) {
			return;
		}

		form.location_img = croppieimg;
		let customer = form;

		$.ajax({
			method: 'POST',
			data: customer,
			url: baseurl+'/customer/save',
			success: function(res){
				var res = JSON.parse(res);
				NProgress.done();
				if(res["success"]){
					showMessage("Customer details has been saved succesfuly.")
					$("#customer_form").removeClass("was-validated");
					form.id = res.id;
					addRow();
					clearForm();
					$("#customer_detail_modal").modal("hide");
				} else {
				
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

	$("#delete-btn").click(() => {
		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, delete it!",
			showLoaderOnConfirm: true,
			preConfirm: e => {

			return $.post(baseurl+'/customer/delete',  {id:form.id},  (res) => {
				if (!res["success"]) {
					throw new Error(response.statusText);
					return false;
				  }
				  return response;
			})
			.fail(function(error) {
				console.log(error)
				Swal.showValidationMessage(
					`Request failed: ${error.statusText}`
				  );
				  return false
			  })
			}
		  })
		  .then(result => {
			if (result.value) {
			  Swal.fire(
				"Deleted!",
				"Customer has been deleted.",
				"success"
			  ).then((result) => {
				window.location = baseurl + "/customer";
			  });
			  
			}
		  });
	  });

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
				}, 500);
				setTimeout(function(){
					croppie.croppie('bind', {
						url: e.target.result
					});

					$('#map_img_preview').croppie("result", {
						type: "base64",
						format: "jpeg"
					}).then(function(img) {
						croppieimg = img;
					});
				},600);
				imghaschanges = true;
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	
});
