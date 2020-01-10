<?php

namespace Altis\Security\Limit_Login_Attempts;

use const Altis\ROOT_DIR;
use function Altis\get_config;

/**
 * Bootstrap.
 */
function bootstrap() {
	// Set default constants for Altis.
	define( 'HM_LIMIT_LOGIN_DIRECT_ADDR', 'HTTP_X_FORWARDED_FOR' );

	$config = get_config()['modules']['security']['limit-login-attempts'];

	// Load plugin.
	require_once ROOT_DIR . '/vendor/humanmade/hm-limit-login-attempts/hm-limit-login-attempts.php';

	// If config is just `true`, enable plugin and show admin page.
	if ( is_array( $config ) ) {
		// Otherwise, don't display admin page and use config values.
		add_action( 'admin_menu', __NAMESPACE__ . '\\remove_admin_page', 99 );

		// Set pre_option filters on each config item.
		foreach ( $config as $option_name => $option_value ) {
			add_filter( 'pre_option_hm_limit_login_' . $option_name, function() {
				return $option_value;
			} );
		}
	}
}

function remove_admin_page() {
	remove_submenu_page( 'options-general.php', 'hm-limit-login-attempts' );
}
