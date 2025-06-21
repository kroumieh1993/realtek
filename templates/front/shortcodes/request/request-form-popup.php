<?php

/**
 * @var $args array
 * @var $attributes array
 */

?><div id="<?php echo $attributes['id']; ?>" class="es-request-form es-request-form--popup" style="background: <?php echo $attributes['background']; ?>">
	<?php if ( ! empty( $attributes['title'] ) ) : ?>
		<h3 class="es-widget__title"><?php echo $attributes['title']; ?></h3>
	<?php endif;
    include es_locate_template( 'front/shortcodes/request/form.php' ); ?>
	<div class="js-es-request-form__response"></div>
</div>

<style>
	#<?php echo $attributes['id']; ?>, #<?php echo $attributes['id']; ?> .es-field__label,
	#<?php echo $attributes['id']; ?> .es-widget__title {
		color: <?php echo $attributes['color']; ?>;
	}

</style>
