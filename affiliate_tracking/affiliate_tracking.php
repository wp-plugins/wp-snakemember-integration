<?php 

function track_affiliate(){
    if ( isset($_REQUEST["aff_id"])){
        save_cookies();
    }
}

function sm_admin_page(){
    echo "ADMIN PAGE";
}


function save_cookies(){
    
    $old_aff_id = $_COOKIE["snakem_aff_id"];
    $old_camp_id = $_COOKIE["snakem_camp_id"];
    /* NO ! ULTIMO COOKIE!!! */
    if ($old_aff_id > 0){
        // NON conservo il vecchio cookie!
        //return;
    }
    
    $new_aff_id = $_REQUEST["aff_id"];
    $new_camp_id = $_REQUEST["camp_id"];

    $domain = get_option('siteurl'); //or home
    $domain = str_replace('http://', '', $domain);
    //echo "DOMAIN: " . $domain;
    if ($new_aff_id != ""){
        setcookie("snakem_aff_id",$new_aff_id,time()+60*60*24*360,"/",$domain);
        setcookie("snakem_camp_id",$new_camp_id,time()+60*60*24*360,"/",$domain);

        //$referer=@$HTTP_REFERER;
    	//$ip = $_SERVER['REMOTE_ADDR'];
        

    }
    
}


function get_camp_id($default = ""){
    $aff_id = $_REQUEST["camp_id"];
    if ($aff_id != ""){
        return $aff_id;
    }
    $aff_id = $_COOKIE["snakem_camp_id"];
    if ($aff_id != ""){
        return $aff_id;
    }

    return $default;

}

function get_aff_id($default = "CG00001"){
    $aff_id = $_REQUEST["aff_id"];
    if ($aff_id != ""){
        return $aff_id;
    }
    $aff_id = $_COOKIE["snakem_aff_id"];
    if ($aff_id != ""){
        return $aff_id;
    }
    
    return $default;

}

function track_click_js(){
    #Print the JS for track click SnakeMember

    $api_key = get_option("sm_api");
    $funnel = (get_option("sm_funnel_id"))?get_option("sm_funnel_id"):0;
    $product = (get_option("sm_product_id"))?get_option("sm_product_id"):0;
    $promo = (get_option("sm_promo_nicename"))?get_option("sm_promo_nicename"):"";
  	if (!$_SERVER['HTTPS']){
      ?>
      <script type="text/javascript">

          var _inntr = _inntr || [];
          _inntr["tracking_code"] = "<?= $api_key ?>";
          _inntr["referer"] = "<?php echo $_SERVER['HTTP_REFERER'] ?>";

          (function() {
              var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
              ga.src = '<?= constant("SNAKEMEMBER_URL")?>/api/trackGoals.js';
              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

          jQuery(document).ready(function(){
              setTimeout(function(){_inntr.track("click", <?= $funnel ?>, '<?= get_aff_id(sm_get_default_aff_id()); ?>', '<?php get_camp_id(0); ?>', <?= $product ?>, '<?= $promo ?>')}, 600);
              
              // Replace & fill aff_ids
              var aff_id = sm_get_aff_id();
    
              if(aff_id != ''){

                jQuery('a[href*="areamembri.it"]').each(function(){
                  var _href = jQuery(this).attr("href");
                  var _appendchar = '?';

                  if(_href.indexOf('?') != -1){
                    // Contains parameter, append with &
                    _appendchar = '&';
                  }

                  jQuery(this).attr("href", _href + _appendchar + "aff_id=" + aff_id);
                });
                
                jQuery('form input[name=aff_id]').val(aff_id);
                
              }

          });

          sm_get_aff_id = function(){
              return '<?= get_aff_id(sm_get_default_aff_id());?>';
          }
          sm_get_camp_id = function(){
              return '<?= get_camp_id(0);?>';
          }
          sm_get_product_id = function(){
            return '<?= $product ?>';
          }
          sm_get_promo_nicename = function(){
            return '<?= $promo ?>';
          }
          sm_get_funnel_id = function(){
            return '<?= $funnel ?>';
          }
        
          sm_track_optin = function(email){
            _inntr.track("optin", sm_get_funnel_id(), sm_get_aff_id(), sm_get_camp_id(), sm_get_product_id(), sm_get_promo_nicename(), 'meta[email]='+encodeURIComponent(email), false);
          }
      </script>
      <?php
  }

}

function sm_get_default_aff_id(){
    //get Option
    return "";
}

function sm_aff_link( $atts, $content = "" ) {
    // Link in attrs link= or Content
    $default = sm_get_default_aff_id();
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
    return $link;
}

