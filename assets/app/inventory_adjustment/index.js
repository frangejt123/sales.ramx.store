
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
				$tr.addClass("info");
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
						$tr.addClass("success");
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
					$tr.removeClass("danger");
					$(e.currentTarget).addClass('d-none');
					$tr.find('.action-btn.delete').removeClass('d-none');
					$tr.find('.input').attr('disabled', false);
				} else if (window.rows[ind]._state == "edited") {
					$tr.removeClass("info");
					$(e.currentTarget).addClass("d-none");
					$tr.find('.action-btn.delete').removeClass('d-none');

					window.rows[ind].quantity = window.rows[ind]._original.quantity;
					window.rows[ind].product_id = window.rows[ind]._original.product_id;

					$tr.find('select').val(window.rows[ind].product_id);
					$tr.find('input').val(window.rows[ind].quantity);

				}
				
			} else if (action == "delete") {
				window.rows[ind]._state = "deleted";
				$tr.addClass("danger");
				$(e.currentTarget).addClass('d-none');
				$tr.find('.action-btn.undo').removeClass('d-none');
				$tr.find('.input').attr('disabled', true);
			}
		}
		
	});

	$("#save-btn").click(e => {
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
									$tr.removeClass("success info danger");
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
					showMessage("Inventory adjustment has been saved succesfuly.")
				}
			},
			error: function (xhr, status, error) {
				NProgress.done();
			},
			beforeSend: function(){
				NProgress.start();
			}
		});
	})

	$("#approve-adjustment").click(e => {

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
    
});


