<?php
/*
Plugin Name: Up Address Validator
Plugin URI:
Description: Validate Woocommerce shipping address during the checkout process. Works also with AJAX request that is used from various one page checkout plugins
Version: 1.1.0
Author: Margarit Koka <UPIO>
Author URI: https://upio.gr/
Update URI:
Text Domain: upio-up-av
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'UP_ADDRESS_VALIDATION_PATH', plugin_dir_path( __FILE__ ) );

include_once UP_ADDRESS_VALIDATION_PATH . 'Autoloader.php';

use Upio\UpAddressValidator\WcAddressValidation;
use Upio\UpAddressValidator\Admin\OptionsAdmin;

class UpAddressValidator {

    private $address_validator;

    public function enableAdmin() {
        if ( is_admin() ) {
            $address_validator = new OptionsAdmin();;
        }
    }

    public function enable() {
        $this->address_validator = new WcAddressValidation();
        $this->address_validator->enable();
    }

    public function activate() {
        $options = array(
            'is_enabled' => '0',
            'google_geocoding_api_key' => '0',
            'config' => array(
                'keep_data_after_remove' => '1'
            ) );
        if ( is_multisite() )  {
            update_blog_option( get_current_blog_id(), 'up_av_options', $options );
        } else {
            update_option( 'up_av_options', $options );
        }
    }

    public function deactivate() {
        $is_data_keep = $this->address_validator->getOption( 'config' )['keep_data_after_remove'];
        if ( $is_data_keep ) {
            return;
        }
        if ( is_multisite() )  {
            delete_blog_option( get_current_blog_id(), 'up_av_options' );
        } else {
            delete_option( 'up_av_options' );
        }
    }
}

if ( class_exists( 'UpAddressValidator' ) ) {
    // initiate plugin
    $upAddressValidator = new UpAddressValidator();
    $upAddressValidator->enableAdmin();
    $upAddressValidator->enable();
    // set wp hooks for plugin lifecycle
    register_activation_hook( __FILE__, array( $upAddressValidator, 'activate' ) );
    register_deactivation_hook( __FILE__, array( $upAddressValidator, 'deactivate' ) );
}
