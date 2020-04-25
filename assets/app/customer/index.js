function routeTo(e) {
	NProgress.start();
	let to = $(e.currentTarget).data("route-to");
	window.location = baseurl + to;
}

$(document).ready(() => {
		/*
	 *	ROUTING BUTTONS
	 */
	$(".routing-btn").click(routeTo);


	$("#save_customer_detail_btn")
});
