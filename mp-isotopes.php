<?php
/*
Plugin Name: MP Isotopes
Plugin URI: http://mintplugins.com
Description: This plugin gives you a template tag which you can put on any archive page isotopes functionality 
Version: 1.0.0.3
Author: Mint Plugins
Author URI: http://mintplugins.com
License: GPL2
*/

/*  Copyright 2014  Phil Johnston  (email : phil@mintplugins.com)

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
	define( 'MP_ISOTOPES_VERSION', '1.0.0.2' );

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
function mp_isotopes_include_files(){
	/**
	 * If mp_core isn't active, stop and install it now
	 */
	if (!function_exists('mp_core_textdomain')){
				
		/**
		 * Include Plugin Checker
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . '/includes/plugin-checker/class-plugin-checker.php' );
		
		/**
		 * Include Plugin Installer
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . '/includes/plugin-checker/class-plugin-installer.php' );
		
		/**
		 * Check if wp_core in installed
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . 'includes/plugin-checker/included-plugins/mp-core-check.php' );
		
	}
	/**
	 * Otherwise, if mp_core is active, carry out the plugin's functions
	 */
	else{
		
		/**
		 * Update script - keeps this plugin up to date
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . 'includes/updater/mp-isotopes-update.php' );
		
		/**
		 * Enqueue Scripts
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . 'includes/misc-functions/enqueue-scripts-styles.php' );
		
		/**
		 * Template Tags
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . 'includes/misc-functions/template-tags.php' );
		
		/**
		 * Other Misc Functions
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . 'includes/misc-functions/misc-functions.php' );
		
		/**
		 * Admin Settings API
		 */
		require( MP_ISOTOPES_PLUGIN_DIR . 'includes/misc-functions/admin-settings-api.php' );		
		
	}
}
add_action('plugins_loaded', 'mp_isotopes_include_files', 9);

