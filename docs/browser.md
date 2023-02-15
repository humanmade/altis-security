# Browser Security

Altis includes a framework to ensure frontend security in browsers. Out of the box, Altis sends basic security headers, but we recommend sending more specific headers where you can.

To set browser security settings, set values in your `composer.json` configuration under `extra.altis.modules.security.browser`:

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"browser": {
						"automatic-integrity": true
					}
				}
			}
		}
	}
}
```

**Note:** In this document, we'll only show the `browser` configuration for brevity.

You can also disable browser security altogether by setting `browser` to `false`:

```json
{
	"browser": false
}
```


## Content-Security-Policy

Altis can automatically gather and send [Content-Security-Policy policies](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy) for you.


### Site-wide policy

Out of the box, only basic policies are sent:

* `base-uri 'self'` is sent, which disables the ability to output `<base>` tags, ensuring links and assets cannot be hijacked
* `object-src 'none'` is sent, which blocks `<object>`, `<embed>`, and `<applet>` tags entirely

(These are considered best practices for all sites, but can be overridden if needed.)

To change the default Content-Security-Policy, you can configure it in your `composer.json` under `extra.altis.modules.security.browser.content-security-policy`:

```json
{
	"browser": {
		"content-security-policy": {
			"base-uri": [
				"self"
			],
			"object-src": [
				"none"
			]
		}
	}
}
```

Keys under `content-security-policy` should be a valid [directive](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy#Directives), and the value should be either a string or list of strings:

```json
{
	"browser": {
		"content-security-policy": {
			"base-uri": [
				"self"
			],
			"object-src": [
				"none"
			],
			"font-src": [
				"https://fonts.gstatic.com",
				"https://cdnjs.cloudflare.com"
			],
			"script-src": [
				"https:",
				"unsafe-inline"
			]
		}
	}
}
```

Special directives (`'self'`, `'unsafe-inline'`, `'unsafe-eval'`, `'none'`, `'strict-dynamic'`) do not need to be double-quoted.

To build Content-Security-Policy policies, we recommend using the [Laboratory CSP toolkit extension](https://addons.mozilla.org/en-US/firefox/addon/laboratory-by-mozilla/) for Firefox, and the [CSP Evaluator tool](https://csp-evaluator.withgoogle.com/).


### Page-specific policies

For page-specific policies, you can add a filter to `altis.security.browser.content_security_policies` to set policies. This filter receives an array, where the keys are the policy directive names. Each item can either be a string or a list of directive value strings:

```php
add_filter( 'altis.security.browser.content_security_policies', function ( array $policies ) : array {
	// Policies can be set as strings.
	$policies['object-src'] = 'none';
	$policies['base-uri'] = 'self';

	// Policies can also be set as arrays.
	$policies['font-src'] = [
		'https://fonts.gstatic.com',
		'https://cdnjs.cloudflare.com',
	];

	// Special directives (such as `unsafe-inline`) are handled for you.
	$policies['script-src'] = [
		'https:',
		'unsafe-inline',
	];

	return $policies;
} );
```

You can also modify individual directives if desired:

```php
// You can filter specific keys via the filter name.
add_filter( 'altis.security.browser.filter_policy_value.font-src', function ( array $values ) : array {
	$values[] = 'https://fonts.gstatic.com';
	return $values;
} );

// A filter is also available with the directive name in a parameter.
add_filter( 'altis.security.browser.filter_policy_value', function ( array $values, string $name ) : array {
	if ( $name === 'font-src' ) {
		$values[] = 'https://cdnjs.cloudflare.com';
	}

	return $values;
} );
```



### Subresource Integrity

Altis automatically adds [subresource integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity) hashes where possible. These are generated for any files on the same server; i.e. any plugin or theme assets.

For external assets, you can manually set the integrity hash. After enqueuing (or registering) your asset, use the `set_hash_for_script()` or `set_hash_for_style()` helpers:

```php
// Setting hashes for scripts.
use function Altis\Security\Browser\set_hash_for_script;
wp_enqueue_script( 'my-handle', 'https://...' );
set_hash_for_script( 'my-handle', 'sha384-...' );

// Setting hashes for styles.
use function Altis\Security\Browser\set_hash_for_style;
wp_enqueue_style( 'my-handle', 'https://...' );
set_hash_for_style( 'my-handle', 'sha384-...' );
```

Automatically-generated hashes are automatically cached in the object cache, linked to the filename and version of the script or stylesheet.

You can disable the automatic generation of the integrity hashes if desired by setting `browser.automatic-integrity` to `false`:

```json
{
	"browser": {
		"automatic-integrity": false
	}
}
```


### Security Headers

Altis automatically adds various miscellaneous security headers by default. These follow best-practices for web security and aim to provide a sensible, secure default.

In some cases, you may want to adjust or disable these headers depending on the use cases of your site.


#### Strict-Transport-Security

The [`Strict-Transport-Security` header](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security) (sometimes called HSTS) is used to enforce HTTPS (TLS/SSL) connections when loading a site and can be used to enhance the site's security.

By default, Altis enables HSTS with the value `max-age=31536000; includeSubDomains`. You can configure the header using the `strict-transport-security` setting:

```json
{
	"browser": {
		"strict-transport-security": "max-age=3600"
	}
}
```

You can also switch the header off completely by setting this to false:

```json
{
	"browser": {
		"strict-transport-security": false
	}
}
```

Finally, if you set the value to `null` then Altis _will_ send the header but only if the current request is already using HTTPS.


#### X-Content-Type-Options

By default, Altis adds a [`X-Content-Type-Options` header](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options) with the value set to `nosniff`. This prevents browsers from attempting to guess the content type based on the content, and instead forces them to follow the type set in the `Content-Type` header.

This should generally always be sent, and your content type should always be set explicitly. If you need to disable it, set `browser.nosniff-header` to `false`:

```json
{
	"browser": {
		"nosniff-header": false
	}
}
```


#### X-Frame-Options

By default, Altis adds a [`X-Frame-Options` header](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options) with the value set to `sameorigin`. This prevents your site from being iframed into another site, which can prevent [clickjacking attacks](https://en.wikipedia.org/wiki/Clickjacking).

This should generally always be sent, but in some cases, you may want to allow specific sites to iframe your site, or allow any sites. To disable the automatic header, set `browser.frame-options-header` to `false`:

```json
{
	"browser": {
		"frame-options-header": false
	}
}
```

You can then send your own headers as needed. We recommend hooking into the `template_redirect` hook to send these headers.


#### X-XSS-Protection

By default, Altis adds a [`X-XSS-Protection` header](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection?) with the value set to `1; mode=block`. This prevents browsers from loading if they detect [cross-site scripting (XSS) attacks](https://www.owasp.org/index.php/Cross-site_Scripting_(XSS)).

This should generally always be sent. If you need to disable it, set `browser.xss-protection-header` to `false`:

```json
{
	"browser": {
		"xss-protection-header": false
	}
}
```
