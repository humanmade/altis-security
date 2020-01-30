<?php

namespace Altis\Security\PHP_Basic_Auth;

use function Altis\get_config;
use function Altis\get_environment_type;

function bootstrap() {
	add_action( 'hmauth_action_before_dev_env_check', __NAMESPACE__ . '\\check_environment_credentials' );
}

/**
 * Set the HM_BASIC_AUTH_USER and HM_BASIC_AUTH_PW constants based on the
 * parameters passed in the composer.json file.
 */
function define_credentials() {
	// Get the config values.
	$config = get_config()['modules']['security']['php-basic-auth'];

	// Bail if we didn't actually set the username/password values.
	if ( ! isset( $config['username'] ) || ! isset( $config['password'] ) ) {
		trigger_error( 'You need to specify both a username and a password to use basic authentication.', E_USER_WARNING );
		return;
	}

	defined( 'HM_BASIC_AUTH_USER' ) or define( 'HM_BASIC_AUTH_USER', $config['username'] );
	defined( 'HM_BASIC_AUTH_PW' ) or define( 'HM_BASIC_AUTH_PW', $config['password'] );
}
