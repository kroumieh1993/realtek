<?php

/**
 * Class Es_New_Agent_Confirm_Email
 */
class Es_New_Agent_Confirm_Email extends Es_Email {

	/**
	 * @return string[]
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
			'{confirmation_link}' => $this->generate_confirmation_link(),
		) );
	}

	/**
	 * Return agent confirmation link.
	 *
	 * @return null|string
	 */
	protected function generate_confirmation_link() {
		$d = $this->get_data();
		$link = null;

		if ( ! empty( $d['user_email'] ) ) {
			$user = get_user_by( 'email', $d['user_email'] );

			if ( $user instanceof WP_User ) {
				$link = add_query_arg( array(
					'auth-action' => 'approve-user',
					'user_email' => $d['user_email'],
					'k' => get_user_meta( $user->ID, 'es_agent_user_approval_key', true ),
				), es_get_page_url( 'login' ) );

				$link = sprintf( "<a href='%s' target='_blank'>%s</a>", $link, $link );
			}
		}

		return apply_filters( 'es_agent_confirmation_link', $link, $this );
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_content() {
		$content = ests( 'new_agent_confirm_email_content' );
		return apply_filters( 'es_new_agent_confirm_email_content', $content );
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_subject() {
		$subject = ests( 'new_agent_confirm_email_subject' );
		return apply_filters( 'es_new_agent_confirm_email_subject', $subject );
	}

	/**
	 * @return mixed|string|void
	 */
	public static function get_label() {
		return __( 'New agent registration confirmation', 'es' );
	}
}
