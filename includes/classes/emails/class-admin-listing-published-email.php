<?php

/**
 * Class Es_Admin_Listing_Agent_Added_Email.
 */
class Es_Admin_Listing_Published_Email extends Es_Email {

	/**
	 * @param array $data
	 */
	public function __construct( $data = array() ) {
		parent::__construct( $data );

		$emails = get_users( array(
			'role' => 'administrator',
			'fields' => array( 'user_email' )
		) );

		$emails = $emails ? wp_list_pluck( $emails, 'user_email' ) : array();
		$emails[] = get_option( 'admin_email' );
		$emails = array_unique( $emails );

		$this->_to = $emails;
	}

	/**
	 * Generate email content.
	 *
	 * @return string
	 */
	public function get_content() {
		return ests( 'admin_listing_published_email_content' );
	}

	/**
	 * Generate email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return ests( 'admin_listing_published_email_subject' );
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

		$agent_name = '';
		$post_id = $this->_data['post_id'];
		$user_entity = es_get_user_entity( $this->_data['user_id'] );

		if ( $user_entity instanceof Es_User ) {
			$agent_name = $user_entity->get_full_name() ?
				$user_entity->get_full_name() : $user_entity->get_wp_entity()->user_login;
		}

		$edit_link = 'post.php?post=%s&action=edit';
		$link = admin_url( sprintf( $edit_link, $post_id ) );

		return array(
			'{post_id}' => $post_id,
			'{post_link}' => get_permalink( $post_id ),
			'{agent_name}' => $agent_name,
			'{admin_permalink}' => $link,
		);
	}

	public static function get_label() {
		return __( 'New listing published by agent', 'es' );
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
		return apply_filters( 'es_email_is_active', ests( 'is_admin_listing_published_enabled' ), get_called_class() );
	}
}
