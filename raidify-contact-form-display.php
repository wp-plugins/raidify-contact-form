<?php

/*
 * This class contains codes that displays the contact form
 * It also contains the SMTP settings.
 */


if (!class_exists('RaidifyContactFormDisplay')) {

    class RaidifyContactFormDisplay {

        var $send = 'rcf_submit';
        var $error = '';
        var $default_input_type = 'text';
        var $rcf_input_elements = array();
        var $rcf_text_area_element = array();
        var $rcf_input_type = array(
            'color', 
            'date',
            'datetime',
            'datetime-local',
            'email',
            'month',
            'number',
            'range',
            'search',
            'tel',
            'time', 
            'url',
            'week'
            );
        
        var $placeholder = array();
        
        /**
         * Construct the contact form display object
         * It also sets the SMTP options and adds the SMPT action
         * 
         * 
        */
        function __construct() {
            include 'raidify-contact-form-processmail.php';

            $options = get_option('rcf_admin_settings');
            $this->rcf_input_elements = $options['input-elements'];
            $this->rcf_text_area_element = $options['text-area-elements'];
            $this->placeholder = $options['placeholder'];
            
            $smtp_option = $options['smtp-option'];

            define('RCF_MAILER', $options['mailer']);
            if(RCF_MAILER == 'smtp'){
                define('RCF_SMTP_PORT_NUMBER', $smtp_option['port-number']);
                define('RCF_SMTP_PASSWORD', $smtp_option['password']);
                define('RCF_SMTP_HOST_SERVER', $smtp_option['host-server']);
                define('RCF_SMTP_FROM', $smtp_option['from']);
                define('RCF_SMTP_FROMNAME', $smtp_option['from-name']);
                define('RCF_SMTP_ENCRYPTION', $smtp_option['encryption']);
                define('RCF_SMTP_AUTHENTICATION', $smtp_option['authentication']);
                define('RCF_SMTP_USERNAME', $smtp_option['username']);
            }           

            add_action('phpmailer_init', array($this,'send_smtp_email'));
            add_filter( 'wp_mail_from_name', array($this,'rcf_my_mail_from_name'));
        }

        public function get_send() {
            return $this->send;
        }

        public function set_send($send) {
            $this->send = $send;
        }
        
        /**
         * This function gets the placeholder and assigns it to 
         * the respective elements
         * 
         * @return String the placeholder
        */        
        private function rcf_get_placeholder($element){
            $placeholders = $this->placeholder;
            foreach ($placeholders as $key => $value) {
                if($key == $element){
                    return $value;
                }
            }
        }

        /* --------------------------------------------------------------
          >>> Function to display the html form
          ----------------------------------------------------------------
         * This function includes the labels and input types for
         * Name, Email, Subject, Message, and the Submit buttons
         */
        public function rcf_display_form() { 
            echo '<div id="rcf-contact-form-display">';
            $request_uri = filter_input(
                    INPUT_SERVER, 
                    'REQUEST_URI', 
                    FILTER_SANITIZE_STRING
                    );
            
            echo '<form id="feedback" method="post" action="'
                    . esc_url($request_uri) 
                    .'#rcf-contact-form-display'
                    .'">'
                    ;
            
            $this->rcf_generate_form_input_elements();
            $this->rcf_generate_form_text_area_elements(60, 8);
            $this->set_send("rcf_submit");
            echo '<p><input type="submit" id="rcf-submit" name="' 
                    . $this->get_send() 
                    . '" class="rcf-form" value=' 
                    .__('send', 'raidify-contact-form')
                    . '></p>'
                    ;
            
            echo '</form>';
            echo '</div>';
        }

        /* --------------------------------------------------------------
          >>> Function to encode HTML values from the form
          ----------------------------------------------------------------
         * This function encodes the values of input types for
         * Name, Email, Subject, Message, and the Submit buttons
         */
        function rcf_encode_values($value) {
            $encodedValue = filter_input(INPUT_POST, $value);
            return (isset($encodedValue)) ? esc_attr($encodedValue) : '';
        }

        /**
         * Generates the input elements
         * 
        */
        private function rcf_generate_form_input_elements() {
            $input_elements = $this->rcf_input_elements;
            foreach ($input_elements as $item) {
                $this->rcf_generate_element_type($item);
                echo '<p><label for="' . $item . '">' 
                        . ucfirst(__($item, 'raidify-contact-form')) 
                        .ProcessMail::rcf_set_required_label($item)
                        . ' : ' 
                        . ProcessMail::rcf_set_form_element_error($item)
                        . '</label><br>'
                        
                . '<input type="' . $this->default_input_type . '" id="' .'rcf-'
                        . $item . '" name="' 
                        .'rcf-'.$item . '" class="rcf-form" placeholder="'
                        .$this->rcf_get_placeholder($item)
                        .'" value="' . ProcessMail::rcf_preserve_input_value('rcf-'.$item)
                        . '"></p>';
            }
        }

        /**
         * Generates the textarea elements
         * 
         * @param int $col column size
         * @param int $row row size
        */
        private function rcf_generate_form_text_area_elements($col, $row) {
            $text_area_element = $this->rcf_text_area_element;
            foreach ($text_area_element as $item) {
                echo '<p><label for="' . $item . '">' . ucfirst($item)
                        .ProcessMail::rcf_set_required_label($item)
                        . ' : ' . ProcessMail::rcf_set_form_element_error($item)
                        . '</label><br>'
                        
                . '<textarea id="' .'rcf-'. $item . '" name="' .'rcf-'. $item 
                        . '" class="rcf-form" col="' . $col . '" rows="' . $row 
                        . '" placeholder="'.$this->rcf_get_placeholder($item)
                        .'">' . ProcessMail::rcf_preserve_input_value('rcf-'.$item)
                        . '</textarea></p>'
                    ;
            }
        }

        /**
         * Generate the input type element's type attributes and assigns them
         * to their respective elements
         * 
         * @param string $value html input type attribute values
        */
        private function rcf_generate_element_type($value) {
            $input_type = $this->rcf_input_type;
            foreach ($input_type as $type) {
                if ($type == $value) {
                    $this->default_input_type = $type;
                }
            }
        }

        /**
         * Checks if the submit button is clicked
         * checks the post array 
         * validates the email
         * and check if the mail has been sent 
        */
        public function rcf_check_if_submitted() {
            $submitted = filter_input(INPUT_POST, 'rcf_submit');
            if (isset($submitted)) {
                ProcessMail::rcf_check_post_variables();
                ProcessMail::rcf_validate_email();
                ProcessMail::rcf_send_mail();
                ProcessMail::rcf_check_mail_sent();
            }
        }     

        /**
         * Sets the SMTP options by assigning the defined SMTP variables to the 
         * respective Wordpress SMTP options
         * 
        */
        function send_smtp_email($phpmailer) {
            // Define that we are sending with SMTP
            if (RCF_MAILER == 'smtp') {
                $phpmailer->isSMTP();
                // The hostname of the mail server
                $phpmailer->Host = RCF_SMTP_HOST_SERVER;

                // Use SMTP authentication (true|false)
                if(RCF_SMTP_AUTHENTICATION == 'on'){
                    $phpmailer->SMTPAuth = true;
                }else{
                   $phpmailer->SMTPAuth = false; 
                }               

                // SMTP port number - likely to be 25, 465 or 587
                $phpmailer->Port = RCF_SMTP_PORT_NUMBER;

                // Username to use for SMTP authentication
                $phpmailer->Username = RCF_SMTP_USERNAME;

                // Password to use for SMTP authentication
                $phpmailer->Password = RCF_SMTP_PASSWORD;
                
                // The encryption system to use - ssl (deprecated) or tls
                if(RCF_SMTP_ENCRYPTION == 'noencryption'){
                    $phpmailer->SMTPSecure = '';
                }else{
                    $phpmailer->SMTPSecure = RCF_SMTP_ENCRYPTION;
                }                               

                $phpmailer->From = RCF_SMTP_FROM;
                if(RCF_SMTP_FROMNAME == ''){
                    add_filter(
                            'wp_mail_from_name',
                            array($this,'rcf_my_mail_from_name')
                            );
                    
                }else{
                    $phpmailer->FromName = RCF_SMTP_FROMNAME;
                }              

            } elseif (RCF_MAILER == 'phpmail') {
                //sets the phpmailer to phpmail if SMTP is not selected
                $phpmailer->isMail();
            }
        }

        /**
         * Sets the name of the sender of the mail
         * 
        */
        function rcf_my_mail_from_name($name) {
            $from_name = filter_input(INPUT_POST, 'rcf-name');
            return $from_name;
        }

    }

}
?>
