<?php
/*
	Plugin Name: Members
	Plugin URI: https://github.com/zutrinken/wp-members
	Description: Display members in a grid on a page via shortcode and custom post types.
	Version: 0.1
	Author: Peter Amende
	Author URI: http://zutrinken.com
	Text Domain: members
	Domain Path: /languages
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

load_plugin_textdomain('members', false, basename( dirname( __FILE__ ) ) . '/languages');

add_action( 'init', 'create_members_post_type' );
function create_members_post_type() {
	register_post_type(
		'members',
		array(
			'labels' => array(
				'name' => __( 'Members','members' ),
				'singular_name' => __( 'Member','members' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array(
				'slug' => 'members',
				'with_front' => FALSE
			),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
		)
	);
	flush_rewrite_rules();
}

/* add custom image-sizes */
if ( function_exists( 'add_theme_support' ) ) { 
	add_theme_support('post-thumbnails');

	add_image_size('member-square', 480, 480, true);
}

function display_members_shortcode($atts) {

	$args = array(
		'post_type' => 'members',
		'order' => 'ASC',
		'orderby' => 'name',
		'showposts' => '200',
	);

	$return = '';

	$listing = new WP_Query($args);
	if ( $listing->have_posts() ):

		$return .= '<div id="members">';

		while ( $listing->have_posts() ): $listing->the_post(); global $post;

			$output = '';
			
			$output .= '<section class="member">';
			
			if ( has_post_thumbnail() ) {
				$thumbnail = '<figure class="member-figure">';
				$thumbnail .= get_the_post_thumbnail($post_id, 'member-square');
			
				if(get_post(get_post_thumbnail_id())->post_excerpt) {
					$caption = '<figcaption class="member-caption">';
					$caption .= get_post(get_post_thumbnail_id())->post_excerpt;
					$caption .= '</figcaption>';
					$thumbnail .= $caption;
				}
				$thumbnail .= '</figure>';
				$output .= $thumbnail;
			}
			$output .= '<h4 class="member-title">'. get_the_title() . '</h4>';
			
			$output .= '<div style="clear:both;"></div></section>';
			
			$return .= apply_filters( 'display_members_shortcode', $output);

		endwhile;
		
		$return .= '</div>';

	endif; wp_reset_query();

	if (!empty($return)) return $return;
}
add_shortcode('members', 'display_members_shortcode');

function members_add_styles() {
	wp_enqueue_style( 'members-css', plugins_url('members.css', __FILE__), array());
}
add_action( 'wp_print_styles', 'members_add_styles' );

?>