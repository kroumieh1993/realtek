<?php

/**
 * Class Es_Walker_Comment
 */
class Es_Walker_Comment extends Walker_Comment {

	/**
	 * Outputs a single comment.
	 *
	 * @since 3.6.0
	 *
	 * @see wp_list_comments()
	 *
	 * @param WP_Comment $comment Comment to display.
	 * @param int        $depth   Depth of the current comment.
	 * @param array      $args    An array of arguments.
	 */
	protected function comment( $comment, $depth, $args ) {
		$this->html5_comment( $comment, $depth, $args );
	}

	/**
	 * Outputs a comment in the HTML5 format.
	 *
	 * @see wp_list_comments()
	 *
	 * @param WP_Comment $comment Comment to display.
	 * @param int        $depth   Depth of the current comment.
	 * @param array      $args    An array of arguments.
	 */
	protected function html5_comment( $comment, $depth, $args ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

		$commenter          = wp_get_current_commenter();
		$show_pending_links = isset( $commenter['comment_author'] ) && $commenter['comment_author'];

		if ( $commenter['comment_author_email'] ) {
			$moderation_note = __( 'Your comment is awaiting moderation.' );
		} else {
			$moderation_note = __( 'Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.' );
		} ?>

		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="es-comment-body">

            <div class="comment-head">
                <div class="comment-author">
                    <?php echo es_get_rating_markup( get_comment_meta( get_comment_ID(), 'es_review_rating', true ), false ); ?>
                    <span class="comment-author-name"><?php printf( _x( 'by %s', 'commented by', 'es' ), get_comment_author() ); ?></span>
                </div>
                <div class="comment-date">
                    <?php comment_date(); ?>
                </div>
            </div>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

		</article><!-- .comment-body -->
		<?php
	}
}
