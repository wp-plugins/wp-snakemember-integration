<?php 

function track_affiliate(){
    if ( isset($_REQUEST["aff_id"]) || isset($_REQUEST["camp_id"]) || isset($_SERVER['HTTP_REFERER']) ){
        save_cookies();
    }
}

function sm_admin_page(){
    echo "ADMIN PAGE";
}


function save_cookies(){
    
    $old_aff_id = $_COOKIE["snakem_aff_id"];
    $old_camp_id = $_COOKIE["snakem_camp_id"];
    $old_source_referer = $_COOKIE["snakem_source_referer"];
    
    $new_aff_id = $_REQUEST["aff_id"];
    $new_camp_id = $_REQUEST["camp_id"];
    $new_source_referer = $_SERVER['HTTP_REFERER'];
    
    $domain = get_option('siteurl'); //or home
    
    $parsed_url = parse_url($domain);
    
    $domain_host = $parsed_url['host'];
    $domain_path = $parsed_url['path'];
    
    if($domain_host == 'localhost'){
      $domain_host = '';
    }
    
    if( $domain_path == ''){
      $domain_path = '/';
    }
    
    $domain_path = '/';
    
    
    if ($new_aff_id != ""){
      setcookie("snakem_aff_id",$new_aff_id,time()+60*60*24*360,$domain_path,$domain_host);
    }
    
    if( $new_camp_id != '' && $new_camp_id != 0 ){
      setcookie("snakem_camp_id",$new_camp_id,time()+60*60*24*360,$domain_path,$domain_host);
    }
    
    if( $new_source_referer != ''){
      setcookie("snakem_source_referer",$new_source_referer,0,$domain_path,$domain_host);      
    }
    
}


function get_camp_id($default = ""){
    $camp_id = $_REQUEST["camp_id"];
    if ($camp_id != "" && $camp_id != 0){
        return $camp_id;
    }
    $camp_id = $_COOKIE["snakem_camp_id"];
    if ($camp_id != "" && $camp_id != 0){
      
        return $camp_id;
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

function get_source_referer($default = ""){
    $source_referer = $_SERVER['HTTP_REFERER'];
    if ($source_referer != ""){
        return $source_referer;
    }
    $source_referer = $_COOKIE["snakem_source_referer"];
    if ($source_referer != ""){
        return $source_referer;
    }

    return $default;

}

function sm_available_domains(){
  return array(
    "areamembri.it",
    "zonamiembros.com",
    "zonamiembros.es"
  );
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
              setTimeout(function(){_inntr.track("click", <?= $funnel ?>, '<?= get_aff_id(sm_get_default_aff_id()); ?>', '<?= get_camp_id(0); ?>', <?= $product ?>, '<?= $promo ?>')}, 600);
              
              // Replace & fill aff_ids
              var aff_id = sm_get_aff_id();

              if(aff_id != ''){
                
                <?php foreach(sm_available_domains() as $av_dom){ ?>
                  jQuery('a[href*="<?php echo $av_dom ?>"]').each(function(){
                    var _href = jQuery(this).attr("href");
                    var _appendchar = '?';

                    if(_href.indexOf('?') != -1){
                      // Contains parameter, append with &
                      _appendchar = '&';
                    }

                    jQuery(this).attr("href", _href + _appendchar + "aff_id=" + aff_id);
                  });
                <?php } ?>
                
                if(aff_id != '' && typeof(aff_id) != 'undefined'){ 
                  //jQuery('form[action*="areamembri.it"] input[name=aff_id]').val(aff_id);
                }
                
              }
              
              // Inyectar y rellenar el camp_id y source_referer y promo_id
              var camp_id = sm_get_camp_id();
              
              if(camp_id != ''){
                <?php foreach(sm_available_domains() as $av_dom){ ?>
                  jQuery('form[action*="<?php echo $av_dom ?>"]').each(function(indx){
                    if( jQuery(this).find('input[name=camp_id]').length > 0 ){
                      jQuery(this).find('input[name=camp_id]').val(camp_id)
                    } else {
                      var new_imp = jQuery('<input>').attr('type','hidden').attr('name', 'camp_id').val(camp_id)
                      jQuery(this).append(new_imp);
                    }
                  })
                <?php } ?>
              }
              
              var source_referer = sm_get_source_referer();
              
              if(source_referer != ''){
                
                <?php foreach(sm_available_domains() as $av_dom){ ?>
                  jQuery('form[action*="<?php echo $av_dom ?>"]').each(function(indx){
                    if( jQuery(this).find('input[name=source_referer]').length > 0 ){
                      jQuery(this).find('input[name=source_referer]').val(source_referer)
                    } else {
                      var new_imp = jQuery('<input>').attr('type','hidden').attr('name', 'source_referer').val(source_referer)
                      jQuery(this).append(new_imp);
                    }
                  })
                <?php } ?>
              }
              
              var promo = "<?php echo $promo ?>";
              
              if(promo != ''){
                
                <?php foreach(sm_available_domains() as $av_dom){ ?>
                  jQuery('form[action*="<?php echo $av_dom ?>"]').each(function(indx){
                    if( jQuery(this).find('input[name=promo_nicename]').length > 0 ){
                      jQuery(this).find('input[name=promo_nicename]').val(promo)
                    } else {
                      var new_imp = jQuery('<input>').attr('type','hidden').attr('name', 'promo_nicename').val(promo)
                      jQuery(this).append(new_imp);
                    }
                  })
                <?php } ?>
              }
              

          });

          sm_get_aff_id = function(){
              return '<?= get_aff_id(sm_get_default_aff_id());?>';
          }
          sm_get_camp_id = function(){
              return '<?= get_camp_id(0);?>';
          }
          sm_get_source_referer = function(){
              return '<?= get_source_referer('');?>';
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

