# Require Login

By default, all websites are publicly accessible. In some situations, you may want to require users to be logged in to access the website. This is especially useful when in pre-launch mode.

## Multisite Environments

In a multisite environment, requiring login is as easy as unchecking the site's public setting in the Edit Site screen. To access this setting, go to My Sites > Network Admin > Sites > Click the URL for the site you want to edit > Attributes > Public.

## Single Site Environments

Single site environments running in Cloud that are not of type `production` have the `require-login` feature enabled by default.

## Overrides

You can also set the `security.require-login` setting to `true` in `composer.json` to require all users to be logged in to view the website (this will override individual sites' public setting in a multisite environment). If you want to make a non-production single site environment public, set the `altis.environments.${ environment-name }.modules.security.require-login` setting to `false`, as shown below.

```json
"altis": {
	"modules": {
		"security": {
			"require-login": true
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
