$(document).ready(()=> {
	$("#logout").click((e) => {
		console.log("logout")
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: siteURL + '/order/logout',
			success(resp) {
				if(resp.loggedOut) {
					window.location.reload();
				}
			}
		})
	})

	$('[data-toggle="offcanvas"]').on('click', function () {
		$('.offcanvas-collapse').toggleClass('open')
	  })
});

function  toCurrency (value) {
	if (isNaN(value)) {
	  return '--'
	} else {
	  return new Intl.NumberFormat('en-PH', {
		style: 'currency',
		currency: 'PHP'
	  }).format(value)
	}
}
