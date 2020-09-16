$(document).ready(() => {
	$("form").submit((e) => {
		e.preventDefault();
		const username = $("#username").val();
		const password = $("#password").val();
		$(".error-message").addClass('d-none');
		$.ajax({
			type: "POST",
			data: { username , password },
			url: siteUrl + '/order/auth',
			success(resp) {
				window.location = siteUrl + '/order'
			},
			error(error) {
				if(error.status == 401) {
					$(".error-message").removeClass('d-none');
				}
			}
		})
	})
});
