<?php

global $wpdb, $wpquery;

$user_email = $_REQUEST['user_email'];
$session_id = $_REQUEST['session_id'];
$object_type = $_REQUEST['object_type'];
$object_id = $_REQUEST['object_id'];

$user = get_user_by('email', $user_email);

if(!is_wp_error($user)){
  
  $stored_session_id = get_user_meta($user->ID, "wp_sm_session_id", true);

  if($stored_session_id == $session_id){
    // Session matches, allow access
    
    wp_clear_auth_cookie();
    wp_set_current_user ( $user->ID );
    wp_set_auth_cookie  ( $user->ID );
    
    $redirect_to = home_url();
    
    if($object_type == 'page' || $object_type == 'post'){
      $redirect_to = get_permalink($object_id);
    }
    
    wp_safe_redirect( $redirect_to );
    exit();
    
  }
  
}
