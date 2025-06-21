<?php

/**
 * @var $query WP_Query
 * @var $layout string
 * @var $wrapper_class string
 * @var $args array Shortcode attributes
 * @var $hash string Encoded shortcode attributes.
 * @var $search_form Es_Search_Form_Shortcode
 */

if ( empty( $args['ignore_search'] ) && ! empty( $args['search_form_selector'] ) ) : ?>
    <div data-search-form-selector='<?php echo $args['search_form_selector']; ?>'>
<?php endif; ?>

<div class="<?php echo $wrapper_class; ?>">
    <?php if ( ! empty( $search_form ) ) : ?>
        <?php echo $search_form->get_content(); ?>
    <?php endif; ?>

    <?php include es_locate_template( 'front/entity/entities-list.php' ); ?>
</div>

<?php if ( empty( $args['ignore_search'] ) && ! empty( $args['search_form_selector'] ) ) : ?>
    </div>
<?php endif;
