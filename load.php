<?php

namespace HM\Platform\Security;

use HM\Platform;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/passwords/namespace.php';

add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled'                   => true,
		'require-login'             => ! in_array( Platform\get_environment_type(), [ 'production', 'local' ], true ),
		'audit-log'                 => true,
		'2-factor-authentication'   => true,
		'minimum-password-strength' => 3,
	];
	Platform\register_module( 'security', __DIR__, 'Security', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
