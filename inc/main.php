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

		$args = array( 
			'ajax_url' => admin_url('admin-ajax.php'),
			'admin' => current_user_can( 'manage_options' ),
			'logged_in' => is_user_logged_in()
		);

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

		$response = array();

		$default_options = array(
			'ignore' => 0,
			'delete' => false
		);

		$options = wp_parse_args( get_option( 'pito_svc_options', array() ), $default_options );

		if ( ( $options['ignore'] == 1 && $_POST['admin'] == 1 ) || ( $options['ignore'] == 2 && $_POST['logged_in'] == 1 ) ) {
			$response['success'] = false;

			wp_send_json( $response );

			die();
		}

		$now = current_time( 'mysql' );
		$now_date = date_parse( $now );

		$defaults = array(
			'count' => 0,
			'count_unique' => 1,
			'count_today' => 0,
			'count_unique_today' => 1,
			'last_hit' => $now
		);

		$old_stats = get_option( 'pito_svc_stats', array() );
		$stats = wp_parse_args( $old_stats, $defaults );

		$stats['count']++;

		if ( ! isset( $_COOKIE['pito_visitor_counter'] ) ) {
			$stats['count_unique']++;
		}

		$last_date = date_parse( $stats['last_hit'] );
		$last_date_unique = ( isset( $_COOKIE['pito_visitor_counter'] ) ) ? date_parse( $_COOKIE['pito_visitor_counter'] ) : false;

		if ( $now_date['day'] != $last_date['day'] ) {
			$stats['count_today'] = 1;
			$stats['count_unique_today'] = 1;
		} else {
			$stats['count_today']++;
			if ( ! empty( $last_date_unique ) && ( $now_date['day'] != $last_date_unique['day'] ) ) {
				$stats['count_unique_today']++;
			}
		}

		$stats['last_hit'] = $now;

		// Hopefully the 2038 bug will be solved by 2037 :p
		$expires = ( time() > 2147483647 - ( 60 * 60 * 24 * 365 ) ) ? time() + ( 60 * 60 * 24 * 365 * 100 ) : 2147483647;

		setcookie( 'pito_visitor_counter', $now, $expires );

		update_option( 'pito_svc_stats', $stats );

		$response['stats'] = $stats;

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

			$response['single_stats'] = $single_stats;
		}

		$response['success'] = true;

		wp_send_json( $response );

		die();
	}

}
?>