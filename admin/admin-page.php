<?php
/**
 * This is the admin class that displays the admin page
 */
class admin_page {
    static $rcf_options = array();
    static $rcf_input_elements = array();
    static $rcf_textarea_elements = array();
    static $rcf_required = array();
    static $rcf_placeholders = array();
    static $rcf_sendto = array();
    static $rcf_required_label ='';
    static $rcf_mailer = '';
    static $rcf_smtp_settings = array();
    static $rcf_new_smtp_settings = array();
    static $empty_smtp_options = array();
    static $smtp_error_message = '';

    /**
     * Constructs the admin-page object
     * 
    */    
    function __construct() {        
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
                $this->rcf_get_sendto($key, $value);
                $this->rcf_get_required_label($key, $value);
                $this->rcf_get_smtp_option($key, $value);
            }
            self::$rcf_options['required'] = self::$rcf_required;
            self::$rcf_options['placeholder'] = self::$rcf_placeholders;
            self::$rcf_options['sendto'] = self::$rcf_sendto;
            self::$rcf_options['required-label'] = self::$rcf_required_label;
            self::$rcf_options['mailer'] = self::$rcf_mailer;
            self::$rcf_options['smtp-option'] =  self::$rcf_new_smtp_settings;
            //checks that the required SMTP options are filled out by the user
            $this->rcf_check_smtp_options();
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
     * sets the text of the required lable
     * 
    */ 
    function rcf_get_required_label($key, $value){
        if($key == 'rcf-required-label'){
            self::$rcf_required_label = trim($value);
        }
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

        echo '<div id="rcf_send_to_content">';
        echo sprintf('<table class=%s>', 'rcf_send_to_table');
        echo '<tr>';
        echo sprintf('<td rowspan=%s id=%s>%s</td>', '2', 'rcf_table_user_info', $use_email);
        //admin user email address
        echo '<td><p>'.'( '.__('Admin Email account', 'raidify-contact-form').' )'.'</p>';
        echo sprintf('<input type=%s name=%s id=%s value="rcf-wpuser" %s>', 'radio', 'rcf-user-sendto', 'rcf-user-sendto', $checked_wpuser);
        echo sprintf('<select name=%s id=%s>', 'rcf_select_user', 'rcf_select_user');
        echo $this->rcf_get_users();
        echo '</select><hr></td>';
        echo '</tr>';
        
        echo '<tr>';
        //custom user email address
        echo '<td><p>'.'( '.__('Custom Email account', 'raidify-contact-form').' )'.'</p>';
        echo sprintf('<input type=%s name=%s id=%s value="rcf-customeuser" %s>', 'radio', 'rcf-user-sendto', 'rcf-user-sendto', $checked_customer);
        echo sprintf('<input type=%s name=%s id=%s value="%s">', 'text', 'rcf-custom-user', 'rcf-custom-user', $custom_email);
        echo '<p>'.__('Select the radio button and enter the email address that emails should be sent to, if left blank the admin email will he used', 'raidify-contact-form').'</p>'.'</td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
    }
    
    /**
     * Generates the input elements for the fields table in the admin page
     * 
    */
    function rcf_generate_input_element_table_body(){
        $input_elements = self::$rcf_options['input-elements'];
        foreach ($input_elements as $item) {
            $checked = "";
            if (in_array($item, self::$rcf_options['required'])) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            $placeholder = self::$rcf_options['placeholder'][$item];
            echo '<tr valign="top">';
            echo sprintf('<td>%s</td>', ucfirst(__($item, 'raidify-contact-form')));
            echo sprintf('<td><input type=%s name=%s class=%s %s></td>', 'checkbox', $item . '-required', 'rcf_checkbox', $checked);
            echo sprintf('<td><input type=%s name=%s class=%s value="%s"></td>', 'text', $item . '-placeholder', 'rcf_textbox', $placeholder);
            echo '</tr>';
        }
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
            echo '<tr valign="top">';
            echo sprintf('<td>%s</td>', ucfirst($item));
            echo sprintf('<td><input type=%s name=%s class=%s %s></td>', 'checkbox', $item . '-required', 'rcf_checkbox', $checked);
            echo sprintf('<td><input type=%s name=%s class=%s value="%s"></td>', 'text', $item . '-placeholder', 'rcf_textbox', $placeholder);
            echo '</tr>';
        }
    }

    /**
     *
     * 
    */
    function rcf_generate_elements_settings(){
        echo '<div id="rcf-fields-settings-content">';
        echo '<div>'.__('Fields settings table', 'raidify-contact-form').'</div>';
        echo sprintf('<table class=%s>', 'rcf_fields_settings');
        echo '<thead>';
        echo '<tr valign="top">';
        $fields = __('Fields', 'raidify-contact-form');
        $required = __('Required', 'raidify-contact-form');
        $placeholder = __('Placeholder', 'raidify-contact-form');
        $table_head = sprintf('<th width="210px" scope="row">%s</th> <th>%s</th> <th>%s</th>', $fields, $required, $placeholder);
        echo $table_head;
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        $this->rcf_generate_input_element_table_body();
        $this->rcf_generate_textarea_element_table_body();
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    /**
     * Displays the all elements for the fields table in the admin page
     * 
    */
    function rcf_display_settings(){
        echo '<div id="rcf_settings_form">';
        echo __('Paste this Short code "[rcf_contact_form]" on the page or post you want this form to appear', 'raidify-contact-form');
        echo self::$smtp_error_message;
        echo '<form method="post" action="">';
        $this->rcf_generate_sendto_form();
        echo '<hr>';
        $this->rcf_generate_elements_settings();
        $this->rcf_generate_required_label();
        echo '<hr>';
        $this->rcf_generate_smtp_conf_table();
        echo '<p><button class="rcf_save_button" name="rcf_save_button">'. __('Save Changes', 'raidify-contact-form') .'</button></p>';
        echo '</form>';
        echo '</div>';
    }
 
    /**
     * Generates the required label settngs
     * 
    */
    function rcf_generate_required_label(){
        $required_label_value = self::$rcf_options['required-label'];
        echo '<div id="rcf-required-label-content">';
        echo '<p>'.__('Use "Required Label" to set the text that appears on the required fields on the contact form', 'raidify-contact-form').'</p>';
        echo '<p><span>' . __('Required Label', 'raidify-contact-form') . '</span>';  
        echo sprintf('<input type=%s name=%s id=%s value="%s" ></p>', 'text', 'rcf-required-label', 'rcf-required-label', $required_label_value);
        echo '</div>';
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
        
        echo sprintf('<p><input type=%s name=%s id=%s value="%s" %s><span>%s</span></p>', 'radio', 'rcf-mailer', 'rcf-mailer', 'phpmail', $check_phpmail, __('Use Php mail', 'raidify-contact-form'));
        echo '<hr>';
        echo '<div id="rcf-smtp-settng-content">';
        $this->rcf_generate_smtp_instructions();
        echo sprintf('<p style="margin-top: 30px;"><input type=%s name=%s id=%s value="%s" %s><span>%s</span></p>', 'radio', 'rcf-mailer', 'rcf-mailer', 'smtp', $check_smtp, $useSMTP);
        echo '<table id="rcf-smtp-option">';
        echo '<tr>';
        echo '<td><span>' . __('SMTP Host Server', 'raidify-contact-form') . ' *' .'</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'text', 'rcf-host-server', 'rcf-host-server', self::$rcf_options['smtp-option']['host-server']);
        echo '</tr>';
        echo '<tr>';
        echo '<td><span>' . __('SMTP Port Number', 'raidify-contact-form') . ' *' . '</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'text', 'rcf-smtp-port', 'rcf-smtp-port', self::$rcf_options['smtp-option']['port-number']);
        echo '</tr>';
        echo '<tr>';
        echo '<td><span>' . __('Use Encryption', 'raidify-contact-form') . ' </span></td>';
        echo sprintf('<td>%s<input type=%s name=%s id=%s value="%s" %s> | ', __('No Encryption', 'raidify-contact-form'), 'radio', 'rcf-smtp-encryption', 'rcf-smtp-encryption', 'noencryption', $check_noencrypt);
        echo sprintf('%s<input type=%s name=%s id=%s value="%s" %s> | ', __('Use SSL', 'raidify-contact-form'), 'radio', 'rcf-smtp-encryption', 'rcf-smtp-encryption', 'ssl', $check_ssl);
        echo sprintf('%s<input type=%s name=%s id=%s value="%s" %s> </td>', __('Use TLS', 'raidify-contact-form'), 'radio', 'rcf-smtp-encryption', 'rcf-smtp-encryption', 'tls', $check_tls);
        echo '</tr>';
        echo '<tr>';
        echo '<td><span>'. __('Use SMTP Authentication', 'raidify-contact-form') .'</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s %s></td>', 'checkbox', 'rcf-smtp-auth', 'rcf-smtp-auth', $check_auth);
        echo '</tr>';
        echo '<tr>';
        echo '<td><span>'. __('From', 'raidify-contact-form') .' *'.'</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'text', 'rcf-smtp-from', 'rcf-smtp-from', self::$rcf_options['smtp-option']['from']);
        echo '</tr>';
        echo '<tr>';
        echo '<td><span>'. __('From Name (Optional)', 'raidify-contact-form') .'</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'text', 'rcf-smtp-from-name', 'rcf-smtp-from-name', self::$rcf_options['smtp-option']['from-name']);
        echo '</tr>';
        echo '<tr>';
        echo '<td><span>'. __('Username', 'raidify-contact-form') .' *'.'</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'text', 'rcf-smtp-username', 'rcf-smtp-username', self::$rcf_options['smtp-option']['username']);
        echo '</tr>';
        echo '<td><span>'. __('Password', 'raidify-contact-form') .' *'.'</span></td>';
        echo sprintf('<td><input type=%s name=%s id=%s value="%s"></td>', 'password', 'rcf-smtp-password', 'rcf-smtp-password', self::$rcf_options['smtp-option']['password']);
        echo '</tr>';
        echo '</table>';
        echo '</div>';
    }

    /**
     * Generates the SMTP instruction section on the admin page
     * 
    */
    function rcf_generate_smtp_instructions(){
        echo '<div id="smtp-instructions">';
        echo '<p>'.__(' To use SMTP, fill the fields marked with "*" ', 'raidify-contact-form').'</p>';
        echo '<p>'.__(' For the encryption system, "TLS" is not the same as "STARTTLS" ', 'raidify-contact-form').'</p>';
        echo '<p>'.__(' If "Use SMTP Authentication" is unchecked, "Username" and "Password" will not be used ', 'raidify-contact-form').'</p>';
        echo '<p>'.__(' If "From Name" is left blank, the name the user puts on the contact form name field will be used', 'raidify-contact-form').'</p>';
        echo self::$smtp_error_message;
        echo '</div>';
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

?>
