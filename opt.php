<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
if (isset($_POST["act"]) && is_user_logged_in()) {
if ( (esc_attr(get_the_author_meta('user_level', get_current_user_id()))) > 9 ) {
$act = esc_sql(esc_attr($_POST["act"]));
global $wpdb;
$mabase = $wpdb->prefix .'ip_stati';
switch ($act) {
    case "PASS":
       $wpdb->delete($mabase, array( 'type' => 'er_auth' ) );
	  
        break;
    case "SUCCESS":
       $wpdb->delete( $mabase, array( 'type' => 'onlogin' ) );
        break;
     case "ERROR":
       $wpdb->delete($mabase, array( 'type' => 'errlogin' ) );
        break;
		 case "default_all":
      $wpdb->query("TRUNCATE TABLE $mabase ");
        break;
		 case "PAGES":
		 $wpdb->query("DELETE FROM $mabase WHERE type > 0 ");
        break;
}	
	

echo json_encode('OK1');
} else echo json_encode('Not_admin');
} else echo json_encode('not post, not login');
?>