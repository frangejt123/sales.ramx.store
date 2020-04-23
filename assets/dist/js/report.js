$(document).ready(function(){
	var docheight = $(window).height();

	$("div#iframecontainer").html('<iframe width="100%" id="report_iframe" style="border: 0px;"></iframe>')


	$("iframe#report_iframe").height((docheight-158)+"px");
	$("input#report_date_from, input#report_date_to").datepicker();

	var reportopt = [{"id":"","text":""}];
    $.each(reportnames, function(ind, row){
        reportopt.push({"id":row["id"],"text":row["text"]});
    });

    $('.select2#report_name').select2({
        placeholder: "",
        data: reportopt
    });

    var branchopt = [{"id":"","text":""}];
    $.each(reportbranch, function(ind, row){
        branchopt.push({"id":row["id"],"text":row["text"]});
    });

    $('.select2#report_branch').select2({
        placeholder: "",
        data: branchopt
    });

    $("button#print_report").on("click", function(){
    	var report = Array(
    		"productmovement",
    		"drinkpercentage"
    	);

    	var id = $('.select2#report_name').val();
    	var datefrom = $("input#report_date_from").val();
    	var dateto = $("input#report_date_to").val();
    	var branch;
    	
    	if(access_level == 0){//if admin
    		branch = $(".select2#report_branch").select2('val');
    	}else{
    		branch = branch_id;
    	}

    	var params = "datefrom="+datefrom+"&dateto="+dateto+"&branch="+branch;
    	var src = baseurl+"/report/"+report[id]+"?"+params;
    	$("iframe#report_iframe").attr("src", baseurl+"/report/"+report[id]+"?"+params);
    });
});