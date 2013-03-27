=== CloudWork Verifi ===
Contributors: cloudworkthemes
Donate link: 
Tags: envato api themeforest login registration
Requires at least: 3.5
Tested up to: 3.6 alpha
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows Envato authors to verify user purchases durning registration
== Description ==

This plugin requires a API key and user account from the [Envato network](http://envato.com) 

It also adds an extra fields to the wp-login.php registration form two that allow users to enter their own passwords and one validates purchases using the Envato API. 

After registration users are automaticlly login in and redirected to the home page which can be filtered to redirect elsewhere.

This plugin currently has two shortcodes [cw-verifi-registration] which allows for user registration via the frontend and [cw-new-user]Hi new user![/cw-new-user] which allows to display a message to new users for the first 10 minutes they are logged in.

== Installation ==

CloudWork Verifi is easily installed automatically via the Plugins tab in your blogs admin panel, then it is configured with your Envato username and API key in Setting -> CW-Verifi

= Manual Installation =

1. Upload the `cw-verifi` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add your Envato API key and Username in Settings->Cloudwork Verifi
1. Enjoy!

== Frequently Asked Questions ==

= Does this plugin allow Users to have multiple API Keys =

Currently that is not supported

= Does this plugin allow Users to have multiple purchase codes =

Currently that is not supported, but may be in future release

= Where can I report a bug =

the best place is either my twitter @chrisakelley or on the [GitHub page](https://github.com/chrisakelley/CloudWork-Verifi)

== Changelog ==

= 0.4 =

* New class for handling the Envato API
* New admin framework
* Added redirect option
* Improved password strength indicator 
* Improved localization
* Improved error handling
* Depreciated cw_get_user_by_meta_data

= 0.3.1 =

* fixed repo bug breaking plugin

= 0.3 =

* Users can now enter their own passwords, passwords required at least 6 characters
* User automatically logged in and redirect to home
* added redirect filters
* set cookie for new users for 10 minutes
* new shortcode to display message to newly register users [cw-new-user]

= 0.2 =

* _cw_purchase_code now stored as array with all buyer information
* squashy buggies
 
= 0.1.2 = 

* bug fixes

= 0.1.1 = 

* typo fix

= 0.1 =

* First
