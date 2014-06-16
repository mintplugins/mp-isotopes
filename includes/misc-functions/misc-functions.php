<?php

/**
 * Create container div for isotopes around loop items - used on archive page for isoptope js
 */
if ( ! function_exists( 'mintplugins_isotopes_container_div_start' ) ):
	function mintplugins_isotopes_container_div_start() {
		global $mp_isotopes;
		if ($mp_isotopes == true){
			echo '<div class="mp_isotopes_container isotope">';
		}
	}
endif;
add_action( 'loop_start', 'mintplugins_isotopes_container_div_start' );

/**
 * End container div for isotopes around loop items - used on archive page for isoptope js
 */
if ( ! function_exists( 'mintplugins_isotopes_container_div_end' ) ):
	function mintplugins_isotopes_container_div_end() {
		global $mp_isotopes;
		if ($mp_isotopes == true){
			echo '</div>';
			$mp_isotopes = false;
		}
		
	}
endif;
add_action( 'loop_end', 'mintplugins_isotopes_container_div_end' );

/**
 * add tag-slug to the post_class() for this custom post type
 */
if( !function_exists( 'mintplugins_isotopes_custom_taxonomy_post_class' ) ) {

	function mintplugins_isotopes_custom_taxonomy_post_class( $classes, $class, $ID ) {
		
		global $mp_isotopes, $post, $mp_isotopes_taxonomy_slugs;
		
		foreach( $mp_isotopes_taxonomy_slugs as $taxonomy_slug ){
			
			$terms = get_the_terms( (int) $ID, $taxonomy_slug );
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "tag-" . $term->slug;
					}
				}
			}
				
		}
		
		if (is_tax('download_category') || is_post_type_archive('download')){
			$terms = get_the_terms( (int) $ID, 'download_tag' );//edd tags
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "tag-" . $term->slug;
					}
				}
			}
		}
		
		if (is_tax('download_tag')){
			$terms = get_the_terms( (int) $ID, 'download_category' );//edd categories
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "category-" . $term->slug;
					}
				}
			}
		}
		
		if (is_tax('product_cat') || is_post_type_archive('product')){
			$terms = get_the_terms( (int) $ID, 'product_tag' );//woocommerce tags
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "tag-" . $term->slug;
					}
				}
			}
		}
		
		if (is_tax('product_tag')){
			$terms = get_the_terms( (int) $ID, 'product_cat' );//woocommerce cat
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "category-" . $term->slug;
					}
				}
			}
		}
		
		if (is_tax(mp_isotopes_get_plugin_option( 'custom_cat' )) || is_post_type_archive(mp_isotopes_get_plugin_option( 'custom_post_type' ))){
			$terms = get_the_terms( (int) $ID, mp_isotopes_get_plugin_option( 'custom_tag' ) );//custom tags
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "tag-" . $term->slug;
					}
				}
			}
		}
		
		if (is_tax(mp_isotopes_get_plugin_option( 'custom_tag' ))){
			$terms = get_the_terms( (int) $ID, mp_isotopes_get_plugin_option( 'custom_cat' ) );//custom cat
	
			if( !empty( $terms ) ) {
				foreach( (array) $terms as $order => $term ) {
					if( !in_array( $term->slug, $classes ) ) {
						$classes[] = "category-" . $term->slug;
					}
				}
			}
		}

		return $classes;

	}
} 
add_filter( 'post_class', 'mintplugins_isotopes_custom_taxonomy_post_class', 10, 3 );