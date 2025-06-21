<?php

/**
 * Class Es_Framework_Entities_List_Field.
 */
class Es_Framework_Entities_List_Field extends Es_Framework_Base_Field {

	/**
	 * @return string
	 */
	function get_input_markup() {
		$config = $this->get_field_config();
		$config['attributes']['data-action'] = $config['ajax-item-action'];
		$config['attributes']['data-item-markup'] = $this->get_item_markup();
		$config['value'] = '';
		unset( $config['skeleton'], $config['item_markup'], $config['attributes']['name'] );

		$select = new Es_Framework_Select_Field( '', $config );

		return $select->get_input_markup();
	}

	/**
	 * @return array
	 */
	public function get_default_config() {

		$default = array(
			'ajax-item-action' => '',
			'attributes' => array(
				'class' => 'js-es-entities-items-search',
			),
			'skeleton' => "{before}
							   <div class='es-entities-list-field js-es-entities-list-field'>
									{items}
	                                <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
	                                    <label for='{id}'>{label}{caption}{input}{description}{reset}</label>
	                                </div>
                               </div>
                           {after}",
		);

		return es_parse_args( $default, parent::get_default_config() );
	}

	/**
	 * @return string
	 */
	public function get_item_markup() {
		$config = $this->get_field_config();
		$name = $config['attributes']['name'];

		return '<div class="entity-box js-es-entity-box entity-box--{id}">
				<input type="hidden" name="' . $name . '[]" value="{value}">
                <div class="entity-box__inner">
                    <div class="entity-box__image">
                        {image}
                    </div>
                    <div class="entity-box__content">
                        <b>{title}</b>
                        <p>{subtitle}</p>
                    </div>
                    <a href="#" class="entity-box__delete js-es-delete-entity"><span class="es-icon es-icon_trash"></span></a>
                </div>
            </div>';
	}

	/**
	 * @return string
	 */
	public function get_items_markup() {
		$config = $this->get_field_config();
		$html = '';

		if ( empty( $config['items'] ) && ! empty( $config['items_callback'] ) ) {
			$config['items'] = call_user_func( $config['items_callback'] );
		}

		if ( ! empty( $config['items'] ) ) {
			foreach ( $config['items'] as $item ) {
				$html .= strtr( $this->get_item_markup(), array(
					'{id}' => $item['id'],
					'{value}' => $item['value'],
					'{title}' => $item['title'],
					'{subtitle}' => $item['subtitle'],
					'{image}' => $item['image'],
				) );
			}
		}

		return "<div class='js-es-items es-entities-list'>" . $html . "</div>";
	}

	/**
	 * @return array
	 */
	public function get_tokens() {
		$tokens = parent::get_tokens();

		$tokens['{items}'] = $this->get_items_markup();

		return $tokens;
	}
}