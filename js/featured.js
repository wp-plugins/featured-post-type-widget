$(document).ready(function (){
	$.ajaxSetup({cache:false});
	
});

function addContent(holdId){
		//alert ($("#bUrl").val());
		var post_id = $("#pDrop_"+holdId).val();
		var purl = $("#bUrl_"+holdId).val();
		var ht="<center><img src=\""+purl+"/wp-content/plugins/featured-post-type/ajax-loader.gif\" alt=\"Loading\" style=\"height:32px;width:32px;\" /><br/>Loading....</center>";
		$("#pBox_"+holdId).html(ht);
		//alert(purl+'/wp-content/plugins/featured-post-type/get-post.php?mainCats='+post_id);
		
		$.get (purl+'/wp-content/plugins/featured-post-type/get-post.php?mainCats='+post_id, function(data) {
			$("#pBox_"+holdId).html(data);
			//  alert(data);
			});
		
		//$("#your_post_here").load("http://<?php echo $_SERVER[HTTP_HOST]; ?>/triqui-ajax/",{id:post_id});
		

		
	};