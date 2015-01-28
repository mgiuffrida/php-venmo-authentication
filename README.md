# PHP Venmo Authenticator #

PHP Venmo Authenticator is a simple 3rd-party script to demonstrate server-side authentication authentication to Venmo with PHP.

## Files ##

|         filename        |         description        |
| ----------------------- | -------------------------- |
| `public/get_token.php`  | Retrieves token.           |
| `public/test_token.php` | Tests token.               |
| `inc/common.php`        | Helpers.                   |
| `inc/config.php`        | Configuration.             |

## Configuration ##

1. [Create an app in the Venmo developer tab.](https://developer.venmo.com/docs/quickstart#create-an-app)
2. Populate the `define`s in `inc/config.php` with your app info.
3. Install certificates, if the existing default CA cert bundle doesn't work. I used [this Mozilla bundle](http://curl.haxx.se/docs/caextract.html). Or, [get the certificate from Venmo.com directly](http://curl.haxx.se/docs/sslcerts.html).
