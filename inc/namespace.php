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
		require_once __DIR__ . '/stream/namespace.php';
		Stream\bootstrap();
	}

	if ( ! empty( $config['2-factor-authentication'] ) ) {
		add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_2fa_dummy_provider' );
		require_once ROOT_DIR . '/vendor/humanmade/two-factor/two-factor.php';
	}

	if ( ! empty( $config['minimum-password-strength'] ) && $config['minimum-password-strength'] > 0 ) {
		Passwords\bootstrap();
	}
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
