<?php
/**
 * Altis Security Module.
 *
 * @package altis/security
 */

namespace Altis\Security; // phpcs:ignore

use Altis;

add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled'                   => true,
		'require-login'             => ! in_array( Altis\get_environment_type(), [ 'production', 'local' ], true ),
		'php-basic-auth'            => false,
		'audit-log'                 => true,
		'2-factor-authentication'   => true,
		'minimum-password-strength' => 2,
		'limit-login-attempts'      => [
			'allowed_retries'     => 4,       // Lock out after this many tries.
			'lockout_duration'    => 1200,    // Lock out for this many seconds - default to 20 minutes.
			'allowed_lockouts'    => 4,       // Long lock out after this many lockouts.
			'long_duration'       => 86400,   // Long lock out for this many seconds - defaults to 24 hours.
			'valid_duration'      => 43200,   // Reset failed attempts after this many seconds - defaults to 12 hours.
			'cookies'             => 1,       // Also limit malformed/forged cookies?
			'lockout_notify'      => 'log',   // Notify on lockout. Values: '', 'log', 'email', 'log,email'.
			'notify_email_after'  => 4,       // If notify by email, do so after this number of lockouts.
			'lockout_method'      => 'ip',    // Method to use for lockout.
			'whitelisted_ips'     => [],      // Array of IP addresses to whitelist.
		],
		'browser' => [
			'automatic-integrity' => true,
			'content-security-policy' => [
				'base-uri' => [
					'self',
				],
				'object-src' => [
					'none',
				],
			],
			'frame-options-header' => true,
			'nosniff-header' => true,
			'xss-protection-header' => true,
		],
	];
	Altis\register_module( 'security', __DIR__, 'Security', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
