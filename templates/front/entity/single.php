<?php

/**
 * @var $entity_name string
 */

do_action( 'es_before_single_wrapper', get_the_ID() );

if ( es_is_review_form() && ests( "is_{$entity_name}_commenting_enabled" ) ) : ?>
    <?php do_action( 'es_reviews_form', get_the_ID() ); ?>
<?php else : ?>
    <div class='es-entity-single js-es-entity-single js-es-<?php echo $entity_name; ?>-single es-<?php echo $entity_name; ?>-single'>
        <div class="es-entity__single-block js-es-entity__single-block">
            <?php do_action( "es_single_{$entity_name}_layout", get_the_ID() ); ?>
        </div>

        <?php $instance = es_get_sections_builder_instance();
        if ( $sections = $instance::get_items( $entity_name ) ) : ?>
            <div class="es-entity-sections">
                <?php foreach ( $sections as $section ) :
                    do_action( "es_single_entity_section", $section, get_the_ID() );
                endforeach; ?>
            </div>
        <?php endif; ?>

        <?php do_action( "es_after_single_{$entity_name}_content" ); ?>
        <?php do_action( 'es_after_single_content' ); ?>
    </div>
<?php endif;

do_action( 'es_after_single_wrapper', get_the_ID() );
