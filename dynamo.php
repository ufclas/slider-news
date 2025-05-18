<?php
/**
 * Plugin Name:       Slider News
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.1
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       slider-news
 *
 * @package           create-block
 */


function create_block_dynamo_render_callback( $attr ) {

	$termName = $attr['postCats'];

	if ($termName)  { 
		$args = array(
			'numberposts'	=> $attr['numberOfItems'],
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'ID',
					'terms'    =>  $termName,
				)
			)
		);
	} else {
		$args = array(
			'numberposts'	=> $attr['numberOfItems'],
		);
	}

	$my_posts = get_posts( $args );
	
	if( ! empty( $my_posts ) ){

		$output = '<div ' . get_block_wrapper_attributes() . '>';
		$output .= '<section class="py-3 slider-section">';

		if ( isset( $attr['message'] ) ) {
			/**
			 * The wp_kses_post function is used to ensure any HTML that is not allowed in a post will be escaped.
			 *
			 * @see https://developer.wordpress.org/reference/functions/wp_kses_post/
			 * @see https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/#escaping-securing-output
			 */
			$output .=  '<div class="row mb-2"><div class="col-12 col-lg-8 col-xxl-7 text-center mx-auto"><h2 class="display-5 mt-2 mb-3">'.$attr['message'].'</h2></div></div>';
		}


		$output .= '<div class="slider">';

		foreach ( $my_posts as $p ){
			
			$title = $p->post_title ? $p->post_title : 'Default title';
			$url = esc_url( get_permalink( $p->ID ) );
			$thumbnail = has_post_thumbnail( $p->ID ) ? get_the_post_thumbnail( $p->ID, 'full'  ) : '';

			$output .= '<a class="col-12 col-md-4 mb-4 position-relative slider-col" tabindex="0" href="' . $url . '">';
            $output .= '<div class="p-4 slider-content-wrapper">';
            $output .= ' <div class="mb-4">';
			if( ! empty( $thumbnail ) && $attr['displayThumbnail'] ){
				$output .= $thumbnail;
			}
            $output .= '  </div>';
            $output .= '<p class="slide-title mb-3">' . $title . '</p>';
			if( get_the_excerpt( $p ) && $attr['displayExcerpt'] ){
				$output .= '<p class="slide-subtext">' . get_the_excerpt( $p ) . '</p>';
			}
            $output .= '</div>';
			$output .= '</a>';
		}
		$output .= '</div><div class="mobile-slide-btn"><button class="slideLeft" type="button"><span aria-label="Previous">‹</span></button><button class="slideRight" type="button"><span aria-label="Next">›</span></button></div><div class="text-center see-more-news"><a class="animated-underline-button" href="' . esc_url(get_permalink(get_option('page_for_posts'))) . ' ">See More News</a></div></section></div>';
	}
	return $output ?? '<strong>Sorry. No posts matching your criteria!</strong>';
}



function author_box_author_plugin_block_init() {
	register_block_type( __DIR__ . '/build', array(
		'render_callback' => 'create_block_dynamo_render_callback'
	) );
}
add_action( 'init', 'author_box_author_plugin_block_init' );