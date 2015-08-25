<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('email_page')){

class email_page{
    static $rcf_options = array();
    static $rcf_sendto = array();
    
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
                $this->rcf_get_sendto($key, $value);
            }
            self::$rcf_options['sendto'] = self::$rcf_sendto;
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
     * gets the email that will be used to recieve the mails
     * 
    */
    function rcf_get_sendto($key, $value){
        $rcf_wpuser = filter_input(INPUT_POST, 'rcf_select_user');
        $rcf_custom_user = filter_input(INPUT_POST, 'rcf-custom-user');
        $sendto_key = substr($key, 0, -7);
        if ($key == $sendto_key . '-sendto') {
            if ($value == 'rcf-wpuser') {
                self::$rcf_sendto['user'] = $value;
                self::$rcf_sendto['user-email'] = $rcf_wpuser;
            } else if($value == 'rcf-customeuser') {
                self::$rcf_sendto['user'] = $value;
                self::$rcf_sendto['user-email'] = trim($rcf_custom_user);
                if(self::$rcf_sendto['user-email'] == ''){
                    //reset sento array back to default value
                   self::$rcf_sendto['user'] = 'rcf-wpuser';
                    self::$rcf_sendto['user-email'] = $rcf_wpuser; 
                }
            } 
        }
    }

    /**
     * get's the admin user name and email that will be used to receive mails
     * 
    */
    function rcf_get_users(){
        $users = get_users( 
                array( 
                    'fields' => array( 
                        'display_name', 
                        'user_email' 
                        ) 
                    )
                );
        
        foreach ($users as $user) {
            return sprintf(
                    '<option value=%s>%s</option>',
                    esc_html( $user->user_email ) ,
                    esc_html( $user->display_name )
                    );
        }
    }

    /**
     * Generates the sendto form
     * 
    */
    function rcf_generate_sendto_form(){
	$use_email = __('Use this email address to receive mails', 'raidify-contact-form');
        $custom_email = "";
        $checked_wpuser = "";
        $checked_customer = "";
        if(self::$rcf_options['sendto']['user'] == 'rcf-wpuser'){
           $checked_wpuser = "checked";
           $checked_customer = "";             
        }else if(!is_null(self::$rcf_options['sendto']['user'] == 'rcf-customeuser')){
            $checked_wpuser = "";
            $checked_customer = "checked";
            $custom_email = self::$rcf_options['sendto']['user-email'];
        }
        ?>
        <div id="rcf_send_to_content">
            <div class="rcf_send_to_instruction">
                <?php echo $use_email;?>
            </div>
            
            <div class="rcf-email-fields-settings-content">
            <div class="rcf_send_to_admin_mail_title">
                <p><?php echo __('Admin Email Account', 'raidify-contact-form'); ?></p>
            </div>
            
            <div class="rcf_send_to_admin_mail">
                <?php echo sprintf('<input type=%s name=%s id=%s value="rcf-wpuser" %s>', 'radio', 'rcf-user-sendto', 'rcf-user-sendto', $checked_wpuser); ?>
                <select name="rcf_select_user" id="rcf_select_user"><?php echo $this->rcf_get_users(); ?></select>
            </div>
            
            <!-- Custom email address -->
            <div class="rcf_send_to_Custom_mail_title">
                <p><?php echo __('Custom Email Account', 'raidify-contact-form'); ?></p>
            </div>
            
            <div class="rcf_send_to_Custom_mail">
                <?php echo sprintf('<input type=%s name=%s id=%s value="rcf-customeuser" %s>', 'radio', 'rcf-user-sendto', 'rcf-user-sendto', $checked_customer); ?>
                <?php echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-custom-user', 'rcf-custom-user', $custom_email); ?>
                <p>
                    <?php echo __('Select the radio button and enter the email address that emails should be sent to, if left blank the admin email will be used', 'raidify-contact-form') ?>
                </p>
            </div>
            
            </div>
        </div>
        <?php
    }
    
    /**
     * Displays the all elements for the fields table in the admin page
     * 
    */
    function rcf_display_settings(){
        ?>
        <div id="rcf_settings_form">
            <form method="post" action="">
                <div class="rcf-instruction-wrapper">
                    <?php echo __('Paste this Short code [rcf_contact_form] on the page or post you want this form to appear', 'raidify-contact-form'); ?>                            
                </div>
                
                <div class="rcf-save-button-wrapper">
                    <div class="rcf-save-button-content">
                        <?php echo '<button class="rcf_save_button" name="rcf_save_button">' . __('Save Changes', 'raidify-contact-form') . '</button>'; ?>
                     </div>                            
                </div>
                <?php $this->rcf_generate_sendto_form(); ?>
            </form>

        </div>
        <?php

    }
    
    
}

}