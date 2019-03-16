<?php

namespace HM\Platform\Security;

use HM\Platform;

require_once __DIR__ . '/inc/namespace.php';

add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled' => true,
		'require-login' => ! in_array( Platform\get_environment_type(), [ 'production', 'local' ], true ),
		'audit-log' => true,
	];
	Platform\register_module( 'security', __DIR__, 'Security', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
