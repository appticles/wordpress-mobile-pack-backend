<?php

if ( ! class_exists( 'WMobilePack' ) ) { 

    /**
     * WMobilePack 
     * 
     * Main class for the Wordpress Mobile Pack plugin. This class handles:
     * 
     * - the install and uninstall of the plugin
     * - setting / getting the plugin's options
     * - loading the admin section, javascript and css files
     * - loading the app in thef frontend 
     * 
     */    
    class WMobilePack {
    
    		
    	/* ----------------------------------*/
    	/* Properties						 */
    	/* ----------------------------------*/
    	
    	public static $wmp_options;
        public static $wmp_allowed_fonts = array('Roboto Light Condensed', 'Crimson Roman', 'Open Sans Condensed Light');
        public static $wmp_basic_theme = 'base';
        
        // the oldest version that will enable the custom select
        public static $wmp_customselect_enable = 3.6;
    		
   		 
    	/* ----------------------------------*/
    	/* Methods							 */
    	/* ----------------------------------*/
    
        /**
         * 
         * Construct method that initializes the plugin's options
         * 
         */
    	public function __construct(){
    	
    		if(!is_array(self::$wmp_options) || empty(self::$wmp_options)){
                
                self::$wmp_options = array(
                
                	'color_scheme'          => 1,
                	'font_headlines'        => self::$wmp_allowed_fonts[0],
                    'font_subtitles'        => self::$wmp_allowed_fonts[0],
                    'font_paragraphs'       => self::$wmp_allowed_fonts[0],
                    'inactive_categories'   => serialize(array()),
					'inactive_pages'   		=> serialize(array()),
                    'joined_waitlists'      => serialize(array()),
                    'display_mode'          => 'normal',
                	'logo'                  => '',
                	'icon'                  => '',
					'cover'					=> '',
					'google_analytics_id'	=> '',
                    'whats_new_updated'     => 0,
                    'whats_new_last_updated' => 0					
                );
            }
    	}
    			
    		
    	/**
         * 
    	 * The wmp_install method is called on the activation of the plugin.
    	 * This method adds to the DB the default settings of the application.
    	 *
    	 */
    	public function wmp_install(){
    		
    		// add settings to database
    		$this->wmp_save_settings(self::$wmp_options);
    	}
    		
    	/**
         * 
    	 * The wmp_uninstall method is called on the deactivation of the plugin.
    	 * This method removes from the DB the settings of the application and associated files.
    	 *
    	 */
    	public function wmp_uninstall(){
    		
            // remove uploaded images and uploads folder
            $logo_path = WMobilePack::wmp_get_setting('logo');
            
            if ($logo_path != '' && file_exists(WMP_FILES_UPLOADS_DIR.$logo_path))
                unlink(WMP_FILES_UPLOADS_DIR.$logo_path);  
            
            $icon_path = WMobilePack::wmp_get_setting('icon');
            
            if ($icon_path != '' && file_exists(WMP_FILES_UPLOADS_DIR.$icon_path))
                unlink(WMP_FILES_UPLOADS_DIR.$icon_path);  
				
				
			$cover_path = WMobilePack::wmp_get_setting('cover');
            
            if ($cover_path != '' && file_exists(WMP_FILES_UPLOADS_DIR.$cover_path))
                unlink(WMP_FILES_UPLOADS_DIR.$cover_path);  
                
            rmdir( WMP_FILES_UPLOADS_DIR );
            
    		// remove settings from database
    		$this->wmp_delete_settings(self::$wmp_options);
			
			// remove the cookies
			setcookie("wmp_theme_mode", "", time()-3600);
			setcookie("wmp_load_app", "", time()-3600);
    	}
    	
    		
    	/**
    	 * 
         * The wmp_admin_init method is used to add the admin menu of the plugin, the css and javascript files.
    	 *
    	 */	
    	public function wmp_admin_init(){
    		
    		// add admin menu hook
    		add_action( 'admin_menu', array( &$this, 'wmp_admin_menu' ) );
            
    		// enqueue css and javascript for the admin area
            add_action( 'admin_enqueue_scripts',array( &$this, 'wmp_admin_enqueue_scripts' ) );
    	}
        
    	
    	/**
         * 
    	 * The wmp_admin_enqueue_scripts is used to enqueue scripts and styles for the admin area.
         * The scripts and styles loaded by this method are used on all admin pages.
    	 *
    	 */	
    	public function wmp_admin_enqueue_scripts() {
    		
    		// enqueue styles
			wp_enqueue_style('css_general', plugins_url(WMP_DOMAIN.'/admin/css/general-min.css'), array(), WMP_VERSION);
            
			wp_enqueue_style('css_main', 'http://dev.webcrumbz.co/~raducu/dashboard-cutting/wp/resources/css/main.css', array(), WMP_VERSION);
            wp_enqueue_style('css_fonts', 'http://dev.webcrumbz.co/~raducu/dashboard-cutting/wp/resources/css/fonts.css', array(), WMP_VERSION);
            wp_enqueue_style('css_ie', 'http://dev.webcrumbz.co/~raducu/dashboard-cutting/wp/resources/css/ie.css', array(), WMP_VERSION);
            
			
			
			
			
            // enqueue scripts
        	if (WMP_BLOG_VERSION < 3.6) 
				$dependencies = array('jquery');
			else 
				$dependencies = array('jquery-core', 'jquery-migrate');
			
		    // enqueue scripts
		    wp_enqueue_script('js_validate', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Lib/jquery.validate.min.js'), $dependencies, '1.11.1');
			wp_enqueue_script('js_validate_additional', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Lib/validate-additional-methods.min.js'), $dependencies, '1.11.1');
			wp_enqueue_script('js_loader', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Loader.min.js'), $dependencies, WMP_VERSION);
			wp_enqueue_script('js_ajax_upload', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/AjaxUpload.min.js'), $dependencies, WMP_VERSION);
			wp_enqueue_script('js_interface', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/JSInterface.min.js'), $dependencies, WMP_VERSION);	
		    wp_enqueue_script('js_scrollbar', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Lib/perfect-scrollbar.min.js'), array(), WMP_VERSION);	
    		
			
			//wp_enqueue_script('js_general', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Default/GENERAL.min.js'), $dependencies, '1.11.1');	
			wp_enqueue_script('js_feedback', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Feedback/WMP_SEND_FEEDBACK.min.js'), array(), WMP_VERSION);	
    	
		}
    	
    	
    	/**
         * 
         * Load specific javascript files for the admin Content submenu page
         * 
         */
        public function wmp_admin_load_content_js(){
            
			wp_enqueue_script('js_jquery_ui', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Lib/jquery-ui-1.10.3.custom.min.js'), array(), '0.9.9');
            
			wp_enqueue_script('js_content_editcategories', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Content/WMP_EDIT_CATEGORIES.min.js'), array(), WMP_VERSION);
            wp_enqueue_script('js_content_editpages', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Content/WMP_EDIT_PAGES.js'), array(), WMP_VERSION);
            wp_enqueue_script('js_content_pagepopup', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Content/WMP_PAGE_POPUP.js'), array(), WMP_VERSION);
			wp_enqueue_script('js_join_waitlist', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Waitlist/WMP_WAITLIST.min.js'), array(), WMP_VERSION);
        }
        
        
        /**
         * 
         * Load specific javascript files for the admin Settings submenu page
         * 
         */
        public function wmp_admin_load_settings_js(){
            wp_enqueue_script('js_settings_editdisplay', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Settings/WMP_EDIT_DISPLAY.min.js'), array(), WMP_VERSION);
            wp_enqueue_script('js_join_waitlist', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Waitlist/WMP_WAITLIST.min.js'), array(), WMP_VERSION);
        }
        
        /**
         * 
         * Load specific javascript files for the admin Look & Feel submenu page
         * 
         */
        public function wmp_admin_load_theme_js(){
            
            $blog_version = floatval(get_bloginfo('version'));
            
            // activate custom select for newer wp versions
            if ($blog_version >= self::$wmp_customselect_enable) {
                wp_enqueue_style('css_select_box_it', plugins_url(WMP_DOMAIN.'/admin/css/jquery.selectBoxIt.css'), array(), '3.8.1');
                wp_enqueue_script('js_select_box_it', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Lib/jquery.selectBoxIt.min.js'), array('jquery','jquery-ui-core', 'jquery-ui-widget'), '3.8.1');
                
                foreach (self::$wmp_allowed_fonts as $key => $font_family)
                    wp_enqueue_style('css_font'.($key+1), plugins_url(WMP_DOMAIN.'/themes/'.self::wmp_app_theme().'/includes/resources/css/font-'.($key+1).'.css'), array(), WMP_VERSION);
            }
            
            wp_enqueue_style('css_magnific_popup', plugins_url(WMP_DOMAIN.'/admin/css/magnific-popup.css'), array(), '0.9.9');
            wp_enqueue_script('js_magnific_popup', plugins_url(WMP_DOMAIN.'/admin/js/UI.Interface/Lib/jquery.magnific-popup.min.js'), array(), '0.9.9');
            wp_enqueue_script('js_settings_previewthemesgallery', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Theming/WMP_THEMES_GALLERY.min.js'), array(), WMP_VERSION);
            
            wp_enqueue_script('js_settings_edittheme', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Theming/WMP_EDIT_THEME.min.js'), array(), WMP_VERSION);
            wp_enqueue_script('js_settings_editimages', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Theming/WMP_EDIT_IMAGES.min.js'), array(), WMP_VERSION);
            wp_enqueue_script('js_settings_editcover', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Theming/WMP_EDIT_COVER.min.js'), array(), WMP_VERSION);
            wp_enqueue_script('js_join_waitlist', plugins_url(WMP_DOMAIN.'/admin/js/UI.Modules/Waitlist/WMP_WAITLIST.min.js'), array(), WMP_VERSION);
        }
        
        
    	/**
    	 * 
         * Build the admin menu and add all admin pages of the plugin
    	 *
    	 */	
    	public function wmp_admin_menu(){
    		
    		// load admin class
    		require_once(WMP_PLUGIN_PATH.'core/class-admin.php');
    		$WMobilePackAdmin = new WMobilePackAdmin;

            // check if we need to request updates for the what's new section
            if (!isset($_COOKIE['wmp_check_updates'])) {
            
                WMobilePackAdmin::wmp_whatsnew_updates();    
                
                // set next update request after 2 days
                setcookie("wmp_check_updates", 1, time()+3600*24*2,'/');
            }
            
            // display notify icon if the what's new section was updated or there's a new plugin version available
            $display_notify_icon = false;
            if (WMobilePack::wmp_get_setting('whats_new_updated') == 1 || self::wmp_new_plugin_version() !== null){
                $display_notify_icon = true;
            }
            
           	// add menu and submenu hooks
    		add_menu_page( 'WP Mobile Pack', 'WP Mobile Pack', 'manage_options', 'wmp-options', '', WP_PLUGIN_URL . '/wordpress-mobile-pack/admin/images/appticles-logo'.($display_notify_icon == true ? '-updates' : '').'.png' );
    		add_submenu_page( 'wmp-options', "What's New", "What's New", 'manage_options', 'wmp-options', array( &$WMobilePackAdmin, 'wmp_options' ) );
    		
            $theme_page = add_submenu_page( 'wmp-options', 'Look & Feel', 'Look & Feel', 'manage_options', 'wmp-options-theme', array( &$WMobilePackAdmin, 'wmp_theme_options') );
            add_action( 'load-' . $theme_page, array( &$this, 'wmp_admin_load_theme_js' ) );   
            
    		$content_page = add_submenu_page( 'wmp-options', 'Content', 'Content', 'manage_options', 'wmp-options-content', array( &$WMobilePackAdmin, 'wmp_content_options') );
            add_action( 'load-' . $content_page, array( &$this, 'wmp_admin_load_content_js' ) );   
            
    		$settings_page = add_submenu_page( 'wmp-options', 'Settings', 'Settings', 'manage_options', 'wmp-options-settings', array( &$WMobilePackAdmin, 'wmp_settings_options') );
            add_action( 'load-' . $settings_page, array( &$this, 'wmp_admin_load_settings_js' ) ); 
            
    		add_submenu_page( 'wmp-options', 'More...', 'More...', 'manage_options', 'wmp-options-upgrade', array( &$WMobilePackAdmin, 'wmp_upgrade_options') ); 
    	}
    		
         	
    	/**
    	 * The wmp_get_setting method is used to read an option value (or options) from the database.
    	 *
    	 * @param $option - array / string 
         * 
    	 * If the $option param is an array, the method will return an array with the values, 
    	 * otherwise it will return only the requested option value.
    	 *
    	 */		
    	public static function wmp_get_setting($option) {
    		
    		// if the passed param is an array, return an array with all the settings
    		if (is_array($option)) {
    			
    			foreach($option as $option_name => $option_value)	{
    				if ( get_option( 'wmpack_' . $option_name ) == '')
    					$wmp_settings[$option_name] = self::$wmp_options[$option_name];
    				else
    					$wmp_settings[$option_name] = get_option( 'wmpack_' . $option_name );
    			}
    			
    			// return array
    			return $wmp_settings;
    			
    		} elseif(is_string($option)) { // if option is a string, return the value of the option
    			
                // check if the option is added in the db 
    			if ( get_option( 'wmpack_' . $option ) === false ) { 
    				$wmp_setting = self::$wmp_options[$option];
    			} else {
    				$wmp_setting = get_option( 'wmpack_' . $option );
                }
        			
    			return $wmp_setting;
    		}
            
    	}
    
    
    	/**
         * 
    	 * The wmp_save_settings method is used to save an option value (or options) in the database.
    	 *
    	 * @param $option - array / string 
         * @param $option_value - optional, mandatory only when $option is a string
         * 
         * @return bool
    	 *
    	 */
    	public function wmp_save_settings( $option, $option_value = '' ) {
    		
            if (current_user_can( 'manage_options' )){
                
        		if (is_array($option) && !empty($option)) {
        		
        			// set option not saved variable
        			$option_not_saved = false;
        		
        			foreach($option as $option_name => $option_value) {
        				
        				if (array_key_exists( $option_name , self::$wmp_options))
        					add_option( 'wmpack_' . $option_name, $option_value );
        				else
        					$option_not_saved = true; // there is at least one option not in the default list
        			}
        		
        			if (!$option_not_saved)
        				return true;
        			else
        				return false; // there was an error
        				
        		} elseif (is_string($option) && $option_value != '') {
        
        			if (array_key_exists( $option , self::$wmp_options))
        				return add_option( 'wmpack_' . $option, $option_value );
        			
        		}
      		
            }
            
    		return false;
    		
    	}
    
        /**
         * 
    	 * The wmp_update_settings method is used to update the setting/settings of the plugin in options table in the database.
    	 *
    	 * @param $option - array / string 
         * @param $option_value - optional, mandatory only when $option is a string
         * 
         * @return bool
    	 *
    	 */
    	public function wmp_update_settings( $option, $option_value = null ) {
    	
            if (current_user_can( 'manage_options' )){
                
        		if (is_array($option) && !empty($option)) {
        			
        			foreach ($option as $option_name => $option_value) {
        				
        				// set option not saved variable
        				$option_not_updated = false;
        				
        				if ( array_key_exists( $option_name , self::$wmp_options ) )
        					update_option( 'wmpack_' . $option_name, $option_value );
        				else
        					$option_not_updated = true; // there is at least one option not in the default list
        					
        				if (!$option_not_updated)
        					return true;
        				else
        					return false; // there was an error
        				
        			}
        		
        			return true;
        			
        		} elseif (is_string($option) && $option_value !== null) {
        			
        			if ( array_key_exists( $option , self::$wmp_options ) )
        				return update_option( 'wmpack_' . $option, $option_value );
        			
        		}
    		}
            
    		return false;
    	}
    	
    
         /**
         * 
    	 * The wmp_delete_settings method is used to delete the setting/settings of the plugin from the options table in the database.
    	 *
    	 * @param $option - array / string 
         * 
         * @return bool
    	 *
    	 */
    	public function wmp_delete_settings( $option ) {
    	
            if (current_user_can( 'manage_options' )){
                
        		if (is_array($option) && !empty($option)) {
        			
        			foreach($option as $option_name => $option_value) {
        				
        				// set option not saved variable
        				$option_not_updated = false;
        				
        				
        				if ( array_key_exists( $option_name , self::$wmp_options ) )
        					delete_option( 'wmpack_' . $option_name );
        				
        			}
        		
        			return true;
        			
        		} elseif (is_string($option)) {
        			
        			if ( array_key_exists( $option , self::$wmp_options ) )
        				return delete_option( 'wmpack_' . $option );
        			
        		}
            }
    	}
    
    
        /**
         * 
         * Method that checks if we can load the mobile web application theme and calls the method that sets the custom theme.
         *
         * The theme is loaded if ALL of the following conditions are met:
         * 
         * - the user comes from a supported mobile device and browser
         * - the user has not deactivate the view of the mobile theme by switching to desktop mode
         * - the display mode of the app is set to 'normal' or is set to 'preview' and an admin is logged in 
         * 
         */		
    	public function wmp_check_load(){
    		
    		$load_app = false;
            
            $desktop_mode = self::wmp_check_desktop_mode();
            
            if ($desktop_mode == false) {
                
                if (self::wmp_check_display_mode()) {
        		
            		if (!isset($_COOKIE["wmp_load_app"])) {
            			
            			// load admin class
            			require_once(WMP_PLUGIN_PATH.'core/mobile-detect.php');
            			$WMobileDetect = new WPMobileDetect;
            			
            			$load_app = $WMobileDetect->wmp_detect_device();
            			
            		} elseif (isset($_COOKIE["wmp_load_app"]) && $_COOKIE["wmp_load_app"] == 1)
            			$load_app = true;	
                        
                    if ($load_app)
                        $this->wmp_load_app();
                }
                
            } else {
                
				// check if the load app cookie is 1 or the user came form a mobile device
				if (!isset($_COOKIE["wmp_load_app"])) {
            			
					// load admin class
					require_once(WMP_PLUGIN_PATH.'core/mobile-detect.php');
					$WMobileDetect = new WPMobileDetect;
					
					$load_app = $WMobileDetect->wmp_detect_device();
					
				} elseif (isset($_COOKIE["wmp_load_app"]) && $_COOKIE["wmp_load_app"] == 1)
					$load_app = true;
				
                // add the option to view the app in the footer of the website
				if ($load_app) {
					
					// add hook in footer
					add_action('wp_footer', array(&$this,'wmp_show_footer_box'));	
				}
            }
    	}
        
        
        /**
        *
        * Check if the app display is enabled
        * 
        * Returns true if display mode is "normal" (enabled for all mobile users) or
        * if display mode is "preview" and an admin is logged in.
        * 
        * @return bool
        *   
        */
        public function wmp_check_display_mode(){
            
            $display_mode = self::wmp_get_setting('display_mode');
            
            if ($display_mode == 'normal')
                return true;
                
            elseif ($display_mode == 'preview') {
                
                if (is_user_logged_in() && current_user_can('create_users'))
                    return true;
            }
            
            return false;
        }
        
        /**
         * 
         * Check if the user selected to view the desktop mode or we can display the app.
         * 
         * The GET/COOKIE "wmp_theme_mode" can have two values: 'desktop' or 'mobile'.
         * 
         * - Desktop mode can be activated from the app by selecting to return to desktop view.
         * - Mobile mode can be reactivated from the footer of the website.
         * 
         * @return bool
         * 
         */
        public function wmp_check_desktop_mode(){
            
            $desktop_mode = false;
            
            if (isset($_GET['wmp_theme_mode']) && is_string($_GET['wmp_theme_mode'])){
                
                if ($_GET['wmp_theme_mode'] == "desktop" || $_GET['wmp_theme_mode'] == "mobile"){
                    setcookie("wmp_theme_mode", $_GET['wmp_theme_mode'], time()+3600*30*24,'/');
                }
                
                if ($_GET['wmp_theme_mode'] == "desktop")
                    $desktop_mode = true;
                    
            } else {
                
                if (isset($_COOKIE["wmp_theme_mode"]) && is_string($_COOKIE['wmp_theme_mode'])){
                    if ($_COOKIE['wmp_theme_mode'] == "desktop")
                        $desktop_mode = true;
                }
            }
            
            return $desktop_mode;
        }
    		
    	/**
         * 
         * Method that loads the mobile web application theme.
         * 
         * The theme url and theme name from the WP installation are overwritten by the settings below.
         * 
         */
    	public function wmp_load_app(){
    		
    		add_filter("stylesheet", array(&$this, "wmp_app_theme"));
            add_filter("template", array(&$this, "wmp_app_theme"));
        
    		add_filter( 'theme_root', array( &$this, 'wmp_app_theme_root' ) );
    		add_filter( 'theme_root_uri', array( &$this, 'wmp_app_theme_root' ) );			
    	}
        
        /**
         * Return the theme name
         */
        public function wmp_app_theme() {
    		return self::$wmp_basic_theme;
    	}
    	
        /**
         * Return path to the mobile themes folder
         */
    	public function wmp_app_theme_root() {
    		return WMP_PLUGIN_PATH . 'themes';
    	}
     	
    		
         /**
          * 
          * Method used to create a token for the comments form.
          * 
          * The method returns a string formed using the encoded domain and a timestamp.
          * 
          * @return string
          * 
          */
    	public static function wmp_set_token(){
    		
    		$token = md5(md5(get_bloginfo("wpurl")).WMP_CODE_KEY);
    		
    		// encode token again
    		$token = base64_encode($token.'_'.strtotime('+1 hour'));
    		
    		// generate token
    		return $token;
    	}
    		
    		
        /**
          * 
          * Method used to check if a generated token is valid.
          * 
          * The method returns true if the token is valid and false otherwise.
          * 
          * @param $token - string
          * @return bool
          * 
          */
    	public static function wmp_check_token($token){
    		
    		if (base64_decode($token,true)){
    			
    			// decode token to get timestamp and encoded url
    			$decoded_token = base64_decode($token,true);
    			
    			if (strpos($decoded_token, "_") !== FALSE) {
    				
    				// get params
    				$arrParams = explode('_',$decoded_token);
    				
    				if (is_array($arrParams) && !empty($arrParams) && count($arrParams) == 2) {
    					
    					// check timestamp
    					if (time() < $arrParams[1]) {
    						
    						// get the generated encoded domain
    						$generated_url = md5(md5(get_bloginfo("wpurl")).WMP_CODE_KEY);
    						// check encoded domain
    						if($arrParams[0] ==  $generated_url)
    							return true;
    					
    					}
    				}
    			}
    		}
    		
    		// by default return false;
    		return false; 
    	}
		
		
		
		 /**
          * 
          * Method used to display a box on the footer of the theme 
		  * 
		  * This method is called from wmp_check_load()
		  * The box containes a link that sets the cookie and loads the app 
          *		  
          */
		public function wmp_show_footer_box(){
			
			// load view
			include(WMP_PLUGIN_PATH.'admin/sections/wmp-show-mobile.php'); 
			
			
		}
		
		
		
		/**
          * 
          * Method wmp_new_plugin_version used to search the transient for a new version of wordpress mobile pack plugin.
		  * 
		  * This method returns the new version number if it exists, null otherwise. 
		  * The transient is updated every 12 hours.
          *		  
          */
		public static function wmp_new_plugin_version(){
			
			// get update plugins transient
			$update_plugins = get_site_transient("update_plugins");
			
			if ($update_plugins) {
				
				// check the plugins tthat have updates
				if (is_array($update_plugins->response) && !empty ($update_plugins->response)) {
					
					foreach($update_plugins->response as $new_version) {
						
						// check if wordpress mobile pack is in the list
						if ($new_version->plugin == 'wordpress-mobile-pack/wordpress-mobile-pack.php'){
						  
							// return the new version number
							return $new_version->new_version;
                        }
					}
				}
			}
			
			//by default return null
			return null;		
        }
   }
}