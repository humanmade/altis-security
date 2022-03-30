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
		'disable-accounts'          => true,
		'2-factor-authentication'   => Altis\get_environment_type() === 'local' ? true : [
			'required' => [
				'super-admin',
				'administrator',
			],
		],
		'minimum-password-strength' => 2,
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
			'strict-transport-security' => true,
			'xss-protection-header' => true,
		],
	];
	$options = [
		'defaults' => $default_settings,
	];
	Altis\register_module( 'security', __DIR__, 'Security', $options, __NAMESPACE__ . '\\bootstrap' );
} );
