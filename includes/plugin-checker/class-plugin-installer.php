<?php
/**
 * This file contains the MP_CORE_Plugin_Installer class
 *
 * @link http://mintplugins.com/doc/plugin-installer-class/
 * @since 1.0.0
 *
 * @package    MP Core
 * @subpackage Classes
 *
 * @copyright  Copyright (c) 2014, Mint Plugins
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @author     Philip Johnston
 */
 
/**
 * Plugin Installer Class for the mp_core Plugin by Mint Plugins
 *
 * @author     Philip Johnston
 * @link       http://mintplugins.com/doc/plugin-installer-class/
 * @since      1.0.0
 * @return     void
 */
if ( !class_exists( 'MP_CORE_Plugin_Installer' ) ){
	class MP_CORE_Plugin_Installer{
		
		/**
		 * Constructor
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Installer::mp_core_install_plugin_page()
		 * @see      MP_CORE_Plugin_Installer::mp_core_install_plugin()
		 * @see      wp_parse_args()
		 * @param    array $args {
		 *      This array holds information the plugin
		 *		@type string 'plugin_name' Name of plugin.
		 *		@type string 'plugin_message' Message which shows up in notification for plugin.
		 *		@type string 'plugin_filename' Name of plugin's main file
		 * 		@type bool   'plugin_required' Whether or not this plugin is required
		 *		@type string 'plugin_download_link' Link to URL where this plugin's zip file can be downloaded
		 *		@type bool   'plugin_group_install' Whether to create the singular install page for this plugin or not
		 *		@type bool   'plugin_license' The license this plugin requires to be downloaded
		 *		@type bool   'plugin_success_link' Where to re-direct the user upon a sucessful install. No redirect if NULL
		 * }
		 * @return   void
		 */
		public function __construct($args){	
					
			//Set defaults for args		
			$defaults = array(
				'plugin_name' => NULL,  
				'plugin_message' => NULL, 
				'plugin_filename' => NULL,
				'plugin_required' => NULL,
				'plugin_download_link' => NULL,
				'plugin_group_install' => NULL,
				'plugin_license' => NULL,
				'plugin_success_link' => NULL,
				'plugin_is_theme' => false
			);
						
			//Get and parse args
			$this->_args = wp_parse_args( $args, $defaults );
												
			//Plugin Name Slug
			$this->plugin_name_slug = sanitize_title ( $this->_args['plugin_name'] ); //EG move-plugins-core	
			
			// Create update/install plugin page
			add_action('admin_menu', array( $this, 'mp_core_install_plugin_page') );
			
			//If this plugin is part of a group install
			if ( $this->_args['plugin_group_install'] ){
				
				//Install plugin
				$this->mp_core_install_plugin();
				
			}	
											
		}
	
		/**
		 * Create mp core install plugin page
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      get_plugin_page_hookname()
		 * @see      add_action()
	 	 * @return   void
		 */
		public function mp_core_install_plugin_page()
		{
			
			// This WordPress variable is essential: it stores which admin pages are registered to WordPress
			global $_registered_pages;
		
			// Get the name of the hook for this plugin
			// We use "options-general.php" as the parent as we want our page to appear under "options-general.php?page=mp_core_install_plugin_page" .  $this->plugin_name_slug
			$hookname = get_plugin_page_hookname('mp_core_install_plugin_page_' .  $this->plugin_name_slug, 'options-general.php');
		
			// Add the callback via the action on $hookname, so the callback function is called when the page "options-general.php?page=mp_core_install_plugin_page" .  $this->plugin_name_slug is loaded
			if (!empty($hookname)) {
				add_action($hookname, array( $this, 'mp_core_install_check_callback') );
			}
		
			// Add this page to the registered pages
			$_registered_pages[$hookname] = true;
		}
		
		/**
		 * Callback function for the update plugin page above.
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      screen_icon()
		 * @see      MP_CORE_Plugin_Installer::mp_core_install_plugin()
	 	 * @return   void
		 */
		public function mp_core_install_check_callback() {
			
			echo '<div class="wrap">';
			
			screen_icon();
						
			echo '<h2>' . __('Install ', 'mp_core') . $this->_args['plugin_name'] . '</h2>';
			
			//Install plugin
			$this->mp_core_install_plugin();
			
			echo '</div>';
			
		}
		
		/**
		 * Callback function for the update plugin page above. This page uses the filesystem api to install a plugin
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      get_option()
		 * @see      wp_remote_post()
		 * @see      is_wp_error()
		 * @see      wp_remote_retrieve_response_code()
		 * @see      wp_remote_retrieve_body()
		 * @see      current_user_can()
		 * @see      wp_verify_nonce()
		 * @see      wp_nonce_url()
		 * @see      WP_Filesystem
		 * @see      WP_Filesystem::wp_plugins_dir()
		 * @see      request_filesystem_credentials()
		 * @see      trailingslashit()
		 * @see      unzip_file()
	 	 * @see      wp_cache_set()
		 * @see      activate_plugin()
		 * @return   void
		 */
		public function mp_core_install_plugin() {
			
			//If this product is licensed
			if ( !empty( $this->_args['plugin_licensed'] ) && $this->_args['plugin_licensed'] ){
				
				//get validity of license saved
				$license_valid = get_option( $this->plugin_name_slug . '_license_status_valid' );
				
				//if license saved is incorrrect
				if ( !$license_valid ) {
				
					//output incorrect license message
					echo "The license entered is not valid";
					
					//output form to try license
					
					//stop the rest of this page from showing
					return true;
					
				}
				
				$api_params = array(
					'api' => 'true',
					'slug' => $this->plugin_name_slug,
					'author' => NULL, //$this->_args['software_version'] - not working for some reason
					'license_key' => $this->_args['plugin_license']
				);
								
				$request = wp_remote_post( $this->_args['plugin_api_url']  . '/repo/' . $this->plugin_name_slug, array( 'method' => 'POST', 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );				
																			
				// make sure the response was successful
				if ( is_wp_error( $request ) || 200 != wp_remote_retrieve_response_code( $request ) ) {
					$failed = true;
				}
								
				//JSON Decode response and store the plugin download link in $this->_args['plugin_download_link']
				$request = json_decode( wp_remote_retrieve_body( $request ) );
				
				//Set the plugin download link to be the package URL from the response
				$this->_args['plugin_download_link'] = $request->package;
				
				
			}
											
			//Make sure this user has the cpability to install plugins:
			if (!current_user_can('install_plugins')){ die('<p>' . __('You don\'t have permission to do this. Contact the system administrator for assistance.', 'mp_core') . '</p>'); } 
			
			//Make sure the action is set to install-plugin
			if ($_GET['action'] != 'install-plugin'){ die('<p>' . __('Oops! Something went wrong', 'mp_core') . '</p>'); }
									
			//Get the nonce previously set
			$nonce=$_REQUEST['_wpnonce'];
			
			//Check that nonce to ensure the user wants to do this
			if (! wp_verify_nonce($nonce, 'install-plugin' ) ) die('<p>' . __('Security Check', 'mp_core') . '</p>'); 
			
			//Set the method for the wp filesystem
			$method = ''; // Normally you leave this an empty string and it figures it out by itself, but you can override the filesystem method here
			
			//Get credentials for wp filesystem
			$url = wp_nonce_url('options-general.php?page=mp_core_install_plugin_page_' .  $this->plugin_name_slug . '&action=install-plugin&plugin=' . $this->plugin_name_slug, 'install-plugin_' . $this->plugin_name_slug );
			if (false === ($creds = request_filesystem_credentials($url, $method, false, false) ) ) {
			
				// if we get here, then we don't have credentials yet,
				// but have just produced a form for the user to fill in, 
				// so stop processing for now
				
				return true; // stop the normal page form from displaying
			}
			
			//Now we have some credentials, try to get the wp_filesystem running
			if ( ! WP_Filesystem($creds) ) {
				// our credentials were no good, ask the user for them again
				request_filesystem_credentials($url, $method, true, false);
				return true;
			}
			
			//By this point, the $wp_filesystem global should be working, so let's use it get our plugin
			global $wp_filesystem;
			
			//If we are installing a theme
			if ( $this->_args['plugin_is_theme'] ){
				
				//Get the plugins directory and name the temp plugin file
				$upload_dir = $wp_filesystem->wp_themes_dir();
			}
			//If we are installing a plugin
			else{
				//Get the plugins directory and name the temp plugin file
				$upload_dir = $wp_filesystem->wp_plugins_dir();
			}
			$filename = trailingslashit($upload_dir).'temp.zip';
			
			//if 'allow_url_fopen' is available, do it the right way using the WP Filesystem api
			if( ini_get('allow_url_fopen') ) {
					
				//Download the plugin file defined in the passed in array
				$saved_file = $wp_filesystem->get_contents( $this->_args['plugin_download_link'] );
			
				//Save the contents into a temp.zip file (string stored in $filename)
				$wp_filesystem->put_contents( $filename, $saved_file, FS_CHMOD_FILE);
				
			}
			//For people with poor/bad server configurations which don't have access to allow_url_fopen, activate the "MP Curl Plugin Installer" Plugin to hook here
			else{
				
				//Set args for CURL hook			
				$curl_args = array(
					'plugin_download_link' => $this->_args['plugin_download_link'],
					'upload_dir' => $upload_dir
				);
				
				//Hook to use CURL for people with poor/bad server configurations. Activate the "MP Curl Plugin Checker" Plugin to hook here
				do_action( 'mp_core_curl_plugin_installer', $curl_args );					
				
			}
			
			//Unzip the temp zip file
			unzip_file($filename, trailingslashit($upload_dir) . '/' );
						
			//Delete the temp zipped file
			$wp_filesystem->rmdir($filename);
							
			//Display a successfully installed message
			echo '<p>' . __( 'Successfully Installed ', 'mp_core' ) .  $this->_args['plugin_name']  . '</p>';
					
			//If we are installing a theme
			if ( $this->_args['plugin_is_theme'] ){
				
				//Set themes cache to NULL so wp_get_themes will get the new theme we just installed 
				wp_clean_themes_cache( true );
								
				$installed_themes = wp_get_themes(); 
								
				//Loop through each installed theme
				foreach( $installed_themes as $theme_slug => $theme ){
					
					echo $theme['headers:WP_Theme:private']['Name'];
					echo $theme['plugin_name'];
					
					//If this theme is the theme we're hoping to install
					if ( $theme['headers:WP_Theme:private']['Name'] == $theme['plugin_name'] ){
						
						//Switch to the theme we just installed
						switch_theme( $theme_slug );
						
						//Stop looping
						break;
							
					}
					
				}
					
			}
			//If we are installing a plugin
			else{
				
				//Set plugin cache to NULL so activate_plugin->validate_plugin->get_plugins will check again for new plugins
				wp_cache_set( 'plugins', NULL, 'plugins' );
			
				//Activate plugin
				print_r ( activate_plugin( trailingslashit( $upload_dir ) . $this->plugin_name_slug . '/' . $this->_args['plugin_filename'] ) );
			}
		
			if ( !empty( $this->_args['plugin_success_link'] ) ){
				//Javascript for redirection
				echo '<script type="text/javascript">';
					echo "window.location = '" . $this->_args['plugin_success_link'] . "';";
				echo '</script>';
				
				echo '</div>';
			}
										
		}
			
	}
}