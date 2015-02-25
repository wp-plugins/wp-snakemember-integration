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
  
  if(wp_sm_no_protection()){
    return false;
  }
  
  switch ($object_class) {
      case 'page' :
        $protected = get_post_meta($object_id, "wp_sm_protected", true);
        break;

      default:
  }
  
  return $protected;
  
}

function wp_sm_user_has_access($object_id, $object_class, $user_id){
  if(!wp_sm_object_is_protected($object_id, $object_class) || current_user_can('manage_options') || wp_sm_no_protection()){
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
  
  if(!wp_sm_prot_use_redirect() && !wp_sm_no_protection()){
    $user_id = $current_user->id;

    if(! wp_sm_user_has_access($post->ID, 'page', $user_id)){
      $content = "Non hai accesso a questo contenuto";
      remove_filter('the_content', 'wp_sm_filter_protected_pages');
      add_filter('the_content', 'wp_sm_void_filter_protected_pages');
    }
  } 
  
  return do_shortcode($content);
}

function wp_sm_filter_protected_pages_redirect($args){
  global $wpdb, $current_user, $post;
  get_currentuserinfo();
  
  if($redir_url = wp_sm_prot_use_redirect()){
    $user_id = $current_user->ID;

    if(! wp_sm_user_has_access($post->ID, 'page', $user_id)){
      wp_redirect( $redir_url ); exit;
    }
  }
  
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

/** 
  @brief Check if is selected the protected redirect instead of content filtering
  @returns false: if content filtering
           the redirect URL: if redirect protection 
**/
function wp_sm_prot_use_redirect(){
  $redir_option = get_option('sm_prot_redir');
  $redir_option_url = get_option('sm_prot_redir_url');
  
  if(! wp_sm_no_protection() && $redir_option && $redir_option_url && $redir_option_url != ''){
    return $redir_option_url;
  } else {
    return false;
  }
}

/**
  @brief Check if !protection is selected
**/
function wp_sm_no_protection(){
  $redir_option = get_option('sm_prot_redir');
  
  if($redir_option == -1){
    return true;
  } else {
    return false;
  }
}
