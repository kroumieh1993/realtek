<?php

/**
 * Class Es_Agency_Fields_Meta_Box
 */
class Es_Agency_Fields_Meta_Box extends Es_Entity_Fields_Meta_Box {

	/**
	 * @var string
	 */
	public static $render_field_callback = 'es_agency_field_render';

	/**
	 * @return string
	 */
	public static function get_entity_name() {
		return 'agency';
	}

	/**
	 * @return string
	 */
	public static function get_post_type_name() {
		return 'agency';
	}

	/**
	 * @return string|void
	 */
	public static function get_metabox_title() {
		return __( 'Agency information', 'es' );
	}

	public static function enqueue_scripts() {
		parent::enqueue_scripts();
		wp_enqueue_script( 'es-agent-metabox', ES_PLUGIN_URL . 'admin/js/agent-metabox.min.js', array( 'jquery', 'es-admin' ), Estatik::get_version() );
	}
}

Es_Agency_Fields_Meta_Box::init();
