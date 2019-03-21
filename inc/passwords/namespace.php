<?php

namespace HM\Platform\Security\Passwords;

use function HM\Platform\get_config;
use WP_Error;
use ZxcvbnPhp\Zxcvbn;

/**
 * Bootstrap.
 */
function bootstrap() {
	add_action( 'check_passwords', __NAMESPACE__ . '\\enforce_password_strength', 10, 3 );
	add_action( 'admin_print_styles-user-edit.php', __NAMESPACE__ . '\\hide_weak_password_prompt' );
	add_action( 'admin_print_styles-profile.php', __NAMESPACE__ . '\\hide_weak_password_prompt' );
}

/**
 * Get minimum required strength for passwords.
 *
 * @return int
 */
function get_minimum_strength() : int {
	$config = get_config()['modules']['security'];

	/**
	 * Filter the minimum required password strength.
	 *
	 * Filter the required strength for a password. These scores correspond to
	 * the following UI strings:
	 *
	 * - 1 = "Very Weak"
	 * - 2 = "Weak"
	 * - 3 = "Medium"
	 * - 4 = "Strong"
	 *
	 * @param int $minimum_strength Minimum required strength. Default is 3.
	 */
	return apply_filters( 'hm-platform.security.passwords.minimum_strength', $config['minimum-password-strength'] );
}

/**
 * Enforce minimum password strength during password check.
 *
 * @param string $user_login User login slug
 * @param string $pass1 First password input field
 * @param string $pass2 Second password input field
 */
function enforce_password_strength( $user_login, &$pass1, &$pass2 ) {
	if ( empty( $pass1 ) || $pass1 !== $pass2 ) {
		// Handled by WordPress, skip.
		return;
	}

	// Add extra data to password strength checks.
	// (Matches list in password-strength-meter.js)
	$user = get_user_by( 'login', $user_login );
	$extra_data = [
		$user->user_login,
		$user->first_name,
		$user->last_name,
		$user->nickname,
		$user->display_name,
		$user->email,
		$user->url,
		$user->description,
	];

	$checker = new Zxcvbn();
	$results = $checker->passwordStrength( $pass1, $extra_data );
	$is_weak = $results['score'] < get_minimum_strength();

	/**
	 * Filter whether a password is considered weak.
	 *
	 * @param boolean $is_weak True if the password is considered weak, and should be rejected. False to allow the password.
	 * @param string $pass1 Supplied password.
	 * @param WP_User $user User the password is being changed for.
	 * @param array $results Results from Zxcvbn's check.
	 */
	$is_weak = apply_filters( 'hm-platform.security.passwords.is_weak', $is_weak, $pass1, $user, $results );
	if ( ! $is_weak ) {
		// Password is strong enough, allow it.
		return;
	}

	// Weak password. Clear both password fields to halt update, and add our
	// custom error.
	$pass1 = $pass2 = null;
	add_action( 'user_profile_update_errors', __NAMESPACE__ . '\\add_strength_error' );
}

/**
 * Add password strength error to the error list.
 *
 * Used as a callback inside enforce_password_strength()
 *
 * @param WP_Error $errors Error object to add error to.
 */
function add_strength_error( WP_Error $errors ) {
	$errors->add( 'pass', __( '<strong>ERROR</strong>: Please use a stronger password.' ), array( 'form-field' => 'pass1' ) );
}

/**
 * Hide "confirm use of weak password" prompt if minimum strength is set.
 */
function hide_weak_password_prompt() {
	// Are we allowing weak passwords?
	if ( get_minimum_strength() < 1 ) {
		return;
	}

	echo '<style>.pw-weak { display: none !important; }</style>';
}
