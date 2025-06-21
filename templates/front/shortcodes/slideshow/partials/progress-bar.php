<?php

/**
 * @var $attributes array
 */

if ( ! empty( $attributes['progress_bar_position'] ) ) : ?>
    <div class="js-es-slick-progress es-slick-progress es-slick-progress--<?php echo $attributes['progress_bar_position']; ?>">
        <div class="es-progress es-bg-secondary"></div>
    </div>
<?php endif;
