<?php

$instance = es_get_shortcode_instance( 'es_prop_management' ); ?>

<div id="<?php echo $current_tab; ?>" class="es-profile__content es-profile__content--<?php echo $current_tab; ?>">
    <?php if ( ! empty( $tabs[ $current_tab ]['label'] ) ) : ?>
        <div class="es-title-with-button">
            <h2 class="heading-font"><?php echo $tabs[ $current_tab ]['label']; ?></h2>
        </div>
    <?php endif;

    echo $instance->get_content(); ?>
</div>
