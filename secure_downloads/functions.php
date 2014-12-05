<?php

// Secure downloads route
// Register a URL that will set this variable to true
function wp_sm_secure_downloads_init() {
    add_rewrite_rule( '^wp_sm_secure_download$', 'index.php?wp_sm_secure_download=true', 'top' );
}

// But WordPress has a whitelist of variables it allows, so we must put it on that list
function wp_sm_secure_downloads_query_vars( $query_vars )
{
    $query_vars[] = 'wp_sm_secure_download';
    return $query_vars;
}

// If this is done, we can access it later
// This example checks very early in the process:
// if the variable is set, we include our page and stop execution after it
function wp_sm_secure_downloads_parse_request( &$wp )
{
    if ( array_key_exists( 'wp_sm_secure_download', $wp->query_vars ) ) {
        include( realpath(dirname( __FILE__ ) ) . '/download.php' );
        exit();
    }
}
// END Secure downloads route

function wp_sm_get_secure_s3_url($hash, $as_attachment = false){
  $url = base64_decode($hash);

  //$clean_url=$url;
  $to_remove="/https?:\/\/s3.*?\.amazonaws.com\//";
  $to_remove_normal_urls=array("/https?:\/\//", "/\.s3\.amazonaws\.com/");

  //quito subtring de la url
  $clean_url = preg_replace($to_remove, "", $url);
  //die(serialize($clean_url));
  $clean_url = preg_replace($to_remove_normal_urls, "", $clean_url); 
 
  $accessKey = get_option('sm_s3_api_key');
  $secretKey = get_option('sm_s3_api_secret');

  $expiry_time = get_option('sm_s3_expiry_time') ? get_option('sm_s3_expiry_time') : '+2 days';

  $timestamp = strtotime($expiry_time);
  $strtosign = "GET\n\n\n$timestamp\n/$clean_url";
  
  if($as_attachment){
    $strtosign = $strtosign."?response-content-disposition=attachment";
  }

  $signature = urlencode(base64_encode(hash_hmac("sha1", utf8_encode($strtosign), $secretKey, true)));

  $destination_url = "$url?AWSAccessKeyId=$accessKey&Expires=$timestamp&Signature=$signature";
  
  if($as_attachment){
    $destination_url = $destination_url."&response-content-disposition=attachment";
  }
  
  return $destination_url;
}

function wp_sm_secure_s3_link( $atts, $content = "" ) {

	$atts = shortcode_atts( array(
		'url' => '',
		'css_class' => ''
	), $atts, 's3_secure_download' );

  $base64_url = base64_encode($atts['url']);
  $css_class = $atts['css_class'];
  
  $secure_url = home_url().'?wp_sm_secure_download=true&hash='.$base64_url;
  // Check for permalinks to return "pretty" URL
  if ( get_option('permalink_structure') ) {
    $secure_url = home_url().'/wp_sm_secure_download?hash='.$base64_url;
  }
  
  if(!$content || $content == ''){
    return $secure_url;
  } else {
    return "<a href=\"$secure_url\" class=\"$css_class\">$content</a>";
  }
  
}

// TODO: doesn't work with Worpdress player
function wp_sm_secure_s3_video( $atts ) {
	$atts = shortcode_atts( array(
		'url' => ''
	), $atts, 's3_secure_video' );
  
  $base64_url = base64_encode($atts['url']);
  
  $secure_url = wp_sm_get_secure_s3_url($base64_url);

  return do_shortcode("[video src=\"$secure_url\" ]");
  
}
