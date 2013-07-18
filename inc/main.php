<?php
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

class PITO_Simple_Visitor_Counter {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_hook' ) );
		add_action( 'wp_ajax_pito_svc_ajax', array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_pito_svc_ajax', array( $this, 'ajax_callback' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init_hook' ) );
	}

	public function enqueue_scripts_hook() {
		global $wp_query;

		wp_enqueue_script( 'pito_svs_ajax', plugins_url( 'scripts/ajax.js', PITO_SVS_ABSPATH ), array( 'jquery' ), false, true );

		$args = array( 'ajax_url' => admin_url('admin-ajax.php') );

		if ( is_singular() ) {
			$args['current_page'] = $wp_query->post->ID;
		} else {
			$args['current_page'] = false;
		}

		wp_localize_script( 'pito_svs_ajax', 'pito_svs_ajax_vars', $args );
	}

	public function widgets_init_hook() {
		register_widget( 'PITO_SVC_Widget' );
	}

	public function ajax_callback() {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
			die();
		}

		$now = current_time( 'mysql' );
		$now_date = date_parse( $now );

		$defaults = array(
			'count' => 0,
			'last_hit' => $now,
			'count_today' => 0
		);

		$old_stats = get_option( 'pito_svc_stats', array() );
		$stats = wp_parse_args( $old_stats, $defaults );

		$stats['count']++;
		$last_date = date_parse( $stats['last_hit'] );

		if ( $now_date['day'] != $last_date['day'] ) {
			$stats['count_today'] = 1;
		} else {
			$stats['count_today']++;
		}

		$stats['last_hit'] = $now;

		update_option( 'pito_svc_stats', $stats );

		if ( ! empty( $_POST['current_page'] ) && ctype_digit( $_POST['current_page'] ) ) {
			$id = intval( $_POST['current_page'] );

			$single_old_stats = get_post_meta( $id, '_pito_svc', true );
			$single_stats = wp_parse_args( $single_old_stats, $defaults );

			$single_stats['count']++;
			$single_last_date = date_parse( $single_stats['last_hit'] );

			if ( $now_date['day'] != $single_last_date['day'] ) {
				$single_stats['count_today'] = 1;
			} else {
				$single_stats['count_today']++;
			}

			$single_stats['last_hit'] = $now;

			update_post_meta( $id, '_pito_svc', $single_stats );
		}

		die();
	}

}
?>