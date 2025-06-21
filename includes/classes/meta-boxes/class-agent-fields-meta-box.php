<?php

/**
 * Class Es_Agent_Fields_Meta_Box
 */
class Es_Agent_Fields_Meta_Box extends Es_Entity_Fields_Meta_Box {

	/**
	 * @var string
	 */
	public static $render_field_callback = 'es_agent_field_render';

	/**
	 * @return string
	 */
	public static function get_entity_name() {
		return 'agent';
	}

	/**
	 * @return string
	 */
	public static function get_post_type_name() {
		return 'agent';
	}

	/**
	 * @return string|void
	 */
	public static function get_metabox_title() {
		return __( 'Agent information', 'es' );
	}

	/**
	 * @return void
	 */
	public static function enqueue_scripts() {
		parent::enqueue_scripts();
		$deps = array( 'jquery', 'es-select2', 'es-admin' );
		wp_enqueue_script( 'es-agent-metabox', ES_PLUGIN_URL . 'admin/js/agent-metabox.min.js', $deps, Estatik::get_version() );
		wp_localize_script( 'es-agent-metabox', 'EstatikAgentMetabox', array(
			'tr' => array(
				'invalid_password_format' => __( 'Invalid password field format', 'es' ),
			),
		) );
	}
}

Es_Agent_Fields_Meta_Box::init();
