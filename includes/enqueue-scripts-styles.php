<?php
/**
 * Enqueue scripts and styles
 */
if ( ! function_exists( 'moveplugins_isotopes_scripts' ) ):
	function moveplugins_isotopes_scripts() {
		if (mp_isotopes_get_plugin_option('enable_css') != 1){
			wp_enqueue_style( 'moveplugins_isotopes_css', plugins_url( '/includes/css/style.css', dirname(__FILE__)));
		}
		wp_enqueue_style( 'moveplugins_isotopes_animation_css', plugins_url( '/includes/css/animation_style.css', dirname(__FILE__)));
		wp_enqueue_script( 'moveplugins_isotopes_isotope', plugins_url( '/js/jquery.isotope.min.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'moveplugins_isotopes_load_isotope', plugins_url( '/js/load_isotope.js', __FILE__ ), array( 'jquery', 'moveplugins_isotopes_isotope' ) );
	}
endif; //moveplugins_isotopes_scripts
add_action( 'wp_enqueue_scripts', 'moveplugins_isotopes_scripts' );
