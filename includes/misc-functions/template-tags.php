<?php
/**
 * Template Tag for isotopes
 */
if ( ! function_exists( 'mp_isotopes' ) ):
	function mp_isotopes( $taxonomy_slug = NULL, $echo = true ){
		
		global $mp_isotopes, $post, $mp_isotopes_taxonomy_slugs;
		
		//Set default for $mp_isotopes_taxonomy_slugs
		if ( empty($mp_isotopes_taxonomy_slugs) ){
			$mp_isotopes_taxonomy_slugs = array();
		}
		
		//Set default for page tags
		$page_tags = array();
		
		//Loop through the posts in the loop
		if ( have_posts() ){

			/* Start the Loop */
			while ( have_posts() ) : the_post();
				
				if ( empty( $taxonomy_slug ) ){
					
					if ( $post->post_type == 'download' ){
						$taxonomy_slug = 'download_tag';
					}
					else if ( $post->post_type == 'product' ){
						$taxonomy_slug = 'product_tag';
					}
					else if ( $post->post_type == 'post' ){
						$taxonomy_slug = 'post_tag';
					}
					else if ( $post->post_type == mp_isotopes_get_plugin_option( 'custom_post_type' ) ){
						$taxonomy_slug = mp_isotopes_get_plugin_option( 'custom_tag' );
					}
				
				}
				
				//Get all tags for this post
				$post_tags = wp_get_post_terms( get_the_ID(), $taxonomy_slug );
				
				//Store this tag in the array of tags to show
				foreach( $post_tags as $post_tag ){
					
					$page_tags[$post_tag->slug] = $post_tag;	
					
				}
			
			endwhile; 
            
        }
		
		//reset the loop so we can use it on the archive page
		rewind_posts();
		
		//Set the global mp_isotopes variable to true so the loop knows to export the container
		$mp_isotopes = true;
		
		$prefix = 'tag';
		
		//If there are no tags, get outta here!
		if ( empty( $page_tags ) ){
			return NULL;	
		}
		
		$html_output = NULL;
		
		if (mp_isotopes_get_plugin_option( 'dropdown_vs_links' ) == 0){
			//list of links
			$html_output .= '<ul data-option-key="filter" class="isotopenav">';
				if (!empty($page_tags)){
					$html_output .= '<li><a class="button" href="#filter" valuemint="*">All</a></li>';
				}
				foreach($page_tags as $tag){
							$html_output .= ('<li><a class="button" href="#filter" valuemint=".' . $prefix .'-' . strtolower(str_replace (" ", "-", $tag->slug)) . '">' . $tag->name . '</a></li>');	
				}
			$html_output .= '</ul>';
		}else{
			//dropdown menu
			$html_output .= '<select id="size" name="filter by" class="isotopenav">';
			$html_output .= ('<option value="">View by...</option>');
			$html_output .= ('<option value="*">' . 'All' . '</option>');
			foreach($page_tags as $tag){
				$html_output .= ('<option value=".' . $prefix . '-' . strtolower(str_replace (" ", "-", $tag->slug)) . '">' . $tag->name . '</option>');
			}
			$html_output .= '</select>';
		}
		
		//Store the taxonomy slug in the global array of all taxonomy slugs
		array_push( $mp_isotopes_taxonomy_slugs, $taxonomy_slug );
		
		//If we should echo this
		if ( $echo ){
			echo $html_output;
		}
		//If we should return
		else{
			return $html_output;	
		}
	
	}
endif; //mp_isotopes

/**
 * Backwards compatibility for mintplugins_isotopes
 */
if ( ! function_exists( 'mintplugins_isotopes' ) ):
	function mintplugins_isotopes( $taxonomy_slug = NULL ){
		mp_isotopes( $taxonomy_slug );
	}
endif; //mintplugins_isotopes

/**
 * Backwards compatibility for mintthemes_isotopes
 */
if ( ! function_exists( 'mintthemes_isotopes' ) ):
	function mintthemes_isotopes( $taxonomy_slug = NULL ){
		mp_isotopes( $taxonomy_slug );
	}
endif; //mintthemes_isotopes