<?php

/**
 * Class Es_Subscriptions_Shortcode.
 */
class Es_Subscriptions_Shortcode extends Es_Shortcode {

	/**
	 * @return string|void
	 */
	public function get_content() {
		ob_start();
		es_load_template( 'front/shortcodes/subscriptions/index.php' );
		wp_enqueue_script( 'es-subscriptions', ES_PLUGIN_URL . '/public/js/subscriptions.min.js', array( 'jquery' ), Estatik::get_version() );
		wp_localize_script( 'es-subscriptions', 'EstatikSubscriptions', array(
			'tr' => array(
				'free' => _x( 'FREE', 'estatik featured listings', 'es' ),
			),
			'settings' => array(
				'currency' => ests( 'currency' ),
				'language' => ests( 'language' ),
			)
		) );
		return ob_get_clean();
	}

	/**
	 * Return shortcode name.
	 *
	 * @return array
	 */
	public static function get_shortcode_name() {
		return array( 'es_subscriptions', 'es_subscription_table' );
	}
}