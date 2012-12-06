<?php
/*
Plugin Name: Isotopes
Plugin URI: http://mintthemes.com
Description: This plugin gives you a template tag which you can put on any archive page isotopes functionality 
Version: 1.0
Author: Phil Johnston
Author URI: http://mintthemes.com
License: GPL2
*/

/*  Copyright 2012  Phil Johnston  (email : phil@mintthemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* To use this function put the following on any archive page
if ( function_exists( 'mintthemes_isotopes' ) ): 
	mintthemes_isotopes(); 
endif;
*/


/**
 * Enqueue scripts and styles
 */
if ( ! function_exists( 'mintthemes_isotopes_scripts' ) ):
	function mintthemes_isotopes_scripts() {
		if (mintthemes_isotopes_get_plugin_option('enable_css') != 1){
			wp_enqueue_style( 'mintthemes_isotopes_css', plugins_url() . '/isotopes/css/style.css' );
		}
		wp_enqueue_style( 'mintthemes_isotopes_animation_css', plugins_url() . '/isotopes/css/animation_style.css' );
		wp_enqueue_script( 'mintthemes_isotopes_isotope', plugins_url( '/js/jquery.isotope.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'mintthemes_isotopes_load_isotope', plugins_url( '/js/load_isotope.js', __FILE__ ), array( 'jquery' ) );
	}
endif; //mintthemes_isotopes_scripts
add_action( 'wp_enqueue_scripts', 'mintthemes_isotopes_scripts' );

/**
 * Template Tag for isotopes
 */
if ( ! function_exists( 'mintthemes_isotopes' ) ):
	function mintthemes_isotopes(){
			
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
			else{ //base archive
					$args = array('base_archive' => 'true', 'base_taxonomy' => 'category', 'related_taxonomy_items' => 'post_tag');
					$prefix = "tag";
			}
			
			$tags = mintthemes_isotopes_get_category_tags($args);
			
			if (mintthemes_isotopes_get_plugin_option( 'dropdown_vs_links' ) == 0){
				//list of links
				echo '<ul data-option-key="filter" class="isotopenav">';
					echo '<li><a href="#filter" valuemint="*">All</a></li>';
					foreach($tags as $tag){
								echo ('<li><a href="#filter" valuemint=".' . $prefix .'-' . strtolower(str_replace (" ", "-", $tag->tag_name)) . '">' . $tag->tag_name . '</a></li>');	
					}
				echo '</ul>';
			}else{
				//dropdown menu
				echo '<select id="size" name="filter by" class="isotopenav">';
				echo ('<option value="">View by...</option>');
				echo ('<option value="*' . strtolower(str_replace (" ", "-", $tag->tag_name)) . '">' . 'All' . '</option>');
				foreach($tags as $tag){
					echo ('<option value=".tag-' . strtolower(str_replace (" ", "-", $tag->tag_name)) . '">' . $tag->tag_name . '</option>');
				}
				echo '</select>';
			}
	}
endif; //mintthemes_isotopes


/**
 * function to get tags by category - used on archive page for isoptope js
 */
if ( ! function_exists( 'mintthemes_isotopes_get_category_tags' ) ):
	function mintthemes_isotopes_get_category_tags($args) {
		global $wpdb;
		if (isset($args['base_archive'])){
			$tags = $wpdb->get_results
			("
				SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, null as tag_link
				FROM
					wp_posts as p1
					LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
					LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
					LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,
		
					wp_posts as p2
					LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
					LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
					LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
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
				SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, null as tag_link
				FROM
					wp_posts as p1
					LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
					LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
					LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,
		
					wp_posts as p2
					LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
					LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
					LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
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
if ( ! function_exists( 'mintthemes_isotopes_container_div_start' ) ):
	function mintthemes_isotopes_container_div_start() {
		if (is_category() || is_tax() || is_archive() || is_tax('download_category') || is_tax('download_tag') || is_tax('product_cat') || is_tax('product_tag') || is_home()){
			echo '<div class="mintthemes_isotopes_container">';
		}
	}
endif;
add_action( 'loop_start', 'mintthemes_isotopes_container_div_start' );

/**
 * End container div for isotopes around loop items - used on archive page for isoptope js
 */
if ( ! function_exists( 'mintthemes_isotopes_container_div_end' ) ):
	function mintthemes_isotopes_container_div_end() {
		if (is_category() || is_tax() || is_archive() || is_tax('download_category') || is_tax('download_tag') || is_tax('product_cat') || is_tax('product_tag') || is_home() ){
			echo '</div>';
		}
		
	}
endif;
add_action( 'loop_end', 'mintthemes_isotopes_container_div_end' );

/**
 * add tag-slug to the post_class() for this custom post type
 */
if( !function_exists( 'mintthemes_isotopes_custom_taxonomy_post_class' ) ) {

	function mintthemes_isotopes_custom_taxonomy_post_class( $classes, $class, $ID ) {
		
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

		return $classes;

	}
} 
add_filter( 'post_class', 'mintthemes_isotopes_custom_taxonomy_post_class', 10, 3 );

/**
 * Admin Page and options
 */ 

function mintthemes_isotopes_plugin_options_init() {
	register_setting(
		'mintthemes_isotopes_options',
		'mintthemes_isotopes_options',
		'mintthemes_isotopes_plugin_options_validate'
	);
	//
	add_settings_section(
		'settings',
		__( 'Settings', 'mintthemes_isotopes' ),
		'__return_false',
		'mintthemes_isotopes_options'
	);
	

	add_settings_field(
		'dropdown_vs_links',
		__( 'Dropdown Menu vs List of Links', 'mintthemes_isotopes' ), 
		'mintthemes_isotopes_settings_field_select',
		'mintthemes_isotopes_options',
		'settings',
		array(
			'name'        => 'dropdown_vs_links',
			'value'       => mintthemes_isotopes_get_plugin_option( 'dropdown_vs_links' ),
			'options'     => array('List of Links', 'Dropdown Menu'),
			'description' => __( 'Do you want isotopes to use a dropdown menu or a list of links?', 'mintthemes_isotopes' )
		)
	);
	add_settings_field(
		'enable_css',
		__( 'Use built-in CSS?', 'mintthemes_isotopes' ), 
		'mintthemes_isotopes_settings_field_select',
		'mintthemes_isotopes_options',
		'settings',
		array(
			'name'        => 'enable_css',
			'value'       => mintthemes_isotopes_get_plugin_option( 'enable_css' ),
			'options'     => array('Yes, use the built in CSS.','No. I\'ll use my own CSS.'),
			'description' => __( 'Do you want use the built in CSS for isotopes?', 'mintthemes_isotopes' )
		)
	);
	

	
}
add_action( 'admin_init', 'mintthemes_isotopes_plugin_options_init' );

/**
 * Change the capability required to save the 'mintthemes_isotopes_options' options group.
 *
 * @see mintthemes_isotopes_plugin_options_init() First parameter to register_setting() is the name of the options group.
 * @see mintthemes_isotopes_plugin_options_add_page() The manage_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function mintthemes_isotopes_option_page_capability( $capability ) {
	return 'manage_options';
}
add_filter( 'option_page_capability_mintthemes_isotopes_options', 'mintthemes_isotopes_option_page_capability' );

/**
 * Add our plugin options page to the admin menu.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_plugin_options_add_page() {
	 add_options_page(
		__( 'Isotopes Settings', 'mintthemes_isotopes' ),
		__( 'Isotopes Settings', 'mintthemes_isotopes' ),
		'manage_options',
		'mintthemes_isotopes_options',
		'mintthemes_isotopes_plugin_options_render_page'
	);
	
}
add_action( 'admin_menu', 'mintthemes_isotopes_plugin_options_add_page' );

/**
 * Returns the options array for Isotopes by Tag.
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_get_plugin_options() {
	$saved = (array) get_option( 'mintthemes_isotopes_options' );
	
	$defaults = array(
		'enable_css' 	=> '',
		'dropdown_vs_links' 	=> '',
	);

	$defaults = apply_filters( 'mintthemes_isotopes_default_plugin_options', $defaults );

	$options = wp_parse_args( $saved, $defaults );
	$options = array_intersect_key( $options, $defaults );

	return $options;
}

/**
 * Get a single plugin option
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_get_plugin_option( $key ) {
	$options = mintthemes_isotopes_get_plugin_options();
	
	if ( isset( $options[ $key ] ) )
		return $options[ $key ];
		
	return false;
}

/**
 * Renders the Theme Options administration screen.
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_plugin_options_render_page() {
	
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( 'Isotopes by Tag Options', 'mintthemes_isotopes' ), 'mintthemes_isotopes' ); ?></h2>
		<?php settings_errors(); ?>

		<form action="options.php" method="post">
			<?php
				settings_fields( 'mintthemes_isotopes_options' );
				do_settings_sections( 'mintthemes_isotopes_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see mintthemes_isotopes_plugin_options_init()
 * @todo set up Reset Options action
 *
 * @param array $input Unknown values.
 * @return array Sanitized plugin options ready to be stored in the database.
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_plugin_options_validate( $input ) {
	$output = array();
	
	
	
	if ( $input[ 'dropdown_vs_links' ] == 0 || array_key_exists( $input[ 'dropdown_vs_links' ], mintthemes_isotopes_get_categories() ) )
		$output[ 'dropdown_vs_links' ] = $input[ 'dropdown_vs_links' ];
		
	if ( $input[ 'enable_css' ] == 0 || array_key_exists( $input[ 'enable_css' ], mintthemes_isotopes_get_categories() ) )
		$output[ 'enable_css' ] = $input[ 'enable_css' ];
		
		
	
	$output = wp_parse_args( $output, mintthemes_isotopes_get_plugin_options() );	
		
	return apply_filters( 'mintthemes_isotopes_plugin_options_validate', $output, $input );
}

/* Fields ***************************************************************/
 
/**
 * Number Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_number( $args = array() ) {
	$defaults = array(
		'menu'        => '', 
		'min'         => 1,
		'max'         => 100,
		'step'        => 1,
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo esc_attr( $id ); ?>">
		<input type="number" min="<?php echo absint( $min ); ?>" max="<?php echo absint( $max ); ?>" step="<?php echo absint( $step ); ?>" name="<?php echo $name; ?>" id="<?php echo $id ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php echo $description; ?>
	</label>
<?php
} 

/**
 * Textarea Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_textarea( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="code large-text" rows="3" cols="30"><?php echo esc_textarea( $value ); ?></textarea>
		<br />
		<?php echo $description; ?>
	</label>
<?php
} 

/**
 * Image Upload Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_image_upload( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>">
        <input id="upload_image_button" type="button" value="<?php echo __( 'Upload Image', 'mintthemes_isotopes' ); ?>" />
		<br /><?php echo $description; ?>
	</label>
<?php
} 

/**
 * Textbox Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_textbox( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>">
		<br /><?php echo $description; ?>
	</label>
<?php
} 

/**
 * Single Checkbox Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_checkbox_single( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'compare'     => 'on',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo esc_attr( $id ); ?>">
		<input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $compare, $value ); ?>>
		<?php echo $description; ?>
	</label>
<?php
} 

/**
 * Radio Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_radio( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'options'     => array(),
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<?php foreach ( $options as $option_id => $option_label ) : ?>
	<label title="<?php echo esc_attr( $option_label ); ?>">
		<input type="radio" name="<?php echo $name; ?>" value="<?php echo $option_id; ?>" <?php checked( $option_id, $value ); ?>>
		<?php echo esc_attr( $option_label ); ?>
	</label>
		<br />
	<?php endforeach; ?>
<?php
}

/**
 * Select Field
 *
 * @since Isotopes by Tag 1.0
 */
function mintthemes_isotopes_settings_field_select( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'options'     => array(),
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<select name="<?php echo $name; ?>">
			<?php foreach ( $options as $option_id => $option_label ) : ?>
			<option value="<?php echo esc_attr( $option_id ); ?>" <?php selected( $option_id, $value ); ?>>
				<?php echo esc_attr( $option_label ); ?>
			</option>
			<?php endforeach; ?>
		</select>
		<?php echo $description; ?>
	</label>
<?php
}

/* Helpers ***************************************************************/

function mintthemes_isotopes_get_categories() {
	$output = array();
	$terms  = get_terms( array( 'category' ), array( 'hide_empty' => 0 ) );
	
	foreach ( $terms as $term ) {
		$output[ $term->term_id ] = $term->name;
	}
	
	return $output;
}
