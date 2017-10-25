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


	$(".download-torrent-pages").click(function (){
	
		$("#download-torrent-results").hide();
		$(".download-torrent-pages").html("Downloading. May take a while. PLease wait ...");
	
		$.ajax({
			url : 'download-torrent-pages.php',
			type : 'POST',
			/*data :  ....,   */
			tryCount : 0,
			retryLimit : 3,
			data: { 
				'imdb': this.id
			},
			success : function(json) {

				var ansi_up = new AnsiUp;
				var html = ansi_up.ansi_to_html(json);

				$(".download-torrent-pages").html("[*]Done::Terminal Output");
				$("#download-torrent-results").html(html);
				$("#download-torrent-results").fadeIn(1000);
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
	});

});


