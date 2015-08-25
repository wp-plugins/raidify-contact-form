<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('admin_tabs')){
    
    class admin_tabs{
        
        //Create WP Admin Tabs on-the-fly.
        function add_tabs($current = NULL){
            $tabs = array(
                'rcf_settings_page' => 'Fields',
                'rcf_email_settings' => 'Email',
                'rcf_smtp_settings' => 'SMTP',
                'rcf_recaptcha_settings' => 'RECAPTCHA'
            );
            if(is_null($current)){
                if(isset($_GET['page'])){
                    $current = $_GET['page'];
                }
            }
            $content = '';
            $content .= '<h2 class="rcf-plugin-title">Raidify Contact Form</h2>';
            $content .= '<h2 class="nav-tab-wrapper">';
            foreach($tabs as $location => $tabname){
                if($current == $location){
                    $class = ' nav-tab-active';
                } else{
                    $class = '';
                }                
                $content .= '<a class="nav-tab'.$class.'" href="?page='.$location.'">'.$tabname.'</a>';
            }
            
            $content .= '</h2>';
            return $content;
            
        }
    }

}