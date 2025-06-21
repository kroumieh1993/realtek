<?php

/**
 * @var int $count_all_posts
 * @var WP_Term[] $categories
 */

$query_category = ! empty( $args['es_category'] ) ? $args['es_category'] : array();
$query_category = ! empty( $_GET['es_category'] ) ? es_clean( $_GET['es_category'] ) : $query_category;
$query_category = ! is_array( $query_category ) ? array( $query_category ) : $query_category; ?>
<div class="es-properties__categories js-es-properties__categories">
    <a data-term="" href="<?php echo remove_query_arg( 'es_category' ); ?>" class="js-es-listings-categories-ajax <?php echo empty( $query_category ) ? 'es-active' : ''; ?>">
        <?php _e( 'All listings', 'es' ); ?> <span class="es-count"><?php echo $count_all_posts; ?></span>
    </a>
    <?php foreach ( $categories as $category ) : ?>
        <a data-term="<?php echo $category->term_id; ?>" href="<?php echo add_query_arg( $category->taxonomy, array( $category->term_id ) ); ?>" class="js-es-listings-categories-ajax <?php echo in_array( $category->term_id, $query_category ) ? 'es-active' : ''; ?>">
            <?php echo $category->name; ?> <span class="es-count"><?php echo $category->count; ?></span>
        </a>
    <?php endforeach; ?>
</div>