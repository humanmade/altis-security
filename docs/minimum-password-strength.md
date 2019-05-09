# Minimum Password Strength

To protect against brute force and dictionary attacks, Altis enforces a minimum password strength.

Passwords are scored one of four possible scores:

* Very Weak (score: 1)
* Weak (score: 2)
* Medium (score: 3)
* Strong (score: 4)

By default, passwords which score below 2 (i.e. Very Weak passwords) will be rejected.

To change the minimum password strength, set the `modules.security.minimum-password-strength` setting to a different score (i.e. `3`).

To disable the minimum password strength checks, set the `modules.security.minimum-password-strength` setting to `0`.


## Additional strength checks

To add additional strength checks, a `altis.security.passwords.is_weak` filter is provided. This filters the boolean `$is_weak` which can be set to `true` to reject a password.

For example, to reject any passwords which contain the word "human":

```php
add_filter( 'altis.security.passwords.is_weak', function ( $is_weak, $password ) {
	if ( strpos( $password, 'human' ) !== false ) {
		return true;
	}

	return $is_weak;
}, 10, 2 );
```

The filter receives other parameters which can be used for more dynamic checks; for example, you could require a higher password strength score for administrators.
