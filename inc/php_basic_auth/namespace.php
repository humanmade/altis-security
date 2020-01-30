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
 *
 * @param array|true $environment An array of options or boolean true (false
 *  would not be passed to this function). If an array was passed, use it to
 *  determine the username and password constants.
 */
function define_credentials( $environment ) {
	// Get the default config values.
	$default = get_config()['modules']['security']['php-basic-auth'];

	// If both this specific environment and the default
	if ( ! is_array( $environment ) && ! is_array( $default ) ) {
		return;
	}

	// Define the username & password with either the values passed by the environment, the defaults, or false if neither exist.
	$credentials = [
		'username' => $environment['username'] ?? $default['username'] ?? false,
		'password' => $environment['password'] ?? $default['password'] ?? false,
	];

	// Bail if we didn't actually set the username/password values.
	if ( ! $credentials['username'] || ! $credentials['password'] ) {
		return;
	}

	define( 'HM_BASIC_AUTH_USER', $credentials['username'] );
	define( 'HM_BASIC_AUTH_PW', $credentials['password'] );
}

