<?php

/**
 * Class Es_File_Path_Field
 */
class Es_File_Path_Field extends Es_Framework_Field {

	public function get_input_markup() {
		$config = $this->get_default_config();
		$input = parent::get_input_markup();
		$input .= "<button style='margin-top: 16px;' class='es-btn es-btn--third es-btn--small js-es-media-editor' type='button'>{$config['upload_button_label']}</button>";

		return $input;
	}

	/**
	 * @return array
	 */
	public function get_default_config() {
		$def = parent::get_default_config();

		return es_parse_args( $def, array(
			'upload_button_label' => "<span class='es-icon es-icon_upload'></span>" . __( 'Upload', 'es' ),
		) );
	}
}
