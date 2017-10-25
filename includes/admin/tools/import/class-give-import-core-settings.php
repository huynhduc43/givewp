<?php
/**
 * Core Settings Import Class
 *
 * This class handles core setting import.
 *
 * @package     Give
 * @subpackage  Classes/Give_Import_Core_Settings
 * @copyright   Copyright (c) 2017, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.8.16
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Give_Import_Core_Settings' ) ) {

	/**
	 * Give_Import_Core_Settings.
	 *
	 * @since 1.8.16
	 */
	final class Give_Import_Core_Settings {

		/**
		 * Importer type
		 *
		 * @since 1.8.16
		 * @var string
		 */
		private $importer_type = 'import_core_setting';

		/**
		 * Instance.
		 *
		 * @since 1.8.16
		 */
		static private $instance;

		/**
		 * Importing donation per page.
		 *
		 * @since 1.8.16
		 *
		 * @var   int
		 */
		public static $per_page = 20;

		/**
		 * Singleton pattern.
		 *
		 * @since 1.8.16
		 *
		 * @access private
		 */
		private function __construct() {
		}

		/**
		 * Get instance.
		 *
		 * @since 1.8.16
		 *
		 * @access public
		 *
		 * @return static
		 */
		public static function get_instance() {
			if ( null === static::$instance ) {
				self::$instance = new static();
			}

			return self::$instance;
		}

		/**
		 * Setup
		 *
		 * @since 1.8.16
		 *
		 * @return void
		 */
		public function setup() {
			$this->setup_hooks();
		}


		/**
		 * Setup Hooks.
		 *
		 * @since 1.8.16
		 *
		 * @return void
		 */
		private function setup_hooks() {
			if ( ! $this->is_donations_import_page() ) {
				return;
			}

			// Do not render main import tools page.
			remove_action( 'give_admin_field_tools_import', array( 'Give_Settings_Import', 'render_import_field', ) );

			// Render donation import page
			add_action( 'give_admin_field_tools_import', array( $this, 'render_page' ) );

			// Print the HTML.
			add_action( 'give_tools_import_core_settings_form_start', array( $this, 'html' ), 10 );

			// Run when form submit.
			add_action( 'give-tools_save_import', array( $this, 'save' ) );

			add_action( 'give-tools_update_notices', array( $this, 'update_notices' ), 11, 1 );

			// Used to add submit button.
			add_action( 'give_tools_import_core_settings_form_end', array( $this, 'submit' ), 10 );
		}

		/**
		 * Update notice
		 *
		 * @since 1.8.16
		 *
		 * @param $messages
		 *
		 * @return mixed
		 */
		public function update_notices( $messages ) {
			if ( ! empty( $_GET['tab'] ) && 'import' === give_clean( $_GET['tab'] ) ) {
				unset( $messages['give-setting-updated'] );
			}

			return $messages;
		}

		/**
		 * Print submit and nonce button.
		 *
		 * @since 1.8.16
		 */
		public function submit() {
			wp_nonce_field( 'give-save-settings', '_give-save-settings' );
			?>
			<input type="hidden" class="import-step" id="import-step" name="step" value="<?php echo $this->get_step(); ?>"/>
			<input type="hidden" class="importer-type" value="<?php echo $this->importer_type; ?>"/>
			<?php
		}

		/**
		 * Print the HTML for core setting importer.
		 *
		 * @since 1.8.16
		 */
		public function html() {
			$step = $this->get_step();

			// Show progress.
			$this->render_progress();
			?>
			<section>
				<table class="widefat export-options-table give-table <?php echo "step-{$step}"; ?>"
				       id="<?php echo "step-{$step}"; ?>">
					<tbody>
					<?php
					switch ( $this->get_step() ) {
						case 1:
							$this->render_upload_html();
							break;

						case 2:
							$this->start_import();
							break;

						case 3:
							$this->import_success();
					}
					?>
					</tbody>
				</table>
			</section>
			<?php
		}

		/**
		 * Show message after the Core Settings Imported
		 *
		 * @since 1.8.16
		 */
		public function import_success() {
			// Imported successfully

			$success = (bool) ( isset( $_GET['success'] ) ? give_clean( $_GET['success'] ) : false );
			$undo = (bool) ( isset( $_GET['undo'] ) ? give_clean( $_GET['undo'] ) : false );
			$query_arg_setting = array(
				'post_type' => 'give_forms',
				'page'      => 'give-settings',
			);

			if ( $undo ) {
				$success = false;
			}

			$query_arg_success = array(
				'post_type' => 'give_forms',
				'page'      => 'give-tools',
				'tab'      => 'import',
				'importer-type'      => 'import_core_setting',
				'step'      => '1',
				'undo'      => 'true',
			);

			$title = __( 'Core Settings Importing Completed!', 'give' );
			if ( $success ) {
				$query_arg_success['undo'] = '1';
				$query_arg_success['step'] = '3';
				$query_arg_success['success'] = '1';
				$text = __( 'Undo Importing', 'give' );
			} else {
				if ( $undo ) {
					$host_give_options = get_option( 'give_settings_old', array() );
					update_option( 'give_settings', $host_give_options );
					$title = __( 'Undo of Core Setting Imported Completed!', 'give' );
				} else {
					$title = __( 'Failed to import', 'give' );
				}

				$text = __( 'Importing Again', 'give' );
			}
			?>
			<tr valign="top" class="give-import-dropdown">
				<th colspan="2">
					<h2><?php echo $title; ?></h2>
					<p>
						<a class="button button-large button-secondary" href="<?php echo add_query_arg( $query_arg_success, admin_url( 'edit.php' ) ); ?>"><?php echo $text; ?></a>
						<a class="button button-large button-secondary" href="<?php echo add_query_arg( $query_arg_setting, admin_url( 'edit.php' ) ); ?>"><?php echo __( 'View Setting', 'give' ); ?></a>
					</p>
				</th>
			</tr>
			<?php
		}

		/**
		 * Will start Import
		 *
		 * @since 1.8.16
		 */
		public function start_import() {
			$type = ( ! empty( $_GET['type'] ) ? give_clean( $_GET['type'] ) : 'replace' );
			$file_name = ( ! empty( $_GET['file_name'] ) ? give_clean( $_GET['file_name'] ) : '' );

			?>
			<tr valign="top" class="give-import-dropdown">
				<th colspan="2">
					<h2 id="give-import-title"><?php esc_html_e( 'Importing', 'give' ) ?></h2>
					<p class="give-field-description"><?php esc_html_e( 'Your core settings are now being imported...', 'give' ) ?></p>
				</th>
			</tr>

			<tr valign="top" class="give-import-dropdown">
				<th colspan="2">
					<div class="give-progress">
						<div style="width: 50%"></div>
					</div>
					<span class="spinner is-active"></span>
					<input type="hidden" value="2" name="step">
					<input type="hidden" value="<?php echo $type; ?>" name="type">
					<input type="hidden" value="<?php echo $file_name; ?>" name="file_name">
				</th>
			</tr>

			<script type="text/javascript">
				jQuery( document ).ready( function () {
					give_on_core_settings_import_start();
				} );
			</script>
			<?php
		}

		/**
		 * Is used to show the process when user upload the donor form.
		 *
		 * @since 1.8.16
		 */
		public function render_progress() {
			$step = $this->get_step();
			?>
			<ol class="give-progress-steps">
				<li class="<?php echo( 1 === $step ? 'active' : '' ); ?>">
					<?php esc_html_e( 'Upload JSON file', 'give' ); ?>
				</li>
				<li class="<?php echo( 2 === $step ? 'active' : '' ); ?>">
					<?php esc_html_e( 'Import', 'give' ); ?>
				</li>
				<li class="<?php echo( 3 === $step ? 'active' : '' ); ?>">
					<?php esc_html_e( 'Done!', 'give' ); ?>
				</li>
			</ol>
			<?php
		}

		/**
		 * Will return the import step.
		 *
		 * @since 1.8.16
		 *
		 * @return int $step on which step doest the import is on.
		 */
		public function get_step() {
			$step    = (int) ( isset( $_REQUEST['step'] ) ? give_clean( $_REQUEST['step'] ) : 0 );
			$on_step = 1;

			if ( empty( $step ) || 1 === $step ) {
				$on_step = 1;
			} elseif ( 2 === $step ) {
				$on_step = 2;
			} elseif ( 3 === $step ) {
				$on_step = 3;
			}

			return $on_step;
		}

		/**
		 * Render donations import page
		 *
		 * @since 1.8.16
		 */
		public function render_page() {
			include_once GIVE_PLUGIN_DIR . 'includes/admin/tools/views/html-admin-page-import-core-settings.php';
		}

		/**
		 * Add json upload HTMl
		 *
		 * Print the html of the file upload from which json will be uploaded.
		 *
		 * @since 1.8.16
		 * @return void
		 */
		public function render_upload_html() {
			$json = ( isset( $_POST['json'] ) ? give_clean( $_POST['json'] ) : '' );
			$type = ( isset( $_POST['type'] ) ? give_clean( $_POST['type'] ) : 'merge' );
			$step = $this->get_step();

			?>
			<tr valign="top">
				<th colspan="2">
					<h2 id="give-import-title"><?php esc_html_e( 'Import Core Settings from a JSON file', 'give' ) ?></h2>
					<p class="give-field-description"><?php esc_html_e( 'This tool allows you to merge or replace core settings data to your give settings via a JSON file.', 'give' ) ?></p>
				</th>
			</tr>

			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="json">Choose a json file:</label>
				</th>
				<td class="give-forminp">
					<div class="give-field-wrap">
						<label for="json">
							<input type="file" name="json" class="give-upload-json-file" value="<?php echo $json; ?>"
							       accept=".json">
							<p class="give-field-description">The file must be a JSON file type only.</p>
						</label>
					</div>
				</td>
			</tr>
			<?php
			$settings = array(
				array(
					'id'          => 'type',
					'name'        => __( 'Merge Type:', 'give' ),
					'description' => __( 'Import the Core Setting from the JSON and then merge or replace with the current settings', 'give' ),
					'default'     => $type,
					'type'        => 'radio_inline',
					'options'     => array(
						'merge'   => __( 'Merge', 'give' ),
						'replace' => __( 'Replace', 'give' ),
					),
				),
			);

			$settings = apply_filters( 'give_import_core_setting_html', $settings );

			Give_Admin_Settings::output_fields( $settings, 'give_settings' );
			?>
			<tr valign="top">
				<th></th>
				<th>
					<input type="submit"
					       class="button button-primary button-large button-secondary <?php echo "step-{$step}"; ?>"
					       id="recount-stats-submit"
					       value="<?php esc_attr_e( 'Submit', 'give' ); ?>"/>
				</th>
			</tr>
			<?php
		}

		/**
		 * Run when user click on the submit button.
		 *
		 * @since 1.8.16
		 */
		public function save() {

			// Get the current step.
			$step = $this->get_step();

			// Validation for first step.
			if ( 1 === $step ) {
				$type          = ( ! empty( $_REQUEST['type'] ) ? give_clean( $_REQUEST['type'] ) : 'replace' );
				$core_settings = self::upload_widget_settings_file();
				if ( ! empty( $core_settings['error'] ) ) {
					Give_Admin_Settings::add_error( 'give-import-csv', __( 'Please do not upload empty JSON file.', 'give' ) );
				} else {
					$file_path = explode( '/', $core_settings['file'] );
					$count     = ( count( $file_path ) - 1 );
					$url       = give_import_page_url( (array) apply_filters( 'give_import_core_settings_importing_url', array(
						'step'          => '2',
						'importer-type' => $this->importer_type,
						'type'          => $type,
						'file_name'     => $file_path[ $count ],
					) ) );

					?>
					<script type="text/javascript">
						window.location = "<?php echo $url; ?>";
					</script>
					<?php
				}
			}
		}

		/**
		 * Get if current page import donations page or not
		 *
		 * @since 1.8.16
		 * @return bool
		 */
		private function is_donations_import_page() {
			return 'import' === give_get_current_setting_tab() && isset( $_GET['importer-type'] ) && $this->importer_type === give_clean( $_GET['importer-type'] );
		}

		/**
		 * Read uploaded JSON file
		 * @return type
		 */
		public static function give_get_core_settings_json( $file_name ) {
			return give_get_core_settings_json( $file_name );
		}

		/**
		 * Upload JSON file
		 * @return boolean
		 */
		public static function upload_widget_settings_file() {
			$upload = false;
			if ( isset( $_FILES['json'] ) ) {
				add_filter( 'upload_mimes', array( __CLASS__, 'json_upload_mimes' ) );

				$upload = wp_handle_upload( $_FILES['json'], array( 'test_form' => false ) );

				remove_filter( 'upload_mimes', array( __CLASS__, 'json_upload_mimes' ) );
			} else {
				Give_Admin_Settings::add_error( 'give-import-csv', __( 'Please upload or provide a valid JSON file.', 'give' ) );
			}

			return $upload;
		}

		/**
		 * Add mime type for JSON
		 *
		 * @param array $existing_mimes
		 */
		public static function json_upload_mimes( $existing_mimes = array() ) {
			$existing_mimes['json'] = 'application/json';

			return $existing_mimes;
		}
	}

	Give_Import_Core_Settings::get_instance()->setup();
}
