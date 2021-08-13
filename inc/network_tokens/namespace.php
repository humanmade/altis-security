<?php
/**
 * Altis Security Network Tokens.
 *
 * Enables cross-network communications using nonce-like network-wide tokens, using global object cache.
 *
 * @package altis/security
 */

namespace Altis\Security\Network_Tokens;

const NETWORK_TOKENS_TTL = 12 * HOUR_IN_SECONDS;
const NETWORK_TOKENS_CACHE_GROUP = 'network-tokens';

/**
 * Bootstrap the feature.
 *
 * @return void
 */
function bootstrap() : void {
	wp_cache_add_global_groups( NETWORK_TOKENS_CACHE_GROUP );
}

/**
 * Generate a textual token for a specific action / user id.
 *
 * @param string $action Token context.
 * @param integer $user_id User ID.
 *
 * @return string
 */
function generate_network_token( string $action, int $user_id ) : string {
	return substr( wp_hash( $action . '|' . $user_id, 'token' ), -12, 10 );
}

/**
 * Create and store a network token.
 *
 * @param string $action Token context.
 *
 * @return string|null
 */
function create_network_token( string $action ) : ?string {
	$user = wp_get_current_user();
	if ( empty( $user ) ) {
		return null;
	}

	$token = generate_network_token( $action, $user->ID ) . $user->ID;

	if ( ! wp_cache_get( $token, NETWORK_TOKENS_CACHE_GROUP ) ) {
		wp_cache_set( $token, 1, NETWORK_TOKENS_CACHE_GROUP, NETWORK_TOKENS_TTL );
	}

	return $token;
}

/**
 * Verify a stored network token.
 *
 * @param string $token Token to verify.
 * @param string $action Token context.
 *
 * @return boolean
 */
function verify_network_token( string $token, string $action ) : bool {
	list( $token, $user_id ) = str_split( $token, 10 );

	// Verify the token is in correct format.
	if ( $token !== generate_network_token( $action, $user_id ) ) {
		return false;
	}

	// Verify the token is saved and is still valid.
	$cache_key = $token . $user_id;
	$cached = wp_cache_get( $cache_key, NETWORK_TOKENS_CACHE_GROUP, false, $found ) ?: '';
	if ( $found && $cached ) {
		return true;
	}

	return false;
}
