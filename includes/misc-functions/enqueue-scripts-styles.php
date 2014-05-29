<?php
/**
 * Enqueue scripts and styles
 */
if ( ! function_exists( 'mintplugins_isotopes_scripts' ) ):
	function mintplugins_isotopes_scripts() {
		
		$enable_css = mp_isotopes_get_plugin_option('enable_css');
		
		if ( $enable_css != 1){
			wp_enqueue_style( 'mintplugins_isotopes_css', plugins_url( '/css/style.css', dirname(__FILE__)));
		}
		wp_enqueue_style( 'mintplugins_isotopes_animation_css', plugins_url( '/css/animation_style.css', dirname(__FILE__)));
		wp_enqueue_script( 'mintplugins_isotopes_isotope', plugins_url( '/js/jquery.isotope.min.js', dirname( __FILE__ ) ), array( 'jquery' ) );
		wp_enqueue_script( 'mintplugins_isotopes_load_isotope', plugins_url( '/js/load_isotope.js', dirname( __FILE__ ) ), array( 'jquery', 'mintplugins_isotopes_isotope' ) );
	}
endif; //mintplugins_isotopes_scripts
add_action( 'wp_enqueue_scripts', 'mintplugins_isotopes_scripts' );
