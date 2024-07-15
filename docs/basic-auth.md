# Basic Authentication

In many instances, the [Require Login](./require-login.md) functionality of Altis is sufficient to block access to websites.
However, sometimes, it's desirable to be able to test elements (particularly on development environments) as a logged-out user.

Altis provides support for Basic Authentication access control, which
uses [standard HTTP Basic authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) to limit access instead of
WordPress users.

**Note:** Require Login only applies to URLs served from your [application servers](docs://cloud/architecture.md), and will not
apply to `/uploads/` or `/tachyon/` URLs.

**Note:** Enabling PHP Basic Auth for an environment will prevent it from being indexed with search engines.

## Configuration

By default, Basic authentication is disabled. To enable it, a value must be passed to `security.php-basic-auth`; either `true` or an
array that includes a username and password.

The recommended setup is to define everything in your `composer.json` file, including the username and passwords. The same
configuration in the manual setup example below could be handled in the Composer file like this:

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

**Note:** The authentication username and password _must_ be defined or basic authentication will not be active.

**Note:** You _must_ specify the username and password in this configuration, even if you are reusing the same username and password
in different environments.

You may also want to [disable Require Login](./require-login.md) in this configuration to ensure only one form of authentication is
used.

### Manual Configuration

Manual setup involves a simpler configuration in your `composer.json` but an additional step in your configuration. Your Composer
file would look like this:

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

This _turns on_ the Basic Auth component, but does not define the username and password. Without the username and password, basic
authentication will not be required. To specify the username and password in this configuration, you must add them as PHP constants
to a file in the `.config/` directory (e.g. `.config/load.php` or a file required by `.config/load.php`). The following example is
recommended:

```php
if ( in_array( \Altis\get_environment_type(), [ 'staging', 'development' ], true ) ) {
    define( 'HM_BASIC_AUTH_USER', 'altisusername' );
    define( 'HM_BASIC_AUTH_PW', 'altispassword' );
}
```

## Overrides

By default, Basic Auth will work on development and staging environments but not local or production environments. These defaults
can be overridden in the `composer.json` file as well, or environment-specific username/password combinations could be defined:

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
