<?php

function sm_create_menu(){

    //creates admin menu for snakemember!!
    add_menu_page("SnakeMember", "SnakeMember" , "manage_options", "snakemember" , "sm_admin_main_options", plugins_url("images/logo.png", dirname(__FILE__) ) );
    
    add_submenu_page("snakemember", "General Options", "General Options" , "manage_options", "snakemember" , "sm_admin_main_options"  );
    
    //call register settings function
  	add_action( 'admin_init', 'wp_sm_register_settings' );
    
}

function wp_sm_register_settings(){
    register_setting( 'wp-sm', 'sm_api' );
  	register_setting( 'wp-sm', 'sm_api_key' );
    register_setting( 'wp-sm', 'sm_funnel_id' );
    register_setting( 'wp-sm', 'sm_product_id' );
    register_setting( 'wp-sm', 'sm_promo_nicename' );
  	register_setting( 'wp-sm', 'sm_s3_api_key' );
    register_setting( 'wp-sm', 'sm_s3_api_secret' );
}

function sm_admin_main_options(){
  include(realpath(dirname(__FILE__)).'/main_options_view.php');
}

add_action("admin_menu" , "sm_create_menu");