<?php

function sm_wp_get_objects( $args ){
  global $wp_xmlrpc_server;
  $wp_xmlrpc_server->escape( $args );

  $username = $args[0];
  $password = $args[1];
  
  $types = $args[2];

  if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
    return $wp_xmlrpc_server->error;
  
  $types_array = explode(',', $types);
  $ret_objs = array();
  
  foreach($types_array as $type){
    
    $ret_objs = array_merge($ret_objs, call_user_func("sm_wp_get_".$type));
    
  }
  
  return $ret_objs;
  
}

function sm_wp_get_pages() {
    return get_pages(array("post_status" => array('publish', 'pending', 'draft', 'future')));
}

function sm_wp_get_session( $args ){
  global $wp_xmlrpc_server;
  $wp_xmlrpc_server->escape( $args );

  $username = $args[0];
  $password = $args[1];
  
  $user_email = $args[2];
  $user_name = $args[3];
  $user_password = $args[4];
  
  $object_id = $args[5];
  $object_class = $args[6];

  if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
    return $wp_xmlrpc_server->error;

  // Find the user by email and create the session
  $requesting_user = get_user_by("email", $user_email);
  
  if ( !$requesting_user ){
    // Create the user and grant access
    $user_id = wp_sm_create_user($user_email, $user_name, $user_password);
  } else {
    $user_id = $requesting_user->ID;
  }
  $permission = wp_sm_grant_access_to_user($user_id, $object_id, $object_class);
  
  $generated_session_id = uniqid();
  update_user_meta($user_id, 'wp_sm_session_id', $generated_session_id);
  
  // Only for pages
  update_post_meta($object_id, "wp_sm_protected", "1");
  
  return array("session_id" => $generated_session_id);    
}

function sm_wp_create_user_with_access($args){
  global $wp_xmlrpc_server;
  $wp_xmlrpc_server->escape( $args );

  $username = $args[0];
  $password = $args[1];
  
  $user_email = $args[2];
  $user_name = $args[3];
  $user_password = $args[4];
  
  $object_id = $args[5];
  $object_class = $args[6];
  
  $user_id = wp_sm_create_user($user_email, $user_name, $user_password);
  $permission = wp_sm_grant_access_to_user($user_id, $object_id, $object_class);
  
}

function sm_wp_ping( $args ){
  global $wp_xmlrpc_server;
  $wp_xmlrpc_server->escape( $args );

  $username = $args[0];
  $password = $args[1];

  if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
    return array("error" => "Invalid credentials");

  return array("result" => "ok");    
}

function sm_wp_protect_object($args){
  global $wp_xmlrpc_server;
  $wp_xmlrpc_server->escape( $args );

  $username = $args[0];
  $password = $args[1];
  
  $object_id = $args[2];
  $object_class = $args[3];

  if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
    return array("error" => "Invalid credentials");
  
  update_post_meta($object_id, "wp_sm_protected", "1");
}

function sm_wp_db_tables(){
  global $wpdb, $wp_sm_db_version;

	$table_name = $wpdb->prefix . "sm_object_permissions";

  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `object_id` int(11) NOT NULL,
                `object_class` varchar(25) NOT NULL,
                `user_id` int(11) NOT NULL,
                `granted_at` datetime NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_option("wp_sm_table_db_version", $wp_sm_db_version);
  }
}

/* UTILS */
function wp_sm_create_user($user_email, $user_name, $user_password){
  
  $user = get_user_by("email", $user_email);
  
  if ( !$user ){
    $userdata = array(
        'user_login'  =>  $user_email,
        'user_email'  =>  $user_email,
        'user_pass'   =>  $user_password,
        'display_name' => $user_name
    );

    $user_id = wp_insert_user( $userdata ) ;
  } else {
  	$user_id = $user->ID;
  }
  
  return $user_id;
}

function wp_sm_grant_access_to_user($user_id, $object_id, $object_class){
  $permission = Permission::find("first", array("conditions" => array("user_id = ? AND object_id = ? AND object_class = ?", $user_id, $object_id, $object_class)));
  
  if(!$permission){
    $permission = Permission::create(array("user_id" => $user_id, "object_id" => $object_id, "object_class" => $object_class ));
  }
  
  update_post_meta($object_id, "wp_sm_protected", "1");
  
}


