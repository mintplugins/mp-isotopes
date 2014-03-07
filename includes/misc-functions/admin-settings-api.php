<?php
/**
 * Admin Page and options
 */ 

function moveplugins_isotopes_plugin_options_init() {
	register_setting(
		'moveplugins_isotopes_options',
		'moveplugins_isotopes_options',
		'moveplugins_isotopes_plugin_options_validate'
	);
	//
	add_settings_section(
		'settings',
		__( 'Settings', 'moveplugins_isotopes' ),
		'__return_false',
		'moveplugins_isotopes_options'
	);
	

	add_settings_field(
		'dropdown_vs_links',
		__( 'Dropdown Menu vs List of Links', 'moveplugins_isotopes' ), 
		'moveplugins_isotopes_settings_field_select',
		'moveplugins_isotopes_options',
		'settings',
		array(
			'name'        => 'dropdown_vs_links',
			'value'       => mp_isotopes_get_plugin_option( 'dropdown_vs_links' ),
			'options'     => array('List of Links', 'Dropdown Menu'),
			'description' => __( 'Do you want isotopes to use a dropdown menu or a list of links?', 'moveplugins_isotopes' )
		)
	);
	add_settings_field(
		'enable_css',
		__( 'Use built-in CSS?', 'moveplugins_isotopes' ), 
		'moveplugins_isotopes_settings_field_select',
		'moveplugins_isotopes_options',
		'settings',
		array(
			'name'        => 'enable_css',
			'value'       => mp_isotopes_get_plugin_option( 'enable_css' ),
			'options'     => array('Yes, use the built in CSS.','No. I\'ll use my own CSS.'),
			'description' => __( 'Do you want use the built in CSS for isotopes?', 'moveplugins_isotopes' )
		)
	);
	
	add_settings_section(
		'custom',
		__( 'Custom Post Type (Optional).', 'moveplugins_isotopes' ),
		'__return_false',
		'moveplugins_isotopes_options'
	);
	
	add_settings_field(
		'custom_post_type',
		__( 'Enter your custom post type slug here:', 'moveplugins_isotopes' ), 
		'moveplugins_isotopes_settings_field_textbox',
		'moveplugins_isotopes_options',
		'custom',
		array(
			'name'        => 'custom_post_type',
			'value'       => mp_isotopes_get_plugin_option( 'custom_post_type' ),
			'description' => __( 'Enter the slug for the custom post type (EG: custom)', 'moveplugins_isotopes' )
		)
	);
	
	add_settings_field(
		'custom_cat',
		__( 'The custom category slug.', 'moveplugins_isotopes' ), 
		'moveplugins_isotopes_settings_field_textbox',
		'moveplugins_isotopes_options',
		'custom',
		array(
			'name'        => 'custom_cat',
			'value'       => mp_isotopes_get_plugin_option( 'custom_cat' ),
			'description' => __( 'Enter the slug for the custom category taxonomy (EG: custom_cat)', 'moveplugins_isotopes' )
		)
	);
	
	add_settings_field(
		'custom_tag',
		__( 'The custom tag slug.', 'moveplugins_isotopes' ), 
		'moveplugins_isotopes_settings_field_textbox',
		'moveplugins_isotopes_options',
		'custom',
		array(
			'name'        => 'custom_tag',
			'value'       => mp_isotopes_get_plugin_option( 'custom_tag' ),
			'description' => __( 'Enter the slug for the custom tag taxonomy (EG: custom_tag)', 'moveplugins_isotopes' )
		)
	);
	

	
}
add_action( 'admin_init', 'moveplugins_isotopes_plugin_options_init' );

/**
 * Change the capability required to save the 'moveplugins_isotopes_options' options group.
 *
 * @see moveplugins_isotopes_plugin_options_init() First parameter to register_setting() is the name of the options group.
 * @see moveplugins_isotopes_plugin_options_add_page() The manage_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function moveplugins_isotopes_option_page_capability( $capability ) {
	return 'manage_options';
}
add_filter( 'option_page_capability_moveplugins_isotopes_options', 'moveplugins_isotopes_option_page_capability' );

/**
 * Add our plugin options page to the admin menu.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Isotopes by Tag 1.0
 */
function moveplugins_isotopes_plugin_options_add_page() {
	 add_options_page(
		__( 'Isotopes Settings', 'moveplugins_isotopes' ),
		__( 'Isotopes Settings', 'moveplugins_isotopes' ),
		'manage_options',
		'moveplugins_isotopes_options',
		'moveplugins_isotopes_plugin_options_render_page'
	);
	
}
add_action( 'admin_menu', 'moveplugins_isotopes_plugin_options_add_page' );

/**
 * Returns the options array for Isotopes by Tag.
 *
 * @since Isotopes by Tag 1.0
 */
function mp_isotopes_get_plugin_options() {
	$saved = (array) get_option( 'moveplugins_isotopes_options' );
	
	$defaults = array(
		'enable_css' 	=> '',
		'dropdown_vs_links' 	=> '',
		'custom_post_type' 	=> '',
		'custom_cat' 	=> '',
		'custom_tag' 	=> '',
	);

	$defaults = apply_filters( 'moveplugins_isotopes_default_plugin_options', $defaults );

	$options = wp_parse_args( $saved, $defaults );
	$options = array_intersect_key( $options, $defaults );

	return $options;
}

/**
 * Get a single plugin option
 *
 * @since Isotopes by Tag 1.0
 */
function mp_isotopes_get_plugin_option( $key ) {
	$options = mp_isotopes_get_plugin_options();
	
	if ( isset( $options[ $key ] ) )
		return $options[ $key ];
		
	return false;
}

/**
 * Renders the Theme Options administration screen.
 *
 * @since Isotopes by Tag 1.0
 */
function moveplugins_isotopes_plugin_options_render_page() {
	
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( 'Isotopes Options', 'moveplugins_isotopes' ), 'moveplugins_isotopes' ); ?></h2>

		<form action="options.php" method="post">
			<?php
				settings_fields( 'moveplugins_isotopes_options' );
				do_settings_sections( 'moveplugins_isotopes_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see moveplugins_isotopes_plugin_options_init()
 * @todo set up Reset Options action
 *
 * @param array $input Unknown values.
 * @return array Sanitized plugin options ready to be stored in the database.
 *
 * @since Isotopes by Tag 1.0
 */
function moveplugins_isotopes_plugin_options_validate( $input ) {
	$output = array();
	
	
	
	if ( $input[ 'dropdown_vs_links' ] == 0 || array_key_exists( $input[ 'dropdown_vs_links' ], moveplugins_isotopes_get_categories() ) )
		$output[ 'dropdown_vs_links' ] = $input[ 'dropdown_vs_links' ];
		
	if ( $input[ 'enable_css' ] == 0 || array_key_exists( $input[ 'enable_css' ], moveplugins_isotopes_get_categories() ) )
		$output[ 'enable_css' ] = $input[ 'enable_css' ];
		
	if ( isset ( $input[ 'custom_post_type' ] ) )
		$output[ 'custom_post_type' ] = esc_attr( $input[ 'custom_post_type' ] );
	
	if ( isset ( $input[ 'custom_cat' ] ) )
		$output[ 'custom_cat' ] = esc_attr( $input[ 'custom_cat' ] );
	
	if ( isset ( $input[ 'custom_tag' ] ) )
		$output[ 'custom_tag' ] = esc_attr( $input[ 'custom_tag' ] );
		
		
	
	$output = wp_parse_args( $output, mp_isotopes_get_plugin_options() );	
		
	return apply_filters( 'moveplugins_isotopes_plugin_options_validate', $output, $input );
}

/* Fields ***************************************************************/
 
/**
 * Number Field
 *
 * @since Isotopes by Tag 1.0
 */
function moveplugins_isotopes_settings_field_number( $args = array() ) {
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
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
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
function moveplugins_isotopes_settings_field_textarea( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
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
function moveplugins_isotopes_settings_field_image_upload( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>">
        <input id="upload_image_button" type="button" value="<?php echo __( 'Upload Image', 'moveplugins_isotopes' ); ?>" />
		<br /><?php echo $description; ?>
	</label>
<?php
} 

/**
 * Textbox Field
 *
 * @since Isotopes by Tag 1.0
 */
function moveplugins_isotopes_settings_field_textbox( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
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
function moveplugins_isotopes_settings_field_checkbox_single( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'compare'     => 'on',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
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
function moveplugins_isotopes_settings_field_radio( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'options'     => array(),
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
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
function moveplugins_isotopes_settings_field_select( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'options'     => array(),
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'moveplugins_isotopes_options[%s]', $name ) );
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

function moveplugins_isotopes_get_categories() {
	$output = array();
	$terms  = get_terms( array( 'category' ), array( 'hide_empty' => 0 ) );
	
	foreach ( $terms as $term ) {
		$output[ $term->term_id ] = $term->name;
	}
	
	return $output;
}
