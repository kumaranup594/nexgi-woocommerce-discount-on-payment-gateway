<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of nexgi_cart_dis_cls
 *
 * @author anup
 */
class nexgi_cart_dis_cls {
    
    public function __construct(){
         
    }
    //put your code here
    public function get_pg_settings() {
        $nexgi_cart_dis_settings = get_option("nexgi_cart_dis_settings", FALSE);
        if ($nexgi_cart_dis_settings == FALSE) {
            return array();
        }
        $nexgi_cart_dis_settings = json_decode(json_encode(json_decode($nexgi_cart_dis_settings,TRUE)),TRUE);
        
        return $nexgi_cart_dis_settings;
    }
    
    /*
     * get pg list
     */
    public function get_payment_gateways() {
        return WC()->payment_gateways->payment_gateways();
    }
    /*
     * get dis types
     */
    
    public function get_dis_types() {
        return array(
            'flat' => 'Flat in Amount',
            'per' => 'in percentage'
        );
    }
    
    /*
     * getOptionValue
     */
    public function getOptionValue($field_prefix, $pg_code, $pg_settings) {
        if(isset($pg_settings[$field_prefix.$pg_code])){
            return $pg_settings[$field_prefix.$pg_code];
        }
        return '';
    }

}
