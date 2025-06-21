<?php

/**
 * @var $args array
 */

?>
<ul class="js-es-control--layouts es-control es-control--layouts">
	<li class="es-control__grid">
		<a href="<?php echo add_query_arg( 'layout', 'grid' ); ?>" data-layout="grid" class="js-es-change-layout es-btn es-btn--icon es-btn--gray es-btn--big <?php echo es_is_grid_layout( $args['layout'] ) ? 'es-btn--active' : ''; ?>">
			<span class="es-icon es-icon_grid"></span>
		</a>
	</li>
	<li class="es-control__list">
		<a href="<?php echo add_query_arg( 'layout', 'list' ); ?>" data-layout="list" class="js-es-change-layout es-btn es-btn--icon es-btn--gray es-btn--big <?php es_active_class( $args['layout'], 'list', 'es-btn--active' ); ?>">
			<span class="es-icon es-icon_grid-row"></span>
		</a>
	</li>
</ul>
