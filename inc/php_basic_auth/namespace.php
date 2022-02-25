<?php
/**
 * Altis Security Basic Auth.
 *
 * @package altis/security
 */

namespace Altis\Security\PHP_Basic_Auth;

use Altis;

/**
 * Set up action hooks.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'hmauth_action_before_dev_env_check', __NAMESPACE__ . '\\define_credentials' );
	add_filter( 'hmauth_filter_dev_env', __NAMESPACE__ . '\\force_enable' );
}

/**
 * Set the HM_BASIC_AUTH_USER and HM_BASIC_AUTH_PW constants based on the
 * parameters passed in the composer.json file.
 */
function define_credentials() {
	// Get the config values.
	$config = Altis\get_config()['modules']['security']['php-basic-auth'];

	// Bail if we didn't actually set the username/password values.
	if ( ! is_array( $config ) ) {
		return;
	}

	// Check username and password are both set.
	if ( ! isset( $config['username'], $config['password'] ) ) {
		trigger_error( 'Both the username and password must be specified for PHP Basic Auth to function', E_USER_WARNING );
		return;
	}

	defined( 'HM_BASIC_AUTH_USER' ) or define( 'HM_BASIC_AUTH_USER', $config['username'] );
	defined( 'HM_BASIC_AUTH_PW' ) or define( 'HM_BASIC_AUTH_PW', $config['password'] );
}

/**
 * Force the use of basic auth, even in production, if configured.
 *
 * @param bool $should_enable Should Basic Auth be enabled?
 * @return bool Existing value by default.
 */
function force_enable( $should_enable ) {
	$environment = Altis\get_environment_type();
	$env_config = Altis\get_config()['environments'][ $environment ]['modules']['security']['basic-auth'] ?? [];

	// If there's no config, use the existing values.
	if ( empty( $env_config ) || ! is_array( $env_config ) ) {
		return $should_enable;
	}

	return true;
}
