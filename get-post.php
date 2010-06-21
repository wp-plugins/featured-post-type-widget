<?php 
define( "WP_INSTALLING", true );
require ('../../../wp-blog-header.php');

function get_first_words_for_fpt($text, $length = 28) {
	if (!$length)
		return $text;
		
	$text = strip_tags($text);
	$words = explode(' ', $text, $length + 1);
	if (count($words) > $length) {
		array_pop($words);
		array_push($words, '...');
		$text = implode(' ', $words);
	}
	return $text;
}

if(isset($_GET['mainCats'])) {
	$pid = $_GET['mainCats'];
	$postsl =get_post ($pid,ARRAY_A);
	$pimg = get_the_post_thumbnail($pid);
	//	print_r($postsl);
	//	print_r($pimg);
	$pout="";
	$pout .= ($pimg);
	$pout .= "<strong><a title=\"Permanent Link to ".$postsl['post_title']."\" rel=\"bookmark\" href=\"".$postsl['guid']."\">".$postsl['post_title']."</a></strong>";
	$pout .= '<div class="post-details">
  						  					</div> <!-- .post-details -->
  					  						<div class="txtBox" style="overflow-x: auto;"> <!-- for images wider than widget area -->';
  	$pout .= get_first_words_for_fpt($postsl['post_content'], 35);
  	
  	$pout .= "<a title=\"Permanent Link to ".$postsl['post_title']."\" rel=\"bookmark\" href=\"".$postsl['guid']."\" class=\"right More\">Read More &gt;&gt;</a></div></div>";
  	
  	echo $pout;
	}
?>
