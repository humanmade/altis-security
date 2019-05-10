<?php

namespace Altis\Security;

use function Altis\get_environment_type;
use function Altis\register_module;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/passwords/namespace.php';

// Don't self-initialize if this is not an Altis execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

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
