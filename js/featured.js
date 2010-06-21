$(document).ready(function (){
	$.ajaxSetup({cache:false});
	$('#pBtn').click(function(){
		//alert ($("#bUrl").val());
		var post_id = $("#pDrop").val();
		var purl = $("#bUrl").val();
		$("#pBox").html("loading...");
		//alert(purl+'/wp-content/plugins/featured-post-type/get-post.php?mainCats='+post_id);
		
		$.get (purl+'/wp-content/plugins/featured-post-type/get-post.php?mainCats='+post_id, function(data) {
			$("#pBox").html(data);
			//  alert(data);
			});
		
		//$("#your_post_here").load("http://<?php echo $_SERVER[HTTP_HOST]; ?>/triqui-ajax/",{id:post_id});
		

		
	});
});
