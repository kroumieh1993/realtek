<?php

/**
 * Class Es_Admin_Listing_Agent_Added_Email.
 */
class Es_Saved_Search_Email extends Es_Email {

	/**
	 * Generate email content.
	 *
	 * @return string
	 */
	public function get_content() {
		ob_start();
		es_load_template( 'common/emails/saved-search-email.php', $this->_data );

		return ob_get_clean();
	}

	/**
	 * Generate email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return __( 'New listings matching your saved search', 'es' );
	}

	public static function get_label() {
		return __( 'Saved search', 'es' );
	}
}
