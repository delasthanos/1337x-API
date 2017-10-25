$(document).ready(function (){
	$(".view-results").click(function (){
		//console.log(this.id);
		//alert(this.id);
		window.location.href = "view-stats.php?imdb="+this.id;
	});
	
	$(".imdb").click( function (){
		var url="http://www.imdb.com/title/";
	});
	
	$("#update-stats-cont").fadeIn(1000);
	
function ganttEach() {

	$.ajax({
		url : 'src/update-stats.php',
		type : 'GET',
		/*data :  ....,   */
		tryCount : 0,
		retryLimit : 3,
		success : function(json) {
			$("#update-stats span").fadeOut(20);
		    $("#update-stats-cont").html(json);
			$("#update-stats span").fadeIn(200);
		    //console.log("ok");
		},
		error : function(xhr, textStatus, errorThrown ) {
		    if (textStatus == 'timeout') {
		        this.tryCount++;
		        if (this.tryCount <= this.retryLimit) {
		            //try again
		            $.ajax(this);
		            return;
		        }            
		        return;
		    }
		    if (xhr.status == 500) {
		        //handle error
		    } else {
		        //handle error
		    }
		}
	});
}
window.setInterval(ganttEach, 5000);
})
