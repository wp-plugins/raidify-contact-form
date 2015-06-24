<?php

/* 
 * This class process the email and sends it through wp_mail
 */

class ProcessMail{
    
    static $errors_fields = array();
    static $missing_fields = array();
    static $expected_fields = array('rcf-message');
    static $rcf_options_settings = array();
    static $required_fields = array();
    static $required_label;
    static $headers;
    static $mail_sent = false;
    static $message;
    static $message_wrap = 70;
    static $mail_to;
    static $mail_subject;
    
    function __construct() {
        
    }
    
    /**
     * Removes rcf_ prefix from the items
     * @return string 
     */
    static function rcf_remove_rcf_prefix($item){
        $no_prefix_name = substr($item, 4);
        return $no_prefix_name;
    }

    /**
     * Gets the options settings and assign them to arrays
     */
    static function rcf_get_options_settings(){
        self::$rcf_options_settings = get_option('rcf_admin_settings');
        self::$required_label = self::$rcf_options_settings['required-label'];
        self::$required_fields = self::$rcf_options_settings['required'];
        self::$mail_to = self::$rcf_options_settings['sendto']['user-email'];
    }
    
    /**
     * Checks the POST variable for missing and required fields
     */
    static function rcf_check_post_variables(){
        self::rcf_get_options_settings();
        $rcfPost = filter_input_array(INPUT_POST);
        foreach ($rcfPost as $key => $value) {
            $rcfTemp = is_array($value) ? $value : trim($value);
            $no_prefix_name = self::rcf_remove_rcf_prefix($key);

            if(empty($rcfTemp) && in_array($no_prefix_name, self::$required_fields)){
                self::$missing_fields[] = $no_prefix_name;
            }elseif (in_array($no_prefix_name, self::$required_fields)) {
                ${$no_prefix_name} = $rcfTemp;
            }
        }
    }

    /**
     * This function was not used but was intended to set a general error
     * for all the fields
     */
    static function rcf_set_general_form_error($error){
        if(self::$missing_fields || self::$errors_fields){
            echo $error;
        }
    }
    
    /**
     * This method sets the individual elements error messages and returns the respective error message
     * @return string 
     */
    static function rcf_set_form_element_error($value){        
            if (self::$missing_fields && in_array($value, self::$missing_fields)) { 
                return '<span class="warning">'.__('Please enter your', 'raidify-contact-form') .' '.__($value, 'raidify-contact-form').'</span>';                
            }elseif (isset (self::$errors_fields['rcf-email'])) {
                if('rcf-'.$value == 'rcf-email'){
                    return '<span class="warning">'.__('Invalid email address', 'raidify-contact-form').'</span>';
                }                
        }        
    }
    
    /**
     * Sets the required filed label
     * @return String returns what the admin has typed as the reqired label in the admin page
     */
    static function rcf_set_required_label($key){
        self::rcf_get_options_settings();
        if(in_array($key, self::$required_fields)){
            return ' '.self::$required_label;
        }
        
    }

    /**
     * Preserves user's input if the form is submited and there was an error
     */
    static function rcf_preserve_input_value($value){
        $post_value = filter_input(INPUT_POST, $value);
        if(isset($value) && !self::$mail_sent){
            return $post_value;
        }else if(isset($value) && self::$mail_sent){
            $post_value = array();
        }     
    }
    
    /**
     * Checks and validates the email address. If the email address is valids if adds
     * it to the header.
     */
    static function rcf_validate_email(){
        $email = filter_input(INPUT_POST, 'rcf-email');
        if(!empty($email)){
            $valid_email = filter_input(INPUT_POST, 'rcf-email', FILTER_VALIDATE_EMAIL);
            $name = filter_input(INPUT_POST, 'rcf-name',FILTER_SANITIZE_STRING);
            if($valid_email){
                self::$headers = 'From:' . $name . '<' . $valid_email . '>' . "\r\n";
                self::$headers .= 'Reply-To:' . $valid_email . "\r\n";   
            }else {
                self::$errors_fields['rcf-email'] = true;
            }
        }
    }
 
    /**
     * This function sets up the email sturcture and send the mail if 
     * no error or missing field is found.
     * It uses wp_mail to send the email.
     */
    static function rcf_send_mail(){
       
       if(!self::$missing_fields && !self::$errors_fields){
           self::$headers .= 'Content-Type: text/plain; charset=utf-8'. "\r\n";
           self::$mail_subject = filter_input(
                   INPUT_POST, 
                   'rcf-subject', 
                   FILTER_SANITIZE_STRING
                   );
           
           self::$message = '';
           $expected_fields = self::$expected_fields;
           foreach ($expected_fields as $item) {
               $temp = filter_input(INPUT_POST, $item);
               if(isset($temp) && !empty($temp)){
                   $val = $temp;  
               }  else {
                   $val = '';
               }
               if(is_array($val)){
                   $val = implode(', ', $val);
               }
               self::$message .= "$val\r\n\r\n";
           }           
           self::$message = wordwrap(
                   self::$message, 
                   self::$message_wrap
                   );
           
           self::$mail_sent = wp_mail(
                   self::$mail_to, self::$mail_subject, 
                   self::$message, self::$headers
                   );
       }
   } 
   
   /**
     * Displays a message to inform the user if the email was sent succesfully or not
     */
    static function rcf_check_mail_sent(){
       $rcf_post = filter_input_array(INPUT_POST);
       if($rcf_post && self::$mail_sent){
           echo __('Mail sent successfully', 'raidify-contact-form');
       }  else {
           echo __('Mail not sent', 'raidify-contact-form');
       }
   }

}

?>
