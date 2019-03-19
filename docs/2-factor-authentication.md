# Two Factor Authentication

For increased security of user authentication, Platform supports the use of a second factor to authorize the login request. The Two Factor Authentication feature is enabled by default, though not required for all users.

To disable the Two Factor Authentication the feature, set the `modules.security.2-factor-authentication` setting to `false`.

Second factor authentication options are Email, Time-based one-time passwords and FIDO Universal 2nd Factor (U2F).

Two Factor methods can be configured by each user in their Edit Profile page in the CMS.

## Requiring Two Factor Authentication

The site can be configured to require all users enable two factor authentication. This can be set via the Network Admin Settings screen, either for all users, or specific roles.
