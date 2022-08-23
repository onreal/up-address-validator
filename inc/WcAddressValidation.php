<?php
namespace Upio\UpAddressValidator;

/**
 * Class responsible for address validation lifecycle
 * @author Margarit Koka
 */
class WcAddressValidation
{
    private static $destination = array();
    private $pluginOptions = array();

    public function __construct () {}

    /**
     * @return array
     */
    private function getPluginOptions (): array {
        if ( ! $this->pluginOptions ) {
            $this->pluginOptions = is_multisite()
                ? get_blog_option( get_current_blog_id(), 'up_av_options', array() )
                : get_option( 'up_av_options', array() );
        }
        return $this->pluginOptions;
    }

    /**
     * @param $option
     *
     * @return string|null|false|array
     */
    public function getOption ( $option ): ?string {
        if ( ! array_key_exists( $option, $options = $this->getPluginOptions() ) ) {
            return false;
        }

        return $options[ $option ];
    }

    /**
     * Get destination to validate address, destination array should contain the following items
     * array ( 'shipping_state' => '', 'shipping_postcode' => '', 'shipping_country' => '' );
     * @return array
     */
    private static function getDestination (): array
    {
        return apply_filters( 'upio_av_destination', self::$destination );
    }

    /**
     * @param array $destination
     */
    private static function setDestination ( array $destination ): void
    {
        self::$destination = $destination;
    }

    /**
     * query string to array key value pair
     * @param $data
     * @return array
     */
    private static function  getDestinationFromQueryString ( $data ): array
    {
        $destination = array();
        $parameters = explode( '&', $data );
        foreach ( $parameters as $parameter ){
            $value = explode('=', urldecode($parameter));
            $destination[$value[0]] = $value[1];
        }

        return $destination;
    }

    /**
     * format Google geocoding API request
     * @return string
     */
    private function getGeocodeUrl (): string
    {
        $destination = self::getDestination();
        $apiKey = apply_filters( 'upio_av_gg_api_key', $this->getOption( 'google_geocoding_api_key' ) );
        $base_url = "https://maps.googleapis.com/maps/api/geocode/json?components=";
        $url = $base_url . 'administrative_area%3A' . $destination['billing_state'];
        $url .= '%7Cpostal_code%3A' . $destination['billing_postcode'];
        $url .= '%7Ccountry%3A' . $destination['billing_country'];
        $url .= '&key=' . $apiKey;
        return apply_filters( 'upio_av_geocoding_request_url', $url );
    }

    /**
     * run address validation process
     * @return void
     */
    private function runValidation ()
    {
        $response = $this->geoCodingRequest();
        $geocodingResponse = apply_filters( 'upio_av_geocoding_validation_response',  $response );
        $this->responseValidation( $geocodingResponse );
    }

    /**
     * Validate geocoding response
     * @param $response
     */
    private function responseValidation ( $response )
    {
        $validationMessage = apply_filters( 'upio_av_validation_message', 'Your address cannot be validated.' );
        if ( !is_object( $response ) || !property_exists( $response, 'status' )
            || !property_exists( $response, 'results' ) )
        {
            wc_add_notice( __( $validationMessage ), 'error' );
            return;
        }
        if ( !is_array( $response->results ) || empty( $response->results ) )
        {
            wc_add_notice( __( $validationMessage ), 'error' );
            return;
        }
        wc_clear_notices();
    }

    /**
     * Google Geocoding API request
     * @return mixed
     */
    private function geoCodingRequest () {
        $curl = curl_init ();
        curl_setopt_array ( $curl, array (
            CURLOPT_URL => $this->getGeocodeUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array (
                "Accept: application/json"
            ),
        ));
        $response = curl_exec ( $curl );
        curl_close ( $curl );
        return json_decode( $response );
    }

    public function enable () {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            return;
        }
        if ( !$this->getOption( 'is_enabled' ) ) {
            return;
        }
        add_action( 'woocommerce_checkout_process', array( $this, 'up_av_validate_address' ) );
        add_action( 'woocommerce_checkout_update_order_review', array( $this, 'up_av_validate_address' ) );
    }

    /**
     * Callback function for WC checkout address validation.
     * @action woocommerce_checkout_process | woocommerce_checkout_update_order_review
     * @param null $data
     */
    public function up_av_validate_address ( $data = null )
    {
        self::setDestination( $data === null ? $_POST : self::getDestinationFromQueryString( $data ) );
        $this->runValidation();
    }
}
