<?php

/**
 * @var $saved_search Es_Saved_Search
 */

?>
<form action="" method="post" class="js-es-saved-search-form">
	<?php if ( $types = Es_Saved_Search_Emailer::get_types() ) :
		es_framework_field_render( 'update_type', array(
			'type' => 'radio',
			'options' => $types,
			'label' => __( 'Update', 'es' ),
			'value' => $saved_search->update_type,
			'attributes' => array(
				'class' => 'js-es-submit-on-change'
			),
		) );
	endif; ?>
	<input type="hidden" name="saved_search_id" value="<?php echo $saved_search->get_id(); ?>"/>
	<input type="hidden" name="action" value="es_saved_search_change_period"/>
	<?php wp_nonce_field( 'es_saved_search_change_period' ); ?>

    <div class="js-es-saved-search__notify es-saved-search__notify"></div>
</form>
