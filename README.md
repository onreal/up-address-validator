Up Address Validation
==================
UP AV handles address validation for Woocommerce checkout page, simple as that. At this point the plugin validates only ZIP, STATE, COUNTRY with Google Geolocation API.

How it works
------------------
Once the plugin is installed and activated, we have to enable functionality through wp-admin `Settings->Up Address Validation`,
after that and only on checkout page, the plugin will fire the appropriate Woocommerce Hooks.

Currently, UP AV check if there are any results from the Google Geocoding request. 
If there are empty results the validation is considered as false.

Installation
------------
To install and configure...

1. Download the plugin as a zip file through gitlab realease page.
2. Upload the plugin through your WordPress admin. `Plugins->Add New->Upload`
3. Activate the plugin under the `Plugins` admin menu.
4. Manage caching under `Settings->Up Address Validation`

TODO
------------
1. Test plugin on more WordPress installations -> In progress

How to contribute ?
------------
If you want to contribute, then you are more than welcome!
Currently, there is a TODO list with things that need to be done, you can hang out with any item you prefer.
But what is most in need is this plugin to be tested on as much WordPress installations as possible,
this will assure a bug free plugin on most environments.

If you found any bug and know the solution, then create a Merge Request and I would love to review and merge.

Requirements
--------------
 - WordPress >= 5.6
 - WooCommerce >= 5.6
 - PHP >= 7.2

Support
--------------
If you found any bug, please feel free to create an issue, I'll do my best to response with a solution.

For hire me to jump on your WP installation and fix issues, or create a plugin or theme for your needs. Contact at
`margarit[@]upio[.]gr`

Version change logs
--------------
### 1.0.0
Initial plugin version.
