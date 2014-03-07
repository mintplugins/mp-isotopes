<?php
/**
 * function to get tags by category - used on archive page for isoptope js
 */
if ( ! function_exists( 'mp_isotopes_get_category_tags' ) ):
	function mp_isotopes_get_category_tags($args) {
		global $wpdb;

		if (isset($args['base_archive'])){
			$tags = $wpdb->get_results
			("
				SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, terms2.slug as tag_slug, null as tag_link
				FROM
					". $wpdb->posts . " as p1
					LEFT JOIN ". $wpdb->term_relationships . " as r1 ON p1.ID = r1.object_ID
					LEFT JOIN ". $wpdb->term_taxonomy . " as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
					LEFT JOIN ". $wpdb->terms . " as terms1 ON t1.term_id = terms1.term_id,
		
					" . $wpdb->posts . " as p2
					LEFT JOIN ". $wpdb->term_relationships . " as r2 ON p2.ID = r2.object_ID
					LEFT JOIN ". $wpdb->term_taxonomy . "  as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
					LEFT JOIN ". $wpdb->terms . " as terms2 ON t2.term_id = terms2.term_id
				WHERE
					t1.taxonomy = '". $args['base_taxonomy'] . "' AND p1.post_status = 'publish' AND
					t2.taxonomy = '" . $args['related_taxonomy_items'] ."' AND p2.post_status = 'publish'
					AND p1.ID = p2.ID
				ORDER by tag_name
			");
			$count = 0;
			foreach ($tags as $tag) {
				$tags[$count]->tag_link = get_tag_link($tag->tag_id);
				$count++;
			}
		}else{
			$tags = $wpdb->get_results
			("
				SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, terms2.slug as tag_slug, null as tag_link
				FROM
					" . $wpdb->posts . " as p1
					LEFT JOIN ". $wpdb->term_relationships . " as r1 ON p1.ID = r1.object_ID
					LEFT JOIN ". $wpdb->term_taxonomy . " as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
					LEFT JOIN ". $wpdb->terms . " as terms1 ON t1.term_id = terms1.term_id,
		
					" . $wpdb->posts . " as p2
					LEFT JOIN ". $wpdb->term_relationships . " as r2 ON p2.ID = r2.object_ID
					LEFT JOIN ". $wpdb->term_taxonomy . " as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
					LEFT JOIN ". $wpdb->terms . " as terms2 ON t2.term_id = terms2.term_id
				WHERE
					t1.taxonomy = '". $args['base_taxonomy'] . "' AND p1.post_status = 'publish' AND terms1.term_id IN (".$args['current_taxonomy_item'].") AND
					t2.taxonomy = '" . $args['related_taxonomy_items'] ."' AND p2.post_status = 'publish'
					AND p1.ID = p2.ID
				ORDER by tag_name
			");
			$count = 0;
			foreach ($tags as $tag) {
				$tags[$count]->tag_link = get_tag_link($tag->tag_id);
				$count++;
			}
		}
		return $tags;
	}
endif;

/**
 * Create container div for isotopes around loop items - used on archive page for isoptope js
 */
if ( ! function_exists( 'moveplugins_isotopes_container_div_start' ) ):
	function moveplugins_isotopes_container_div_start() {
		global $mp_isotopes;
		if ($mp_isotopes == true){
			echo '<div class="moveplugins_isotopes_container">';
		}
	}
endif;
add_action( 'loop_start', 'moveplugins_isotopes_container_div_start' );

/**
 * End container div for isotopes around loop items - used on archive page for isoptope js
 */
if ( ! function_exists( 'moveplugins_isotopes_container_div_end' ) ):
	function moveplugins_isotopes_container_div_end() {
		global $mp_isotopes;
		if ($mp_isotopes == true){
			echo '</div>';
			$mp_isotopes = false;
		}
		
	}
endif;
add_action( 'loop_end', 'moveplugins_isotopes_container_div_end' );

/**
 * add tag-slug to the post_class() for this custom post type
 */
if( !function_exists( 'moveplugins_isotopes_custom_taxonomy_post_class' ) ) {

	function moveplugins_isotopes_custom_taxonomy_post_class( $classes, $class, $ID ) {
		
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
add_filter( 'post_class', 'moveplugins_isotopes_custom_taxonomy_post_class', 10, 3 );