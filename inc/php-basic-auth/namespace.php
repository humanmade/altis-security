<?php

namespace Altis\Security\PHP_Basic_Auth;

use function Altis\get_config;
use function Altis\get_environment_type;

function bootstrap() {
	add_filter( 'hmauth_filter_on_dev_env', __NAMESPACE__ . '\\filter_dev_env' );

	$config = get_config()['modules']['security']['php-basic-auth'];

	if ( is_array( $config ) ) {
		define( 'HM_BASIC_AUTH_USER', $config['username'] );
		define( 'HM_BASIC_AUTH_PW', $config['password'] );
	}
}

function filter_dev_env() {
	// Bail early for local environments.
	if ( get_environment_type() === 'local' ) {
		return false;
	}

	// Enable on all other non-production environments.
	if ( ! get_environment_type() !== 'production' ) {
		return true;
	}

	// Return false for anything else (default).
	return false;
}
