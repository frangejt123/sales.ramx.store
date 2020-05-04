
function routeTo(e) {
	NProgress.start();
	let to = $(e.currentTarget).data("route-to");
	window.location = baseurl + to;
}

function addPreNew() {

	let tmp = document.getElementsByTagName("template")[0];
	if(!tmp) {
		return;
	}

	let row = tmp.content.cloneNode(true);


	let _row = {
		id: null,
		tmp_id: "pre_new",
		product_id: null,
		quantity: null,
		_state: "pre_new"
	};



	$("#adjustment-detail-table tbody").append(row);
	window.rows.push(_row);
}


function findIndex(array, callback) {
	let ind = 0;
	for(let row of array) {
		if(callback(row)) {
			return ind;
		} else {
			ind++;
		}
	}

	return -1;
}


function hasPreNew() {
	let data = window.rows.filter(r => {
		return r._state === "pre_new";
	});
	return data.length > 0;
}

function *generateId() {
	let ind = 1;
	while(true) yield ind++;
}

function getChangedData() {
	return window.rows.filter(r => {
		return r._state !== "pre_new" && r._state !== "";
	})
}

function today() {
	var d = new Date();

	var month = d.getMonth()+1;
	var day = d.getDate();

	return  d.getFullYear() + '-' +
		(month<10 ? '0' : '') + month + '-' +
		(day<10 ? '0' : '') + day;
}

function showUndo(tr) {
	$tr.find('.action-btn.undo').removeClass('d-none');
	$tr.find('.action-btn.delete').addClass('d-none');
}
function showDelete(tr) {
	$tr.find('.action-btn.delete').removeClass('d-none');
	$tr.find('.action-btn.undo').addClass('d-none');
}

function isEditable() {
	if(window.adjustment) {
		return window.adjustment.status == 1;
	} else {
		return true;
	}
}

function setData() {
	if(window.adjustment) {
		$("#type").val(window.adjustment.type);
		// $("#date").val(window.adjustment.date);
		for(let i in window.rows) {
			window.rows[i].tmp_id = window.rows[i].id;
			window.rows[i]._state = "";
			window.rows[i]._original = Object.assign({}, window.rows[i]);
		}

		if(!isEditable()) {
			setDisabledPage();
		}

	} else {
		$("#date").val(today());
	}
}

function setDisabledPage() {
	$(".input").attr("disabled", true)
		.addClass("form-disabled")
	$("#type").addClass("form-readonly").attr("disabled", true);
	$("#date").addClass("form-readonly").attr("readonly", true);
	$("#remarks").addClass("form-readonly").attr("readonly", true);

	//hide action buttons
	$(".action-btn, #save-btn, #approve-btn, #delete-btn").addClass("d-none");
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


$(document).ready(() => {

	setData();


	let getId = generateId();
	/*
	 *	ROUTING BUTTONS
	 */
	$(".routing-btn").click(routeTo);


	if(isEditable()) {
		addPreNew();
	}





	$("tbody").delegate(".input", "change", (e) => {
		let $tr = $(e.currentTarget.parentElement.parentElement);
		let tmp_id = $tr.data("tmp-id");
		let model = $(e.currentTarget).data("model");
		let val = $(e.currentTarget).val();
		let ind = findIndex(window.rows, (row) => {
			return row.tmp_id == tmp_id
		});

		if(ind > -1) {
			window.rows[ind][model] = val;

			if(window.rows[ind]._state == "") {
				window.rows[ind]._state = "edited";
				$tr.addClass("table-info");
				$tr.find('.action-btn.undo').removeClass('d-none');
				$tr.find('.action-btn.delete').addClass('d-none');
			}



			if(model == "product_id") {
				//exists check state
				if(!window.rows[ind].id) {
					window.rows[ind]._state = "new";
					window.rows[ind].tmp_id = getId.next().value;
					$tr.data("tmp-id", window.rows[ind].tmp_id)
					$tr.attr("data-tmp-id", window.rows[ind].tmp_id);
					$tr.addClass("table-success");
					$tr.find(".action-btn.undo").removeClass("d-none")
				}


				if(!hasPreNew()) {
					addPreNew();
				}

			}
		}

	});

	$("tbody").delegate(".action-btn", "click", (e) => {
		let $tr = $(e.currentTarget.parentElement.parentElement);
		let tmp_id = $tr.data("tmp-id");
		let action = $(e.currentTarget).data("action");

		let ind = findIndex(window.rows, r => {
			return r.tmp_id == tmp_id;
		});

		if(ind > -1) {
			if(action == "undo") {
				//remove from window.rows
				if(window.rows[ind]._state == "new") {
					window.rows.splice(ind, 1);
					//remove from html
					$tr.remove();
				} else if(window.rows[ind]._state == "deleted") {
					window.rows[ind]._state = "";
					$tr.removeClass("table-danger");
					$(e.currentTarget).addClass('d-none');
					$tr.find('.action-btn.delete').removeClass('d-none');
					$tr.find('.input').attr('disabled', false);
				} else if (window.rows[ind]._state == "edited") {
					$tr.removeClass("table-info");
					$(e.currentTarget).addClass("d-none");
					$tr.find('.action-btn.delete').removeClass('d-none');

					window.rows[ind].quantity = window.rows[ind]._original.quantity;
					window.rows[ind].product_id = window.rows[ind]._original.product_id;

					$tr.find('select').val(window.rows[ind].product_id);
					$tr.find('input').val(window.rows[ind].quantity);

				}

			} else if (action == "delete") {
				window.rows[ind]._state = "deleted";
				$tr.addClass("table-danger");
				$(e.currentTarget).addClass('d-none');
				$tr.find('.action-btn.undo').removeClass('d-none');
				$tr.find('.input').attr('disabled', true);
			}
		}

	});


	let sending = false;
	$("#save-btn").click(e => {

		e.preventDefault();

		if(sending) {
			return;
		}

		sending = true;

		let type = $("#type").val();
		let date = $("#date").val();
		let remarks = $("#remarks").val();
		let _state = window.adjustment ? "edited" : "new";
		let id = window.adjustment ? window.adjustment.id:null;
		if(type == "") {
			return;
		}

		if(date == "") {
			return;
		}

		let changedData = getChangedData();


		let param = {
			adjustment : {
				id,
				type,
				date,
				remarks,
				_state
			}
		}

		if(window.adjustment) {

		} else {
			if(changedData.length == 0) {
				return;
			}
		}

		if(changedData.length > 0) {
			param.details = changedData;
		}

		$.ajax({
			method: 'POST',
			url: baseurl + '/inventory/adjustment/save',
			data: param,
			success: function (res) {
				var res = JSON.parse(res);

				NProgress.done();
				if(res.adjustment) {

					$("#adjustment_id").val(res.adjustment.id);
					window.adjustment = res.adjustment;
					if(res.details) {
						for(let detail of res.details) {
							let ind = findIndex(window.rows, r => {
								return r.tmp_id == detail.tmp_id;
							});

							// update window.rows
							if(ind > -1) {
								let state  = window.rows[ind]._state;
								let $tr = $("tbody").find(`[data-tmp-id='${detail.tmp_id}']`);

								if(state == "deleted") {
									window.rows.splice(ind, 1);
									$tr.remove();
								} else {
									window.rows[ind]._state = "";
									window.rows[ind].id = detail.id;
									window.rows[ind]._original = Object.assign({}, window.rows[ind]);
									$tr.removeClass("table-success table-info table-danger");
									$tr.find('.action-btn.undo').addClass('d-none');
									$tr.find('.action-btn.delete').removeClass('d-none');
								}
							} else {
								console.log("error ind not found")
							}
						}


					}
					$("#approve-btn").removeClass("d-none");
					$("#delete-btn").removeClass("d-none");
					showMessage("Inventory adjustment has been saved successfuly.")
				}

				sending = false;
			},
			error: function (xhr, status, error) {
				NProgress.done();
				sending = false;
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	})

	let approving = false;

	$("#approve-adjustment").click(e => {
		e.preventDefault();

		if(approving) {
			return;
		}

		approving = true;

		let param = {
			id: window.adjustment.id
		};

		$.ajax({
			method: 'POST',
			url: baseurl + '/inventory/adjustment/approve',
			data: param,
			success: function (res) {
				var res = JSON.parse(res);

				if(res.adjustment.success) {
					//set status to approve
					window.adjustment.status = 2;
					$("#approved_by").text(res.adjustment.approved_by_name);
					$("#status-display").text("Approve")
						.removeClass("text-success")
						.addClass("text-primary");
					//set inputs to readonly
					setDisabledPage();
					showMessage("Inventory adjustment has been approved succesfuly.")
					$("#approve-modal").modal("hide");
				}
			}
		});
	});

	$("#delete-adjustment").click(e => {
		let param = {
			id: window.adjustment.id
		};

		$.ajax({
			method: 'POST',
			url: baseurl + '/inventory/adjustment/delete',
			data: param,
			success: function (res) {
				var res = JSON.parse(res);

				if(res.adjustment.success) {
					showMessage("Inventory adjustment has been deleted succesfuly.")
					NProgress.start();
					window.location = baseurl + "/inventory/adjustment";
				}
			}
		});
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


	$(".sortable").click(function(e) {
		let ind = e.currentTarget.cellIndex;
		let tbl = $(e.currentTarget.offsetParent).attr("id");
		let isSortUp = $(e.currentTarget).find("i").hasClass("fa-sort-up");

		$(".sortable i").removeClass("fa-sort-up fa-sort-down");
		$(".sortable i").addClass("fa-sort");

		$(e.currentTarget).find("i").removeClass("fa-sort");

		if(isSortUp) {
			$(e.currentTarget).find("i").addClass("fa-sort-down");
		} else {
			$(e.currentTarget).find("i").addClass("fa-sort-up");
		}

		sortTable(ind, tbl);
	});

});

function sortTable(n, tableId) {
	var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
	table = document.getElementById(tableId);
	switching = true;
	// Set the sorting direction to ascending:
	dir =  "asc";
	/* Make a loop that will continue until
	no switching has been done: */
	while (switching) {
		// Start by saying: no switching is done:
		switching = false;
		rows = table.rows;
		/* Loop through all table rows (except the
		first, which contains table headers): */
		for (i = 1; i < (rows.length - 1); i++) {
			// Start by saying there should be no switching:
			shouldSwitch = false;
			/* Get the two elements you want to compare,
			one from current row and one from the next: */
			x = rows[i].getElementsByTagName("TD")[n];
			y = rows[i + 1].getElementsByTagName("TD")[n];
			/* Check if the two rows should switch place,
			based on the direction, asc or desc: */
			if (dir == "asc") {
				if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
					// If so, mark as a switch and break the loop:
					shouldSwitch = true;
					break;
				}
			} else if (dir == "desc") {
				if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
					// If so, mark as a switch and break the loop:
					shouldSwitch = true;
					break;
				}
			}
		}
		if (shouldSwitch) {
			/* If a switch has been marked, make the switch
			and mark that a switch has been done: */
			rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			switching = true;
			// Each time a switch is done, increase this count by 1:
			switchcount ++;
		} else {
			/* If no switching has been done AND the direction is "asc",
			set the direction to "desc" and run the while loop again. */
			if (switchcount == 0 && dir == "asc") {
				dir = "desc";
				switching = true;
			}
		}
	}
}


