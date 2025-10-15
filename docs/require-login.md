# Require Login

By default, all websites are publicly accessible. In some situations, you may want to require users to be logged in to access the
website. This is especially useful when in pre-launch mode.

Environments running in Cloud that are not of type `production` have the `require-login` feature enabled by default.

Enabling Require Login for an environment will also prevent it from being indexed by search engines. Note: Although you can
uncheck the "Discourage search engines from indexing this site" option in the Reading Settings admin page of a site, this will have
no effect if Require Login is enabled. The option will remain checked.

**Note:** Require Login only applies to URLs served from your [application servers](docs://cloud/architecture.md), and will not
apply to `/uploads/` or `/tachyon/` URLs.

## Controlling Site Access

Requiring login on individual sites is as easy as unchecking the site's public setting in the Edit Site screen. To access this
setting, go to [My Sites > Network Admin > Sites](internal://network-admin/sites.php) and then click the URL for the site you want
to edit. From there you check the box for whether the site is public or not under the "Attributes" section.

## Excluding Pages and Endpoints

In certain cases you may need to exclude a URL or PHP file from redirecting to the login page when Require Login is active. This is
possible using the `hm-require-login.allowed_pages` filter:

```php
add_filter( 'hm-require-login.allowed_pages', function ( array $allowed, ?string $page = null ) : array {
    // Allow registration on multisite.
    $allowed[] = 'wp-activate.php';
    $allowed[] = 'wp-signup.php';
    return $allowed;
}, 10, 2 );
```

The 2nd parameter `$page` is populated from WordPress's `$pagenow` global variable. If you need to make exceptions for frontend URLs
this value will be `index.php`, as such this will require additional logic to restrict which requests are allowed.

To allow a custom REST API endpoint you would do something similar to the following example:

```php
add_filter( 'hm-require-login.allowed_pages', function ( array $allowed, ?string $page = null ) : array {
    if ( $_SERVER['REQUEST_URI'] === ( '/' . rest_get_url_prefix() . '/public-endpoint/' ) ) {
        $allowed[] = $page;
    }

    return $allowed;
}, 10, 2 );
```

## Environment Specific Overrides

You can also set the `security.require-login` setting to `true` in `composer.json` to require all users to be logged in to view the
website (this will override individual sites' public setting). You can require login for all environments by adding the setting
directly under `altis.modules`, or individual environments by nesting it within `altis.environments`. The following example sets all
environments except for local to require login:

```json
{
    "altis": {
        "modules": {
            "security": {
                "require-login": true
            }
        }
    },
    "environments": {
        "local": {
            "modules": {
                "security": {
                    "require-login": false
                }
            }
        }
    }
}
```
