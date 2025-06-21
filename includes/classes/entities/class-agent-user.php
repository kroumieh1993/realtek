<?php

/**
 * Class Es_Agent.
 *
 * @property $status int
 * @property $post_id int
 * @property $approval_key string
 */
class Es_Agent_User extends Es_User {

	/**
	 * @return array|mixed|void
	 */
	public static function get_default_fields() {
		$default_fields = parent::get_default_fields();

		$default_fields['approval_key'] = array(
			'default_value' => md5( uniqid() )
		);

		return apply_filters( 'es_agent_user_default_fields', array_merge( $default_fields, array(
			'post_id' => array(),
		) ) );
	}

	/**
	 * Check related user entity.
	 *
	 * @return bool|WP_User
	 */
	public function has_post_entity() {
		return $this->post_id && get_post( $this->post_id );
	}

	/**
	 * Check related user entity.
	 *
	 * @return bool|Es_Agent_Post
	 */
	public function get_post_entity() {
		return $this->has_post_entity() ? es_get_agent( $this->post_id ) : null;
	}

	/**
	 * @return string|null
	 */
	public static function get_entity_name() {
		return 'agent_user';
	}

	/**
	 * @return string|void
	 */
	public function get_entity_prefix() {
		return 'es_agent_user_';
	}

	public function activate() {
		parent::activate();

		if ( $this->has_post_entity() && ( $post = $this->get_post_entity() ) ) {
			$post->activate();
		}
	}
}