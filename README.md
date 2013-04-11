CloudWork-Verifi
================

Adds an extra field to the default wp-login.php that will verify user purchases, requires Envato Username and API key 

also adds a shortcode [cw-verifi-registration] to add registration form to the front-end 

Todo
------

* Allow multiple purchase codes
* Author dashboard

Change Log
-----------------
= 0.4.3 =

* Reverted to some older code
* Simplified shortcode

= 0.4.2 =

* bug fix

= 0.4.1 =

* bug fix

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