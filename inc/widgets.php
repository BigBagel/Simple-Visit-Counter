<?php
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

class PITO_SVC_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array( 'classname'=>'pito_svc_widget', 'description'=>'Display a visitor counter.' );

		parent::__construct( 'pito_svc_widget', $name = 'Simple Visitor Counter', $widget_ops );
	}

	/* widget output */
	public function widget( $args, $instance ) {
		global $wp_query;
		extract( $args );

		$instance_defaults = array(
			'title' => 'Hit Counter',
			'total' => true,
			'single' => true,
			'today' => true,
			'list' => false
		);
			
		$instance = wp_parse_args( $instance, $instance_defaults );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$total = $instance['total'];
		$single = $instance['single'];
		$today = $instance['today'];
		$list = $instance['list'];

		$defaults = array(
			'count' => 0,
			'count_unique' => 0,
			'count_today' => 0,
			'count_unique_today' => 0
		);

		$stats = wp_parse_args( get_option( 'pito_svc_stats', array() ), $defaults );
			
		echo $before_widget;
			
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		echo ( $list ) ? '<ul>' : '<p>';

		if( $total ) {
			echo ( $list ) ? '<li>' : '';
			esc_html_e( 'Total hits: ', 'pito_svc' );
			echo '<span class="svc_number count">' . $stats['count'] . '</span>';
			echo ( $list ) ? '</li><li>' : '<br />';
			esc_html_e( 'Total visitors: ', 'pito_svc' );
			echo '<span class="svc_number count_unique">' . $stats['count_unique'] . '</span>';
			echo ( $list ) ? '</li>' : '';
		}

		if ( $today ) {
			echo ( $total && ! $list ) ? '<br />' : '';
			echo ( $list ) ? '<li>' : '';
			esc_html_e( "Today's hits: ", 'pito_svc' );
			echo '<span class="svc_number count_today">' . $stats['count_today'] . '</span>';
			echo ( $list ) ? '</li><li>' : '<br />';
			esc_html_e( "Today's visitors: ", 'pito_svc' );
			echo '<span class="svc_number count_unique_today">' . $stats['count_unique_today'] . '</span>';
			echo ( $list ) ? '</li>' : '';
		}

		if ( $single && is_singular() ) {
			$single_stats = wp_parse_args( get_post_meta( $wp_query->post->ID, '_pito_svc', true ), $defaults );

			if ( $total ) {
				echo ( $list ) ? '<li>' : '<br />';
				esc_html_e( 'Total page hits: ', 'pito_svc' );
				echo '<span class="svc_number single_count">' . $single_stats['count'] . '</span>';
				echo ( $list ) ? '</li>' : '';
			}

			if ( $today ) {
				echo ( $list ) ? '<li>' : '<br />';
				esc_html_e( "Today's page hits: ", 'pito_svc' );
				echo '<span class="svc_number single_count_today">' . $single_stats['count_today'] . '</span>';
				echo ( $list ) ? '</li>' : '';
			}
		}

		echo ( $list ) ? '</ul>' : '</p>';
			
		echo $after_widget;
	}
		
	/* widget options update */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
			
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['total'] = ( isset( $new_instance['total'] ) ) ? true : false;
		$instance['single'] = ( isset( $new_instance['single'] ) ) ? true : false;
		$instance['today'] = ( isset( $new_instance['today'] ) ) ? true : false;
		$instance['list'] = ( isset( $new_instance['list'] ) ) ? true : false;
		
		return $instance;
	}

	public function form( $instance ) {
		$defaults = array( 
			'title' => '', 
			'total' => true,
			'single' => true, 
			'today' => true,
			'list' => false
		);

		$instance = extract( wp_parse_args( (array) $instance, $defaults ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'pito_svc' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" type="text" style="width:100%;" />
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'total' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'total' ); ?>" type="checkbox" value="1" <?php checked( $total ); ?> />
			<label for="<?php echo $this->get_field_id( 'total' ); ?>"><?php esc_html_e( "Show total count?", 'pito_svc' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'today' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'today' ); ?>" type="checkbox" value="1" <?php checked( $today ); ?> />
			<label for="<?php echo $this->get_field_id( 'today' ); ?>"><?php esc_html_e( "Show today's count?", 'pito_svc' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'single' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'single' ); ?>" type="checkbox" value="1" <?php checked( $single ); ?> />
			<label for="<?php echo $this->get_field_id( 'single' ); ?>"><?php esc_html_e( 'Show count for individual pages and posts?', 'pito_svc' ); ?></label>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'list' ); ?>" class="checkbox" name="<?php echo $this->get_field_name( 'list' ); ?>" type="checkbox" value="1" <?php checked( $list ); ?> />
			<label for="<?php echo $this->get_field_id( 'list' ); ?>"><?php esc_html_e( 'Display as unordered list?', 'pito_svc' ); ?></label>
		</p>

		<?php
	}
}
?>