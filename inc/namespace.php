<?php
/**
 * Altis Security.
 *
 * @package altis/security
 */

namespace Altis\Security;

use Altis;
use Altis\Security\PHP_Basic_Auth;

/**
 * Set up action hooks.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\on_plugins_loaded', 1 );
}

/**
 * Load plugins.
 *
 * @return void
 */
function on_plugins_loaded() {
	$config = Altis\get_config()['modules']['security'];

	if ( $config['browser'] ) {
		Browser\bootstrap( $config['browser'] );
	}

	if ( $config['php-basic-auth'] ) {
		require_once Altis\ROOT_DIR . '/vendor/humanmade/php-basic-auth/plugin.php';
		PHP_Basic_Auth\bootstrap();
	}

	if ( ! is_site_public() ) {
		require_once Altis\ROOT_DIR . '/vendor/humanmade/require-login/plugin.php';
	}

	if ( $config['audit-log'] ) {
		require_once __DIR__ . '/stream/namespace.php';
		Stream\bootstrap();
	}

	if ( ! empty( $config['2-factor-authentication'] ) ) {
		add_filter( 'two_factor_providers', __NAMESPACE__ . '\\remove_2fa_dummy_provider' );
		add_filter( 'two_factor_universally_forced', __NAMESPACE__ . '\\override_two_factor_universally_forced' );
		add_filter( 'two_factor_forced_user_roles', __NAMESPACE__ . '\\override_two_factor_forced_user_roles' );
		if ( ! defined( 'WP_INSTALLING' ) || ! WP_INSTALLING ) {
			require_once Altis\ROOT_DIR . '/vendor/humanmade/two-factor/two-factor.php';
		}
	}

	if ( ! empty( $config['minimum-password-strength'] ) && $config['minimum-password-strength'] > 0 ) {
		Passwords\bootstrap();
	}

	if ( $config['disable-accounts'] ) {
		require_once Altis\ROOT_DIR . '/vendor/humanmade/disable-accounts/plugin.php';
	}
}

/**
 * Find out whether the site is/should be public.
 *
 * @return bool
 */
function is_site_public() : bool {
	// Allow public access during the install process.
	if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
		return true;
	}

	if ( defined( 'WP_INITIAL_INSTALL' ) && WP_INITIAL_INSTALL ) {
		return true;
	}

	// Allow overrides from composer.json.
	$config = Altis\get_config()['modules']['security'];
	if ( $config['require-login'] ) {
		return false;
	}

	// If there are no overrides, return whether the site is set to public.
	return get_site()->public ?? false;
}

/**
 * Remove the Dummy provider from the 2FA options.
 *
 * @param array $providers 2FA providers list.
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
 * @param bool $is_forced If true forces 2FA to be required.
 * @return bool
 */
function override_two_factor_universally_forced( bool $is_forced ) : bool {
	$config = Altis\get_config()['modules']['security']['2-factor-authentication'];
	if ( is_array( $config ) && ( ! empty( $config['required'] ) || is_bool( $config['required'] ) ) ) {
		return $config['required'];
	}

	return $is_forced;
}

/**
 * Override the two factor forced setting for enabled roles with values
 * from the Altis configuration.
 *
 * @param array|null $roles Roles required to use 2FA.
 * @return array|null
 */
function override_two_factor_forced_user_roles( $roles ) {
	$config = Altis\get_config()['modules']['security']['2-factor-authentication'];
	if ( ! empty( $config['required'] ) && is_array( $config['required'] ) ) {
		return $config['required'];
	}

	return $roles;
}
