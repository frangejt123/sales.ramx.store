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


$(document).ready((e) => {
	var croppieimg = "";
	var imghaschanges;
	var croppie;
	var croppieready = false;

	
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


	$(".input").change(e => {
		let model = $(e.currentTarget).data("model");
		let resource = $(e.currentTarget).data('resource');
		window[resource][model] =  $(e.currentTarget).val();
		window[resource].hasChanges = true;
	}); 

	$("#save-btn").click((e) => {
		e.preventDefault();
		$("form").trigger('submit');
	})

	$('form').submit((e) => {
		e.preventDefault();
	
		if(!$(e.currentTarget)[0].checkValidity()) {
			$('form').addClass('was-validated')
			return;
		}

		let data = {};
		let hasChanges = false;
		
		if(window.customer.hasChanges) {
			data.customer = window.customer;
			hasChanges = true;
		}

		if(window.user.hasChanges) {
			data.user = window.user;
			hasChanges = true;
			if("password" in window.user) {
				if("cpassword" in window.user) {
					if(window.user.password != window.user.cpassword) {
						$("#password").addClass('is-invalid');
						return;
					} else {
						$("#password").removeClass('is-invalid');
						$("#cpassword").removeClass('is-invalid');
					}
				} else {
					$("#cpassword").addClass('is-invalid');
					return;
				}
			}

		}
	
		if(!hasChanges) {
			return;
		}

		$.ajax({
			method: 'POST',
			data,
			url: siteURL + '/order/save_profile',
			success(resp) {
				Swal.fire({
					position: 'top-end',
					icon: 'success',
					title: 'Changes has been saved!',
					showConfirmButton: false,
					timer: 1500
				  })
			
			},
			error(e) {

			}
		})
	})

})
