<?php

add_action( 'wp_ajax_sm_load_products', 'wp_sm_load_products_callback' );
add_action( 'wp_ajax_sm_load_promos', 'wp_sm_load_promos_callback' );


function wp_sm_load_products_callback() {
	global $wpdb; // this is how you get access to the database

	# Make CURL call to retrieve all products
  //set POST variables
  $url = SNAKEMEMBER_URL.'/products.json';
  $fields = array(
  						'api_user' => urlencode($_REQUEST['api_user']),
  						'api_key' => urlencode($_REQUEST['api_key']),
              'recursive' => true
  				);

  //url-ify the data for the POST
  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
  $fields_string = rtrim($fields_string, '&');

  //open connection
  $ch = curl_init();

  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url."?".$fields_string);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_VERBOSE, true);

  $json_response = curl_exec($ch);

	$response_objs = json_decode($json_response);
  
  //close connection
  curl_close($ch);

	if($response_objs && count($response_objs) > 0){
		echo $json_response;
	} else {
		echo array();
	}

	die(); // this is required to terminate immediately and return a proper response
}

function wp_sm_load_promos_callback() {
	global $wpdb; // this is how you get access to the database

	# Make CURL call to retrieve all products
  //set POST variables
  $url = SNAKEMEMBER_URL.'/affiliate_promos.json';
  $fields = array(
  						'api_user' => urlencode($_REQUEST['api_user']),
  						'api_key' => urlencode($_REQUEST['api_key'])
  				);

  //url-ify the data for the POST
  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
  $fields_string = rtrim($fields_string, '&');

  //open connection
  $ch = curl_init();

  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url."?".$fields_string);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_VERBOSE, true);

  $json_response = curl_exec($ch);
	$response_objs = json_decode($json_response);
  
  //close connection
  curl_close($ch);

	if($response_objs && count($response_objs) > 0){
		echo $json_response;
	} else {
		echo array();
	}

	die(); // this is required to terminate immediately and return a proper response
}