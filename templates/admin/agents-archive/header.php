<div class="es-wrap">
	<div class="es-head">
		<h1><?php _e( 'Agents', 'es' ); ?></h1>
        <?php if ( Es_Agent_Post::count() ) : ?>
            <a href="<?php echo admin_url( 'post-new.php?post_type=agent' ); ?>" class="es-btn es-btn--secondary es-btn--icon">
                <span class="es-icon es-icon_plus"></span>
		        <?php _e( 'Add new agent', 'es' ); ?>
            </a>
        <?php endif; ?>
		<div class="es-head__logo">
			<?php do_action( 'es_logo' ); ?>
		</div>
	</div>
</div>

