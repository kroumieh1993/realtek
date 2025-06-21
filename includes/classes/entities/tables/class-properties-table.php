<?php

/**
 * Class Es_Properties_Table
 */
class Es_Properties_Table extends Es_Entities_Table {

	/**
	 * @return array|mixed|void
	 */
	public static function get_table_columns_fields() {
		$fields = array(
			'_manage-checkbox',
			'ID',
			'gallery',
			'post_title',
			'address',
			'date_added',
			'price',
			'es_category',
			'es_type',
			'es_status',
			'_manage-buttons'
		);

		return apply_filters( sprintf( 'es_%s_get_table_columns_fields', static::get_entity_type() ), $fields );
	}

	/**
	 * @return string
	 */
	public static function get_entity_type() {
		return 'property';
	}
}
