<?php
/*
Plugin Name: Simple Visit Counter
Plugin URI: https://github.com/BigBagel/Simple-Visit-Counter
Description: Adds a visitor counter widget.
Version: 1.0.0
Author: Eric Bakenhus
*/

if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

define( 'PITO_SVS_ABSPATH', __FILE__ );
define( 'PITO_SVS_DIRPATH', plugin_dir_path( PITO_SVS_ABSPATH ) );

require PITO_SVS_DIRPATH . 'inc/main.php';
require PITO_SVS_DIRPATH . 'inc/widgets.php';
global $pito_svc_instance;
$pito_svc_instance = new PITO_Simple_Visitor_Counter();

if ( is_admin() ) {
	require PITO_SVS_DIRPATH . 'inc/admin.php';
	global $pito_svc_admin_instance;
	$pito_svc_admin_instance = new PITO_Simple_Visitor_Counter_Admin();
}