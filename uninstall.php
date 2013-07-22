<?php
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = get_option( 'pito_svc_options' );
$delete_stats = ( isset( $options['delete'] ) ) ? $options['delete'] : false;

delete_option( 'pito_svc_options' );
if ( $delete_stats ) {
	delete_option( 'pito_svc_stats' );
	delete_post_meta_by_key( '_pito_svc' );
}
?>