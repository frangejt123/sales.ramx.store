$(document).ready(function(){

	NProgress.configure({ showSpinner: false });
	$("input#search_driver").on("keyup", function(){
		// Declare variables
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("search_driver");
		filter = input.value.toUpperCase();
		table = document.getElementById("dispatch_table");
		tr = table.getElementsByTagName("tr");

		// Loop through all table rows, and hide those who don't match the search query
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[1];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
	});

	$("#cancel_dispatch_btn").on("click", function(){
		NProgress.start();
		window.location = baseurl;
	});

	$("#dispatch_table").on("click", "tr", function(){
		var td = $(this).find("td");
		$("#name").val(td[1].textContent);
		var id = td[0].textContent;
		window.location = baseurl + "/dispatch/detail/"+btoa(id);
	});

	$("#create_dispatch_btn").on("click", function(){
		window.location = baseurl + "/dispatch/newdispatch";
	});

	function validate(s) {
		var rgx = /^[0-9]*\.?[0-9]*$/;
		return s.match(rgx);
	}

	function ucwords (str) {
		return (str + '')
			.replace(/^(.)|\s+(.)/g, function ($1) {
				return $1.toUpperCase()
			})
	}
});
