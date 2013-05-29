<?php
/**
 * Template Tag for isotopes
 */
if ( ! function_exists( 'mp_isotopes' ) ):
	function mp_isotopes(){
		
		global $mp_isotopes;
		$mp_isotopes = true;
			
			$custom_cat = mp_isotopes_get_plugin_option( 'custom_cat' );
			$custom_cat = empty( $custom_cat ) ? 'none' : $custom_cat;
			
			$custom_tag = mp_isotopes_get_plugin_option( 'custom_tag' );
			$custom_tag = empty( $custom_tag ) ? 'none' : $custom_tag;
			
			$custom_post_type = mp_isotopes_get_plugin_option( 'custom_post_type' );
			$custom_post_type = empty( $custom_post_type ) ? 'none' : $custom_post_type;
			
			
			if ( is_category() ) {//normal category pages
				$args = array('base_taxonomy' => 'category','current_taxonomy_item' => get_query_var('cat'), 'related_taxonomy_items' => 'post_tag' );
				$prefix = "tag";
			}
			elseif ( is_tag() ) {//normal tag pages
				$args = array('base_taxonomy' => 'post_tag','current_taxonomy_item' => get_query_var('tag_id'), 'related_taxonomy_items' => 'category' );
				$prefix = "category";
			}
			elseif ( is_tax('download_category') ) {//easy digital downloads category tax page
				$term = get_term_by( 'slug', get_query_var('download_category'), 'download_category' );
				$tax = $term->term_id;
				$args = array('base_taxonomy' => 'download_category','current_taxonomy_item' => $tax, 'related_taxonomy_items' => 'download_tag' );
				$prefix = "tag";
			}
			elseif ( is_tax('download_tag') ) {//easy digital downloads tag tax page
				$term = get_term_by( 'slug', get_query_var('download_tag'), 'download_tag' );
				$tax = $term->term_id;
				$args = array('base_taxonomy' => 'download_category','current_taxonomy_item' => $tax, 'related_taxonomy_items' => 'download_category' );
				$prefix = "category";
			}
			elseif(is_post_type_archive('download')){//easy digital downloads base archive page
				$args = array('base_archive' => 'true', 'base_taxonomy' => 'download_category', 'related_taxonomy_items' => 'download_tag');
				$prefix = "tag";
			}
			elseif ( is_tax('product_cat') ) { //woocommerce category tax page
				$term = get_term_by( 'slug', get_query_var('product_cat'), 'product_cat' );
				$tax = $term->term_id;
				$args = array('base_taxonomy' => 'product_cat','current_taxonomy_item' => $tax, 'related_taxonomy_items' => 'product_tag' );
				$prefix = "tag";
			}
			elseif ( is_tax('product_tag') ) { //woocommerce tag tax page
				$term = get_term_by( 'slug', get_query_var('product_tag'), 'product_tag' );
				$tax = $term->term_id;
				$args = array('base_taxonomy' => 'product_cat','current_taxonomy_item' => $tax, 'related_taxonomy_items' => 'product_cat' );
				$prefix = "category";
			}
			elseif(is_post_type_archive('product')){//woocommerce base archive page
				$args = array('base_archive' => 'true', 'base_taxonomy' => 'product_cat', 'related_taxonomy_items' => 'product_tag');
				$prefix = "tag";
			}
			elseif ( is_tax($custom_cat) ) { //custom category tax page
				$term = get_term_by( 'slug', get_query_var($custom_cat), $custom_cat );
				$tax = $term->term_id;
				$args = array('base_taxonomy' => $custom_cat,'current_taxonomy_item' => $tax, 'related_taxonomy_items' => $custom_tag );
				$prefix = "tag";
			}
			elseif ( is_tax($custom_tag) ) { //custom tag tax page
				$term = get_term_by( 'slug', get_query_var($custom_tag), $custom_tag );
				$tax = $term->term_id;
				$args = array('base_taxonomy' => $custom_cat,'current_taxonomy_item' => $tax, 'related_taxonomy_items' => $custom_cat );
				$prefix = "category";
			}
			elseif(is_post_type_archive($custom_post_type)){//custom base archive page
				$args = array('base_archive' => 'true', 'base_taxonomy' => $custom_cat, 'related_taxonomy_items' => $custom_tag);
				$prefix = "tag";
			}
			elseif( is_post_type_archive('post') ){ //base archive
					$args = array('base_archive' => 'true', 'base_taxonomy' => 'category', 'related_taxonomy_items' => 'post_tag');
					$prefix = "tag";
			}
			else{
				return;
			}
			
			$tags = mp_isotopes_get_category_tags($args);
			
			if (mp_isotopes_get_plugin_option( 'dropdown_vs_links' ) == 0){
				//list of links
				echo '<ul data-option-key="filter" class="isotopenav">';
					if (!empty($tags)){
						echo '<li><a href="#filter" valuemint="*">All</a></li>';
					}
					foreach($tags as $tag){
								echo ('<li><a href="#filter" valuemint=".' . $prefix .'-' . strtolower(str_replace (" ", "-", $tag->tag_slug)) . '">' . $tag->tag_name . '</a></li>');	
					}
				echo '</ul>';
			}else{
				//dropdown menu
				echo '<select id="size" name="filter by" class="isotopenav">';
				echo ('<option value="">View by...</option>');
				echo ('<option value="*' . strtolower(str_replace (" ", "-", $tag->tag_slug)) . '">' . 'All' . '</option>');
				foreach($tags as $tag){
					echo ('<option value=".tag-' . strtolower(str_replace (" ", "-", $tag->tag_slug)) . '">' . $tag->tag_name . '</option>');
				}
				echo '</select>';
			}
	}
endif; //mp_isotopes

/**
 * Backwards compatibility for moveplugins_isotopes
 */
if ( ! function_exists( 'moveplugins_isotopes' ) ):
	function moveplugins_isotopes(){
		mp_isotopes();
	}
endif; //moveplugins_isotopes

/**
 * Backwards compatibility for mintthemes_isotopes
 */
if ( ! function_exists( 'mintthemes_isotopes' ) ):
	function mintthemes_isotopes(){
		mp_isotopes();
	}
endif; //mintthemes_isotopes