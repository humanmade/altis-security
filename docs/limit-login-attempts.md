# Limit Login Attempts

To protect against brute force attacks, Altis has the ability to limit the number of login attempts possible both through normal login as well as using auth cookies.

This feature blocks an IP address from making further attempts after a specified limit on retries is reached, making a brute-force attack difficult or impossible.

- Limit for the number of retry attempts when logging in (for each IP).
- Limit the number of attempts to log in using auth cookies in same way.
- Informs user about remaining retries or lockout time on login page.
- Optional logging, optional email notification.

To disable the ability to limit the number of login attempts, set the `modules.security.limit-login-attempts` setting to `false`.
