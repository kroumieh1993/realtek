<?php

/**
 * Class EstatikProperty.
 */
class Es_Elementor_Agent_Document extends \ElementorPro\Modules\ThemeBuilder\Documents\Single_Base {

	/**
	 * @return array
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location'] = 'single';
		$properties['condition_type'] = 'agent';

		return $properties;
	}

	/**
	 * Return document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'single-agent';
	}

	/**
	 * Return document sub type.
	 *
	 * @return string
	 */
	public static function get_sub_type() {
		return 'agent';
	}

	/**
	 * @return mixed
	 */
	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$config['category'] = 'single agent';

		return $config;
	}

	/**
	 * @return string
	 */
	protected static function get_site_editor_type() {
		return 'agent';
	}

	/**
	 * @return string|void
	 */
	public static function get_title() {
		return __( 'Single Agent', 'es' );
	}

	/**
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Single Agent', 'es' );
	}
}
