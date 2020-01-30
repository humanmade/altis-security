# PHP Basic Auth

In many instances, the Require Login component of Altis is sufficient to block access to websites. However, sometimes, it's desirable to be able to test elements -- particularly on development environments -- as a logged-out user. In these cases, the Require Login module does not solve the problem and another method of blocking access is required. That's where PHP Basic Auth comes in.

PHP Basic Auth allows an engineering team to restrict access to a site using basic PHP authentication. On first load, the website will request a username and password -- if they are not passed, or the wrong username and password are provided, the site will not load.

**Note:** The authentication username and password _must_ be defined or basic authentication will not be active.

## Configuration

By default, PHP Basic Auth is disabled. To enable it, a value must be passed to `security.php-basic-auth` -- either `true` or an array that includes a username and password.

### Altis

The recommended setup is to define everything in your `composer.json` file, including the username and passwords. The same configuration in the below, manual setup example could be handled in the Composer file like this:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"php-basic-auth": {
						"username": "altisusername",
						"password": "altispassword"
					}
				}
			}
		}
	}
}
```

### Manual

Manual setup involves a simpler configuration in your `composer.json` but an additional step in your configuration. Your Composer file would look like this:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"php-basic-auth": true
				}
			}
		}
	}
}
```

This _turns on_ the PHP Basic Auth component, but does not define the username and password. Without the username and password, basic authentication will not be required. To specify the username and password in this configuration, you must add them as PHP constants to a file in the `.config/` directory (e.g. `.config/load.php` or a file required by `.config/load.php`). The following example is recommended:

```php
if ( in_array( \Altis\get_environment_type(), [ 'staging', 'development' ] ) {
	define( 'HM_BASIC_AUTH_USER', 'altisusername' );
	define( 'HM_BASIC_AUTH_PW', 'altispassword' );
}
```

## Overrides

By default, PHP Basic Auth will work on development and staging environments but not local or production environments. These defaults can be overridden in the `composer.json` file as well, or environment-specific username/password combinations could be defined:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"php-basic-auth": {
						"username": "devuser",
						"password": "devpass"
					}
				}
			},
			"environments": {
				"local": {
					"modules": {
						"security": {
							"php-basic-auth": {
								"username": "altis",
								"password": "altis"
							}
						}
					}
				},
				"production": {
					"modules": {
						"security": {
							"php-basic-auth": {
								"username": "produser",
								"password": "prodpass"
							}
						}
					}
				}
			}
		}
	}
}
```
