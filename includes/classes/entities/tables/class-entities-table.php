<?php

/**
 * Class Es_Entities_Table.
 */
abstract class Es_Entities_Table {

	/**
	 * Entities table config array.
	 *
	 * @var array
	 */
	protected $_args;

	/**
	 * Es_Entities_Table constructor.
	 *
	 * @param $args
	 */
	public function __construct( $args ) {
		$entity = es_get_entity( static::get_entity_type() );

		$this->_args = es_parse_args( $args, array(
			'query_args' => array(
				'post_type' => $entity::get_post_type_name(),
				'post_status' => 'any',
			),
		) );
	}

	/**
	 * @return mixed|void
	 */
	public function get_wp_query_args() {
		return apply_filters( sprintf( 'es_' . static::get_entity_type() . '_table_wp_query_args' ), $this->_args['query_args'] );
	}

	/**
	 * Return list of columns fields.
	 *
	 * @return array
	 */
	abstract public static function get_table_columns_fields();

	/**
	 * @return string
	 */
	abstract public static function get_entity_type();

	/**
	 * Display entities table.
	 *
	 * @return void
	 */
	public function render() {
		es_load_template( 'common/tables/entities-table.php', array(
			'entities_query' => new WP_Query( $this->get_wp_query_args() ),
			'instance' => $this,
			'empty_entity' => es_get_entity( static::get_entity_type() ),
		) );
	}
}
