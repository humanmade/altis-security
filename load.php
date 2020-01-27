<?php

namespace Altis\Security; // @codingStandardsIgnoreLine

use function Altis\register_module;

add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled'                   => true,
		'require-login'             => false,
		'php-basic-auth'            => false,
		'audit-log'                 => true,
		'2-factor-authentication'   => true,
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
			'xss-protection-header' => true,
		],
	];
	register_module( 'security', __DIR__, 'Security', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
