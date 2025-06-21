<?php

class Es_New_User_Registered_Admin_Email extends Es_Email {

	/**
	 * @return mixed|string[]|void
	 */
	public function get_tokens() {
		$d = $this->get_data();

		$d = wp_parse_args( $d, array(
			'user_login' => null,
			'user_email' => null,
		) );

		return array_merge( parent::get_tokens(), array(
			'{user_login}' => $d['user_login'],
			'{user_email}' => $d['user_email'],
		) );
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_content() {
		$content = ests( 'new_user_registered_admin_email_content' );
		return apply_filters( 'es_new_user_registered_admin_email_content', $content );
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_subject() {
		$subject = ests( 'new_user_registered_admin_email_subject' );
		return apply_filters( 'es_new_user_registered_admin_email_subject', $subject );
	}

	public static function get_label() {
		return __( 'Admin new user registered', 'es' );
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
		return apply_filters( 'es_email_is_active', ests( 'is_new_user_registered_admin_enabled' ), get_called_class() );
	}
}
