<?php

/**
 * Class Es_Agent_Agency_Review_Form_Shortcode
 */
class Es_Agent_Agency_Review_Form_Shortcode extends Es_Shortcode {

	/**
	 * @return string|void
	 */
	public function get_content() {
		add_filter( 'comment_form_submit_field', array( __CLASS__, 'add_recaptcha' ), 9, 1 );
		add_action( 'comment_form_top', array( $this, 'add_rating_field' ) );


		$atts =  $this->get_attributes();

		if ( ! empty( $atts['entity_id'] ) && ( $entity = es_get_entity_by_id( $atts['entity_id'] ) ) ) {
			ob_start();
			es_load_template( 'front/partials/comment-form.php', array(
				'entity' => $entity,
			) );
			return ob_get_clean();
		}
	}

	/**
	 * @return Exception|string
	 */
	public static function get_shortcode_name() {
		return 'es_agent_agency_review_form';
	}

	/**
	 * Add rating field to the review form.
	 *
	 * @return void
	 */
	public function add_rating_field() {
		$atts = $this->get_attributes();
		$entity = es_get_entity_by_id( $atts['entity_id'] ); ?>
		<?php if ( ests( 'is_' . $entity::get_entity_name() . '_rating_enabled' ) ) : ?>
			<?php es_framework_field_render( 'rating', array(
				'label' => sprintf( __( 'How was %s’s work overall?', 'es' ), get_the_title( $entity->get_id() ) ),
				'type' => 'rating',
				'description' => __( 'Choose a rating', 'es' ),
			) ); ?>
		<?php endif;
	}

	/**
	 * @return string
	 */
	public static function add_recaptcha( $submit ) {
		do_action( 'es_recaptcha', 'comment_form' );

		return $submit;
	}

	/**
	 * Return shortcode default attributes.
	 *
	 * @return array|void
	 */
	public function get_default_attributes() {
		return apply_filters( sprintf( '%s_default_attributes', static::get_shortcode_name() ), array(
			'entity_id' => get_the_ID(),
		) );
	}
}
