<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('smtp_page')){
    
class smtp_page{
    static $rcf_options = array();
    static $rcf_mailer = '';
    static $rcf_smtp_settings = array();
    static $rcf_new_smtp_settings = array();
    static $empty_smtp_options = array();
    static $smtp_error_message = '';
    
    function __construct() {
        include 'admin_tabs.php';
        $obj = new admin_tabs();
        echo $obj->add_tabs();
        $this->rcf_get_options();
        $this->rcf_save_button_clicked();
        $this->rcf_display_settings();
    }
          
    /**
     * Checks if the save button is clicked, gets all the options set by the user
     * and updates the wordpress options database
     * 
    */ 
    function rcf_save_button_clicked() {
        $save = filter_input(INPUT_POST, 'rcf_save_button');
        $rcfPost = filter_input_array(INPUT_POST);
        if (isset($save)) {
            foreach ($rcfPost as $key => $value) {
                $this->rcf_get_smtp_option($key, $value);
            }
            self::$rcf_options['mailer'] = self::$rcf_mailer;
            self::$rcf_options['smtp-option'] =  self::$rcf_new_smtp_settings;
            $this->rcf_check_smtp_options();
            //updates the options database
            update_option('rcf_admin_settings', self::$rcf_options);
        } else {
            return '';
        }
    }    
    
    /**
     * Gets the options data from the wordpress options database
     * and points $rcf_options to the data
     * 
    */ 
    function rcf_get_options(){
        self::$rcf_options = get_option('rcf_admin_settings');
    }
    
    /**
     * Displays the all elements for the fields table in the admin page
     * 
    */
    function rcf_display_settings(){
        ?>
        <div id="rcf_settings_form">
            <div class="rcf-instruction-wrapper">
                <?php echo __('Paste this Short code [rcf_contact_form] on the page or post you want this form to appear', 'raidify-contact-form'); ?>                            
            </div>
            
            <form method="post" action="">
                <div class="rcf-save-button-wrapper">
                    <div class="rcf-save-button-content">
                        <?php echo '<button class="rcf_save_button" name="rcf_save_button">'. __('Save Changes', 'raidify-contact-form') .'</button>'; ?>
                    </div>                            
                </div>
                
                <?php $this->rcf_generate_smtp_conf_table(); ?>
            </form>
            
        </div>
        
        <?php
        
    }    
    
    /**
     * gets the SMTP options and sets the $rcf_new_smtp_settings variable
     * 
    */ 
    function rcf_get_smtp_option($key, $value){
        
        switch ($key) {
            case 'rcf-mailer':
                self::$rcf_mailer = $value;
                break;
            case 'rcf-host-server':
                self::$rcf_new_smtp_settings['host-server'] = trim($value);
                break;
            case 'rcf-smtp-port':
                self::$rcf_new_smtp_settings['port-number'] = trim($value);
                break;
            case 'rcf-smtp-encryption':
                self::$rcf_new_smtp_settings['encryption'] = trim($value);
                break;
            case 'rcf-smtp-auth':
                self::$rcf_new_smtp_settings['authentication'] = trim($value);
                break;
            case 'rcf-smtp-from':
                self::$rcf_new_smtp_settings['from'] = trim($value);
                break;
            case 'rcf-smtp-from-name':
                self::$rcf_new_smtp_settings['from-name'] = trim($value);
                break;
            case 'rcf-smtp-username':
                self::$rcf_new_smtp_settings['username'] = trim($value);
                break;
            case 'rcf-smtp-password':
                self::$rcf_new_smtp_settings['password'] = trim($value);
                break;
            default:
                break;
        }
        
    }    
     
    /**
     * Generates the SMTP configuration table on the admin page
     * 
    */
    function rcf_generate_smtp_conf_table(){
        $mailer = self::$rcf_options['mailer'];
        $check_phpmail = '';
        $check_smtp = '';
        $check_auth = '';
        $check_noencrypt = '';
        $check_ssl = '';
        $check_tls = '';
        if($mailer == 'phpmail'){
           $check_phpmail = 'checked'; 
        }elseif($mailer == 'smtp'){
            $check_smtp = 'checked';
        }

        if(isset(self::$rcf_options['smtp-option']['authentication'])
                && !is_null(self::$rcf_options['smtp-option']['authentication'])
                && self::$rcf_options['smtp-option']['authentication'] == 'on'){
            $check_auth = 'checked';
        }else{
            $check_auth = '';
        }
        
        if(self::$rcf_options['smtp-option']['encryption'] == 'noencryption'){
            $check_noencrypt = 'checked';
        }elseif (self::$rcf_options['smtp-option']['encryption'] == 'ssl') {
            $check_ssl = 'checked';
        }elseif (self::$rcf_options['smtp-option']['encryption'] == 'tls') {
            $check_tls = 'checked';
        }
        $useSMTP = __('Use SMTP', 'raidify-contact-form');
        ?>

        <div class="rcf_php_mail_content">
            <div class="rcf-php-mail-settings-header">
                <?php 
                echo sprintf('<p><input type=%s name=%s id=%s value="%s" %s><span>%s</span></p>', 'radio', 'rcf-mailer', 'rcf-mailer', 'phpmail', $check_phpmail, __('Use Php mail', 'raidify-contact-form')); ?>
            </div>
            
        </div>
        
        <div id="rcf-smtp-setting-content">
            <div class="rcf-smtp-setting-instruction">
                <?php $this->rcf_generate_smtp_instructions(); ?>                
            </div>
            
            <div class="rcf-smtp-mail-settings-header">
                <?php 
                echo sprintf('<p style="margin-top: 30px;"><input type=%s name=%s id=%s value="%s" %s><span>%s</span></p>', 'radio', 'rcf-mailer', 'rcf-mailer', 'smtp', $check_smtp, $useSMTP);
                ?>
            </div>
            <div class="rcf-smtp-mail-settings-content">
                
                <p>
                    <span><?php echo __('SMTP Host Server', 'raidify-contact-form') . ' *'; ?></span>
                    <?php echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-host-server', 'rcf-host-server', self::$rcf_options['smtp-option']['host-server']); ?>
                </p>
                
                <p>
                    <span><?php echo __('SMTP Port Number', 'raidify-contact-form') . ' *' ; ?></span>
                    <?php echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-smtp-port', 'rcf-smtp-port', self::$rcf_options['smtp-option']['port-number']); ?>
                </p>
                
                <p>
                    <span><?php echo __('Use Encryption', 'raidify-contact-form'); ?></span><br>
                    <?php 
                        echo sprintf('<span class="encryption-settings"><input type=%s name=%s id=%s value="%s" %s>%s</span> ', 'radio', 'rcf-smtp-encryption', 'rcf-smtp-encryption', 'noencryption', $check_noencrypt, __('No Encryption', 'raidify-contact-form'));
                        echo sprintf('<span class="encryption-settings"><input type=%s name=%s id=%s value="%s" %s>%s</span> ', 'radio', 'rcf-smtp-encryption', 'rcf-smtp-encryption', 'ssl', $check_ssl, __('Use SSL', 'raidify-contact-form'));
                        echo sprintf('<span class="encryption-settings"><input type=%s name=%s id=%s value="%s" %s>%s</span>', 'radio', 'rcf-smtp-encryption', 'rcf-smtp-encryption', 'tls', $check_tls, __('Use TLS', 'raidify-contact-form'));                    
                    ?>
                    
                </p>  

                <p> 
                    <br>
                    <?php echo sprintf('<input type=%s name=%s id=%s %s>', 'checkbox', 'rcf-smtp-auth', 'rcf-smtp-auth', $check_auth); ?>
                    <span><?php echo __('Use SMTP Authentication', 'raidify-contact-form') ; ?></span>
                </p>                

                <p>
                    <br>
                    <span><?php echo __('From', 'raidify-contact-form') . ' *' ; ?></span>
                    <?php echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-smtp-from', 'rcf-smtp-from', self::$rcf_options['smtp-option']['from']); ?>
                </p>
                
                <p>
                    <span><?php echo __('From Name (Optional)', 'raidify-contact-form') ; ?></span>
                    <?php echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-smtp-from-name', 'rcf-smtp-from-name', self::$rcf_options['smtp-option']['from-name']); ?>
                </p>
                
                <p>
                    <span><?php echo __('Username', 'raidify-contact-form') .' *' ; ?></span>
                    <?php echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-smtp-username', 'rcf-smtp-username', self::$rcf_options['smtp-option']['username']); ?>
                </p>

                <p>
                    <span><?php echo __('Password', 'raidify-contact-form') .' *' ; ?></span>
                    <?php echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'password', 'rcf-smtp-password', 'rcf-smtp-password', self::$rcf_options['smtp-option']['password']); ?>
                </p>
            </div>
            
            
        </div>
        <?php
        
    }

    /**
     * Generates the SMTP instruction section on the admin page
     * 
    */
    function rcf_generate_smtp_instructions(){
        ?>
        <div id="smtp-instructions">
            <?php echo self::$smtp_error_message; ?>
            <ul><?php 
                echo '<li>' .__(' To use SMTP, fill the fields marked with "*" ', 'raidify-contact-form'). '</li>'
                    .'<li>' .__(' For the encryption system, "TLS" is not the same as "STARTTLS" ', 'raidify-contact-form'). '</li>'
                    .'<li>' .__(' If "Use SMTP Authentication" is unchecked, "Username" and "Password" will not be used ', 'raidify-contact-form'). '<li>'
                    .'<li>' .__(' If "From Name" is left blank, the name the user puts on the contact form name field will be used', 'raidify-contact-form'). '</li>';            
            ?>
            </ul>
            
        </div>
        <?php
        
    }

    /**
     * Checks if the SMTP options are not empty
     * 
    */
    function rcf_check_smtp_options(){
        $new_smtp_settings = self::$rcf_new_smtp_settings;
        foreach ($new_smtp_settings as $key => $value) {
            if (empty($value) && $key != 'from-name') {
                self::$empty_smtp_options[] = $key;
            }
        }
        //If SMTP options is empty, reset it back to phpmail
        if(!empty(self::$empty_smtp_options) && self::$rcf_options['mailer'] == 'smtp'){
            self::$rcf_options['mailer'] ='phpmail';
            $this->rcf_display_smtp_settings_error();
        }
    }

    /**
     * Displays the SMTP settings error message
     * 
    */
    function rcf_display_smtp_settings_error(){
        self::$smtp_error_message = '<p class="rcf-warning">'.__('To use SMTP fill the following SMTP options', 'raidify-contact-form').'</p>';
        $empty_smtp_options = self::$empty_smtp_options;
        foreach ($empty_smtp_options as $item) { 
            if($item == 'host-server'){
                self::$smtp_error_message .='<p class="rcf-warning rcf-warning-content">'.__('SMTP Host Server', 'raidify-contact-form').'</p>';
            }elseif ($item == 'port-number') {
                self::$smtp_error_message .='<p class="rcf-warning rcf-warning-content">'.__('SMTP Port Number', 'raidify-contact-form').'</p>';
            }elseif ($item == 'from') {
                self::$smtp_error_message .='<p class="rcf-warning rcf-warning-content">'.__('From', 'raidify-contact-form').'</p>';
            }elseif ($item == 'username') {
                self::$smtp_error_message .='<p class="rcf-warning rcf-warning-content">'.__('Username', 'raidify-contact-form').'</p>';
            }elseif ($item == 'password') {
                self::$smtp_error_message .='<p class="rcf-warning rcf-warning-content">'.__('Password', 'raidify-contact-form').'</p>';
            }
        }
    }   
    
}   
    
}