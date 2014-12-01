<?php

?>
<div class="wrap">
  <h2><?php _e('WP Snakemember | Global Options', 'wp-sm') ?></h2>
  <form action="options.php" method="post" enctype="multipart/form-data" name="wp_smtp_form">
    <?php settings_fields( 'wp-sm' ); ?>
    <?php do_settings_sections( 'wp-sm' ); ?>
    <table class="form-table">
    	<tbody>
        <tr valign="top">
      		<th scope="row">
      			<?php _e('API User', 'wp-sm') ?>		</th>
      		<td>
      			<label>
      				<input type="text" name="sm_api" value="<?php echo esc_attr( get_option('sm_api') ); ?>" size="20" />
              <p class="description"><?php _e('This is the API user of your main business', 'wp-sm') ?>.</p>
      			</label>
      		</td>
      	</tr>
      	<tr valign="top">
      		<th scope="row">
      			<?php _e('API Key', 'wp-sm') ?>		</th>
      		<td>
      			<label>
      				<input type="text" name="sm_api_key" value="<?php echo esc_attr( get_option('sm_api_key') ); ?>" size="20" >
              <p class="description"><?php _e('This is the API key of your main business', 'wp-sm') ?>.</p>
      			</label>
      		</td>
      	</tr>
        
      	<tr valign="top">
      		<th scope="row">
            
          </th>
      		<td>
      			<label>
      				<a href="#" class="button sm_load_items"><?php _e('Load items from SnakeMember', 'wp-sm') ?></a> 
      			</label>
      		</td>
      	</tr>
        
      	<tr valign="top">
      		<th scope="row">
      			<?php _e('Product', 'wp-sm') ?>		<div class="sm_product_spinner spinner"></div></th>
      		<td>
      			<label>
              <select name="sm_product_id" id="sm_product_select" disabled>
                <option><?php _e('Click "Load items..." above to display products', 'wp-sm') ?></option>
              </select>
      			</label>
      		</td>
      	</tr>
        
      	<tr valign="top">
      		<th scope="row">
      			<?php _e('Affiliate Promo', 'wp-sm') ?>	<div class="sm_promo_spinner spinner"></div></th>
      		<td>
      			<label>
              <select name="sm_promo_nicename" id="sm_promo_select" disabled>
                <option><?php _e('Click "Load items..." above to display promos', 'wp-sm') ?></option>
              </select>
              <p class="description"><?php _e('This promo will be used to track the affiliate optins', 'wp-sm') ?>.</p>
      			</label>
      		</td>
      	</tr>
        
        <tr valign="top">
      		<th scope="row" colspan="2">
      			<h3><?php _e('Amazon S3 Account Credentials', 'wp-sm') ?></h3>
          </th>
            
      	</tr>
        
        <tr valign="top">
      		<th scope="row">
      			<?php _e('S3 API Key', 'wp-sm') ?>		</th>
      		<td>
      			<label>
      				<input type="text" name="sm_s3_api_key" value="<?php echo esc_attr( get_option('sm_s3_api_key') ); ?>" size="43" />
      			</label>
      		</td>
      	</tr>
      	<tr valign="top">
      		<th scope="row">
      			<?php _e('S3 API Secret', 'wp-sm') ?>		</th>
      		<td>
      			<label>
      				<input type="text" name="sm_s3_api_secret" value="<?php echo esc_attr( get_option('sm_s3_api_secret') ); ?>" size="43" >
      			</label>
      		</td>
      	</tr>
        <tr valign="top">
      		<th scope="row"></th>
          <td><p class="description"><?php echo sprintf(__('If you\'ve any doubts, check the <a href="%s" target="_blank">Amazon S3 FAQs</a>', 'wp-sm'), 'http://aws.amazon.com/s3/faqs/') ?></p></td>
        </tr>
  	
      </tbody>
    </table>

    <?php submit_button(); ?>

  </form>
  
</div>

<script type="text/javascript">


function load_products(){
  jQuery('.sm_product_spinner').show();
  
  jQuery.ajax({
    url: ajaxurl,
    dataType: 'json',
    data: {
      action: 'sm_load_products',
      api_user: jQuery('input[name=sm_api]').val(),
      api_key: jQuery('input[name=sm_api_key]').val()
    },
    success: function(data){
      if(data && data.length > 0){
        console.log("Loaded "+data.length+" products");
        
        jQuery('#sm_product_select').html('');
        
        var new_els = false;
        
        jQuery.each(data, function( index, value ) {
          new_els = jQuery('<option></option>').attr('value', value.id).text(value.name);
          jQuery('#sm_product_select').append(new_els)
        });
        
        <?php if(get_option('sm_product_id')){ ?>
          jQuery('#sm_product_select').val('<?php echo get_option('sm_product_id'); ?>').change();
        <?php } ?>
        
        jQuery('#sm_product_select').prop('disabled', false);
        
      }
    },
    complete: function(){
      jQuery('.sm_product_spinner').hide();
    }
  });
}

function load_promos(){
  jQuery('.sm_promo_spinner').show();
  
  jQuery.ajax({
    url: ajaxurl,
    dataType: 'json',
    data: {
      action: 'sm_load_promos',
      api_user: jQuery('input[name=sm_api]').val(),
      api_key: jQuery('input[name=sm_api_key]').val()
    },
    success: function(data){
      if(data && data.length > 0){
        console.log("Loaded "+data.length+" products");
        
        jQuery('#sm_promo_select').html('');
        
        var new_els = false;
        
        jQuery.each(data, function( index, value ) {
          new_els = jQuery('<option></option>').attr('value', value.nicename).text(value.title);
          jQuery('#sm_promo_select').append(new_els)
        });
        
        <?php if(get_option('sm_promo_nicename')){ ?>
          jQuery('#sm_promo_select').val('<?php echo get_option('sm_promo_nicename'); ?>').change();
        <?php } ?>
        
        jQuery('#sm_promo_select').prop('disabled', false);
        
      }
    },
    complete: function(){
      jQuery('.sm_promo_spinner').hide();
    }
  });
}

jQuery(document).ready(function(){
  jQuery('.sm_load_items').on('click', function(ev){
    ev.preventDefault();
    
    load_products();    
    load_promos();

  });
  
  if(jQuery('input[name=sm_api]').val() != '' && jQuery('input[name=sm_api_key]').val() != ''){
    jQuery('.sm_load_items').trigger('click');
  }
});

</script>