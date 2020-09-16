// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
'use strict'

window.addEventListener('load', function () {
	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.getElementsByClassName('needs-validation')

	// Loop over them and prevent submission
	Array.prototype.filter.call(forms, function (form) {
	form.addEventListener('submit', function (event) {
		if (form.checkValidity() === false) {
		event.preventDefault()
		event.stopPropagation()
		}
		form.classList.add('was-validated')
	}, false)
	})
}, false)
}())

$(document).ready(function() {
	$('form').submit(function(e) {
		e.preventDefault();
		$('#loadingBtn').removeClass('d-none')
		$('#submitBtn').addClass('d-none')

		const customerName = $('#customerName').val();
		const transId = $('#transactionId').val();

		const data = { customerName, transId }

		$.ajax({
			method: 'get',
			data: data,
			url: baseurl+'/order/tracking',
			success: function(res){
				const resp = JSON.parse(res);
				$("p#result").html(resp.message);
				$('#loadingBtn').addClass('d-none')
				$('#submitBtn').removeClass('d-none')
			},
			error: function(xhr, status, error){
			
			},
			beforeSend: function(){
				
			}})

	});
});


