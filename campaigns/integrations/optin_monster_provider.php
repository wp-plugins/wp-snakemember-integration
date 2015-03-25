<?php
/**
 * Snakemember provider class.
 *
 * @since   1.0.0
 *
 * @package Optin_Monster
 * @author  Innova Experience
 */
class Optin_Monster_Provider_SnakeMember extends Optin_Monster_Provider {

    /**
     * Path to the file.
     *
     * @since 2.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Slug of the provider.
     *
     * @since 2.0.0
     *
     * @var string
     */
    public $provider = 'snakemember';

    /**
     * Holds the API instance.
     *
     * @since 2.0.0
     *
     * @var string
     */
    public $api = false;

    /**
     * Primary class constructor.
     *
     * @since 2.0.0
     */
    public function __construct() {

        // Construct via the parent object.
        parent::__construct();

    }

    /**
     * Authentication method for providers.
     *
     * @since 2.0.0
     *
     * @param array $args     Data submitted by the user to be passed for authentication.
     * @param int   $optin_id The optin ID being used.
     *
     * @return  string|object Output of the email lists or WP_Error.
     */
    public function authenticate( $args = array(), $optin_id ) {
      
      $providers                                        = Optin_Monster_Common::get_instance()->get_email_providers( true );
      $uniqid                                           = "SMM";
      $providers[ $this->provider ][ $uniqid ]['api']   = "";
      $providers[ $this->provider ][ $uniqid ]['label'] = __("SnakeMember Account", 'wp-sm');
      update_option( 'optin_monster_providers', $providers );
      
      $this->save_account( $optin_id, $this->provider, $uniqid );
      
      return $this->get_lists();

    }

    /**
     * Retrieval method for getting lists.
     *
     * @since 2.0.0
     *
     * @param array  $args     Args to be passed for list retrieval.
     * @param string $list_id  The list ID to check for selection.
     * @param string $uniqid   The account ID to target.
     * @param string $optin_id The current optin ID
     *
     * @return  string|WP_Error Output of the email lists or WP_Error.
     */
    public function get_lists( $args = array(), $list_id = '', $uniqid = '', $optin_id = '' ) {

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
          $lists[] = array("id" => $campaign->nicename, "name" => $campaign->name);
        }
      }

        return $this->build_list_html( $lists, $list_id );

    }

    /**
     * Method for building out the list selection HTML.
     *
     * @since 2.0.0
     *
     * @param array  $lists   Lists for the email provider.
     * @param string $list_id The list identifier
     * @param string $optin_id The current optin ID
     *
     * @return string $html HTML string for selecting lists.
     */
    public function build_list_html( $lists, $list_id = '', $optin_id = '' ) {

        $output = '<div class="optin-monster-field-box optin-monster-provider-lists optin-monster-clear">';
            $output .= '<p class="optin-monster-field-wrap"><label for="optin-monster-provider-list">' . __( 'Email provider list', 'wp-sm' ) . '</label><br />';
                $output .= '<select id="optin-monster-provider-list" name="optin_monster[provider_list]">';
                    foreach ( $lists as $offset => $data ) {
                        $output .= '<option value="' . $data['id'] . '"' . selected( $list_id, $data['id'], false ) . '>' . $data['name'] . '</option>';
                    }
                $output .= '</select>';
            $output .= '</p>';
        $output .= '</div>';

        return $output;

    }

    /**
     * Method for opting into the email service provider.
     *
     * @since 2.0.0
     *
     * @param array  $account Args to be passed when opting in.
     * @param string $list_id The list identifier.
     * @param array  $lead    The lead information. Should be sanitized.
     *
     * @return bool|WP_Error True on successful optin.
     */
    public function optin( $account = array(), $list_id, $lead ) {
      
      // Retrieve the campaigns from the SnakeMember installation
      $server_url = SNAKEMEMBER_URL;
      $server_path = '/campaigns/subscribe.json';

      $post_fields = array(
        "listname" => $list_id,
        "email" => $lead['lead_email'],
        "name" => $lead['lead_name'],
        "referer" => $lead['referrer'],
        "redirect" => home_url(),
        "meta_redirect_onlist" => home_url()
      );

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $server_url.$server_path);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    	curl_setopt($ch, CURLOPT_USERAGENT, 'cURL Request');
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    	ob_start();
    		$result = curl_exec($ch);
    	ob_end_clean();
    	curl_close($ch);
      
      $response = json_decode($result);
      
      if(!$response->error){
        return true;
      } else {
        return false;
      }

    }


}