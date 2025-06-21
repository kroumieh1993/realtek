<?php

/**
 * Class Es_Framework_Draggable_Files_Field.
 */
class Es_Framework_Draggable_Files_Field extends Es_Framework_Base_Field {

	/**
	 * Return media items markup.
     *
     * @return string
	 */
    public function get_items_html() {
        $config = $this->get_field_config();
        $wrapper = $config['items_wrapper'];
        $items = '';

        if ( ! empty( $config['value'] ) && is_array( $config['value'] ) ) {
	        foreach ( $config['value'] as $attachment_id ) {
	            if ( $attachment = wp_get_attachment_metadata( $attachment_id ) ) {
	            	$caption = wp_get_attachment_caption( $attachment_id );
		            $item_template = $this->get_item_template( $attachment_id );

		            $item = strtr( $item_template, array(
		            	'{name}' => basename( $attachment['file'] ),
		            	'{size}' => size_format( filesize( get_attached_file( $attachment_id ) ), 2 ),
		            	'{caption}' => $caption ? $caption : '',
			            '{src}' => wp_attachment_is_image( $attachment_id ) ?
                            wp_get_attachment_image_url( $attachment_id ) : $config['default_thumbnail_url'],
		            ) );

		            $items .= $item;
                }
            }
        }

        return strtr( $wrapper, array( '{items_list}' => $items, '{uploader_type}' => $config['uploader_type'] ) );
    }

	/**
	 * Return media item template.
	 *
	 * @param string $value
	 *
	 * @return bool|mixed
	 */
    public function get_item_template( $value = '' ) {
        $config = $this->get_field_config();
	    $template = '';

        if ( 'images' == $config['uploader_type'] ) {
            $template =  $config['image_template'];
        } else if ( 'files' == $config['uploader_type'] ) {
            $template = $config['file_template'];
        }

	    $hidden_input = es_framework_get_field( $this->_field_key, array(
		    'type' => 'hidden',
		    'value' => $value,
		    'attributes' => array(
			    'class' => 'js-es-media-field',
			    'name' => $config['attributes']['name'] . '[]',
		    ),
	    ) );

        return strtr( $template, array( '{hidden_input}' => $hidden_input->get_input_markup(), '{attachment_id}' => $value ) );
    }

	/**
     * Return default field config.
     *
	 * @return array
	 */
	public function get_default_config() {
		$max_upload_size = wp_max_upload_size();

		if ( ! $max_upload_size ) {
			$max_upload_size = 0;
		}

		$def = array(
			// if uploader type eq images
			'uid' => uniqid(),

			'image_template' =>
				'<div class="es-media js-es-file">
					{hidden_input}
                    <div class="es-media__image">
                        <div class="js-es-progress es-media__progress es-secondary-bg"></div>
                         <img src="{src}">
                         <ul class="es-control es-control--files">
                            <li class="es-control__item">
                                <a href="#" class="es-btn es-btn--icon es-btn--default es-btn--small es-btn--delete-file js-es-delete-media"><span class="es-icon es-icon_trash"></span></a>
                                <a href="#" class="es-btn es-btn--icon es-btn--default es-btn--small es-btn--reload-file js-es-reload-media"><span class="es-icon es-icon_preload"></span></a>
                            </li>
                         </ul>                             
                    </div>
                     
                    <div class="es-file__caption-container">
						<input type="text" data-attachment-id="{attachment_id}" class="js-es-caption js-es-file__caption-field es-ignore-style content-font" value="{caption}" placeholder="' . __( 'Your caption here', 'es' ) . '"\'>
					</div>
                 </div>',

			// if uploader type eq files
			'file_template' =>
				"<div class='es-file js-es-file'>
					{hidden_input}
					<div class='es-file__inner'>
						<span class='es-icon es-icon_file es-file__icon'></span>
						<div class='es-file__info'>
							<div class='es-file__title es-file__caption-container'>
								<input type='text' data-attachment-id='{attachment_id}' class='js-es-caption js-es-file__caption-field es-ignore-style content-font' value='{caption}' placeholder='" . __( 'Your caption here', 'es' ) . "'>
							</div>
							<span class='es-file__name'>{name}</span>
							<span class='es-file__size'>{size}</span>
						</div>
						<div class='js-es-file__msg es-file__msg'></div>
						<a href='#' class='es-file__btn es-btn--delete-file js-es-delete-media'><span class='es-icon es-icon_trash'></span></a>
						<a href='#' class='es-file__btn es-btn--reload-file js-es-reload-media'><span class='es-icon es-icon_preload'></span></a>
					</div>
					<div class='es-file__progress-wrap'><div class='js-es-progress es-file__progress es-secondary-bg'></div></div>
				</div>",

			'items_wrapper' => '<div class="es-files-list es-files-list--{uploader_type} js-files-list">{items_list}</div>',
			'uploader_type' => 'images', // Also 'files' allowed
			'upload_button_label' => __( 'Upload files', 'es' ),
			'upload_button_description' => __( 'or drag them in', 'es' ),
			'allowed_mime_types' => array(),
			'ajax_nonce' => wp_create_nonce( 'es_framework_upload_file' ),
			'ajax-action' => 'es_framework_upload_file',
			'description' => sprintf( __( 'One file must be less than %s.', 'es' ), $max_upload_size ),
			'default_thumbnail_url' => plugin_dir_url( __FILE__ ) . '../assets/images/def-image.jpeg',
			'skeleton' => "{before}
                               <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                   {label}
                                   <div data-nonce='{ajax_nonce}' data-mime-allowed='{allowed_mime_types}' data-ajax-action='{ajax-action}' class='es-uploader-area js-es-uploader-area'>
                                   		{file_field}
                                        <div class='js-es-hidden-item es-hidden'>
                                   			{hidden_item}
                                   		</div>
                                        {items}
                                        {input}
                                   </div>
                               </div>
                           {after}",
		);

		return es_parse_args( $def, parent::get_default_config() );
	}

	/**
	 * @return array
	 */
	public function get_tokens() {
		$tokens = parent::get_tokens();
		$config = $this->get_field_config();
		$accept = ! empty( $config['allowed_mime_types'] ) ? implode( ', ', $config['allowed_mime_types'] ) : false;

		$file_field = es_framework_get_field( '', array(
			'type' => 'file',
			'attributes' => array(
				'accept' => $accept,
				'id' => 'upload-file-field-' . $config['uid'],
				'class' => 'js-es-drag-files-field es-hidden',
				'multiple' => 'multiple',
				'name' => $config['uid'],
			),
		) );

		return es_parse_args( $tokens, array(
            '{uploader_type}' => $config['uploader_type'],
            '{items}' => $this->get_items_html(),
			'{hidden_item}' => strtr( $this->get_item_template(), array( '{src}' => '' ) ),
			'{ajax-action}' => $config['ajax-action'],
			'{allowed_mime_types}' => es_esc_json_attr( $config['allowed_mime_types'] ),
			'{upload_button_label}' => $config['upload_button_label'],
			'{upload_button_description}' => $config['upload_button_description'],
			'{file_field}' => $file_field->get_input_markup(),
			'{ajax_nonce}' => $config['ajax_nonce'],
        ) );
	}

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();

		$markup = '<div class="es-uploader-area__info">
                <button class="es-btn es-btn--secondary es-btn--upload" data-trigger-click="#upload-file-field-' . $config['uid'] . '">
                    <span class="es-icon es-icon_upload"></span>
		            {upload_button_label}
                </button>
                {upload_button_description}
                {description}
            </div>';

		$markup = strtr( $markup, array(
			'{upload_button_label}' => $config['upload_button_label'],
			'{upload_button_description}' => $config['upload_button_description'] ?
				'<p class="or-drag"><b>' . $config['upload_button_description'] . '</b></p>' : '',
			'{description}' => $config['description'] ? sprintf( $config['description_wrapper'], $config['description'] ) : '',
		) );

		return $markup;
	}
}
