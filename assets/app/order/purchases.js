	var loading = false;
	var currentPage = 'all';
	let settings = {
		all : {
			start: 5,
			length: 5,
			loaded: true,
			no_more_results: false,
			empty: false
		},

		packing : {
			start: 0,
			length: 5,
			loaded: false,
			no_more_results: false,
			empty: false
		},
		
		topay : {
			start: 0,
			length: 5,
			loaded: false,
			no_more_results: false,
			empty: false
		},

		toreceive : {
			start: 0,
			length: 5,
			loaded: false,
			no_more_results: false,
			empty: false
		},

		completed : {
			start: 0,
			length: 5,
			loaded: false,
			no_more_results: false,
			empty: false
		},

		cancelled : {
			start: 0,
			length: 5,
			loaded: false,
			no_more_results: false,
			empty: false
		},

		delivered : {
			start: 0,
			length: 5,
			loaded: false,
			no_more_results: false,
			empty: false
		},
	}
$(document).ready(() => {
	//set scroll to top
	$(window).scrollTop(0);

	$("#purchasesNav .nav-link").click(e => {
		$("#purchasesNav .nav-link").removeClass('active');

		$(e.currentTarget).addClass('active');
	})

	

	$(window).scroll(function() {
		if($(window).scrollTop() == $(document).height() - $(window).height()) {
			loadPurchases()
		}
	}); // window scroll

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		
		let page = $(e.target).data('page');
		currentPage = page;

		if(!settings[currentPage].loaded) {
			 $("#empty").addClass('d-none')
			loadPurchases()
		}
		showEmpty()
	})
 
	 $('.tab-content').delegate(".order-card", 'click',  (e) => {
		let id = $(e.currentTarget).data('id');
		window.location = siteURL + '/order/detail/' + btoa(id.toString());
	});

function showEmpty() {
	setTimeout(function() {
		if($('#'+currentPage).children().length == 0) {
			$("#empty").removeClass('d-none');
			
		} else {
			$('#empty').addClass('d-none');
	
		}
	}, 300)
	
}
	
function appendPurchases(resp) {


	for(let row of resp) {

		//construct detail html
		let detail_html = [];
		for(let key in row.detail) {
		
			let detail = row.detail[key];
			if(key <= 2) {
				detail_html = [
					'<li class="list-group-item d-flex border-top-0   border-bottom justify-content lh-condensed border-left-0 border-right-0">',
						'<div class="mr-3 img-placeholder">',
						detail["prod_img"] ? '<img src="' + baseURL + 'assets/prod_img/' + detail["prod_img"] + '" class="card-img" height="100" width="100"/>' : '',	
						'</div>',
						'<div class="mr-auto">',
							'<h6 class="my-0">' + detail["description"] + '</h6>',
							'<small class="text-muted">' + detail["quantity"] + ' ' + detail["uom"] + '</small>',
						'</div>',
						'<span class="text-muted">' + toCurrency(detail["total_price"], 2) + '</span>',
					'</li>'
				]
			} else {
				
				let more_detail  = row["detail_count"] - (key + 1);
				if(more_detail > 0 ) {
					detail_html.push('<a class=" list-group-item  text-muted font-smaller p-1 text-center border-left-0 border-right-0" href="#">View ' + more_detail + ' more item/s</a>');
				}
			}
		}

		let order_html = `
			<div class="card mb-3 order-card" data-id="${row["id"]}">
				<div class="card-header d-flex">
					<span class="mr-auto order_number" >Order # ${row["order_number"]}</span>
					<span class=" d-none d-lg-block">${row["tracking"]["message"]}</span>
					<span class=" border-left ml-3 pl-3" style="color: ${row["tracking"]["color"]} !important"> ${row["tracking"]["status"]}</span>
				</div>
				<div class="card-body px-0 ">
					<ul class="list-group mb-3">
						${detail_html.join('\n')}
						<li class="list-group-item d-flex  border-left-0 border-right-0">
							<div class="text-success ml-auto">
								<h6 class="my-0">Total Amount</h6>
							</div>
							<span class="text-success ml-5">${toCurrency(row["total"])}</span>
						</li>
						<li class="list-group-item d-flex border-left-0 border-right-0">
						<div class="ml-auto" >
							<h6 class="my-0">Payment Method</h6>
						</div>
						<span class="ml-5" >${payment_method[row['payment_method']]}</span>
						</li>
						<li class="list-group-item d-lg-none border-left-0 border-right-0 border-bottom-0">
							<span>${row["tracking"]["message"]}</span>
						</li>
					</ul>
				</div>
			</div>
		`;

	  
		$("#"+currentPage).append(order_html).fadeIn();
		
	}
}


	function loadPurchases() {
		if(loading || settings[currentPage].no_more_results) {
			return;
		}
		   // ajax call get data from server and append to the div
		   $('#loader').removeClass('d-none');
		 
		   loading = true;
		   $(window).scrollTop($(window).scrollTop()-100);
		   let data = {
			   start : settings[currentPage].start,
			   length: settings[currentPage].length,
			   type: currentPage
		   }

			$.ajax({
				method: 'GET',
				url: siteURL + '/order/transaction',
				data: data,
				dataType: "json",
				success(resp) {
					
					loading = false,
					$('#loader').addClass('d-none');
					
					settings[currentPage].start += settings[currentPage].length;
					
					if(resp.length < 5 ) {
						settings[currentPage].no_more_results = true;

						if(resp.length == 0) {
							showEmpty()
						}
					} 

					appendPurchases(resp);

				},
				error(jqXHR, textStatus,  error) {
					console.log(jqXHR, textStatus,  error)
				}
			})
	}


	showEmpty();
})
