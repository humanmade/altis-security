<?php

namespace Altis\Security\Stream;

use const Altis\ROOT_DIR;
use WP_Admin_Bar;

function bootstrap() {
	add_filter( 'wp_stream_admin_menu_title', function () : string {
		return __( 'Audit Log' );
	} );
	add_filter( 'wp_stream_admin_page_title', function () : string {
		return __( 'Audit Log Records' );
	} );

	add_filter( 'site_option_active_sitewide_plugins', __NAMESPACE__ . '\\set_stream_networked_activated' );
	add_filter( 'site_option_wp_stream_network', __NAMESPACE__ . '\\default_stream_network_options' );
	add_filter( 'default_site_option_wp_stream_network', __NAMESPACE__ . '\\default_stream_network_options' );
	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_stream_admin_pages', 11 );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\override_network_admin_bar_menu', 100 );

	require_once ROOT_DIR . '/vendor/humanmade/stream/stream.php';
}

function default_stream_network_options( $options ) : array {
	$options['general_site_access'] = 0;
	$options['general_keep_records_indefinitely'] = true;
	return $options;
}

/**
 * Filter the network wide activated plugin to include stream
 *
 * This will not actually get included (as the path doesn't exist)
 * but this is the only way to trigger Stream's Network Admin configuration
 * option.
 *
 * @param array|bool $plugins
 * @return array
 */
function set_stream_networked_activated( $plugins ) : array {
	$plugins['stream/stream.php'] = true;
	return $plugins;
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
 * @param WP_Admin_Bar $wp_admin_bar
 */
function override_network_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
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
