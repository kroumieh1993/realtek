<?php

/**
 * Class Es_Properties_Slideshow_Widget.
 */
class Es_Properties_Slideshow_Widget extends Es_Properties_Filter_Widget {

	/**
	 * Es_Widget_Example constructor.
	 */
	public function __construct() {
		parent::__construct( 'es-properties-slideshow', _x( 'Estatik Properties Slideshow', 'widget name', 'es' ) );
	}

	/**
	 * Render widget content.
	 *
	 * @param $instance array
	 *
	 * @return void
	 */
	public function render( $instance = array() ) {
		/** @var Es_Properties_Slideshow_Shortcode $shortcode */
		if ( ! empty( $instance['fields_to_show'] ) ) {
			$instance['fields_to_show'] = implode( ',', $instance['fields_to_show'] );
		}
		$shortcode = es_get_shortcode_instance( 'es_properties_slideshow', $instance );
		echo $shortcode->get_content();
	}

	/**
	 * Return default widget data.
	 *
	 * @return array
	 */
	public function get_default_data() {
		$shortcode = es_get_shortcode_instance( 'es_properties_slideshow' );
		return array_merge( parent::get_default_data(), $shortcode->get_default_attributes() );
	}

	/**
	 * Return widget fields array.
	 *
	 * @param $instance
	 * @return array
	 */
	public function get_widget_form( $instance ) {
		$uid = uniqid();

		$config = $this->get_page_access_fields();

		$config = array_merge( $config, array(
			'layout' => array(
				'label' => __( 'Select slideshow template', 'es' ),
				'type' => 'radio-image',
				'options' => array(
					'info-block' => __( 'With info block', 'es' ),
					'side-previews' => __( 'With left previews', 'es' ),
					'side-info' => __( 'With left info', 'es' ),
					'bottom-previews' => __( 'With bottom previews', 'es' ),
				),
				'images' => array(
					'info-block' => ES_PLUGIN_URL . 'admin/images/info-block.svg',
					'side-previews' => ES_PLUGIN_URL . 'admin/images/side-previews.svg',
					'side-info' => ES_PLUGIN_URL . 'admin/images/side-info.svg',
					'bottom-previews' => ES_PLUGIN_URL . 'admin/images/bottom-previews.svg',
				),
			),

			'info_background' => array(
				'label' => __( 'Select color of info background ', 'es' ),
				'type' => 'color',
			),

			'max_height' => array(
				'type' => 'text',
				'label' => __( 'Main image max height', 'es' ),
			),

			'enable_progress_bar' => array(
				'label' => __( 'Enable progress bar', 'es' ),
				'type' => 'switcher',
				'attributes' => array(
					'data-toggle-container' => '.es-progress-location-' . $uid
				),
			),

			'progress_bar_position' => array(
				'before' => "<div class='es-progress-location-{$uid}'>",
				'after' => '</div>',
				'label' => __( 'Location of progress bar', 'es' ),
				'type' => 'radio-bordered',
				'options' => array(
					'top' => __( 'Top', 'es' ),
					'bottom' => __( 'Bottom', 'es' ),
				),
			),

			'is_arrows_enabled' => array(
				'label' => __( 'Enable arrows', 'es' ),
				'type' => 'switcher',
			),
		) );

		$second_config = array(
			'fields_to_show' => array(
				'type' => 'checkboxes',
				'label' => __( 'Parameters to show', 'es' ),
				'options' => array(
					'title' => __( 'Title', 'es' ),
					'es_label' => __( 'Labels', 'es' ),
					'address' => __( 'Address', 'es' ),
					'price' => __( 'Price', 'es' ),
					'price_note' => __( 'Price badge', 'es' ),
					'es_type' => __( 'Property type', 'es' ),
					'es_category' => __( 'Property category', 'es' ),
				),
			),

			'enable_wishlist' => array(
				'label' => __( 'Enable saving listing to Saved homes', 'es' ),
				'type' => 'switcher',
			),
		);

		return array_merge( $config, parent::get_widget_form( $instance ), $second_config );
	}
}

new Es_Properties_Slideshow_Widget();
