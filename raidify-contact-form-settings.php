<?php

/* 
 * This is the settings class. This class adds the menu link on the wordpress dashboard,
 * registers the sytlesheet,and creates a new object of the admin page if the current user
 * has the admin privillages
 * 
 */

if(!class_exists('Raidify_contact_form_settings')){
    
    class Raidify_contact_form_settings{
        
        var $rcf_options = array();

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
            
            $this->rcf_options = get_option('rcf_admin_settings');
            
            if($this->rcf_options['google-recaptcha']['rcf_use_google_recaptcha'] == 'on'){
                add_action( 'wp_enqueue_scripts', array(&$this, 'rcf_register_google_recaptcha_script') );
            }else{
                return;
            }            
            
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

            add_menu_page(
                    $page_title, 
                    $menu_title, 
                    $capability, 
                    $menu_slug, 
                    array(
                        &$this,
                        $function
                    )
                    );
            
            add_submenu_page( 'rcf_settings_page', $page_title, 'Fields', $capability, 'rcf_settings_page', array(&$this, 'rcf_settings_page') );
            add_submenu_page( 'rcf_settings_page', $page_title, 'Email', $capability, 'rcf_email_settings', array(&$this, 'rcf_email_page') );
            add_submenu_page( 'rcf_settings_page', $page_title, 'SMTP', $capability, 'rcf_smtp_settings', array(&$this, 'rcf_smtp_page') );
            add_submenu_page( 'rcf_settings_page', $page_title, 'recaptcha', $capability, 'rcf_recaptcha_settings', array(&$this, 'rcf_recaptcha_page') );
        }
        
        /**
         * Checks of current user has admin privellages and creates a new admin page if true
         */
        public function rcf_settings_page() {
            //If current user does not have admin capabilities dispay message
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            //display field settings page
            include(sprintf("%s/admin/field.php", dirname(__FILE__))); 
            new field();
            
        }
        
        
        /**
         * Checks of current user has admin privellages and creates a new email page if true
         */
        public function rcf_email_page() {
            //If current user does not have admin capabilities dispay message
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            //display email settings page            
            include(sprintf("%s/admin/email.php", dirname(__FILE__)));  
            new email_page();            
        }
        
        /**
         * Checks of current user has admin privellages and creates a new smtp page if true
         */
        public function rcf_smtp_page() {
            //If current user does not have admin capabilities dispay message
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            //display smtp settings page
            include(sprintf("%s/admin/smtp.php", dirname(__FILE__)));  
            new smtp_page();            
        }
        
        /**
         * Checks of current user has admin privellages and creates a new recaptcha page if true
         */
        public function rcf_recaptcha_page() {
                        //If current user does not have admin capabilities dispay message
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            //display google recaptcha settings page
            include(sprintf("%s/admin/reCaptcha.php", dirname(__FILE__)));  
            new reCaptcha();            
        }
        
       
        /**
         * function that registers and enqueue style
         */
        function rcf_style() {
                wp_register_style(
                        'raidify_admin_css', 
                        MYPLUGIN_PLUGIN_URL . '/css/admin-style.css',
                        false,
                        '1.0.0'
                        );
                
                wp_enqueue_style('raidify_admin_css');
        }
        
        function rcf_register_google_recaptcha_script(){
            wp_register_script( 'rcf-google-recaptcha', 'https://www.google.com/recaptcha/api.js' );
            wp_enqueue_script( 'rcf-google-recaptcha' );
        }
        
        
        function admin_tabs($tabs, $current = NULL) {
            if (is_null($current)) {
                if (isset($_GET['page'])) {
                    $current = $_GET['page'];
                }
            }
            $content = '';
            $content .= '<h2 class="nav-tab-wrapper">';
            foreach ($tabs as $location => $tabname) {
                if ($current == $location) {
                    $class = ' nav-tab-active';
                } else {
                    $class = '';
                }
                $content .= '<a class="nav-tab' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
            }
            $content .= '</h2>';
            return $content;
        }

    }
    
}

?>
