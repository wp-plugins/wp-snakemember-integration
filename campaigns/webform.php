<?php

function sm_get_webform($listname, $redirect, $redirect_on_list, $call_to_action, $privacy = 0, $copy_privacy = "", $aff_id = "", $snake_uri = ""){

    if ($snake_uri == ""){
        $snake_uri = constant("SNAKEMEMBER_URL");
    }
    
    $webform = '<form method="post" class="autoresponder snakem_webform" action="'.$snake_uri.'/campaigns/subscribe">
                <div style="display: none;">
                    <input type="hidden" name="listname" value="'.$listname.'" />
					          <input type="hidden" name="meta_redirect_onlist" value="'.$redirect_on_list.'" />
                    <input type="hidden" name="redirect" value="'.$redirect.'" />
                    <input type="hidden" name="aff_id" value="'.$aff_id.'" />

                </div>
                <div class="row">
                    <div class="large-12 columns">
                        <input type="text" name="name" id="email" class="input" value="" size="20" tabindex="10" placeholder="Il tuo nome..." required />
                    </div>
                </div>
                <div class="row">
                    <div class="large-12 columns">
                        <input type="text" name="email" id="email" class="input" value="" size="20" tabindex="20" placeholder="La tua email..." required />
                    </div>
                </div>';

    if( $privacy ==  1 )
    {
        $webform .= '<div class="row">
                    <div class="privacy large-12 columns">
                        <p><label class="disclaimer">
                           <input type="checkbox" required="" checked="" class="privacy-mail-chimp"> "'.$copy_privacy.'"
                        </p>
                    </div>
                </div>';
    }

    $webform .= '<div class="row">
                    <div class="mail-chimp-button large-12 columns text-center">
                            <button type="submit" name="submit" id="submit" class="skincolor button">'.$call_to_action.'</button>
                    </div>
                </div></form>';
    return $webform;

}
