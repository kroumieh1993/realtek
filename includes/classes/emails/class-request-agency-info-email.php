<?php

/**
 * Class Es_Request_Agency_Info_Email.
 */
class Es_Request_Agency_Info_Email extends Es_Email {

	/**
	 * @return mixed|string[]|void
	 */
	public function get_tokens() {
		$d = es_parse_args( $this->_data, array(
			'phone' => array(
				'code' => '',
				'tel' => '',
			),
			'name' => '',
			'email' => '',
			'message' => '',
			'post_id' => '',
		) );

		return array_merge( parent::get_tokens(), array(
			'{post_id}' => $d['post_id'],
			'{post_link}' => get_permalink( $d['post_id'] ),
			'{name}' => $d['name'],
			'{email}' => $d['email'],
			'{phone}' => $d['phone'] ? es_get_formatted_tel( $d['phone'] ) : '',
			'{request}' => $d['message'],
		) );
	}

	public function get_content() {
		$content = ests( 'request_agency_info_email_content' );
		return apply_filters( 'es_request_agency_info_email_content', $content );
	}

	public function get_subject() {
		$subject = ests( 'request_agency_info_email_subject' );
		return apply_filters( 'es_request_agency_info_email_subject', $subject );
	}

	public static function get_label() {
		return __( 'Request agency info', 'es' );
	}

	/**
	 * Return email headers.
	 *
	 * @return string[]
	 */
	public function get_headers() {
		$data = $this->get_data();
		$headers = array(
			'Content-Type: text/html',
		);

		if ( ! empty( $data['name'] ) && ! empty( $data['email'] ) ) {
			$headers[] = sprintf( 'Reply-To: %s <%s>', $data['name'], $data['email'] );
		}

		return apply_filters( 'es_email_headers', array_merge( $headers, $this->_headers ), $this );
	}
}
