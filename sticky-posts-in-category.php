<?php
/**
 * @package Sticky Posts In Category
 * @author Ryann Micua
 * @version 1.0
 *
 * Plugin Name: Sticky Posts In Category
 * Plugin URL: http://pogidude.com/plugins/sticky-posts-in-category-pages-plugin/
 * Description: Shows sticky posts on top of Category pages
 * Author: Ryann Micua
 * Author URI: http://pogidude.com/about/
 * Version: 1.0
 * License: GPLv2
 */
 
class PD_StickyPostsInCategory{
	function PD_StickyPostsInCategory(){
		return $this->__construct();
	}
	
	function __construct(){
		define( 'PD_SPIC_VERSION', '1.0' );
		//e.g. /var/www/example.com/wordpress/wp-content/plugins/exit-crusher
		define( "PD_SPIC_DIR", plugin_dir_path( __FILE__ ) );
		//e.g. exit-crusher/exit-crusher.php
		define( "PD_SPIC_BASENAME", plugin_basename( __FILE__ ) );
		
		//add_action('pre_get_posts', array( $this, 'stickPostsInCategory' ) );
		//add_filter('parse_query', array( $this, 'stickPostsInCategory' ) );
		add_filter('the_posts', array( $this, 'putStickyOnTop' ) );
	}
	
	function stickPostsInCategory( &$q ){
		//TODO: run this only on category pages
		
		$sticky_posts = get_option('sticky_posts');
		
		$q->set('ignore_sticky_posts',false);

		return $q;
	}
	
	function putStickyOnTop( $posts ){
		global $wp_query;
		
		if( is_category() && is_main_query() ){
		
			$sticky_posts = get_option('sticky_posts');
			$num_posts = count( $posts );
			$sticky_offset = 0;
			//loop over posts and relocate stickies to the front
			for( $i = 0; $i<$num_posts; $i++){
				if( in_array( $posts[$i]->ID, $sticky_posts ) ){
					$sticky_post = $posts[$i];
					//remove sticky post from current position
					array_splice( $posts, $i, 1 );
					//move to front, after other stickies
					array_splice( $posts, $sticky_offset, 0, array( $sticky_post ) );
					//increment the sticky offset. the next sticky will be placed at this offset.
					$sticky_offset++;
					//remove post from sticky posts array
					$offset = array_search( $sticky_post->ID, $sticky_posts );
					unset( $sticky_posts[$offset] );
				}
			}
		
		}
		
		return $posts;
	}
	
	function activate(){
	}
}

$PD_StickyPostsInCategory = new PD_StickyPostsInCategory();

register_activation_hook( __FILE__, array( 'PD_StickyPostsInCategory', 'activate' ) );
