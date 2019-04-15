<?php

namespace HM\Platform\Security;

use HM\Platform;

require_once __DIR__ . '/inc/namespace.php';

// Don't self-initialize if this is not a Platform execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled'                 => true,
		'require-login'           => ! in_array( Platform\get_environment_type(), [ 'production', 'local' ], true ),
		'audit-log'               => true,
		'2-factor-authentication' => true,
	];
	Platform\register_module( 'security', __DIR__, 'Security', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
