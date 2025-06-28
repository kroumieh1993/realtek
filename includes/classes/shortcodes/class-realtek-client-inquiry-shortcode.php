<?php

/**
 * Class Realtek_Client_Inquiry_Shortcode.
 */
class Realtek_Client_Inquiry_Shortcode extends Es_Shortcode {

    /**
     * Return shortcode name.
     *
     * @return string
     */
    public static function get_shortcode_name() {
        return 'client_inquiry_form';
    }

    /**
     * Return shortcode content.
     *
     * @return string
     */
    public function get_content() {
        ob_start();

        //$view_path = ES_PLUGIN_PATH . 'views/view-client-inquiry-form.php';
        $view_path = __DIR__ . '/views/view-client-inquiry-form.php';
        if ( file_exists( $view_path ) ) {
            include $view_path;
        } else {
            echo '<div class="ci-notice ci-error">❌ View file not found.</div>';
        }

        return ob_get_clean();
    }
}
