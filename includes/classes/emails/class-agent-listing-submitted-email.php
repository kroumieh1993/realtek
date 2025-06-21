<?php

/**
 * Class Es_Admin_Listing_Agent_Added_Email.
 */
class Es_Agent_Listing_Submitted_Email extends Es_Email {

	/**
	 * Generate email content.
	 *
	 * @return string
	 */
	public function get_content() {
		return ests( 'agent_listing_submitted_email_content' );
	}

	/**
	 * Generate email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return ests( 'agent_listing_submitted_email_subject' );
	}

	/**
	 * Get emails tokens to replace.
	 *
	 * @return array
	 */
	public function get_tokens() {
		$this->_data = wp_parse_args( $this->_data, array(
			'post_id' => null,
			'user_id' => null,
		) );

		$user_entity = es_get_user_entity( $this->_data['user_id'] );
		$post_id = $this->_data['post_id'];

		return array(
			'{post_id}' => $post_id,
			'{post_link}' => get_permalink( $post_id ),
			'{agent_name}' => $user_entity->get_full_name() ? $user_entity->get_full_name() : $user_entity->get_wp_entity()->user_login,
		);
	}

	public static function get_label() {
		return __( 'Agent new listing submitted', 'es' );
	}

	/**
	 * @return false
	 */
	public static function is_disableable() {
		return apply_filters( 'es_email_is_disableable', true, get_called_class() );
	}

	/**
	 * @return bool
	 */
	public static function is_active() {
		return apply_filters( 'es_email_is_active', ests( 'is_agent_listing_submitted_enabled' ), get_called_class() );
	}
}
