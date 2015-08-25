<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reCaptcha
 *
 * @author Olaleye
 */

if(!class_exists('reCaptcha')){
class reCaptcha {
    var $rcf_options = array();
    var $usegrc;
    var $sitekey;
    var $secretkey;
    var $checked;
    
    function __construct() {
        include 'admin_tabs.php';
        $obj = new admin_tabs();
        echo $obj->add_tabs();
        $this->rcf_options = get_option('rcf_admin_settings');
        $this->usegrc = $this->rcf_options['google-recaptcha']['rcf_use_google_recaptcha'];
        $this->sitekey = $this->rcf_options['google-recaptcha']['rcf_gr_sitekey'];
        $this->secretkey = $this->rcf_options['google-recaptcha']['rcf_gr_secretkey'];
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
            $this->rcf_options['google-recaptcha']['rcf_use_google_recaptcha'] = isset($rcfPost['rcf_use_google_recaptcha']) ? $rcfPost['rcf_use_google_recaptcha'] : '';
            $this->rcf_options['google-recaptcha']['rcf_gr_sitekey'] = trim($rcfPost['rcf_gr_sitekey']); 
            $this->rcf_options['google-recaptcha']['rcf_gr_secretkey'] = trim($rcfPost['rcf_gr_secretkey']); 
            
            update_option('rcf_admin_settings', $this->rcf_options);
            
            $this->usegrc = isset($rcfPost['rcf_use_google_recaptcha']) ? $rcfPost['rcf_use_google_recaptcha'] : '';
            $this->sitekey = trim($rcfPost['rcf_gr_sitekey']);
            $this->secretkey = trim($rcfPost['rcf_gr_secretkey']);
        } else {
            return '';
        }
    }    
    
    function rcf_update_checkbox($checkbox){
        return $checkbox =="on" ? 'checked' : '';
    }
    
    function rcf_generate_recaptcha_form(){

        ?>
        <form>
            <div class="rcf_form_content">
                <div class="rcf_form_wrapper">
                    <input type="checkbox" name="rcf_use_google_recaptcha" id="rcf_chkbx" <?php echo $this->rcf_update_checkbox($this->usegrc); ?>/>
                    <span><?php echo __('Use Google Recaptcha', 'raidify-contact-form'); ?></span>
                    
                    <p>
                        <label for="sitekey"><?php echo __('Site Key', 'raidify-contact-form'); ?></label>
                        <input type="text" name="rcf_gr_sitekey" value="<?php echo $this->sitekey; ?>" class="rcf_inputfield" placeholder="Enter site key">
                    </p>
                    
                    <p>
                        <label for="secretkey"><?php echo __('Secret Key', 'raidify-contact-form'); ?></label>
                        <input type="password" name="rcf_gr_secretkey" value="<?php echo $this->secretkey ?>" class="rcf_inputfield" placeholder="Enter secret key">
                    </p>
                </div>
                                
            </div>

        </form>
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
                <?php $this->rcf_generate_recaptcha_form(); ?>
            </form>

        </div>
        <?php

    }
    
    
}

}