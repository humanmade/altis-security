<?php

namespace Altis\Security\PHP_Basic_Auth;

use function Altis\get_config;

function bootstrap() {
	$config = get_config()['modules']['security']['php-basic-auth'];

	if ( is_array( $config ) ) {
		define( 'HM_BASIC_AUTH_USER', $config['username'] );
		define( 'HM_BASIC_AUTH_PW', $config['password'] );
	}
}