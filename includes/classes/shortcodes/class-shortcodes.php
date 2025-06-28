<?php

/**
 * Class Es_Shortcodes_List.
 */
class Es_Shortcodes_List {

    public static $_shortcodes = array();

    /**
     * Initialize plugin shortcodes.
     */
    public static function init() {
        static::load_files();
    }

    /**
     * Load plugin shortcodes files.
     *
     * @return void
     */
    public static function load_files() {
        $path = ES_PLUGIN_CLASSES . 'shortcodes' . DS;

        $files = apply_filters( 'es_shortcodes_list_files', array(
            $path . 'class-shortcode.php',
            $path . 'class-my-entities-shortcode.php',
            $path . 'class-single-entity-shortcode.php',
            'Realtek_Client_Inquiry_Shortcode' => $path . 'class-realtek-client-inquiry-shortcode.php',
            'Es_Search_Form_Shortcode' => $path . 'class-search-form-shortcode.php',
            'Es_Agent_Agency_Search_Form_Shortcode' => $path . 'class-agent-agency-search-form-shortcode.php',
            'Es_My_Listing_Shortcode' => $path . 'class-my-listing-shortcode.php',
            'Es_Properties_Map_Shortcode' => $path . 'class-properties-map-shortcode.php',
            'Es_My_Agents_Shortcode' => $path . 'class-my-agents-shortcode.php',
            'Es_My_Agencies_Shortcode' => $path . 'class-my-agencies-shortcode.php',
            'Es_Properties_Slideshow_Shortcode' => $path . 'class-properties-slideshow-shortcode.php',
            'Es_Properties_Slider_Shortcode' => $path . 'class-properties-slider-shortcode.php',
            'Es_Request_Form_Shortcode' => $path . 'class-request-form-shortcode.php',
            'Es_Locations_Grid_Shortcode' => $path . 'class-locations-grid-shortcode.php',
            'Es_Authentication_Shortcode' => $path . 'class-authentication-shortcode.php',
            'Es_Profile_Shortcode' => $path . 'class-profile-shortcode.php',
            'Es_Property_Field_Shortcode' => $path . 'class-property-field-shortcode.php',
            'Es_Agent_Field_Shortcode' => $path . 'class-agent-field-shortcode.php',
            'Es_Agency_Field_Shortcode' => $path . 'class-agency-field-shortcode.php',
            'Es_Single_Property_Shortcode' => $path . 'class-single-property-shortcode.php',
            'Es_Single_Agent_Shortcode' => $path . 'class-single-agent-shortcode.php',
            'Es_Single_Agency_Shortcode' => $path . 'class-single-agency-shortcode.php',
            'Es_Agent_Agency_Review_Form_Shortcode' => $path . 'class-agent-agency-review-form-shortcode.php',
            'Es_Property_Management_Shortcode' => $path . 'class-property-management-shortcode.php',
            'Es_Subscriptions_Shortcode' => $path . 'class-subscriptions-shortcode.php',

            // Backward compatibility for Realtek 3 shortcodes.
            'Es_Login_Shortcode' => 'class-login-shortcode.php', // [es_login]
            'Es_Restore_Password_Shortcode' => 'class-reset-password-shortcode.php', //[es_register]
            'Es_Register_Shortcode' => 'class-register-shortcode.php', // [es_reset_pwd]
            'Es_Compare_Shortcode' => 'class-compare-shortcode.php', // [es_compare]
        ) );

        if ( ! empty( $files ) ) {
            foreach ( $files as $shortcode_class => $file ) {
                if ( ! class_exists( $shortcode_class ) || ! $shortcode_class ) {
                    require_once $file;

                    if ( $shortcode_class && ! is_numeric( $shortcode_class ) ) {
                        /** @var Es_Shortcode $shortcode */

	                    $reflect = new ReflectionClass( $shortcode_class );

	                    if ( ! $reflect->isAbstract() ) {
		                    $shortcode = new $shortcode_class();

		                    $name = $shortcode::get_shortcode_name();

		                    if ( is_array( $name ) ) {
			                    foreach ( $name as $shortcode_name ) {
				                    static::$_shortcodes[ $shortcode_name ] = $shortcode_class;
				                    add_shortcode( $shortcode_name, array( $shortcode, 'build' ) );
			                    }
		                    } else {
			                    static::$_shortcodes[ $name ] = $shortcode_class;
			                    add_shortcode( $name, array( $shortcode, 'build' ) );
		                    }
	                    }
                    }
                }
            }
        }
    }
}

Es_Shortcodes_List::init();

