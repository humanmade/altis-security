<?php

namespace HM\Platform\Security;

use const HM\Platform\ROOT_DIR;
use function HM\Platform\get_config;

function bootstrap() {
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\on_plugins_loaded', 1 );
}

function on_plugins_loaded() {
	$config = get_config()['modules']['security'];

	if ( $config['require-login'] ) {
		require_once ROOT_DIR . '/vendor/humanmade/require-login/plugin.php';
	}
	if ( $config['audit-log'] ) {
		add_filter( 'wp_stream_admin_menu_title', function () : string {
			return __( 'Audit Log' );
		} );
		add_filter( 'wp_stream_admin_page_title', function () : string {
			return __( 'Audit Log Records' );
		} );
		/**
		 * Filter the network wide activated plugin to include stream
		 *
		 * This will not actually get included (as the path doesn't exist)
		 * but this is the only way to trigger Stream's Network Admin configuration
		 * option.
		 */
		add_filter( 'site_option_active_sitewide_plugins', function ( $plugins ) : array {
			$plugins['stream/stream.php'] = true;
			return $plugins;
		} );

		add_filter( 'site_option_wp_stream_network', __NAMESPACE__ . '\\default_stream_network_options' );
		add_filter( 'default_site_option_wp_stream_network', __NAMESPACE__ . '\\default_stream_network_options' );

		add_action( 'netdwork_admin_menu', function () {
			/**
			 * @var \Stream\Plugin
			 */
			global $wp_stream;
			remove_submenu_page( $wp_stream->admin->records_page_slug, $wp_stream->admin->network->network_settings_page_slug );
		}, 11 );
		require_once ROOT_DIR . '/vendor/humanmade/stream/stream.php';
	}

	if ( ! empty( $config['2-factor-authentication'] ) ) {
		add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_2fa_dummy_provider' );
		require_once ROOT_DIR . '/vendor/humanmade/two-factor/two-factor.php';
	}
}

function default_stream_network_options( $options ) : array {
	$options['general_site_access'] = 0;
	$options['keep_records_indefinitely'] = true;
	return $options;
}

/**
 * Remove the Dummy provider from the 2FA options.
 *
 * @param array $providers
 * @return array
 */
function remove_2fa_dummy_provider( array $providers ) : array {
	if ( isset( $providers['Two_Factor_Dummy'] ) ) {
		unset( $providers['Two_Factor_Dummy'] );
	}
	return $providers;
}
