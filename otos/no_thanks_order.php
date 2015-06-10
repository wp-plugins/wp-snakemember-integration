<?php 

$snake_path = '/purchase/get_thank_url.json';

$post_fields = array(
	"order_code" => $_REQUEST['wp_sm_oto_parent_order_code']	
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, constant('SNAKEMEMBER_URL').$snake_path);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);

ob_start();
	curl_exec($ch);
	$result = ob_get_contents();
ob_end_clean();
curl_close($ch);

// Decode the response, containing the order & user details
$response = json_decode($result);
$thanks_url = $response->url;

wp_redirect($thanks_url);
exit();