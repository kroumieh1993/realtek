<?php

/**
 * Class Es_Compare.
 */
class Es_Compare {

	/**
	 * @var Es_Compare
	 */
	protected static $_instance;

	/**
	 * @var Es_Container
	 */
	protected $_container;

	/**
	 * @return Es_Compare
	 */
	public static function get_instance() {
		if ( ! static::$_instance ) {
			static::$_instance = new static();
		}

		return static::$_instance;
	}

	private function __construct() {}
	private function __clone() {}

	/**
	 * @return Es_Compare_Cookie|Es_Compare_User
	 */
	protected function get_container() {
		if ( ! $this->_container ) {
			if ( ! is_user_logged_in() ) {
				$this->_container = new Es_Compare_Cookie();
			} else {
				$this->_container = new Es_Compare_User( get_current_user_id() );
			}
		}

		return apply_filters( 'es_compare_container', $this->_container );
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return apply_filters( 'es_compare_is_enabled', (bool) ests( 'is_compare_enabled' ) );
	}

	/**
	 * @return mixed|void
	 */
	public function is_auth_required() {
		return apply_filters( 'es_compare_is_auth_required', (bool) ests( 'is_compare_auth_required' ) );
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return apply_filters( 'es_compare_fields', ests( 'compare_fields' ) );
	}

	/**
	 * @return mixed|void
	 */
	public function can_add() {
		return apply_filters( 'es_compare_can_add', ests( 'compare_max_entities_num' ) > count( $this->get_entities_ids() ) );
	}

	/**
	 * @param $post_id
	 */
	public function add( $post_id ) {
		$this->get_container()->add( $post_id );
	}

	/**
	 * @param $post_id
	 */
	public function remove( $post_id ) {
		$this->get_container()->remove( $post_id );
	}

	/**
	 * @return mixed|void
	 */
	public function get_entities_ids() {
		$ids = $this->get_container()->get_items_ids();

		if ( ! empty( $ids ) ) {
			foreach ( $ids as $key => $id ) {
				if ( get_post_status( $id ) != 'publish' ) {
					$this->remove( $id );
					unset( $ids[ $key ] );
				}
			}
		}

		return apply_filters( 'es_compare_entities_ids', $ids ? $ids : array() );
	}

	/**
	 * @param $id
	 *
	 * @return bool
	 */
	public function has( $id ) {
		$entities = $this->get_entities_ids();
		return $entities && in_array( $id, $entities );
	}

	/**
	 * @return WP_Query
	 */
	public function get_query() {
		$query = false;
		$args = array();

		if ( $ids = $this->get_entities_ids() ) {
			$args = apply_filters( 'es_compare_get_query_args', array(
				'post_type' => 'properties',
				'post_status' => 'publish',
				'posts_per_page' => ests( 'compare_max_entities_num' ),
				'post__in' => $ids,
				'orderby' => 'post__in',
			) );

			$query = new WP_Query( $args );
		}

		return apply_filters( 'es_compare_get_query', $query, $args );
	}

	/**
	 * @return mixed|void
	 */
	public function get_grouped_fields() {
		return apply_filters( 'es_compare_grouped_fields', es_get_taxonomies_list() );
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	public function is_grouped_field( $field ) {
		return in_array( $field, $this->get_grouped_fields() );
	}

	/**
	 * @return array
	 */
	public function group_terms() {
		$data = array();

		if ( ( $fields = $this->get_grouped_fields() ) && ( $entities_ids = $this->get_entities_ids() ) ) {
			foreach ( $fields as $field ) {
				foreach ( $entities_ids as $entity_id ) {
					$terms = get_the_terms( $entity_id, $field );

					if ( $terms ) {
						foreach ( $terms as $term ) {
							$data[ $field ][ $term->term_id ] = $term;
						}
					}
				}
			}
		}

		return $data;
	}
}
