<?php

/**
 * Class Es_Framework_Rating_Field.
 */
class Es_Framework_Rating_Field extends Es_Framework_Field {

	public function get_input_markup() {
		$this->_field_config['type'] = 'number';
		$input = parent::get_input_markup();
		$attr = $this->get_field_config();
		$value = $attr['value'];

		$input .= '<div class="es-rating js-es-rating es-rating--interactive">';
		for ( $i = 1; $i <= 5; $i++ ) {
			$is_active = $value >= $i ? 'es-star--active' : '';
			$input .= "<div data-value='{$i}' class='es-star {$is_active}'></div>";
		}
		$input .= '</div>';

		return $input;
	}

	public function get_default_config() {
		$def = array(
			'attributes' => array(
				'min' => 0,
				'max' => 5,
				'class' => 'es-field__input es-hidden',
			),
			'wrapper_class' => 'js-es-field--rating es-field--inline',
			'skeleton' => "{before}
                               <div class='es-field es-field__{field_key} es-field--{type} {wrapper_class}'>
                                   <label for='{id}'>{label}<div class='es-rating-field-content'>{caption}{unit_before}{input}{unit_after}{description}</div></label>
                               </div>
                           {after}",
		);
		return es_parse_args( $def, parent::get_default_config() );
	}
}
