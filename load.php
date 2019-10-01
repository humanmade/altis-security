<?php

namespace Altis\Security; // @codingStandardsIgnoreLine

use function Altis\get_environment_type;
use function Altis\register_module;

add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled'                   => true,
		'require-login'             => ! in_array( get_environment_type(), [ 'production', 'local' ], true ),
		'audit-log'                 => true,
		'2-factor-authentication'   => true,
		'minimum-password-strength' => 2,
	];
	register_module( 'security', __DIR__, 'Security', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
