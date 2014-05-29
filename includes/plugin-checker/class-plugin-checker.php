<?php
/**
 * This file contains the MP_CORE_Plugin_Checker class and its activating function
 *
 * @link http://mintplugins.com/doc/plugin-checker-class/
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
 * Plugin Checker Class for the MP Core Plugin by Mint Plugins.
 * 
 * This Class is run at init by the 'mp_core_plugin_checker' function. 
 * It accepts a multidimentional array of plugins to check for existence.
 * Plugins can either be installed in a group, or singularly (1 at a time). If they are set to be 1 at a time
 * there will be a notification in the Dashboard that it needs to be installed/Activated. This is configured in the $args array.
 * Note: The actual check only happens on the admin side so resources are not being wasted on the front end
 *
 * @author     Philip Johnston
 * @link       http://mintplugins.com/doc/plugin-checker-class/
 * @since      1.0.0
 * @return     void
 */
if ( !class_exists( 'MP_CORE_Plugin_Checker' ) ){
	class MP_CORE_Plugin_Checker{
		
		/**
		 * Constructor
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Checker::mp_core_create_pages()
		 * @see      MP_CORE_Plugin_Checker::mp_core_plugin_check_notice()
		 * @see      wp_parse_args()
		 * @param    array $args {
		 *      This array holds information for multiple plugins. Visit link for details.
		 *		@type array {
		 *      	This array could be in here an unlimited number of times and holds Plugin information. Visit link for details.
		 *			@type string 'plugin_name' Name of plugin.
		 *			@type string 'plugin_message' Message which shows up in notification for plugin.
		 *			@type string 'plugin_filename' Name of plugin's main file
		 *			@type string 'plugin_download_link' Link to URL where this plugin's zip file can be downloaded
		 *			@type string 'plugin_api_url' URL to the place where the api can be checked for this plugin
		 *			@type bool   'plugin_licensed' Whether this plugin required a license to be installed/downloaded
		 *			@type string 'plugin_licensed_parent_name' Optional name of a plugin whose license could ALSO be used for this plugin
		 *			@type string 'plugin_info_link' Link to URL containing info for this plugin 
		 * 			@type bool   'plugin_required' Whether or not this plugin is required
		 *			@type bool   'plugin_group_install' Whether to install this plugin with "the group" or on it's own
		 *			@type bool   'plugin_wp_repo' Whether to look for this plugin on the WP Repo or not	
		 * 		}
		 * }
		 * @return   void
		 */
		public function __construct( $args ){
				
			//Set defaults for args		
			foreach ( $args as $key => $arg ){
				
				$defaults[$key] = array(
					'plugin_name' => NULL,  
					'plugin_message' => NULL, 
					'plugin_filename' => NULL,
					'plugin_download_link' => NULL,
					'plugin_api_url' => NULL,
					'plugin_licensed' => NULL,
					'plugin_licensed_parent_name' => NULL,
					'plugin_info_link' => NULL,
					'plugin_required' => false,
					'plugin_group_install' => true,
					'plugin_wp_repo' => true,
					'plugin_is_theme' => false
				);
				
				//Get and parse args
				$this->_args[$key] = wp_parse_args( $args[$key], $defaults[$key] );
				
			}
			
			//Make sure we are not on the "plugin-install.php" page because there is a conflict with the plugins_api on this page		
			if ( stripos( basename( $_SERVER['PHP_SELF'] ), 'plugin-install.php' ) === false ){
				//Set up install page/pages
				$this->mp_core_create_pages();
	
			}
																			
			//Get the "page" URL variable
			$page = isset($_GET['page']) ? $_GET['page'] : NULL;
			
			//Make sure we are not on the "mp_core_install_plugins_page" page - where this message isn't necessary			
			if ( stripos( $page, 'mp_core_install_plugins_page' ) === false ){
				
				//Also, make sure we are not on the "mp_core_install_plugin_page" page (singular) - where this message also isn't necessary			
				if ( stripos( $page, 'mp_core_install_plugin_page' ) === false ){
					
					//Check for plugins in questionno them first
					add_action( 'admin_notices', array( $this, 'mp_core_plugin_check_notice') );
											
				}
			}
						
		}
		
		/**
		 * Loop through each passed-in plugin to see if it needs an install page
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Checker::mp_core_install_plugins_page()
		 * @see      plugins_api()
		 * @see      sanitize_title()
		 * @see      MP_CORE_Plugin_Installer
	 	 * @return   void
		 */
		public function mp_core_create_pages(){
			
			//Loop through each plugin that is supposed to be installed
			foreach ( $this->_args as $plugin_key => $plugin ){
				
				$plugin_name_slug = sanitize_title ( $plugin['plugin_name'] ); //EG move-plugins-core
					
				// Create update/install plugins page
				add_action('admin_menu', array( $this, 'mp_core_install_plugins_page') );
				
				//Stop looping - only one install page is needed
				//break;
				
				//If we should check the WP Repo
				if ( $plugin['plugin_wp_repo'] ){
					
					/** If plugins_api isn't available, load the file that holds the function */
					if ( !function_exists( 'plugins_api' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
					}
	
					//Check if this plugin exists in the WP Repo
					$args = array( 'slug' => $plugin_name_slug);
					$api = plugins_api( 'plugin_information', $args );					
					
					//If it does exist in the WP Repo
					if ( !empty( $api->download_link ) ){ 
					
						//Set the plugin's download link to be the one from the WP Repo
						$this->_args[$plugin_key]['plugin_download_link'] = $api->download_link;
						
						//Set the same for the current loop's plugin_download_link
						$plugin['plugin_download_link'] = $api->download_link;
	
					}
					
				}
				
				//If this plugin is NOT supposed to be installed as part of the group, create its WordPress install page
				if ( !$plugin['plugin_group_install'] ){
					//Create Install Page for this plugin
					new MP_CORE_Plugin_Installer( $plugin );
				}
										
			}
		}
		
		/**
		 * Create page where plugins are installed called "mp_core_install_plugins_page"
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Checker::mp_core_install_check_callback()
		 * @see      get_plugin_page_hookname()
	 	 * @return   void
		 */
		public function mp_core_install_plugins_page()
		{
			// This WordPress variable is essential: it stores which admin pages are registered to WordPress
			global $_registered_pages;
		
			// Get the name of the hook for this plugin
			// We use "options-general.php" as the parent as we want our page to appear under "options-general.php?page=mp_core_install_plugins_page"
			$hookname = get_plugin_page_hookname('mp_core_install_plugins_page', 'options-general.php');
		
			// Add the callback via the action on $hookname, so the callback function is called when the page "options-general.php?page=mp_core_install_plugins_page" is loaded
			if (!empty($hookname)) {
				add_action($hookname, array( $this, 'mp_core_install_check_callback') );
			}
		
			// Add this page to the registered pages
			$_registered_pages[$hookname] = true;
					
		}
		
		/**
		 * This is the callback function for the "install plugin page" above. This creates the output for the install plugins page
		 * and uses the mp_core_check_plugins function with the $show_notices set to false. In doing so, it gets an array of each
		 * plugin that needs tp be installed so that we can run the MP_CORE_Plugin_Installer class for each plugin.
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Checker::mp_core_check_plugins()
		 * @see      screen_icon()
		 * @see      MP_CORE_Plugin_Installer
	 	 * @return   void
		 */
		public function mp_core_install_check_callback() {
								
			echo '<div class="wrap">';
			
			screen_icon();
						
			echo '<h2>' . __('Install Items', 'mp_core') . '</h2>';
						
			//Check plugins and store needed ones in $plugins
			$plugins = $this->mp_core_check_plugins( $this->_args, false );
						
			//Loop through each plugin that is supposed to be installed
			foreach ( $plugins as $plugin_key => $plugin ){
				
				//If this plugin requires a license to be installed
				if ( $plugin['plugin_licensed'] ){
									
					$plugin_name_slug = sanitize_title ( $plugin['plugin_name'] );
					
					//If this plugin could use a different/parent plugin's license (this is likely an add-on plugin).
					//If this passes, it just means the user doesn't have to enter the same license again
					if (!empty( $plugin['plugin_licensed_parent_name'] ) ){
						
						$parent_plugin_name_slug = sanitize_title ( $plugin['plugin_licensed_parent_name'] );
						
						//Get the previously saved license for the parent plugin from the database
						$license_key = get_option( $parent_plugin_name_slug . '_license_key' );
						
						$verify_license_args = array(
							'software_name'      => $plugin['plugin_name'],
							'software_api_url'   => $plugin['plugin_api_url'],
							'software_license_key'   => $license_key, //EG move-plugins-core_license_key
							'software_store_license' => true, //We'll store the parent's license for this add-on
						);
						
						//If the previously saved parent's license from the database is valid for this add-on
						if ( mp_core_verify_license( $verify_license_args ) ){
							
							$plugin['plugin_license'] = $license_key;
							
							//Install and activate this plugin - right here, right now
							new MP_CORE_Plugin_Installer( $plugin );
					
						}
						//If the previously saved license from the database is not valid
						else{
							
							//Create License Form ?>
							
							<div id="<?php echo $plugin_name_slug; ?>-plugin-license-wrap" class="wrap mp-core-plugin-license-wrap">
								
								<p class="plugin-description"><?php echo __( "You need a license for ", 'mp_core' ) . $plugin['plugin_name']; ?></p>
								
								<form method="post">
													
									<input style="float:left; margin-right:10px;" id="<?php echo $plugin_name_slug; ?>_license_key" name="<?php echo $plugin_name_slug; ?>_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license_key ); ?>" />						
									
									<?php wp_nonce_field( $plugin_name_slug . '_nonce', $plugin_name_slug . '_nonce' ); ?>
											
									<br />
										
									<?php submit_button(__('Submit License', 'mp_core') ); ?>
								
								</form>
							</div>
					   
							<?php
							
						}
						
					}
					//If this license can't use a another plugin's license but needs its own
					else{
						//Check if there's a license waiting in the $_POST var for this plugin
						if( isset( $_POST[ $plugin_name_slug . '_license_key' ] ) ) {
											
							//If there is a submitted license, store it in the license_key variable 
							$license_key = $_POST[ $plugin_name_slug . '_license_key' ];
							
							//Check nonce
							if( ! check_admin_referer( $plugin_name_slug . '_nonce', $plugin_name_slug . '_nonce' ) ) 	
								return false; // get out if we didn't click the Activate button
								
							$verify_license_args = array(
								'software_name'      => $plugin['plugin_name'],
								'software_api_url'   => $plugin['plugin_api_url'],
								'software_license_key'   => $license_key, //EG move-plugins-core_license_key
								'software_store_license' => true, //Store this newly submitted license
							);
										
							//If this license is valid	
							if ( mp_core_verify_license( $verify_license_args ) ){
								
								$plugin['plugin_license'] = $license_key;
								
								//Install and activate this plugin - right here, right now
								new MP_CORE_Plugin_Installer( $plugin );
						
							}
							//If this license is not valid
							else{
								
								//Create License Form ?>
								
								<div id="<?php echo $plugin_name_slug; ?>-plugin-license-wrap" class="wrap mp-core-plugin-license-wrap">
									
									<p class="plugin-description"><?php echo __( "You need a license for ", 'mp_core' ) . $plugin['plugin_name']; ?></p>
									
									<form method="post">
														
										<input style="float:left; margin-right:10px;" id="<?php echo $plugin_name_slug; ?>_license_key" name="<?php echo $plugin_name_slug; ?>_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license_key ); ?>" />						
										
										<?php wp_nonce_field( $plugin_name_slug . '_nonce', $plugin_name_slug . '_nonce' ); ?>
												
										<br />
											
										<?php submit_button(__('Submit License', 'mp_core') ); ?>
									
									</form>
								</div>
						   
								<?php
								
							}	
							
						}
						//If there is no submitted license waiting in the $_POST var
						else{
							
							//Get the previously saved license from the database
							$license_key = get_option( $plugin_name_slug . '_license_key' );
							
							$verify_license_args = array(
								'software_name'      => $plugin['plugin_name'],
								'software_api_url'   => $plugin['plugin_api_url'],
								'software_license_key'   => $license_key, //EG move-plugins-core_license_key
								'software_store_license' => false, //Just verify license, we don't need to store it
							);
							
							//If the previously saved license from the database is valid	
							if ( mp_core_verify_license( $verify_license_args ) ){
								
								$plugin['plugin_license'] = $license_key;
								
								//Install and activate this plugin - right here, right now
								new MP_CORE_Plugin_Installer( $plugin );
						
							}
							//If the previously saved license from the database is not valid
							else{
								
								//Create License Form ?>
								
								<div id="<?php echo $plugin_name_slug; ?>-plugin-license-wrap" class="wrap mp-core-plugin-license-wrap">
									
									<p class="plugin-description"><?php echo __( "You need a license for ", 'mp_core' ) . $plugin['plugin_name']; ?></p>
									
									<form method="post">
														
										<input style="float:left; margin-right:10px;" id="<?php echo $plugin_name_slug; ?>_license_key" name="<?php echo $plugin_name_slug; ?>_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license_key ); ?>" />						
										
										<?php wp_nonce_field( $plugin_name_slug . '_nonce', $plugin_name_slug . '_nonce' ); ?>
												
										<br />
											
										<?php submit_button(__('Submit License', 'mp_core') ); ?>
									
									</form>
								</div>
						   
								<?php
								
							}
						}
					}					
				}
				//If this plugin does not require a license
				else{
					//Install and activate this plugin - right here, right now
					new MP_CORE_Plugin_Installer( $plugin );
				}
				
			}
			
			//Set redirect to referring page when complete
			$redirect_after_install_url = $_SERVER['HTTP_REFERER'];
			
			//Check if we should redirect to the theme page - option is set when new MP theme activated
			$theme_page_redirect = get_option('mp_core_theme_redirect_after_install');
			
			//If this option has been saved
			if ( !empty($theme_page_redirect) ){
				
				//If theme pages redirect is true
				if ( $theme_page_redirect ){
					
					//Change redirect url to be the themes page
					$redirect_after_install_url = admin_url('themes.php');
				}
				
			}
			
			/*
			//Javascript for redirection
			echo '<script type="text/javascript">';
				echo "window.location = '" . $redirect_after_install_url . "';";
			echo '</script>';
			*/
			
			echo '</div>';
			
			//Reset theme redirect option  to false so new plugins redirect to referrer instead of themes page
			update_option('mp_core_theme_redirect_after_install', false);
									
		}
		
		/**
		 * This is run on the "admin_notices" WP Hook and calls the mp_core_check_plugins() function 
		 * with the $show_notices parameter set to true so that it echoes notices in the WP dashboard for installation of necessary plugins.
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Checker::mp_core_check_plugins()
	 	 * @return   void
		 */
		public function mp_core_plugin_check_notice() {
			
			//Check plugins and output notices
			$this->mp_core_check_plugins( $this->_args, true );
			
		}
		
		/**
		 * Check to see each plugin's status and either return them in an array if un-installed/un-activated
		 * Or output a notice that it needs to be installed/activated. This is dependant on the $show_notices parameter. 
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      MP_CORE_Plugin_Checker::mp_core_check_plugins()
		 * @see      MP_CORE_Plugin_Checker::mp_core_close_message()
		 * @see      MP_CORE_Plugin_Checker::mp_core_dismiss_message()
		 * @see      sanitize_title()
		 * @see      apply_filters()
		 * @see      get_option()
		 * @see      wp_nonce_url()
	 	 * @param    array $plugins This has the same format as the $args plugin in the construct function of this class
		 * @param    boolean $show_notices If true it will output notices and return nothing. If false it will return an array of plugins
		 * @return   array $plugins If $show_notices is set to false this will be returned and match the $plugins array 
		 */
		public function mp_core_check_plugins( $plugins, $show_notices = false ) {
			
			//Set plugins to install to be a blank array
			$plugins_to_install = array();
						
			//Loop through each plugin that is supposed to be installed
			foreach ( $plugins as $plugin_key => $plugin ){
				
				//If this "plugin" is actually a "theme"
				if ( $plugin['plugin_is_theme'] == true ){
					
					//Get list of all installed themes
					$installed_themes = wp_get_themes();
					
					//Loop through each installed theme
					foreach( $installed_themes as $theme_slug => $theme ){
					
						//If this theme is not the theme we're hoping to install
						if ( $theme['headers:WP_Theme:private']['Name'] != $plugin['plugin_name'] ){
							
							//For now, set this theme to be listed as not installed
							$theme_installed = false;
							
						}
						//If this is the theme we're hoping to install, it already is!
						else{
							
							//This theme is installed
							$theme_installed = true;
							
							//Stop looping
							break;
								
						}
						
					}
					
					//If the theme was not installed
					if ( $theme_installed == false ){
						//Add the theme to the "plugins to install" list
						$plugins_to_install[$plugin_key] = $plugin;
					}
					
				}else{
								
					//Set plugin name slug by sanitizing the title. Plugin's title must match title in WP Repo
					$plugin_name_slug = sanitize_title ( $plugin['plugin_name'] ); //EG move-plugins-core
					
					//Get array of active plugins - duplicate_hook
					$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ));
					
					//Set default for $plugin_active
					$plugin_active = false;
					
					//Loop through each active plugin's string EG: (subdirectory/filename.php)
					foreach ($active_plugins as $active_plugin){
						//Check if the filename of the plugin in question exists in any of the plugin strings
						if (strpos($active_plugin, $plugin['plugin_filename'])){	
							
							//Plugin is active
							$plugin_active = true;
							
							//Stop looping
							break;
							
						}
					}
					
					
					//If this plugin is not active
					if (!$plugin_active){
					
						//If the user has just clicked "Dismiss", than add that to the options table
						$this->mp_core_close_message( $plugin );
											
						//Check to see if the user has ever dismissed this message
						if (get_option( 'mp_core_plugin_checker_' . $plugin_name_slug ) != "false"){
													
							//Take steps to see if the Plugin already exists or not
							 
							//Check if the plugin file exists in the plugin root
							$plugin_root_files = array_filter(glob('../wp-content/plugins/' . '*'), 'is_file');
							
							//Preset value for plugin_exists to false
							$plugin_exists = false;
							
							//Preset value for $plugin_directory
							$plugin_directory = NULL;
							
							//Check if the plugin file is directly in the plugin root
							if (in_array( '../wp-content/plugins/' . $plugin['plugin_filename'], $plugin_root_files ) ){
								
								//Set plugin_exists to true
								$plugin_exists = true;
								
							}
							//Check if plugin exists in a subfolder inside the plugin root
							else{	
												 
								//Find all directories in the plugins directory
								$plugin_dirs = array_filter(glob('../wp-content/plugins/' . '*'), 'is_dir');
																									
								//Loop through each plugin directory
								foreach ($plugin_dirs as $plugin_dir){
									
									//Scan all files in this plugin and store them in an array
									$plugins_files = scandir($plugin_dir);
									
									//If the plugin filename in question is in this plugin's array, than this plugin exists but it is not active
									if (in_array( $plugin['plugin_filename'], $plugins_files ) ){
										
										//Set plugin_exists to true
										$plugin_exists = true;
										
										//Set the plugin directory for later use
										$plugin_directory = explode('../wp-content/plugins/', $plugin_dir);
										$plugin_directory = !empty($plugin_directory[1]) ? $plugin_directory[1] . '/' : NULL;
										
										//Stop checking through plugins
										break;	
									}							
								}
							}
							
							//This plugin exists but is just not active
							if ($plugin_exists && $show_notices){
								
									echo '<div class="updated fade"><p>';
									
									echo $plugin['plugin_message'] . '</p>';					
									
									//Activate button
									echo '<a href="' . wp_nonce_url('plugins.php?action=activate&plugin=' . $plugin_directory . $plugin['plugin_filename'] . '&plugin_status=all&paged=1&s=', 'activate-plugin_' . $plugin_directory . $plugin['plugin_filename']) . '" title="' . esc_attr__('Activate this plugin') . '" class="button">' . __('Activate', 'mp_core') . ' "' . $plugin['plugin_name'] . '"</a>'; 
									//Dismiss button
									$this->mp_core_dismiss_button( $plugin );
									
									echo '</p></div>';
							
							//This plugin doesn't even exist on this server	 	
							}else{
																						
								//If this plugin should show notification by itself - not with a group of other plugins
								if ( $plugin['plugin_group_install'] == NULL || !$plugin['plugin_group_install'] ){
									
									//If we are using this function to output notices
									if ($show_notices){
										
										echo '<div class="updated fade"><p>';
								
										echo $plugin['plugin_message'] . '</p>';
									
										//Display a custom download button."; 
										printf( '<a class="button" href="%s" style="display:inline-block; margin-right:.7em;"> ' . __('Automatically Install', 'mp_core') . ' "' . $plugin['plugin_name'] . '"</a>', admin_url( sprintf( 'options-general.php?page=mp_core_install_plugin_page_' . $plugin_name_slug . '&action=install-plugin&_wpnonce=%s', wp_create_nonce( 'install-plugin' ) ) ) );	
										
										//Dismiss button
										$this->mp_core_dismiss_button( $plugin );
										
										echo '</p></div>';
									
									}
								
								}
								
								//If this plugin should install with a group of other plugins
								else{
									
									//Add this plugin to the list of plugins that need to actually be installed.
									$plugins_to_install[$plugin_key] = $plugin;
									
								}
								
							}//If this plugin doesn't exist on this server
						}//If the user has never dismissed this plugin
					}//If this plugin is not active
				}//If this plugin is a plugin - not a theme
			}//Loop through each plugin passed in
			
			//If there are Multiple Plugins to install at once
			if ( !empty( $plugins_to_install ) ){
				
				//If we should show the notices in the WP Dashboard
				if ($show_notices){
					
					//Show "Install all items" button
					
					echo '<div class="updated fade"><p>';
										
					echo __( 'There are items that need to be installed.' , 'mp_core' ) . '</p>';
				
					printf( '<a class="button" href="%s" style="display:inline-block; margin-right:.7em;"> ' . __('Install All Items', 'mp_core')  . '</a>', admin_url( sprintf( 'options-general.php?page=mp_core_install_plugins_page&action=install-plugin&_wpnonce=%s', wp_create_nonce( 'install-plugin' ) ) ) );	
					
					echo '| <a href="#TB_inline?width=600&height=550&inlineId=mp-core-installer-details" class="thickbox">' . __( 'Details', 'mp_core' ) . "</a>";
					
					echo '</p></div>';
					
					//Add Thickbox
					add_thickbox();
					
					//Output Details
					echo '<div id="mp-core-installer-details" style="display:none;">';
						 echo '<h2>' . __( 'These items will be installed:', 'mp_core' ) . '</h2>';
							echo '<ol>'; 	
											 
							foreach( $plugins_to_install as $plugin_info ){
								
								echo '<li>';
									echo $plugin_info['plugin_name'] . ' - <a href="' . $plugin_info['plugin_info_link'] . '" target="_blank">' . $plugin_info['plugin_info_link'] . '</a>';
								echo '</li>';
									 
							}
														 
							echo '</ol>';							 
														
							printf( '<a class="button" href="%s" style="display:inline-block; margin-right:.7em;"> ' . __('Install All Items', 'mp_core')  . '</a>', admin_url( sprintf( 'options-general.php?page=mp_core_install_plugins_page&action=install-plugin&_wpnonce=%s', wp_create_nonce( 'install-plugin' ) ) ) );
							
							echo '</p>';	
							
					echo '</div>';
				
				}
				//If we shouldn't show any notices
				else{
					
					//Return the array of plugins that need to be installed.
					return $plugins_to_install;	
				}
			
			}
	
		}//End Function
		
		/**
		 * Function to display "Dismiss" message in notices
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      sanitize_title()
		 * @see      wp_nonce_field()
	 	 * @param    array $dismiss_args This array holds Plugin information matching the second array in the construct function
		 * @return   void
		 */
		 public function mp_core_dismiss_button( $dismiss_args ){
			 
			$plugin_name_slug = sanitize_title ( $dismiss_args['plugin_name'] ); //EG move-plugins-core
			 
			$dismiss_args['plugin_required'] = (!isset($dismiss_args['plugin_required']) ? true : $dismiss_args['plugin_required']);
			if ($dismiss_args['plugin_required'] == false){
				echo '<form id="mp_core_plugin_checker_close_notice" method="post" style="display:inline-block; margin-left:.7em;">
							<input type="hidden" name="mp_core_plugin_checker_' . $plugin_name_slug . '" value="false"/>
							' . wp_nonce_field('mp_core_plugin_checker_' . $plugin_name_slug . '_nonce','mp_core_plugin_checker_' . $plugin_name_slug . '_nonce_field') . '
							<input type="submit" id="mp_core_plugin_checker_dismiss" class="button" value="Dismiss" /> 
					   </form>'; 
			}
		 }
		
		/**
		 * Function to fire if the Close button has been clicked
		 *
		 * @access   public
		 * @since    1.0.0
		 * @see      sanitize_title()
		 * @see      wp_verify_nonce()
		 * @see      update_option()
	 	 * @param    array $close_args This array holds Plugin information matching the second array in the construct function
		 * @return   void
		 */
		 public function mp_core_close_message( $close_args ){
			 
			$plugin_name_slug = sanitize_title ( $close_args['plugin_name'] ); //EG move-plugins-core
			 
			if (isset($_POST['mp_core_plugin_checker_' . $plugin_name_slug])){
				//verify nonce
				if (wp_verify_nonce($_POST['mp_core_plugin_checker_' . $plugin_name_slug . '_nonce_field'],'mp_core_plugin_checker_' . $plugin_name_slug . '_nonce') ){
					//update option to not show this message
					update_option( 'mp_core_plugin_checker_' . $plugin_name_slug, "false" );
				}
			}
		 }
	}
}

/**
 * Get the Plugin Checker Started
 *
 * @since    1.0.0
 * @link     http://mintplugins.com/doc/plugin-checker-class/
 * @author   Philip Johnston
 * @see      MP_CORE_Plugin_Checker
 * @see      apply_filters()
 * @see      add_action()
 * @return   void
 */
if ( !function_exists( 'mp_core_plugin_checker' ) ){
	function mp_core_plugin_checker(){
		
		//Set default for $mp_core_plugins_to_check
		$mp_core_plugins_to_check = array();
		
		/**
		 * Filter Hook for adding plugins to check.
		 *
		 * @since 1.0.0
		 *
		 * @param string 'mp_core_check_plugins' The name of this filter hook.
		 * @param array $mp_core_plugins_to_check {
		 *      This array holds information for multiple plugins. Visit link for details.
		 *		@type array {
		 *      	This array could be in here an unlimited number of times and holds Plugin information. Visit link for details.
		 *			@type string 'plugin_name' Name of plugin.
		 *			@type string 'plugin_message' Message which shows up in notification for plugin.
		 *			@type string 'plugin_filename' Name of plugin's main file
		 *			@type string 'plugin_download_link' Link to URL where this plugin's zip file can be downloaded
		 *			@type string 'plugin_info_link' Link to URL containing info for this plugin 
		 * 			@type bool   'plugin_required' Whether or not this plugin is required
		 *			@type bool   'plugin_group_install' Whether to install this plugin with "the group" or on it's own
		 *			@type bool   'plugin_wp_repo' Whether to look for this plugin on the WP Repo or not	
		 * 		}
		 * }
		 */
		$mp_core_plugins_to_check = apply_filters('mp_core_check_plugins', $mp_core_plugins_to_check );
					
		//If nothing to install, quit
		if ( empty( $mp_core_plugins_to_check ) ){
			return;	
		}
		
		//Remove duplicate plugins
		$mp_core_plugins_to_check = array_unique($mp_core_plugins_to_check, SORT_REGULAR);
		
		//Start checking plugins
		new MP_CORE_Plugin_Checker( $mp_core_plugins_to_check );
	}
	add_action( '_admin_menu', 'mp_core_plugin_checker' );
}