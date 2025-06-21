<?php

/**
 * @var $entity_name string
 * @var $entity_name_plural string
 */

?>
<div class="js-es-<?php echo $entity_name_plural; ?> es-<?php echo $entity_name_plural; ?> es-entities es-entities--list" data-layout="es-entities--list">
	<?php es_load_template( "front/entity/content-archive.php", array(
		'ignore_wrapper' => true,
		'entity_control_config' => array(
			'is_full' => true,
			'show_sharing' => true,
			'context' => 'default'
		)
	) ); ?>
</div>
