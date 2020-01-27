# Limit Login Attempts

To protect against brute force attacks, Altis has the ability to limit the number of login attempts possible both through normal login as well as using auth cookies.

This feature blocks an IP address from making further attempts after a specified limit on retries is reached, making a brute-force attack difficult or impossible.

- Limit for the number of retry attempts when logging in (for each IP).
- Limit the number of attempts to log in using auth cookies in same way.
- Informs user about remaining retries or lockout time on login page.
- Optional logging, optional email notification.

**Note:** In this document, we'll only show the `limit-login-attempts` configuration for brevity.

To set the limit login attempts security settings, set values in your `composer.json` configuration under `extra.altis.modules.security.limit-login-attempts`. The default values are below, but all can be overriden.

```json
{
	"extra": {
		"altis": {
			"modules": {
				"security": {
					"limit-login-attempts": {
						"allowed_retries": 4,      // Lock out after this many tries.
						"lockout_duration": 1200,  // Lock out for this many seconds - default to 20 minutes.
						"allowed_lockouts": 4,     // Long lock out after this many lockouts.
						"long_duration": 86400,    // Long lock out for this many seconds - defaults to 24 hours.
						"valid_duration": 43200,   // Reset failed attempts after this many seconds - defaults to 12 hours.
						"cookies": 1,              // Also limit malformed/forged cookies?
						"lockout_notify": "log",   // Notify on lockout. Values: '', 'log', 'email', 'log,email'.
						"notify_email_after": 4,   // If notify by email, do so after this number of lockouts.
						"lockout_method": "ip"     // Method to use for lockout.
					}
				}
			}
		}
	}
}
```
## Configuration settings

By placing the configuration settings in your `composer.json`, the settings are locked to those values and the admin settings page is hidden.

### Allowed Retries

Limit the number of retries a user has before we lock them out. Defaults to 4.

### Lockout Duration

The amount of time, in seconds, a user is locked out on normal lock outs. Defaults to 20 minutes - `1200`.

### Allowed Lockouts

Limit the number of lockouts a user has before we lock them out with a long lock out. Defaults to `4`.

### Long Lockout Duration

The amount of time, in seconds, a user is locked out on long lock outs. Defaults to 24 hours - `86400`.

### Valid Duration

The amount of time, in seconds, the system waits to reset the failed attempts. Defaults to 12 hours - `43200`.

### Cookies

Setting to determine if we should limit any malformed or forged cookies. Defaults to `true`.

### Lockout Notify

Setting to determine which method should be used to notify site admin about a lockout. Valid values are: empty for no notification, `log`, `email`, or `log,email`. Defaults to `log`.

### Notify Email After

If `lockout_notify` set to either `email` or `log,email`, this setting is used to determine after how many lockouts the admin should notified via email. Defaults to `4`.

### Lockout Method

Setting to determine which method should be used to lock users out. Valid values are: `ip`, `username`, or `ip,username`. Defaults to `ip`.

## Enable/Disable

You can also disable the functionality altogether by setting `limit-login-attempts` to `false`:

```json
{
	"limit-login-attempts": false
}
```

If `true` provided instead of a configuration object, then the plugin will use the same defaults but are able to be overridden via the settings page in the admin under `Settings -> Limit Logins`.

```json
{
	"limit-login-attempts": true
}
```
