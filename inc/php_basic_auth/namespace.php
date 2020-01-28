<?php

namespace Altis\Security\PHP_Basic_Auth;

use function Altis\get_config;
use function Altis\get_environment_type;

function bootstrap() {
	add_filter( 'hmauth_filter_on_dev_env', __NAMESPACE__ . '\\filter_dev_env' );
}

/**
 * Conditionally short-circuit the basic authentication settings based on the
 * composer.json configuration.
 */
function filter_dev_env() {
	// Collect the environment overrides.
	$env_local = get_config()['environments']['local']['security']['php-basic-auth'];
	$env_dev   = get_config()['environments']['local']['security']['php-basic-auth'];
	$env_stage = get_config()['environments']['local']['security']['php-basic-auth'];
	$env_prod  = get_config()['environments']['local']['security']['php-basic-auth'];

	switch ( get_environment_type() ) {
		case 'local':
			// Local environments are false by default, so check if this has been overridden.
			if ( $env_local ) {
				define_credentials( $env_local );
				return true;
			}

			// Local environment authentication defaults to false.
			return false;

		case 'development':
			// Development environments are true by default, so check if it was explicitly disabled.
			if ( $env_dev === false ) {
				return false;
			}

			define_credentials( $env_dev );
			return true;

		case 'staging':
			// Staging environments are true by default, so check if it was explicitly disabled.
			if ( $env_stage === false ) {
				return false;
			}

			define_credentials( $env_stage );
			return true;

		case 'production':
			// Production environments need to be explicitly enabled, otherwise they default to false.
			if ( $env_prod ) {
				define_credentials( $env_prod );
				return true;
			}

			return false;

		default:
			// If we're in a development environment, default to enabling basic auth.
			if ( in_array( get_environment_type(), [ 'development', 'staging' ] ) ) {
				// Use the default credentials from the config.
				define_credentials( true );
				return true;
			}

			return false;
	}
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

