<?php
/*
Plugin Name: MP Isotopes
Plugin URI: http://moveplugins.com
Description: This plugin gives you a template tag which you can put on any archive page isotopes functionality 
Version: beta1.0.0.8
Author: Move Plugins
Author URI: http://moveplugins.com
License: GPL2
*/

/*  Copyright 2012  Phil Johnston  (email : phil@moveplugins.com)

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
if ( function_exists( 'mp_isotopes' ) ): 
	mp_isotopes(); 
endif;
*/

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/
// Plugin version
if( !defined( 'MP_ISOTOPES_VERSION' ) )
	define( 'MP_ISOTOPES_VERSION', '1.0.0.0' );

// Plugin Folder URL
if( !defined( 'MP_ISOTOPES_PLUGIN_URL' ) )
	define( 'MP_ISOTOPES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Plugin Folder Path
if( !defined( 'MP_ISOTOPES_PLUGIN_DIR' ) )
	define( 'MP_ISOTOPES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin Root File
if( !defined( 'MP_ISOTOPES_PLUGIN_FILE' ) )
	define( 'MP_ISOTOPES_PLUGIN_FILE', __FILE__ );

/*
|--------------------------------------------------------------------------
| GLOBALS
|--------------------------------------------------------------------------
*/

//None at the moment

/*
|--------------------------------------------------------------------------
| INTERNATIONALIZATION
|--------------------------------------------------------------------------
*/

function mp_isotopes_textdomain() {

	// Set filter for plugin's languages directory
	$mp_isotopes_lang_dir = dirname( plugin_basename( MP_ISOTOPES_PLUGIN_FILE ) ) . '/languages/';
	$mp_isotopes_lang_dir = apply_filters( 'mp_isotopes_languages_directory', $mp_isotopes_lang_dir );


	// Traditional WordPress plugin locale filter
	$locale        = apply_filters( 'plugin_locale',  get_locale(), 'mp_isotopes' );
	$mofile        = sprintf( '%1$s-%2$s.mo', 'mp_isotopes', $locale );

	// Setup paths to current locale file
	$mofile_local  = $mp_isotopes_lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/mp-isotopes/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/isotopes folder
		load_textdomain( 'mp_isotopes', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/message_bar/languages/ folder
		load_textdomain( 'mp_isotopes', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'mp_isotopes', false, $mp_isotopes_lang_dir );
	}

}
add_action( 'init', 'mp_isotopes_textdomain', 1 );

/*
|--------------------------------------------------------------------------
| INCLUDES
|--------------------------------------------------------------------------
*/

include_once( MP_ISOTOPES_PLUGIN_DIR . 'includes/enqueue-scripts-styles.php' );
include_once( MP_ISOTOPES_PLUGIN_DIR . 'includes/template-tags.php' );
include_once( MP_ISOTOPES_PLUGIN_DIR . 'includes/misc-functions.php' );
include_once( MP_ISOTOPES_PLUGIN_DIR . 'includes/custom-hooks.php' );
include_once( MP_ISOTOPES_PLUGIN_DIR . 'includes/admin-settings-api.php' );
if( is_admin() ) {
	//none at the moment
} else {
	//none at the moment
}


