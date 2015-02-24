<?php

/* 
 * This is the settings class. This class adds the menu link on the wordpress dashboard,
 * registers the sytlesheet,and creates a new object of the admin page if the current user
 * has the admin privillages
 * 
 */

if(!class_exists('Raidify_contact_form_settings')){
    
    class Raidify_contact_form_settings{
        
        /**
         * Contructor used to register actions
         */
        public function __construct() {
            //define my plugin name
            if (!defined('MYPLUGIN_PLUGIN_NAME')) {
                define('MYPLUGIN_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
            }
            
            //define my plugin url
            if (!defined('MYPLUGIN_PLUGIN_URL')) {
                define('MYPLUGIN_PLUGIN_URL', WP_PLUGIN_URL . '/' . MYPLUGIN_PLUGIN_NAME);
            }          
            
            //Register actions
            add_action('admin_menu', array(&$this, 'rcf_add_menu'));
            add_action('admin_init', array(&$this, 'rcf_style'));
        }
        
        /**
         * Function that sets the menu of the admin page, and slug-name
         */
        public function rcf_add_menu() {
            // Add the top-level admin menu
            $page_title = 'Raidify Contact Form Settings';
            $menu_title = 'Raidify Contact Form';
            $capability = 'manage_options';
            $menu_slug = 'rcf_settings_page';
            $function = 'rcf_settings_page';

            add_menu_page($page_title, $menu_title, $capability, $menu_slug, array(&$this,$function));
        }
        
        /**
         * Checks of current user has admin privellages and creates a new admin page if true
         */
        public function rcf_settings_page() {
            //If current user does not have admin capabilities dispay message
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            //display admin page
            include(sprintf("%s/admin/admin-page.php", dirname(__FILE__)));  
            new admin_page();
        }

        /**
         * function that registers and enqueue style
         */
        function rcf_style() {
                wp_register_style('custom_wp_admin_css', MYPLUGIN_PLUGIN_URL . '/css/admin-style.css', false, '1.0.0');
                wp_enqueue_style('custom_wp_admin_css');
        }

    }
    
}

?>
