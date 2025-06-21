<?php

use \Elementor\Plugin;

/**
 * Class Es_Elementor_Init.
 */
class Es_Elementor_Init {

	/**
	 * Initialize estatik elementor integration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/controls/register', array( 'Es_Elementor_Init', 'register_custom_control' ) );
        add_action( 'elementor/widgets/widgets_registered', array( 'Es_Elementor_Init', 'register_widgets' ) );
        add_action( 'elementor/elements/categories_registered', array( 'Es_Elementor_Init', 'register_category' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( 'Es_Assets', 'register_global_assets' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( 'Es_Assets', 'admin_assets' ) );
        add_action( 'elementor/editor/before_enqueue_scripts', array( 'Es_Elementor_Init', 'enqueue_assets' ) );
        add_filter( 'elementor/widgets/black_list', array( 'Es_Elementor_Init', 'widgets_black_list' ) );
		add_action( 'elementor/documents/register', array( 'Es_Elementor_Init', 'register_document_type' ) );
		add_action( 'elementor/db/before_save', array( 'Es_Elementor_Init', 'save_temp_post_content' ) );
	}

	/**
	 * @param $controls_manager \Elementor\Controls_Manager
	 */
	public static function register_custom_control( $controls_manager ) {
		require_once( ES_PLUGIN_CLASSES . 'elementor/controls/select2.php' );

		$controls_manager->unregister( 'select2' );
		$controls_manager->register( new \Es_Elementor_Select2_Control() );
	}

	/**
	 * @param $manager Elementor\Core\Documents_Manager
	 */
	public static function register_document_type( $manager ) {
		if ( class_exists( 'ElementorPro\Modules\ThemeBuilder\Documents\Single_Base' ) ) {
			require_once untrailingslashit( ES_PLUGIN_CLASSES . '/class-elementor-property-document.php' );
			require_once untrailingslashit( ES_PLUGIN_CLASSES . '/class-elementor-agent-document.php' );
			require_once untrailingslashit( ES_PLUGIN_CLASSES . '/class-elementor-agency-document.php' );

			$manager->register_document_type( 'single-properties', 'Es_Elementor_Property_Document' );
			$manager->register_document_type( 'single-agent', 'Es_Elementor_Agent_Document' );
			$manager->register_document_type( 'single-agency', 'Es_Elementor_Agency_Document' );
		}
	}

    /**
     * Disable default estatik widgets for elementor.
     *
     * @param $list
     * @return array
     */
	public static function widgets_black_list( $list ) {
        $list[] = 'Es_Request_Form_Widget';
        $list[] = 'Es_Search_Form_Widget';
        $list[] = 'Es_Properties_Slider_Widget';
        $list[] = 'Es_Listings_Widget';
        $list[] = 'Es_Properties_Slideshow_Widget';
        $list[] = 'Es_Locations_Grid_Widget';

        return $list;
    }

	/**
	 * Enqueue elementor editor assets.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
        wp_enqueue_style( 'es-admin' );
        wp_enqueue_script( 'es-admin' );
    }

    /**
     * Register elementor widgets.
     *
     * @return void
     * @throws Exception
     */
	public static function register_widgets() {
        if ( class_exists( 'Elementor\Widget_Base' ) ) {
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-base.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-query.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-search-form-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-properties-slider-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-properties-slideshow-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-request-form-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-listings-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-authentication-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-half-map-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-locations-grid-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-agents-widget.php';
            require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-agencies-widget.php';

			if ( ests( 'is_subscriptions_enabled' ) ) {
				require_once ES_PLUGIN_CLASSES . 'widgets' . DS . 'elementor' . DS . 'class-elementor-subscriptions-table-widget.php';
				Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Subscriptions_Table_Widget() );
			}

            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Search_Form_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Properties_Slider_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Properties_Slideshow_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Request_Form_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Listings_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Authentication_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Half_Map_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Locations_Grid_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Agents_Widget() );
            Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Es_Agencies_Widget() );
        }
	}

	/**
	 * Register new Estatik category.
	 *
	 * @param $elements_manager \Elementor\Elements_Manager
	 */
	public static function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'estatik-category',
			array(
				'title' => _x( 'Estatik', 'Elementor widgets category name', 'es' ),
			)
		);
	}

	/**
	 * @return void
	 */
	public static function save_temp_post_content() {
		$post_id = filter_input( INPUT_POST, 'editor_post_id' );
		$entity = es_get_entity_by_id( $post_id );

		if ( $entity instanceof Es_Entity ) {
			$post = get_post( $post_id );

			$content_copied = get_post_meta( $post_id, 'es_post_content_copied', true );
			$is_valid_content = stristr( $post->post_content, '[es_single' ) === false && stristr( $post->post_content, 'es-single' ) === false;

			if ( ! $content_copied && empty( $entity->alternative_description ) && $is_valid_content ) {
				$entity->save_field_value( 'alternative_description', $post->post_content );
				update_post_meta( $post_id, 'es_post_content_copied', 1 );
			}
		}
	}
}

Es_Elementor_Init::init();
