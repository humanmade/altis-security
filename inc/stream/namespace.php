<?php

namespace HM\Platform\Security\Stream;

use const HM\Platform\ROOT_DIR;

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
