<div class="es-comments">
	<?php if ( comments_open() ) : ?>
		<div class="es-comments__head">
			<?php if ( have_comments() ) : ?>
				<?php echo es_get_rating_markup( 5 ); ?>
			<?php else : ?>
				<p><?php printf( __( 'Be one of the first clients to review %s`s work to help them get started.', 'es' ), get_the_title() ); ?></p>
			<?php endif; ?>

			<?php if ( ests( 'is_' . $post->post_type . '_commenting_enabled' ) ) : ?>
                <a href="<?php echo add_query_arg( 'form', 'review', get_the_permalink() ); ?>" class="es-btn es-btn--secondary">
                    <span class="es-icon es-icon_plus"></span>
                    <?php _e( 'Write a review', 'es' ); ?>
                </a>
            <?php endif; ?>
		</div>

		<?php if ( have_comments() ) : ?>
			<ol class="es-comments__list">
				<?php wp_list_comments(
					array(
						'walker' => new Es_Walker_Comment(),
						'style'  => 'ol',
                        'max_depth' => 1
					)
				) ?>
			</ol>
		<?php endif;
	endif; ?>
</div>
