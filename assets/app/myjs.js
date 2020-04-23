$(document).ready(function() {

    let template = `
            
    
    
    `;



            $.ajax({
                url: baseurl+'/main/gettransactionslip',
                method: "get",
                success: function(result) {
                    for (let row of JSON.parse(result)) {

                        let details = row.details;

                        let tmpl = `  <div class="row " id="os_${row.id}">
                                            <div class="col">
                                                    <div class="card" >
                                                        <div class="card-header">
                                                           <h4 class="card-title">  Table # ${row.table_number} : <font style="font-size: 15px;color:#c65239;font-weight: bold">${row.datetime}</font>
                                                           <button class="btn btn-success float-right complete" data-id="${row.id}">COMPLETE ORDER</button>
                                                           </h4>
                                    
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-striped table-full-width ">`;
                            for(let detail of details) {
                                tmpl += `<tr class="">
                                            <td>${detail.description}</td>
                                            <td><span class="det_serve_${detail.id}">${detail.serve}</span>/<span class="det_qty_${detail.id}">${detail.quantity}</span></td>
                                            <td>
                                                <button class="btn btn-primary add" data-id="${detail.id}">+1</button>
                                                <button class="btn btn-danger minus" data-id="${detail.id}"> -1</button>
                                            </td>
                                        </tr>`
                            }

                            tmpl += ` </table>
                                        </div>
                                    </div>
                                </div>
                            </div><br/><br/>`;


                        $("#main").append(tmpl);
                    }

                }
            })

        


        $("#main").on("click",".add", function(e) {
            let id = $(this).data("id");
            let prev = $("span.det_serve_"+id).html();
            let qty = $("span.det_qty_"+id).html();

            if(prev == qty)
              return;

            $.ajax({
               url: baseurl+"/main/addQty/"+id,
               success(res){
                   $("span.det_serve_"+id).html(parseInt(prev) + 1);
               }
            });
        });

         $("#main").on("click",".minus", function(e) {
            let id = $(this).data("id");
            let prev = $("span.det_serve_"+id).html();

            if(prev == 0)
              return;

            $.ajax({
               url: baseurl+"/main/minusQty/"+id,
               success(res){
                   let prev = $("span.det_serve_"+id).html();
                   $("span.det_serve_"+id).html(parseInt(prev) - 1);
               }
            });
        });

          $("#main").on("click",".complete", function(e) {
            let id = $(this).data("id");

            $.ajax({
               url: baseurl+"/main/completetrans/"+id,
               success(res){
                   if(res) {
                       $("#os_"+id).remove()
                   }
               }
            });



        });




});