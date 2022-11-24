<?php
/**
 * Addons for Require Login.
 *
 * Pass query arg like: foo/bar?altis-auth=TOKEN
 */

namespace Altis\Security\Require_Login;

use Altis;

const QUERY_ARG = 'altis-auth';

/**
 * Setup.
 *
 * @return void
 */
function bootstrap() : void {
	add_filter( 'hm-require-login.allowed_pages', __NAMESPACE__ . '\\allow_request_for_valid_token', 10, 2 );
}

/**
 * Filter require login to allow request if a valid token is passed. .
 *
 * @param array $allowed Allowed pages.
 * @param string|null $page Page.
 * @return array
 */
function allow_request_for_valid_token( array $allowed, ?string $page ) : array {
	$tokens = (array) ( Altis\get_config()['modules']['security']['require-login']['bypass-tokens'] ?? [] );

	/**
	 * Filters the list of accepted values for $_GET['altis-auth'] to bypass require login.
	 *
	 * @param array $tokens Array of string tokens that by pass require login.
	 */
	$tokens = apply_filters( 'altis.security.require-login.bypass-tokens', $tokens );

	if (
		isset( $_GET[ QUERY_ARG ] ) &&
		in_array( $_GET[ QUERY_ARG ], array_values( $tokens ), true )
	) {
		$allowed[] = $page;
	}

	return $allowed;
}
