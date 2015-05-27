<?php

# Generate remote order URL

// Register a URL that will set this variable to true
function wp_sm_oto_order_init() {
    add_rewrite_rule( '^wp_sm_oto$', 'index.php?wp_sm_oto=true', 'top' );
}

// But WordPress has a whitelist of variables it allows, so we must put it on that list
function wp_sm_oto_order_query_vars( $query_vars )
{
    $query_vars[] = 'wp_sm_oto';
    return $query_vars;
}

// If this is done, we can access it later
// This example checks very early in the process:
// if the variable is set, we include our page and stop execution after it
function wp_sm_oto_order_parse_request( &$wp )
{
    if ( array_key_exists( 'wp_sm_oto', $wp->query_vars ) ) {
        include( realpath(dirname( __FILE__ ) ) . '/remote_oto_order.php' );
        exit();
    }
}

// Register methods
add_action( 'init', 'wp_sm_oto_order_init', 12 );
add_action( 'query_vars', 'wp_sm_oto_order_query_vars' );
add_action( 'parse_request', 'wp_sm_oto_order_parse_request' );

// Shortcode for remote order URL
function wp_sm_oto_order_sttag( $atts ) {
  
  return home_url().'/index.php?wp_sm_oto=true&wp_sm_oto_parent_order_code='.$_REQUEST['order_code'];
  
}
add_shortcode( 'wp_sm_oto_order', 'wp_sm_oto_order_sttag' );

########################################################################

# "No thanks" OTO URL

// Register a URL that will set this variable to true
function wp_sm_oto_no_thanks_init() {
    add_rewrite_rule( '^wp_sm_oto_no_thanks$', 'index.php?wp_sm_oto_no_thanks=true', 'top' );
}

// But WordPress has a whitelist of variables it allows, so we must put it on that list
function wp_sm_oto_no_thanks_query_vars( $query_vars )
{
    $query_vars[] = 'wp_sm_oto_no_thanks';
    return $query_vars;
}

// If this is done, we can access it later
// This example checks very early in the process:
// if the variable is set, we include our page and stop execution after it
function wp_sm_oto_no_thanks_parse_request( &$wp )
{
    if ( array_key_exists( 'wp_sm_oto_no_thanks', $wp->query_vars ) ) {
        include( realpath(dirname( __FILE__ ) ) . '/no_thanks_order.php' );
        exit();
    }
}

// Register methods
add_action( 'init', 'wp_sm_oto_no_thanks_init', 12 );
add_action( 'query_vars', 'wp_sm_oto_no_thanks_query_vars' );
add_action( 'parse_request', 'wp_sm_oto_no_thanks_parse_request' );

// Shortcode for remote order URL
function wp_sm_oto_no_thanks_sttag( $atts ) {
  
  return home_url().'/index.php?wp_sm_oto_no_thanks=true&wp_sm_oto_parent_order_code='.$_REQUEST['order_code'];
  
}
add_shortcode( 'wp_sm_oto_no_thanks', 'wp_sm_oto_no_thanks_sttag' );

