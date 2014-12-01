<?php

add_shortcode('snakem_webform', 'sm_webform_shortcode');

function sm_webform_shortcode( $attrs, $content = "" ) {
    // Link in attrs link= or Content
    /*$default = sm_get_default_aff_id();
    $aff_id = get_aff_id($default);

    $link = $atts['link'];
    if ($link == ""){
        $link = $content;
    }
    //concat aff_id = ?
    if ($aff_id != ""){
        $pos = strpos($link, "?");
        if ($pos === false){
            $link .= "?";
        }
        else {
            $link .= "&";
        }
        $link .= "aff_id=" . $aff_id;
    }
    return $link;*/


    $listname = $attrs['listname'];
    $redirect_on_list = $attrs['redirect_on_list'];
    $redirect = $attrs['redirect'];
    $privacy = $attrs['privacy'];
    $copy_privacy = $attrs['copy_privacy'];
    $call_to_action = $attrs['call_to_action'];

    if (!$redirect || $redirect == ""){
        $redirect =  get_bloginfo( "url" );
    }

    if (!$redirect_on_list || $redirect_on_list == ""){
        $redirect_on_list = $redirect;
    }
    if (!$call_to_action || $call_to_action == ""){
        $call_to_action = $content;
    }
    $aff_id = false;
    if ( function_exists("get_aff_id")){
        $default = sm_get_default_aff_id();
        $aff_id = get_aff_id($default);
    }
    return sm_get_webform($listname, $redirect, $redirect_on_list,$call_to_action, $privacy, $copy_privacy , $aff_id);


}
