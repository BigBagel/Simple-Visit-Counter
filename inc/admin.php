<?php
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) ) {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	exit( 'Do not access this file directly.' );
}

class PITO_Simple_Visitor_Counter_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'admin_init', array( $this, 'add_admin_init' ) );
	}

	public function add_admin_page() {
		add_options_page( 'Simple Visitor Counter', 'Visitor Counter', 'manage_options', 'pito_svc', array( $this, 'options_page' ) );
	}

	public function add_admin_init() {
		register_setting( 'pito_svc_options', 'pito_svc_options', array( $this, 'validate_opts' ) );
	}

	public function validate_opts( $input ) {
		$new_input = array();

		$new_input['delete'] = ( 1 == $input ) ? true : false;
		$new_input['ignore'] = ( 0 == $input['ignore'] || 1 == $input['ignore'] || 2 == $input['ignore'] ) ? intval( $input['ignore'] ) : 0;

		return $new_input;
	}

	public function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have permission to access that page.', 'Get off my lawn!' );
		}

		$defaults = array(
			'delete' => false,
			'ignore' => 0
		);

		extract( wp_parse_args( get_option( 'pito_svc_options', array() ), $defaults ) );

		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php esc_html_e( 'Simple Hit Counter', 'pito_svc' ); ?></h2>

			<form action="options.php" method="post">
				<?php settings_fields( 'pito_svc_options' ); ?>

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Count Hits From', 'pito_svc' ); ?>:</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span>Count Hits From</span>
									</legend>
									<label title="Everybody">
										<input name="pito_svc_options[ignore]" type="radio" value="0" <?php checked( $ignore, 0 ); ?> />
										<span>Everybody</span>
									</label>
									<br />
									<label title="Non-administrators">
										<input name="pito_svc_options[ignore]" type="radio" value="1" <?php checked( $ignore, 1 ); ?> />
										<span>Non-administrators</span>
									</label>
									<br />
									<label title="Non-logged-in Users">
										<input name="pito_svc_options[ignore]" type="radio" value="2" <?php checked( $ignore, 2 ); ?> />
										<span>Non-logged-in Users</span>
									</label>
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Plugin Deletion', 'pito_svc' ); ?>:</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span>Plugin Deletion</span>
									</legend>
									<label for="pito_svc_options[delete]">
										<input name="pito_svc_options[delete]" id="pito_svc_options[delete]" type="checkbox" value="1" <?php checked( $delete ); ?> />
										Delete visitor stats on plugin deleltion
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input name="Submit" type="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save', 'pito_svc'); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

}