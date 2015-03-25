<?php

require_once realpath(dirname(__FILE__)) . "/widget.php";

require_once realpath(dirname(__FILE__)) . "/shortcodes.php";
require_once realpath(dirname(__FILE__)) . "/webform.php";

require_once realpath(dirname(__FILE__)) . "/integrations/optin_monster.php";


if( !function_exists('snake_campaigns_register_widgets') )
{
    function snake_campaigns_register_widgets() {
        register_widget( 'SnakeMemberCampaigns_Widget' ); // registra SnakeMemberCampaigns_Widget
    }

    add_action( 'widgets_init', 'snake_campaigns_register_widgets' );
}

function get_snake_lists_callback() {

  // Retrieve the campaigns from the SnakeMember installation
  $server_url = SNAKEMEMBER_URL;
  $server_path = '/campaigns';

  $post_fields = array(
    "api_user" => get_option('sm_api'),
    "api_key" => get_option('sm_api_key'),
    "format" => "json"
  );

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $server_url.$server_path.'?'.http_build_query($post_fields));
	curl_setopt($ch, CURLOPT_USERAGENT, 'cURL Request');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	ob_start();
		$result = curl_exec($ch);
	ob_end_clean();
	curl_close($ch);

	// Decode the response, they are objects representing the CXUs
	$campaigns = json_decode($result);

  $lists = array();

  if($campaigns && is_array($campaigns) && count($campaigns) > 0){
    foreach($campaigns as $campaign){
      // Return an array of hashes containing nicename and name
      $lists[] = array("nicename" => $campaign->nicename, "name" => $campaign->name);
    }
  }

  $resp = array('data' => $lists );

  echo json_encode($resp);

	die(); // this is required to return a proper result
}

?>