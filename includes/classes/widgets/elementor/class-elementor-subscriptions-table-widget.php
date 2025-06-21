<?php

/**
 * Class Elementor_Es_Agencies_Widget.
 */
class Elementor_Es_Subscriptions_Table_Widget extends Elementor_Es_Base_Widget {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'es-subscriptions-table-widget';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return _x( 'Estatik Subscriptions', 'widget name', 'es' );
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param array $instance
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$shortcode = es_get_shortcode_instance( 'es_subscription_table' );
		echo $shortcode->get_content();
	}
}
