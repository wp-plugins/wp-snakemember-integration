<?php
// Get the URL based on the hash & force download for the retrieved URL (attachment)
$destination_url = wp_sm_get_secure_s3_url($_REQUEST['hash'], true);

if( is_user_logged_in() ){
  header('Content-Disposition: attachment;');
  header("Location: $destination_url");
}
exit();