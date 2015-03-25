<?php

function wp_sm_om_provider_object($api, $provider){
  if($provider == 'snakemember'){
    require_once realpath(dirname(__FILE__)) . '/optin_monster_provider.php';
    $api = new Optin_Monster_Provider_SnakeMember();
  }

  return $api;
}

add_filter('optin_monster_provider_object', 'wp_sm_om_provider_object', 10, 2);


function wp_sm_om_providers($providers){
  $providers[] = array(
                'name'  => 'SnakeMember',
                'value' => 'snakemember'
            );
            
  return $providers;
}

add_filter('optin_monster_providers', 'wp_sm_om_providers');

function wp_sm_optin_monster_account_output($output, $provider, $optin_id ){
  if($provider == 'snakemember'){
    $output = sprintf(__('First, make sure you have your SnakeMember credentials correctly configured <a href="%s">here</a>', 'wp-sm'), admin_url("admin.php?page=snakemember"));
  }
  return $output;
}

function wp_sm_optin_monster_account_title($title, $provider, $optin_id){
  if($provider == 'snakemember'){
    $title = "SnakeMember";
  }
  return $title;
}

function wp_sm_optin_monster_account_doc($doc, $provider, $optin_id){
  if($provider == 'snakemember'){
    $doc = "https://areamembri.it";
  }
  return $doc;
}

function wp_sm_optin_monster_account_href($href, $provider, $optin_id){
  if($provider == 'snakemember'){
    $href = '#';
  }
  return $href;
}

function wp_sm_optin_monster_account_external($external, $provider, $optin_id){
  if($provider == 'snakemember'){
    $external = false;
  }
  return $external;
}

add_filter( 'optin_monster_account_output', 'wp_sm_optin_monster_account_output', 10, 3 );
add_filter( 'optin_monster_account_title', 'wp_sm_optin_monster_account_title', 10, 3);
add_filter( 'optin_monster_account_doc', 'wp_sm_optin_monster_account_doc', 10, 3);
add_filter( 'optin_monster_account_href', 'wp_sm_optin_monster_account_href', 10, 3);
add_filter( 'optin_monster_account_external', 'wp_sm_optin_monster_account_external', 10, 3);
