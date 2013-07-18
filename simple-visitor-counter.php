<?php
/*
Plugin Name: Simple Visitor Counter
Plugin URI: http://pito.tamu.edu/
Description: Displays visitor count with widget .
Version: 0.0.1
Author: Eric Bakenhus
Author URI: http://pito.tamu.edu/
*/

class PITO_Simple_Visitor_Counter {

	public function __contruct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_hook' ) );
		add_action( 'wp_ajax_pito_svc_ajax', array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_pito_svc_ajax', array( $this, 'ajax_callback' ) );
	}

	public function enqueue_scripts_hook() {
		global $wp_query;

		wp_enqueue_script( 'pito_svs_ajax', plugins_url( 'scripts/ajax.js', __FILE__ ), array( 'jquery' ), false, true );
		
		$args = array( 'ajax_url' => admin_url('admin-ajax.php') );

		if ( is_singular() ) {
			$args['current_page'] = $wp_query->post->ID;
		} else {
			$args['current_page'] = false;
		}

		wp_localize_script( 'pito_svs_ajax', 'pito_svs_ajax_vars', $args );
	}

	public function ajax_callback() {
		$options = get_option( 'pito_svc_options' );
		$options['site_count'] = ( isset( $options['site_count'] ) ) ? intval( $options['site_count'] ) + 1 : 1;
		update_option( 'pito_svc_options', $options );

		if ( ! empty( $_POST['current_page'] ) && ctype_digit( $_POST['current_page'] ) ) {
			$id = intval( $_POST['current_page'] );
			$current_count = get_post_meta( $id, '_pito_svc_count', true );
			$current_count = ( ! empty( $current_count ) ) ? $current_count + 1 : 1;
			update_post_meta( $id, '_pito_svc_count', $current_count );
		}

		die();
	}

}

global $pito_svc_instance;
$pito_svc_instance = new PITO_Simple_Visitor_Counter();