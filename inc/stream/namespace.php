<?php
/**
 * Altis Security Audit Log.
 *
 * @package altis/security
 */

namespace Altis\Security\Stream;

use Altis;
use HM\Platform\XRay;
use WP_Admin_Bar;

/**
 * Bootstrap Stream plugin.
 *
 * @return void
 */
function bootstrap() {
	if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
		return;
	}

	add_filter( 'wp_stream_admin_menu_title', function () : string {
		return __( 'Audit Log' );
	} );
	add_filter( 'wp_stream_admin_page_title', function () : string {
		return __( 'Audit Log Records' );
	} );

	add_filter( 'wp_stream_is_network_activated', '__return_true' );
	add_filter( 'site_option_wp_stream_network', __NAMESPACE__ . '\\default_stream_network_options' );
	add_filter( 'default_site_option_wp_stream_network', __NAMESPACE__ . '\\default_stream_network_options' );
	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_stream_admin_pages', 11 );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\override_network_admin_bar_menu', 100 );
	add_filter( 'wp_stream_record_array', __NAMESPACE__ . '\\filter_wp_stream_record_array', 10, 1 );
	require_once Altis\ROOT_DIR . '/vendor/humanmade/stream/stream.php';
}

/**
 * Set default Stream plugin config options.
 *
 * @param array $options Stream configuration options.
 * @return array
 */
function default_stream_network_options( array $options ) : array {
	$options['general_site_access'] = 0;
	$options['general_keep_records_indefinitely'] = true;
	return $options;
}

/**
 * Remove the stream network admin settings page.
 */
function remove_stream_admin_pages() {
	/**
	 * @var \Stream\Plugin
	 */
	global $wp_stream;
	remove_submenu_page( $wp_stream->admin->records_page_slug, $wp_stream->admin->network->network_settings_page_slug );
}

/**
 * Override the Stream admin bar menu.
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar manager class.
 */
function override_network_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	// Menu item is only registered inside the admin, so don't try and replace
	// it if it hasn't been registered.
	if ( empty( wp_stream_get_instance()->admin ) ) {
		return;
	}

	$wp_admin_bar->remove_menu( 'network-admin-stream' );
	$href = add_query_arg(
		[
			'page' => wp_stream_get_instance()->admin->records_page_slug,
		],
		network_admin_url( wp_stream_get_instance()->admin->admin_parent_page )
	);

	$wp_admin_bar->add_menu(
		[
			'id'     => 'network-admin-stream',
			'parent' => 'network-admin',
			'title'  => esc_html__( 'Audit Log', 'altis' ),
			'href'   => esc_url( $href ),
		]
	);
}

/**
 * Add the Xray ID to the log item meta data.
 *
 * @param array $record The stream log record to filter.
 *
 * @return array
 */
function filter_wp_stream_record_array( $record ) : array {
	if ( ! function_exists( 'XRay\\get_root_trace_id' ) ) {
		return $record;
	}

	$record['meta']['xray'] = XRay\get_root_trace_id();
	return $record;
}
