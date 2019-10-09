<?php

namespace Altis\Security;

use const Altis\ROOT_DIR;
use function Altis\get_config;
use function Altis\get_environment_type;

function bootstrap() {
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\on_plugins_loaded', 1 );
}

function on_plugins_loaded() {
	$config = get_config()['modules']['security'];

	if ( ! is_site_public() ) {
		require_once ROOT_DIR . '/vendor/humanmade/require-login/plugin.php';
	}

	if ( $config['audit-log'] ) {
		require_once __DIR__ . '/stream/namespace.php';
		Stream\bootstrap();
	}

	if ( ! empty( $config['2-factor-authentication'] ) ) {
		add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_2fa_dummy_provider' );
		add_filter( 'two_factor_universally_forced', __NAMESPACE__ . '\\override_two_factor_universally_forced' );
		add_filter( 'two_factor_forced_user_roles', __NAMESPACE__ . '\\override_two_factor_forced_user_roles' );
		require_once ROOT_DIR . '/vendor/humanmade/two-factor/two-factor.php';
	}

	if ( ! empty( $config['minimum-password-strength'] ) && $config['minimum-password-strength'] > 0 ) {
		Passwords\bootstrap();
	}
}

/**
 * Find out whether the site is/should be public.
 *
 * @return bool
 */
function is_site_public() : bool {

	// Allow overrides from composer.json.
	$config = get_config()['modules']['security'];
	if ( $config['require-login'] ) {
		return false;
	}

	// If there are no overrides, return whether the site is set to public.
	$site = get_site();

	return $site instanceof WP_Site ? $site->public : false;
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

/**
 * Override the two factor forced setting with values from the Altis configuration.
 *
 * @param bool $is_forced
 * @return bool
 */
function override_two_factor_universally_forced( bool $is_forced ) : bool {
	$config = get_config()['modules']['security']['2-factor-authentication'];
	if ( ! empty( $config['required'] ) && is_bool( $config['required'] ) ) {
		return $config['required'];
	}

	return $is_forced;
}

/**
 * Override the two factor forced setting for enabled roles with values
 * from the Altis configuration.
 *
 * @param array|null $roles
 * @return array|null
 */
function override_two_factor_forced_user_roles( $roles ) {
	$config = get_config()['modules']['security']['2-factor-authentication'];
	if ( ! empty( $config['required'] ) && is_array( $config['required'] ) ) {
		return $config['required'];
	}

	return $roles;
}
