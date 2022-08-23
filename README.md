Up Address Validation
==================
UP AV handles address validation for Woocommerce checkout page, simple as that. At this point the plugin validates only ZIP, STATE, COUNTRY with Google Geocoding API.

How it works
------------------
Once the plugin is installed and activated, we have to enable functionality through wp-admin `Settings->Up Address Validation`,
after that and only on checkout page, the plugin will fire the appropriate Woocommerce Hooks.

Currently, UP AV check if there are any results from the Google Geocoding request. 
If there are empty results the validation is considered as false.

Available filter Hooks
------------------
### upio_av_destination
Set your own destination in order to validate with geocoding API

    add_filter( 'upio_av_destination', 'my_destination_validation' );
    function my_destination_validation ( $destination ) {
        $destination['shipping_state'] = 'ATH';
        $destination['shipping_postcode'] = '16232';
        $destination['shipping_country'] = 'GR';
        return $destination;
    }

### upio_av_gg_api_key
Override Google geocoding API with this filter

    add_filter( 'upio_av_gg_api_key', 'my_geocoding_api_key' );
    function my_geocoding_api_key ( $apiKey ) {
        $apiKey = 'YOUR_API_KEY_FTOU_KAI_VGENO';
        return $apiKey;
    }

### upio_av_geocoding_request_url
Override Google geocoding request URL

    add_filter( 'upio_av_geocoding_request_url', 'my_geocoding_request_url' );
    function my_geocoding_request_url ( $url ) {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?components=administrative_area=ATH&postal_code=16252&country=GR&key=YOUR_API_KEY_FTOU_KAI_VGENO';
        return $url;
    }

### upio_av_geocoding_validation_response
Set you custom geocoding validation response, more examples to come here

    add_filter( 'upio_av_geocoding_validation_response', 'my_geocoding_request_response' );
    function my_geocoding_request_response ( $response ) {
        $response = (object) array ( 'results' => array() );
        return $response;
    }

### upio_av_validation_message
Set you validation that will be shown if address cannot validate

    add_filter( 'upio_av_validation_message', 'my_validation_message' );
    function my_validation_message ( $message ) {
        $message = 'Address could be validated, please set a proper one!';
        return $message;
    }

Requirements
--------------
- WordPress >= 5.6
- WooCommerce >= 5.6
- PHP >= 7.2

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

Support
--------------
If you found any bug, please feel free to create an issue, I'll do my best to response with a solution.

For hire me to jump on your WP installation and fix issues, or create a plugin or theme for your needs. Contact at
`margarit[@]upio[.]gr`

Version change logs
--------------
### 1.1.0
Add filter hooks wherever is usable.
### 1.0.0
Initial plugin version.
