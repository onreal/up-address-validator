<?php
namespace Upio\UpAddressValidator\Admin;

class OptionsAdmin {
    private $upAVOptions;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'upAVAddPluginPage' ) );
        add_action( 'admin_init', array( $this, 'upAVPageInit' ) );
    }

    public function upAVAddPluginPage() {
        add_options_page(
            'Up Address Validation',
            'Up Address Validation',
            'manage_options',
            'up-address-validation',
            array( $this, 'upAVCreateAdminPage' )
        );
    }

    public function upAVCreateAdminPage() {
        $this->upAVOptions = is_multisite()
            ? get_blog_option( get_current_blog_id(), 'up_av_options', array() )
            : get_option( 'up_av_options', array() );
        ?>

        <div class="wrap">
            <h2>Up Address Validation</h2>
            <p>Validate address (COUNTRY,ZIP,STATE) on Woocommerce checkout page. The validation is made with Google Geocoding API.</p>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'up_address_validation_option_group' );
                do_settings_sections( 'up-address-validation-admin' );
                submit_button();
                ?>
            </form>
        </div>
    <?php }

    public function upAVPageInit() {
        register_setting(
            'up_address_validation_option_group',
            'up_av_options',
            array( $this, 'upAVValidationSanitize' )
        );

        add_settings_section(
            'up_address_validation_setting_section',
            'Settings',
            array( $this, 'upAVSectionInfo' ),
            'up-address-validation-admin'
        );

        add_settings_field(
            'is_enabled',
            'Activate plugin',
            array( $this, 'isEnabledCallback' ),
            'up-address-validation-admin',
            'up_address_validation_setting_section'
        );

        add_settings_field(
            'google_geocoding_api_key',
            'Google Geocoding API Key',
            array( $this, 'googleGeocodingApiKeyCallback' ),
            'up-address-validation-admin',
            'up_address_validation_setting_section'
        );
    }

    public function upAVValidationSanitize($input) {
        $sanitary_values = array();
        if ( isset( $input['is_enabled'] ) ) {
            $sanitary_values['is_enabled'] = $input['is_enabled'];
        }

        if ( isset( $input['google_geocoding_api_key'] ) ) {
            $sanitary_values['google_geocoding_api_key'] = sanitize_text_field( $input['google_geocoding_api_key'] );
        }

        return $sanitary_values;
    }

    public function upAVSectionInfo() {

    }

    public function isEnabledCallback() {
        printf(
            '<input type="checkbox" name="up_av_options[is_enabled]" id="is_enabled" value="is_enabled" %s> <label for="is_enabled">Enable Woocommerce address validation</label>',
            ( isset( $this->upAVOptions['is_enabled'] ) && $this->upAVOptions['is_enabled'] === 'is_enabled' ) ? 'checked' : ''
        );
    }

    public function googleGeocodingApiKeyCallback() {
        printf(
            '<input class="regular-text" type="text" name="up_av_options[google_geocoding_api_key]" id="google_geocoding_api_key" value="%s">',
            isset( $this->upAVOptions['google_geocoding_api_key'] ) ? esc_attr( $this->upAVOptions['google_geocoding_api_key']) : ''
        );
    }

}
