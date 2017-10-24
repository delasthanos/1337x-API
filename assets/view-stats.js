$(document).ready(function (){
	$(".view-results").click(function (){
		//console.log(this.id);
		//alert(this.id);
		window.location.href = "view-stats.php?imdb="+this.id;
	});
	
	$(".imdb").click( function (){
		var url="http://www.imdb.com/title/";
	});
})
