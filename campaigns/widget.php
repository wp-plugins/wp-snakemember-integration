<?php

class SnakeMemberCampaigns_Widget extends WP_Widget {

    function SnakeMemberCampaigns_Widget() {
        // $widget_ops mostra la descrizione del widget
        $widget_ops = array( 'classname' => 'widget_snake_campaigns', 'description' => __('Form for SnakeMember Campaigns', 'wp-sm') );
        parent::__construct( false, __('SnakeMember Campaigns Form', 'wp-sm'), $widget_ops );
    }

    // funzione per la visualizzazione del widget nel frontend
    function widget( $args, $instance ) {
        // extract() estrae gli argomenti trasformandoli in varibili
        extract($args);
        // operatore ternario. $title = Ultimi articoli, se $instance['title'] è vuoto, altrimenti e quello impostato.
        $title = ( empty($instance['title'] )) ? __('SnakeMember Campaigns Form', 'wp-sm') : $instance['title'];
        $listname = isset( $instance['listname'] ) ? esc_attr( $instance['listname'] ) : '';

        // Credentials group
        // $snake_uri = isset( $instance['snake_uri'] ) ? esc_attr( $instance['snake_uri'] ) : '';
//         $api_user = isset( $instance['api_user'] ) ? esc_attr( $instance['api_user'] ) : '';
//         $api_key = isset( $instance['api_key'] ) ? esc_attr( $instance['api_key'] ) : '';
        // Redirect group
        $redirect = isset( $instance['redirect'] ) ? esc_attr( $instance['redirect'] ) : '';
        $redirect_on_list = isset( $instance['redirect_on_list'] ) ? esc_attr( $instance['redirect_on_list'] ) : '';

        // Misc
        $meta_required = isset( $instance['meta_required'] ) ? esc_attr( $instance['meta_required'] ) : '';
        $call_to_action = isset( $instance['call_to_action'] ) ? esc_attr( $instance['call_to_action'] ) : '';
        $privacy = isset( $instance['privacy'] ) ? (bool) $instance['privacy'] : true;
        $copy_privacy = isset( $instance['copy_privacy'] ) ? $instance['copy_privacy']  : '';

        // Inizio Widget ***************************** */
        echo $before_widget;
        // titolo del widget

        /*TODO: USE sm_get_webform($listname, $redirect, $redirect_on_list,$call_to_action, $privacy, $copy_privacy , $aff_id); */
        echo '<div class="widget-title copy-form">' . $title . '</div>';

        echo '<form method="post" class="autoresponder" action="'.SNAKEMEMBER_URL.'/campaigns/subscribe">
                <div style="display: none;">
                    <input type="hidden" name="listname" value="'.$listname.'" />
					          <input type="hidden" name="meta_redirect_onlist" value="'.$redirect_on_list.'" />
                    <input type="hidden" name="redirect" value="'.$redirect.'" />';
                    if(get_aff_id() && get_aff_id() != '' && get_aff_id() != 0){
                      echo '<input type="hidden" name="aff_id" value="'.get_aff_id().'" />';
                      
                      if(get_option('sm_promo_nicename') && get_option('sm_promo_nicename') != ''){
                        echo '<input type="hidden" name="custom promo_nicename" value="'.get_option('sm_promo_nicename').'" />';
                      }
                    }
        echo   '</div>
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
            echo'<div class="row">
                    <div class="privacy large-12 columns">
                        <p><label class="disclaimer">
                           <input type="checkbox" required="" checked="" class="privacy-mail-chimp"> "'.$copy_privacy.'"
                        </p>
                    </div>
                </div>';
        }

        echo '<div class="row">
                    <div class="mail-chimp-button large-12 columns text-center">
                            <button type="submit" name="submit" id="submit" class="skincolor button">'.$call_to_action.'</button>
                    </div>
                </div></form>';

        // Fine Widget ***************************** */
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        global $allowedposttags;

        /**
         * strip_tags salva eliminando i tag html
         * wp_kses permette il salvataggio dei tag html
         */

        $instance['title'] = wp_kses( $new_instance['title'], $allowedposttags);
        $instance['listname'] = strip_tags($new_instance['listname']);
        // Redirect group
        $instance['redirect'] = strip_tags($new_instance['redirect']);
        $instance['redirect_on_list'] = strip_tags($new_instance['redirect_on_list']);
        // Credentials group
        // $instance['snake_uri'] = strip_tags($new_instance['snake_uri']);
//         $instance['api_user'] = strip_tags($new_instance['api_user']);
//         $instance['api_key'] = strip_tags($new_instance['api_key']);
		    // Misc
        $instance['meta_required'] = strip_tags($new_instance['meta_required']);
        $instance['call_to_action'] = strip_tags($new_instance['call_to_action']);
        $instance['privacy'] = (bool) $new_instance['privacy'];
        $instance['copy_privacy'] = wp_kses($new_instance['copy_privacy']);

        return $instance;
    }

    function form( $instance ) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

		    // $snake_uri = isset( $instance['snake_uri'] ) ? esc_attr( $instance['snake_uri'] ) : '';
//         $api_user = isset( $instance['api_user'] ) ? esc_attr( $instance['api_user'] ) : '';
//         $api_key = isset( $instance['api_key'] ) ? esc_attr( $instance['api_key'] ) : '';

        $listname = isset( $instance['listname'] ) ? esc_attr( $instance['listname'] ) : '';
        $redirect = isset( $instance['redirect'] ) ? esc_attr( $instance['redirect'] ) : '';
        $redirect_on_list = isset( $instance['redirect_on_list'] ) ? esc_attr( $instance['redirect_on_list'] ) : '';

        $meta_required = isset( $instance['meta_required'] ) ? esc_attr( $instance['meta_required'] ) : '';
        $call_to_action = isset( $instance['call_to_action'] ) ? esc_attr( $instance['call_to_action'] ) : '';
        $privacy = isset( $instance['privacy'] ) ? (bool) $instance['privacy'] : true;
        $copy_privacy = isset( $instance['copy_privacy'] ) ? esc_attr( $instance['copy_privacy'] ) : '';
        ?>

        <p><em><?php _e('Il widget visualizza il form SnakeMember', 'wp-sm'); ?></em></p>

        <p><label for="<?php echo $this->get_field_id('title');?>">
                <?php _e('Copy del Form: ( incolla qui il tuo codice html ) ', 'wp-sm');?><textarea rows="5" class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" ><?php echo $title; ?></textarea>
            </label></p>

        <p style="display:none"><input type="button" name="load_api" value="<?php _e('Carica liste', 'wp-sm');?>" id="<?php echo $this->get_field_id('load_api');?>" class="button load_snake_lists" /></p>

        <p><label for="<?php echo $this->get_field_id('listname');?>">
                <?php _e('Nome lista: ', 'wp-sm');?><br/>
				<select class="mc_list_select" name="<?php echo $this->get_field_name('listname');?>" id="<?php echo $this->get_field_id('listname');?>" disabled>
					<option ><?php _e('Loading...', 'wp-sm')?></option>
				</select>
				<input type="hidden" name="db_listname" id="<?php echo $this->get_field_id('db_listname');?>" class="mc_db_listname" value="<?php echo $listname; ?>" />
            </label></p>

        <p><label for="<?php echo $this->get_field_id('redirect');?>">
                <?php _e('Redirect: ', 'wp-sm');?><input class="widefat" id="<?php echo $this->get_field_id('redirect');?>" name="<?php echo $this->get_field_name('redirect');?>" type="text" value="<?php echo $redirect; ?>" />
            </label></p>
        <p><label for="<?php echo $this->get_field_id('redirect_on_list');?>">
                <?php _e('Redirect (on list): ', 'wp-sm');?><input class="widefat" id="<?php echo $this->get_field_id('redirect_on_list');?>" name="<?php echo $this->get_field_name('redirect_on_list');?>" type="text" value="<?php echo $redirect_on_list; ?>" />
            </label></p>

        <p><label for="<?php echo $this->get_field_id('meta_required');?>">
                <?php _e('Campi del form: ', 'wp-sm');?>
                <select id="<?php echo $this->get_field_name('meta_required');?>" name="<?php echo $this->get_field_name('meta_required');?>" class="widefat" style="width:100%;">

                    <option> Seleziona </option>
                    <?php
                    $options = array('Solo Email' => 'email', 'Nome e Email' => 'name,email');
                    foreach ($options as $option => $val) {
                        echo '<option value="' . $val . '" id="' . $val . '"', $meta_required == $val ? ' selected="selected"' : '', '>', $option, '</option>';
                    }
                    ?>

                </select>
            </label></p>

        <p><label for="<?php echo $this->get_field_id('call_to_action');?>">
                <?php _e('Testo Bottone: ', 'wp-sm');?><input class="widefat" id="<?php echo $this->get_field_id('call_to_action');?>" name="<?php echo $this->get_field_name('call_to_action');?>" type="text" value="<?php echo $call_to_action; ?>" />
            </label></p>

        <p><input class="checkbox" type="checkbox" <?php checked( $privacy ); ?> id="<?php echo $this->get_field_id( 'privacy' ); ?>" name="<?php echo $this->get_field_name( 'privacy' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'privacy' ); ?>"><?php _e( 'Vuoi attivare una checkbox per accettare la normativa sulla privacy? Devi aver creato una pagina privacy. (facoltativo)', 'wp-sm' ); ?></label></p>

        <p><label for="<?php echo $this->get_field_id('copy_privacy');?>">
                <?php _e('Informativa sulla privacy (facoltativo): ', 'wp-sm');?><textarea class="widefat" id="<?php echo $this->get_field_id('copy_privacy');?>" name="<?php echo $this->get_field_name('copy_privacy');?>" type="text" ><?php echo $copy_privacy; ?></textarea>
            </label></p>
			<script type="text/javascript">

			jQuery('#<?php echo $this->get_field_id('load_api');?>').on('click', function(){
				var widg_container = jQuery(this).closest('.widget-content');
        console.log('CALL');
				jQuery.ajax({
				  url: '<?php echo home_url() ?>/wp-admin/admin-ajax.php',
					type: 'GET',
					dataType: 'json',
					cache: false,
					data: {
						action: 'get_snake_lists'
					},
					success: function(resp) {
						jQuery(widg_container).find('.mc_list_select').removeAttr('disabled').html('');
						console.log(resp);
						var lists = resp['data'];

						for (index = 0; index < lists.length; ++index) {
							var elmnt = lists[index];
							var new_opt = jQuery('<option></option>').attr('value', elmnt.nicename).html(elmnt.name);
						    jQuery(widg_container).find('.mc_list_select').append(new_opt);
						}

						if(jQuery('#<?php echo $this->get_field_id('db_listname');?>').val() != ''){
							var val_list = jQuery('#<?php echo $this->get_field_id('db_listname');?>').val();
							console.log(val_list);
							jQuery(widg_container).find('.mc_list_select option[value='+val_list+']').prop('selected', 'selected').change();
						}
					}
				});
			});
      
      var sm_api = "<?php echo get_option('sm_api') ?>";
      var sm_api_key = "<?php echo get_option('sm_api_key') ?>";
      
			if(sm_api != '' && sm_api_key != ''){
				var widg_container = jQuery('#<?php echo $this->get_field_id('listname');?>').closest('.widget-content');

				jQuery(widg_container).find('.load_snake_lists').trigger('click');
			}

			</script>
    <?php
    }
}
