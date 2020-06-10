<?php
/**
 * Altis Security Limit Logins Integration.
 *
 * @package altis/security
 */

namespace Altis\Security\Limit_Login_Attempts;

use Altis;

/**
 * Bootstrap.
 */
function bootstrap() {
	// Set default constants for Altis.
	define( 'HM_LIMIT_LOGIN_DIRECT_ADDR', 'HTTP_X_FORWARDED_FOR' );

	$config = Altis\get_config()['modules']['security']['limit-login-attempts'];

	// Load plugin.
	if ( ! defined( 'WP_INSTALLING' ) || ! WP_INSTALLING ) {
		add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
	}

	// If config is just `true`, enable plugin and show admin page.
	if ( is_array( $config ) ) {
		// Otherwise, don't display admin page and use config values.
		add_action( 'admin_menu', __NAMESPACE__ . '\\remove_admin_page', 99 );

		if ( ! empty( $config['whitelisted_ips'] ) ) {
			add_filter( 'hm_limit_login_whitelist_ip', __NAMESPACE__ . '\\check_whitelist', 10, 2 );
		}

		// Set pre_option filters on each config item.
		foreach ( $config as $option_name => $option_value ) {
			// Whitelisted IP's are used for the above filter, not options.
			if ( $option_name === 'whitelisted_ips' ) {
				continue;
			}

			// Only options that are not integers are `lockout_notify` and `lockout_method`.
			if ( ! in_array( $option_name, [ 'lockout_notify', 'lockout_method' ], true ) ) {
				$option_value = intval( $option_value );
			}

			add_filter( 'pre_option_hm_limit_login_' . $option_name, function () use ( $option_value ) {
				return $option_value;
			} );
		}
	}
}

/**
 * Includes the plugin file.
 */
function load_plugin() {
	require_once Altis\ROOT_DIR . '/vendor/humanmade/hm-limit-login-attempts/hm-limit-login-attempts.php';
}

/**
 * Check the current IP against the whitelisted IP's from the config.
 *
 * @param bool $allow Determines if the IP is allowed or not.
 * @param string $ip  Current IP address to check.
 *
 * @return bool
 */
function check_whitelist( $allow, $ip ) {
	$config = Altis\get_config()['modules']['security']['limit-login-attempts'];

	if ( in_array( $ip, $config['whitelisted_ips'], true ) ) {
		return true;
	}

	return $allow;
}

/**
 * Removes the settings page from the admin menu.
 */
function remove_admin_page() {
	remove_submenu_page( 'options-general.php', 'hm-limit-login-attempts' );
}
