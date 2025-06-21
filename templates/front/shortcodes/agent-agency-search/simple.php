<?php
/**
 * @var $container_classes string
 * @var $args array
 * @var $title string
 * @var $attributes array
 * @var $search_page_id int
 * @var $search_page_uri string
 * @var $search_page_exists bool
 * @var $shortcode_instance Es_Agent_Agency_Search_Form_Shortcode
 */

$uniqid = uniqid();
$settings = es_get_settings_container();

if ( $attributes['fields'] ) : ?>
<?php if ( ! empty( $attributes['background'] ) ) : ?>
    <style>
        #es-search--<?php echo $uniqid; ?> {
            background: <?php echo $attributes['background']; ?>;
        }
    </style>
<?php endif; ?>

<div class="es-entities__search">
    <div class="<?php echo $container_classes; ?>" id="es-search--<?php echo $uniqid; ?>">
        <form action="<?php echo $search_page_uri; ?>" method="get">
            <input type="hidden" name="es" value="1"/>

	        <?php if ( ! $search_page_exists ) : ?>
                <input type="hidden" name="s"/>
                <input type="hidden" name="post_type" value="properties"/>
	        <?php else: ?>
		        <?php if ( ! get_option( 'permalink_structure' ) ) : ?>
                    <input type="hidden" name="page_id" value="<?php echo $search_page_id; ?>"/>
		        <?php endif; ?>
	        <?php endif; ?>

            <?php if ( ! empty( $attributes['fields'] ) ) : ?>
                <div class="es-search__fields-wrap">
                    <?php foreach ( $attributes['fields'] as $field ) :
                        do_action( 'es_search_render_field', $field, $attributes );
                    endforeach; ?>

                    <button type="submit" class="es-btn es-btn--primary">
                        <span class="es-icon es-icon_search"></span><?php _e( 'Search', 'es' ); ?>
                    </button>

                    <button type="submit" class="es-btn es-btn--primary es-btn--icon">
                        <span class="es-icon es-icon_search"></span>
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php endif;
