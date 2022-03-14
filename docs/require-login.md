# Require Login

By default, all websites are publicly accessible. In some situations, you may want to require users to be logged in to access the website. This is especially useful when in pre-launch mode.

Environments running in Cloud that are not of type `production` have the `require-login` feature enabled by default.

**Note:** Enabling Require Login for an environment will prevent it from being indexed with search engines.

## Controlling Site Access

Requiring login on individual sites is as easy as unchecking the site's public setting in the Edit Site screen. To access this setting, go to [My Sites > Network Admin > Sites](internal://network-admin/sites.php) and then click the URL for the site you want to edit. From there you check the box for whether the site is public or not under the "Attributes" section.

## Overrides

You can also set the `security.require-login` setting to `true` in `composer.json` to require all users to be logged in to view the website (this will override individual sites' public setting). You can require login for all environments by adding the setting directly under `altis.modules`, or individual environments by nesting it within `altis.environments`. The following example sets all environments except for local to require login:

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
