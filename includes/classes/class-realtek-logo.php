<?php

/**
 * Class Es_Realtek_Logo
 */
class Es_Realtek_Logo {

	/**
	 * @return void
	 */
	public static function init() {
		if ( ! ests( 'is_white_label_enabled' ) ) {
			add_action( 'es_logo', arraY( 'Es_Realtek_Logo', 'render' ) );
		}
	}

	/**
	 * Return realtek logo.
	 *
	 * @return void
	 */
	public static function render() {
		echo sprintf( "<img class='es-logo' src='%s'>", static::get_url() );
	}

	/**
	 * Return logo URL.
	 *
	 * @return string
	 */
	public static function get_url() {
		return apply_filters( 'es_logo_url', ES_PLUGIN_URL . 'admin/images/logo-ver.svg' );
	}
}

Es_Realtek_Logo::init();
