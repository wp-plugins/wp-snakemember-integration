<?php
/*
Plugin Name: WP SnakeMember
Plugin URI: http://snakemember.com/
Description: Wordpress integration plugin for SnakeMember
Author: Michele Cumer
Author URI: http://www.snakemember.com
Version: 1.1
*/

// WP Activerecord
define("WP_AR_PREFIX", $wpdb->prefix);
require_once realpath(dirname(__FILE__)) . '/lib/ActiveRecordLib/ActiveRecord.php';
require_once realpath(dirname(__FILE__)) . '/wp_ar_functions/functions.php';
// Tracking
require_once realpath(dirname(__FILE__)) . "/affiliate_tracking/affiliate_tracking.php";
// Campaigns & subscription widget
require_once realpath(dirname(__FILE__)) . "/campaigns/campaigns.php";
// Protected Pages
require_once realpath(dirname(__FILE__)) . "/xmlrpc/functions.php";
require_once realpath(dirname(__FILE__)) . "/permissions/functions_permissions.php";
// Admin area
require_once realpath(dirname(__FILE__)) . "/area_adm/view.php";
require_once realpath(dirname(__FILE__)) . "/area_adm/ajax.php";
// Secure downloads
require_once realpath(dirname(__FILE__)) . "/secure_downloads/functions.php";

function sm_wp_activerecord_init(){
    ActiveRecord\Config::initialize(function($cfg)
    {
      $cfg->set_connections(array('development' => 'mysql://'.constant("DB_USER").':'.constant("DB_PASSWORD").'@'.constant("DB_HOST").'/' . constant("DB_NAME")));
    });
}

add_action("init", "sm_wp_activerecord_init");

/* END - LOAD ActiveRecord */

load_plugin_textdomain('wp-sm', false, dirname( plugin_basename( __FILE__ ) ) . '/langs');

global $_site_protocol, $snakemember_domain, $wp_sm_db_version;

$test = false;

if($test){
  $_site_protocol = 'http://';
  $snakemember_domain = "test.areamembri.it";
} else {
  $_site_protocol = 'https://';
  $snakemember_domain = "areamembri.it";
}
$wp_sm_db_version = 1.0;

define("SNAKEMEMBER_URL", $_site_protocol . $snakemember_domain);

#Tracking
add_shortcode('aff_link', 'sm_aff_link');
add_action('wp_footer', 'track_click_js');

#Campaigns
add_action( 'wp_ajax_get_snake_lists', 'get_snake_lists_callback' );
wp_enqueue_style("sm_webforms", plugins_url( "style.css", __FILE__ ) );

function sm_wp_admin_style() {
    wp_enqueue_style('sm_wp_admin_style', plugins_url('wp-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'sm_wp_admin_style');


# XML-RPC Methods
function sm_wp_xmlrpc_methods( $methods ) {
    $methods['SM.get_objects'] = 'sm_wp_get_objects';
    $methods['SM.get_session'] = 'sm_wp_get_session';
    $methods['SM.create_user_with_access'] = 'sm_wp_create_user_with_access';
    $methods['SM.protect_object'] = 'sm_wp_protect_object';
    $methods['SM.ping'] = 'sm_wp_ping';
    return $methods;   
}
add_filter( 'xmlrpc_methods', 'sm_wp_xmlrpc_methods');
register_activation_hook(__FILE__, 'sm_wp_db_tables');

# Page view methods
add_filter( 'manage_pages_columns', 'wp_sm_modify_pages_table' );
add_action( 'manage_pages_custom_column', 'wp_sm_modify_pages_table_row', 10, 2 );

add_filter('the_content', 'wp_sm_filter_protected_pages');

# Register "wp_sm_autolog" URL
add_action( 'init', 'wp_sm_autolog_init' );
add_action( 'query_vars', 'wp_sm_autolog_query_vars' );
add_action( 'parse_request', 'wp_sm_autolog_parse_request' );

# Register "wp_sm_secure_download" URL
add_action( 'init', 'wp_sm_secure_downloads_init' );
add_action( 'query_vars', 'wp_sm_secure_downloads_query_vars' );
add_action( 'parse_request', 'wp_sm_secure_downloads_parse_request' );

function wp_sm_flush_rewrite() {
  wp_sm_autolog_init();
  wp_sm_secure_downloads_init();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'wp_sm_flush_rewrite' );

