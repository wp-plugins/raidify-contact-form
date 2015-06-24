<?php

/*
 * Plugin Name: Raidify Contact Form
 * Plugin URI: http://raidify.com/raidify-contact-form/
 * Description: Raidify contact form is a free customizable contact form with SMTP (Simple Mail Transfer Protocol) support.
 * Author: Olaleye Osunsanya
 * Version: 1.0.1
 * Author URI: http://raidify.com/
 * Text Domain: raidify-contact-form
 * Domain Path: /languages/
 *      
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('Raidify_Contact_Form')){
    
    class Raidify_Contact_Form{
        
        /**
         * Construct the plugin object
         */
        public function __construct() {
            $plugin_basename = plugin_basename(__FILE__);

            if (!defined('MYPLUGIN_VERSION_KEY')) {
                define('MYPLUGIN_VERSION_KEY', 'myplugin_version');
            }

            if (!defined('MYPLUGIN_VERSION_NUM')) {
                define('MYPLUGIN_VERSION_NUM', '1.0.0');
            }
            
            require_once (sprintf("%s/raidify-contact-form-settings.php", dirname(__FILE__)));
            new Raidify_contact_form_settings();

            add_option(MYPLUGIN_VERSION_KEY, MYPLUGIN_VERSION_NUM);
            
            //register shortcode
            add_shortcode( 
                    'rcf_contact_form', 
                    array( 
                        $this, 
                        'rcf_shortCode' 
                        ) 
                    );
            
            //add settings link to plugin on plugin page            
            add_filter(
                    "plugin_action_links_$plugin_basename", 
                    array(
                        $this, 
                        'plugin_settings_link'
                        )
                    );
            
            //load the text domain for internationalization
            add_action('plugins_loaded', array($this, 'rcf_load_textdomain'));
            
        }
        
        /**
         * Activate the plugin
         */
        public static function rcf_activate(){
           if (!current_user_can('activate_plugins')) {
                return;
            }

            $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
            check_admin_referer("activate-plugin_{$plugin}");
        }

        /**
         * Deactivate the plugin
         */ 
        public static function rcf_deactivate(){
            if (!current_user_can('activate_plugins')) {
                return;
            }
            $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
            check_admin_referer("deactivate-plugin_{$plugin}");
        }
        
        /**
         * set the plugin settings link
         * @param string $links
         * @return string $links
         */
        function plugin_settings_link($links) {
            $settings_link = '<a href="admin.php?page=rcf_settings_page">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }
        
        /**
         * Function that generates the shortcode and creates a new object
         * of the contact form, check if the form is submitted and also 
         * displays the form.
         * 
         * @return object the content of the output buffer and ends output buffering
         */ 
        public function rcf_shortCode() {
            ob_start();
            include_once 'raidify-contact-form-display.php';
            $rcfDisplay = new RaidifyContactFormDisplay();
            $rcfDisplay->rcf_check_if_submitted();
            $rcfDisplay->rcf_display_form();
            return ob_get_clean();
        }
		
        /**
         * This function loads the language files form their folder.
         */ 
	function rcf_load_textdomain(){
        load_plugin_textdomain(
                'raidify-contact-form', 
                false, 
                dirname(plugin_basename(__FILE__)).'/languages'
                );        
    }
	
    }    
}


/**
 * Checks of the class is loaded
 */
if(class_exists('Raidify_Contact_Form')){
    //Registers the activation method
    register_activation_hook(__FILE__, array('Raidify_Contact_Form', 'rcf_activate'));  
    //Registers the deactivation method
    register_deactivation_hook(__FILE__, array('Raidify_Contact_Form', 'rcf_deactivate'));
    
    //Instantiate the plugin class
    $raidify_contact_form = new Raidify_Contact_Form();
    
     /**
     * Check for register_uninstall_hook hook
     */
    if ( function_exists('register_uninstall_hook') ){
	register_uninstall_hook(__FILE__, 'rcf_uninstall');
}
 /**
 * Delete options in database after unistall
 */
    function rcf_uninstall() {
        delete_option('rcf_admin_settings');
    }

}

?>