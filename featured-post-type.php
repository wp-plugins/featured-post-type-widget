<?php
/*
Plugin Name: Featured Post Type
Plugin URI: http://ranjith.zfs.in/plugins/featured-post-type/
Description: Plugin with multi-widget functionality that displays selected post types and taxonomies from settings (set with user options). Also includes user options to display: Author and meta details; comment totals; post categories; post tags; and either full post, excerpt, or your choice of the amount of words (or any combination).  
Version: 1.0
Author: Ranjith Siji
Author URI: http://ranjith.zfs.in/
License: GPL2
*/

/*  Copyright 2009-2010  Ranjith Siji  (email : ranjith.siji@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

    The license for this software can also likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html
*/
global $wp_version;
$exit_message = 'Featured Post Type requires WordPress version 3.0 or newer.'.$wp_version.' <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>';
if (version_compare($wp_version, "3.0-RC3", "<")) {
	exit ($exit_message);
}

/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'load_my_fpt_widget' );
/* Add Jquery into the Heading. TODO - Resolve jquery conflict and disable it if it is already loaded */
add_action('wp_head', 'jq_add_js');

/*Function to add Jquery into the Engine */
function jq_add_js()
{
	//	echo '<script type="text/javascript" src="'.get_bloginfo("url").'/wp-content/plugins/featured-post-type/js/jquery.min.js"></script>';
		echo '<script type="text/javascript" src="'.get_bloginfo("url").'/wp-content/plugins/featured-post-type/js/featured.js"></script>';
		
}

/* Function that registers our widget. */
function load_my_fpt_widget() {
	register_widget( 'Featured_Post_Type_Widget' );
}

// Begin the mess of Excerpt Length fiascoes
function get_first_words_for_fpt($text, $length = 55) {
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
// End Excerpt Length

class Featured_Post_Type_Widget extends WP_Widget {
	
	function Featured_Post_Type_Widget() {
		/* Widget settings. */
  		$widget_ops = array('classname' => 'featured-post-type', 'description' => __('Displays Selected Post type from a specific post type and taxonomy.'));
  
  		/* Widget control settings. */
  		$control_ops = array('width' => 450, 'height' => 350, 'id_base' => 'featured-post-type');
  
  		/* Create the widget. */
  		$this->WP_Widget('featured-post-type', 'Featured Post Type', $widget_ops, $control_ops);
  	}
	
	function widget( $args, $instance ) {
  		extract( $args );
  
  		/* User-selected settings. */
  		$title    			= apply_filters('widget_title', $instance['title'] );
  		$cat_choice 		= $instance['cat_choice'];
  		$post_choice 		= $instance['post_choice'];
  		$show_count	  	= $instance['show_count'];
  		$list_count	  	= $instance['list_count'];
  		$drop          = $instance['drop']; /* Plugin requires string variable to generate HTML dropdown! */
  		$show_meta		  = $instance['show_meta'];
  		$show_comments	= $instance['show_comments'];
  		$show_cats  		= $instance['show_cats'];
  		$show_cat_desc  = $instance['show_cat_desc'];
  		$show_tags  		= $instance['show_tags'];
  		$only_titles  	= $instance['only_titles'];
  		$show_full		  = $instance['show_full'];
		  $excerpt_length	= $instance['excerpt_length'];
		  $count          = $instance['count']; /* Plugin requires counter variable to be part of its arguments?! */
		  
		  /* Before widget (defined by themes). */
  		echo $before_widget;
		
  		/* Title of widget (before and after defined by themes). */
  		$cat_choice_class = '';
      $cat_choice_class = preg_replace("/[,]/","-",$cat_choice);
  		if ( $title )
  			echo $before_title . '<span class="featured-post-class-' . $cat_choice_class . '">' . $title . '</span>' . $after_title;
		
  		/* Display posts from widget settings. */
  		query_posts("taxonomy=$cat_choice&posts_per_page=$list_count&post_type=$post_choice");
  		if ( $show_cat_desc ) {
  		  echo '<div class="fpt-cat-desc">' . category_description() . '</div>';
  		}
  		//echo $count;
  		if (have_posts()) : while (have_posts()) : the_post();
  			/* static $count = 0; */ /* see above */
          
  			if ($count < $show_count) {
  			/*	break;
  			} else { */ ?>
  				<div <?php post_class(); ?> id="pBox">
  				<?php //the_post_thumbnail( 'single-post-thumbnail' ); ?>
  				<?php the_post_thumbnail(); ?>
  					<strong><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></strong>
  					<div class="post-details">
  						<?php if ( $show_meta ) {  
  							_e('by '); the_author(); _e(' on '); the_time('M j, Y'); ?><br />
  						<?php }
  						if ( $show_comments ) {         
  							_e('with '); comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments'), '',__('Comments Closed')); ?><br />
  						<?php } 
  						if ( $show_cats ) { 
  							_e('in '); the_category(', '); ?><br />
  						<?php }
              if ( $show_tags ) {
  							the_tags(__('as '), ', ', ''); ?><br />
  						<?php } ?>
  					</div> <!-- .post-details -->
  					<?php if ( !$only_titles ) { ?>
  						<div style="overflow-x: auto;" class="txtBox"> <!-- for images wider than widget area -->
  							<?php if ( $show_full ) { 
  								the_content();
  							} else if (isset($instance['excerpt_length']) && $instance['excerpt_length'] > 0) {
  								echo get_first_words_for_fpt(get_the_content(), $instance['excerpt_length']);
  							} else {
  								the_excerpt();
  							} ?>
  							<a class="right More" href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to'); ?> <?php the_title_attribute(); ?>">Read More &gt;&gt;</a>
  						</div>
  					<?php } ?>
  				</div> <!-- .post #post-ID -->
				
  				<?php   }
  				$count++; /*echo $count ;*/

				$drop .="<option value=\"".get_the_ID()."\">".get_the_title()."</option>";
  				
  			endwhile;
  			?>
  			
  			<select id="pDrop" name="pDrop">
				<option value="-1">Select <?php echo $post_choice; ?> </option>
				<?php echo( $drop); ?>
  			</select>
  				<input type="hidden" id="bUrl" value="<?php bloginfo("url"); ?>"
  				<input type="button" id="pBtn" value="go"/>
  			<?php
  			else : 
  				_e('Yes, we have no taxonomies, or posts, today.');
  			endif; 
        
  		/* After widget (defined by themes). */
  		echo $after_widget;
    	}
    	
    	function update( $new_instance, $old_instance ) {
  		$instance = $old_instance;
  
  		/* Strip tags (if needed) and update the widget settings. */
  		$instance['title']          = strip_tags( $new_instance['title'] );
  		$instance['cat_choice']     = strip_tags( $new_instance['cat_choice'] );
  		$instance['post_choice']     = strip_tags( $new_instance['post_choice'] );
  		$instance['show_count']     = $new_instance['show_count'];
  		$instance['list_count']     = $new_instance['list_count'];
  		$instance['drop']          = $new_instance['drop']; /* creating drop down HTML in single Loop */
  		$instance['show_meta']      = $new_instance['show_meta'];
  		$instance['show_comments']	= $new_instance['show_comments'];
  		$instance['show_cats']      = $new_instance['show_cats'];
  		$instance['show_cat_desc']	= $new_instance['show_cat_desc'];
  		$instance['show_tags']		  = $new_instance['show_tags'];
  		$instance['only_titles']    = $new_instance['only_titles'];
  		$instance['show_full']      = $new_instance['show_full'];
		  $instance['excerpt_length']	= $new_instance['excerpt_length'];
		  $instance['count']          = $new_instance['count']; /* added to be able to reset count to zero for every instance of the plugin */
		   
  		
  		return $instance;
  	}
	
	function form( $instance ) {
    	/* Set up some default widget settings. */
    	$defaults = array(
				'title'           => __('Featured Category'),
				'cat_choice'		  => '1',
				'post_choice'		=>'post',
				'count'           => '1', /* resets count to zero as default */
				'drop'				=>'Select Type',/* The dropdown HTML in a string - Reset Here*/
				'show_count'		  => '3',
				'list_count'		  => '10',
				'show_meta'			  => false,
				'show_comments'	  => false,
				'show_cats'			  => false,
				'show_cat_desc'	  => false,
				'show_tags'			  => false,
				'only_titles'     => false,
				'show_full'			  => false,
				'excerpt_length'	=> ''
        );
    	$instance = wp_parse_args( (array) $instance, $defaults );
		?>
    
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label>
  			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
  		</p>
		
		<p>
  			<label for="<?php echo $this->get_field_id( 'cat_choice' ); ?>"><?php _e('Taxonomy  Names, separated by commas (no spaces):'); ?></label>
  			<input id="<?php echo $this->get_field_id( 'cat_choice' ); ?>" name="<?php echo $this->get_field_name( 'cat_choice' ); ?>" value="<?php echo $instance['cat_choice']; ?>" style="width:90%;" />
  		</p>
  		<p>
  			<label for="<?php echo $this->get_field_id( 'post_choice' ); ?>"><?php _e('Post Type Name:'); ?></label>
  			<input id="<?php echo $this->get_field_id( 'post_choice' ); ?>" name="<?php echo $this->get_field_name( 'post_choice' ); ?>" value="<?php echo $instance['post_choice']; ?>" style="width:90%;" />
  		</p>
  		
  	<p>
				<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_cat_desc'], true ); ?> id="<?php echo $this->get_field_id( 'show_cat_desc' ); ?>" name="<?php echo $this->get_field_name( 'show_cat_desc' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'show_cat_desc' ); ?>"><?php _e('Show first Category choice description?'); ?></label>
		</p>
		
		<p>
  		<label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e('Total Posts to Display:'); ?></label>
  		<input id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="<?php echo $instance['show_count']; ?>" style="width:90%;" />
  	</p>
  	<p>
  		<label for="<?php echo $this->get_field_id( 'list_count' ); ?>"><?php _e('Total Posts on Drop Down List:'); ?></label>
  		<input id="<?php echo $this->get_field_id( 'list_count' ); ?>" name="<?php echo $this->get_field_name( 'list_count' ); ?>" value="<?php echo $instance['list_count']; ?>" style="width:90%;" title="Enter No of post type  to display in the drop down " /> 
  		<?php echo $instance['drop']; ?>
  	</p>
		
		<table width="90%">
			<tr>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_meta'], true ); ?> id="<?php echo $this->get_field_id( 'show_meta' ); ?>" name="<?php echo $this->get_field_name( 'show_meta' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_meta' ); ?>"><?php _e('Display Author Meta Details?'); ?></label>
					</p>
				</td>
				<td>  
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_comments'], true ); ?> id="<?php echo $this->get_field_id( 'show_comments' ); ?>" name="<?php echo $this->get_field_name( 'show_comments' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_comments' ); ?>"><?php _e('Display Comment Totals?'); ?></label>
					</p>
				</td>
			</tr>
			<tr>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_cats'], true ); ?> id="<?php echo $this->get_field_id( 'show_cats' ); ?>" name="<?php echo $this->get_field_name( 'show_cats' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_cats' ); ?>"><?php _e('Display the Post Categories?'); ?></label>
					</p>
				</td>
				<td>
					<p>
						<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_tags'], true ); ?> id="<?php echo $this->get_field_id( 'show_tags' ); ?>" name="<?php echo $this->get_field_name( 'show_tags' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'show_tags' ); ?>"><?php _e('Display the Post Tags?'); ?></label>
					</p>
				</td>
			</tr>
		</table>
		
		<hr /> <!-- separates meta details display from content/excerpt display options -->
  		<p>The default is to show the excerpt, if it exists, or the first 55 words of the post as the excerpt.</p>
		
		<p>
  			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['only_titles'], true ); ?> id="<?php echo $this->get_field_id( 'only_titles' ); ?>" name="<?php echo $this->get_field_name( 'only_titles' ); ?>" />
  			<label for="<?php echo $this->get_field_id( 'show_full' ); ?>"><?php _e('Display only the Post Titles?'); ?></label>
  		</p>
  
		<p>
  			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['show_full'], true ); ?> id="<?php echo $this->get_field_id( 'show_full' ); ?>" name="<?php echo $this->get_field_name( 'show_full' ); ?>" />
  			<label for="<?php echo $this->get_field_id( 'show_full' ); ?>"><?php _e('Display entire Post?'); ?></label>
  		</p>
		
		<p>
  			<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e('Set your preferred value for the amount of words'); ?></label>
  			<input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo $instance['excerpt_length']; ?>" style="width:90%;" />
  		</p>
		<?php
  	
	}
}
?>
