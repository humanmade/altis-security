<?php

namespace Altis\Security\Limit_Login_Attempts;

use const Altis\ROOT_DIR;

/**
 * Bootstrap.
 */
function bootstrap() {
	// Set default constants for Altis.
	define( 'HM_LIMIT_LOGIN_DIRECT_ADDR', 'HTTP_X_FORWARDED_FOR' );

	// Load plugin.
	require_once ROOT_DIR . '/vendor/humanmade/hm-limit-login-attempts/hm-limit-login-attempts.php';
}
