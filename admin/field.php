<?php
/**
 * This is the field's class that displays the fields page
 */

if(!class_exists('field')){
class field {
    static $rcf_options = array();
    static $rcf_input_elements = array();
    static $rcf_textarea_elements = array();
    static $rcf_required = array();
    static $rcf_placeholders = array();
    static $rcf_required_label ='';

    /**
     * Constructs the admin-page object
     * 
    */    
    function __construct() {  
        include 'admin_tabs.php';
        $obj = new admin_tabs();
        echo $obj->add_tabs();
        $this->rcf_get_options();
        $this->rcf_save_button_clicked();
        $this->rcf_display_settings();
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
     * Checks if the save button is clicked, gets all the options set by the user
     * and updates the wordpress options database
     * 
    */ 
    function rcf_save_button_clicked() {
        $save = filter_input(INPUT_POST, 'rcf_save_button');
        $rcfPost = filter_input_array(INPUT_POST);
        if (isset($save)) {
            foreach ($rcfPost as $key => $value) {
                $this->rcf_get_required_elements($key);
                $this->rcf_get_element_with_placeholder($key, $value);
                $this->rcf_get_required_label($key, $value);
            }
            self::$rcf_options['required'] = self::$rcf_required;
            self::$rcf_options['placeholder'] = self::$rcf_placeholders;
            self::$rcf_options['required-label'] = self::$rcf_required_label;
            //updates the options database
            update_option('rcf_admin_settings', self::$rcf_options);
        } else {
            return '';
        }
    }
    
    /**
     * gets the elements that have been checked as required, removes the -required surfix
     *  and updates the $rcf_required array with the key from the post array
     * 
    */ 
    function rcf_get_required_elements($key){
        $required_key = substr($key, 0, -9);
        if ($key == $required_key . '-required') {
            self::$rcf_required[] = $required_key;
        }
    }

    /**
     * gets the elements that have placeholder set, removes the -placeholder surfix
     *  and updates the $rcf_placeholders array with the key from the post array
     * 
    */ 
    function rcf_get_element_with_placeholder($key, $value){
        //removes the -placeholder surfix
        $placeholder_key = substr($key, 0, -12);
        if ($key == $placeholder_key . '-placeholder') {
            self::$rcf_placeholders[$placeholder_key] = trim($value);
        }
    }

    /**
     * sets the text of the required lable
     * 
    */ 
    function rcf_get_required_label($key, $value){
        if($key == 'rcf-required-label'){
            self::$rcf_required_label = trim($value);
        }
    }
    
    /**
     * Generates the input elements for the fields table in the admin page
     * 
    */
    function rcf_generate_input_element_table_body(){
        $input_elements = self::$rcf_options['input-elements'];
        ?>
        
        <?php 
        foreach ($input_elements as $item) {
            $checked = "";
            if (in_array($item, self::$rcf_options['required'])) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            $placeholder = self::$rcf_options['placeholder'][$item];
            ?>
            
                <div class="rcf-admin-fields-settings-header">
                    <?php echo sprintf('%s', ucfirst(__($item, 'raidify-contact-form'))); ?>
                </div>
                
                <div class="rcf-admin-fields-settings-content">
                    <p>
                    <span class="rcf-admin-field-title"><?php echo __('Required', 'raidify-contact-form'); ?></span>
                    <?php echo sprintf('<input type=%s name=%s class=%s %s>', 'checkbox', $item . '-required', 'rcf_checkbox', $checked); ?>
                    </p>
                    
                    <p>
                    <span class="rcf-admin-field-title"><?php echo __('Placeholder', 'raidify-contact-form'); ?></span>
                    <?php echo sprintf('<input type=%s name=%s class=%s value="%s">', 'text', $item . '-placeholder', 'rcf_textbox', $placeholder); ?>
                    </p>
                </div>
                
            
            <?php
        }
        ?>

        <?php
    }

    /**
     * Generates the textarea elements for the fields table in the admin page
     * 
    */
    function rcf_generate_textarea_element_table_body(){
        $text_area_elements = self::$rcf_options['text-area-elements'];
        foreach ($text_area_elements as $item) {
            $checked = "";
            if (in_array($item, self::$rcf_options['required'])) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            $placeholder = self::$rcf_options['placeholder'][$item];                      
                        ?>

                <div class="rcf-admin-fields-settings-header">
                    <?php echo sprintf('%s', ucfirst(__($item, 'raidify-contact-form'))); ?>
                </div>
                
                <div class="rcf-admin-fields-settings-content">
                    <p>
                        <span class="rcf-admin-field-title"><?php echo __('Required', 'raidify-contact-form'); ?></span>
                    <?php echo sprintf('<input type=%s name=%s class=%s %s>', 'checkbox', $item . '-required', 'rcf_checkbox', $checked); ?>
                    </p>
                    
                    <p>
                    <span class="rcf-admin-field-title"><?php echo __('Placeholder', 'raidify-contact-form'); ?></span>
                    <?php echo sprintf('<input type=%s name=%s class=%s value="%s">', 'text', $item . '-placeholder', 'rcf_textbox', $placeholder); ?>
                    </p>
                </div>
                
            <?php
        }
    }

    /**
     *
     * 
    **/
    function rcf_generate_elements_settings(){
        ?>
            <div id="rcf-fields-settings-content">
                <div class="rcf-admin-fields-settings">
                    <div class="rcf-fields-settings-title">
                    <?php echo __('Fields settings', 'raidify-contact-form'); ?>
                </div>
                <?php
                    $this->rcf_generate_input_element_table_body();
                    $this->rcf_generate_textarea_element_table_body();
                ?>
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
                                <?php echo '<button class="rcf_save_button" name="rcf_save_button">'. __('Save Changes', 'raidify-contact-form') .'</button>'; ?>
                            </div>                            
                        </div>
                        <?php 
                            $this->rcf_generate_elements_settings();
                            $this->rcf_generate_required_label();                        
                        ?>
                    </form>                    
                </div>
        <?php
    }
 
    /**
     * Generates the required label settngs
     * 
    */
    function rcf_generate_required_label(){
        $required_label_value = self::$rcf_options['required-label'];
        ?>
        <div id="rcf-required-label-content">
            <div class="rcf-required-label-instruction">
                <p><?php echo __('Use "Required Label" to set the text that appears on the required fields on the contact form', 'raidify-contact-form'); ?></p>
            </div>
            
            <div>
                <p><span><?php echo __('Required Label', 'raidify-contact-form'); ?></span>
                <?php echo sprintf('<input type=%s name=%s id=%s value="%s" ></p>', 'text', 'rcf-required-label', 'rcf-required-label', $required_label_value); ?>
                </p>
            </div>
        </div>
        <?php

    }
   
}

}

