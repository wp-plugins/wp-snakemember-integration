<?php
  
function wp_sm_modify_pages_table( $column ) {
    $column['is_protected'] = 'Is Protected';

    return $column;
}

function wp_sm_modify_pages_table_row( $column_name, $page_id ) {

    switch ($column_name) {
        case 'is_protected' :
          $protected = wp_sm_object_is_protected($page_id, "page");
          echo ($protected ? 'Protected' : '');
          break;

        default:
    }
}

function wp_sm_object_is_protected($object_id, $object_class){
  $protected = false;
  
  switch ($object_class) {
      case 'page' :
        $protected = get_post_meta($object_id, "wp_sm_protected", true);
        break;

      default:
  }
  
  return $protected;
  
}

function wp_sm_user_has_access($object_id, $object_class, $user_id){
  if(!wp_sm_object_is_protected($object_id, $object_class) || current_user_can('manage_options')){
    return true;
  } else {
    
    $permission = Permission::find("first", array("conditions" => array("user_id = ? AND object_id = ? AND object_class = ?",$user_id, $object_id, $object_class)));
    
    if($permission){
      return true;
    }
    
  }
  
  return false;
  
}

function wp_sm_filter_protected_pages($content){
  global $wpdb, $current_user, $post;
  get_currentuserinfo();

  $user_id = $current_user->id;

  if(! wp_sm_user_has_access($post->ID, 'page', $user_id)){
    $content = "Non hai accesso a questo contenuto";
    remove_filter('the_content', 'wp_sm_filter_protected_pages');
    add_filter('the_content', 'wp_sm_void_filter_protected_pages');
  }
  
  return $content;
  
}

function wp_sm_void_filter_protected_pages($content){
  return '';  
}

# Set the rewrite rule for autologin

// Register a URL that will set this variable to true
function wp_sm_autolog_init() {
    add_rewrite_rule( '^wp_sm_autolog$', 'index.php?wp_sm_autolog=true', 'top' );
}

// But WordPress has a whitelist of variables it allows, so we must put it on that list
function wp_sm_autolog_query_vars( $query_vars )
{
    $query_vars[] = 'wp_sm_autolog';
    return $query_vars;
}

// If this is done, we can access it later
// This example checks very early in the process:
// if the variable is set, we include our page and stop execution after it
function wp_sm_autolog_parse_request( &$wp )
{
    if ( array_key_exists( 'wp_sm_autolog', $wp->query_vars ) ) {
        include( realpath(dirname( __FILE__ ) ) . '/../autolog.php' );
        exit();
    }
}
