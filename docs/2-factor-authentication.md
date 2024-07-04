# Two Factor Authentication

For increased security of user authentication, Altis supports the use of a second factor to authorize the login request. The Two
Factor Authentication feature is enabled by default, and only required for network administrators and site administrators by
default.

To disable Two Factor Authentication, set the `modules.security.2-factor-authentication` setting to `false`.

Second factor authentication options are Email, Time-based one-time passwords and FIDO Universal 2nd Factor (U2F).

Two Factor methods can be configured by each user in their Edit Profile page in the CMS.

**Note:** Two Factor Authentication is not required on local environments for convenience, in order to require it, use local
environment specific configuration.

## Requiring Two Factor Authentication

The site can be configured to require all users enable two factor authentication, or set requirement options on a per-role basis. To
require all users of the site enable two factor authentication set the `modules.security.2-factor-authentication.required` setting
to `true`:

```json
"altis": {
    "modules": {
        "security": {
            "2-factor-authentication": {
                "required": true
            }
        }
    }
}
```

Alternatively, to require two factor authentication only for specific user roles, define the roles in
the `modules.security.2-factor-authentication.required` array:

```json
"altis": {
    "modules": {
        "security": {
            "2-factor-authentication": {
                "required": [
                    "super-admin",
                    "administrator",
                    "editor"
                ]
            }
        }
    }
}
```
