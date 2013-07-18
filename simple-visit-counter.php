<?php
/*
Plugin Name: Simple Visit Counter
Plugin URI: http://pito.tamu.edu/
Description: Displays visitor count with widget .
Version: 0.0.1
Author: Eric Bakenhus
Author URI: http://pito.tamu.edu/
*/

if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

define( 'PITO_SVS_ABSPATH', __FILE__ );

require plugin_dir_path( __FILE__ ) . 'inc/main.php';
require plugin_dir_path( __FILE__ ) . 'inc/widgets.php';
global $pito_svc_instance;
$pito_svc_instance = new PITO_Simple_Visitor_Counter();

if ( is_admin() ) {
	require plugin_dir_path( __FILE__ ) . 'inc/admin.php';
	global $pito_svc_admin_instance;
	$pito_svc_admin_instance = new PITO_Simple_Visitor_Counter_Admin();
}