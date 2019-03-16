<?php

namespace HM\Platform\Security;

use const HM\Platform\ROOT_DIR;
use function HM\Platform\get_config;

function bootstrap() {
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\on_plugins_loaded', 1 );
}

function on_plugins_loaded() {
	$config = get_config();
	if ( $config['modules']['security']['require-login'] ) {
		require_once ROOT_DIR . '/vendor/humanmade/require-login/plugin.php';
	}
	if ( $config['modules']['security']['audit-log'] ) {
		require_once ROOT_DIR . '/vendor/humanmade/stream/stream.php';
	}
}
