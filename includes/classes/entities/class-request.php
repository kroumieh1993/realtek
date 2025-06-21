<?php

/**
 * Class Es_Request.
 *
 * @property $email string
 * @property $recipient_custom_emails string
 * @property $note string
 * @property $property_id int
 * @property $post_id int
 * @property $agent_post_id int
 * @property $recipient_entity_id int
 * @property $user_id int
 * @property $is_viewed bool
 * @property $tel array
 */
class Es_Request extends Es_Post {

	/**
	 * Return entities fields list.
	 *
	 * @return array
	 */
	public static function get_default_fields() {
		$fields = array(
			// Used for client name.
			'post_title' => array(
				'system' => true,
			),
			'post_excerpt' => array(
				'system' => true,
			),
			'ID' => array(
				'system' => true,
			),
			'post_id' => array(), // Post ID where request form is placed.
			'recipient_entity_id' => array(), // Agent or Agency post Ids.
			'recipient_user_id' => array(), // admin recipients user Ids.
			'recipient_custom_emails' => array(), // Custom recipient emails.
			'tel' => array(),
			'email' => array(),
			'note' => array(),
			'is_viewed' => array(
				'default_value' => false,
			),
			'keywords' => array(),
		);

		return apply_filters( sprintf( 'es_%s_default_fields', static::get_entity_name() ), $fields );
	}

	/**
	 * @return string
	 */
	public function get_entity_prefix() {
		return 'es_request_';
	}

	/**
	 * @return mixed
	 */
	public static function get_post_type_name() {
		return 'request';
	}

	public function set_as_viewed() {
		$this->save_field_value( 'is_viewed', 1 );
	}

	/**
	 * @return string|null
	 */
	public static function get_entity_name() {
		return 'request';
	}
}
