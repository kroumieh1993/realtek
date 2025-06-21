<?php

/**
 * @var $section_info array
 * @var $id string
 */

$property = es_get_the_property();
$fb_instance = es_get_fields_builder_instance();
$fields = $fb_instance::get_section_fields( $section_info['machine_name'], 'property' );
$deleted_fields = ests( 'fb_property_deleted_fields' );

if ( $fields ):
	$j = 0;
	$i = 0;
	$has_content = false;
	ob_start();
	foreach ( $fields as $field_key => $field_config ) :
        if ( in_array( $field_key, $deleted_fields ) ) continue;
        if ( empty( $field_config['is_pdf_visible'] ) || $field_key == 'post_content' ) continue;
        if ( $field_key == 'floor_plans' && ( $value = es_get_the_field( $field_key ) ) ) :

            ob_start();
	        unset( $field_config['label'] ); ?>
            <table width="100%" cellspacing="0">
                <?php foreach ( $value as $attachment_id ) :
                    echo ! ( $j % 4 ) ? '<tr>' : null; ?>
                    <td valign="top" class="es-field es-field--<?php echo $field_key; ?>" style="word-wrap:break-word;">
                        <?php echo wp_get_attachment_image( $attachment_id ); ?>
		                <?php if ( $caption = wp_get_attachment_caption( $attachment_id ) ) : ?>
                            <br><br><?php echo $caption; ?>
                        <?php endif; ?>
                    </td>
	                <?php echo ( $j % 4 ) ? '</tr>' : null; ?>
	                <?php $j++; ?>
                <?php endforeach; ?>
                <?php echo ! ( $j % 4 ) ? '<td></td></tr>' : null; ?>
            </table>
        <?php $value = ob_get_clean();

        elseif ( $field_key == 'appointments' && ( $value = es_get_the_field( $field_key ) ) ) :
            unset( $field_config['label'] );
            ob_start(); ?>
            <table id="appointments" border="0" cellspacing="5">
                <?php foreach ( $value as $appointment ) : ?>
                <tr>
                    <td><?php printf( "<li><b>%s</b><span>%s to %s</span></li>", $appointment['date'], $appointment['start_time'], $appointment['end_time'] ); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php $value = ob_get_clean();
        else :
            $value = es_get_the_formatted_field( $field_key );
        endif;

        if ( in_array( $field_key, array( 'documents' ) ) ) continue;

		$value = ! empty( $field_config['taxonomy'] ) ? strip_tags( $value ) : $value;
		if ( empty( $value ) ) continue;
		$has_content = true;
		echo ! ( $i % 2 ) ? '<tr>' : null; ?>
		<td valign="top" class="es-field es-field--<?php echo $field_key; ?>" style="word-wrap:break-word;">
            <?php if ( ! empty( $field_config['label'] ) && empty( $field_config['hide_name_in_pdf'] ) ) : ?>
                <b><?php echo $field_config['label']; ?>:</b>
            <?php endif; ?>
            <?php echo $value; ?>
        </td>
		<?php echo ( $i % 2 ) ? '</tr>' : null; ?>
		<?php $i++;
	endforeach; ?>
    <?php echo ! ( $i % 2 ) ? '<td></td></tr>' : null;
	$content = ob_get_clean();

	if ( $content && $has_content ) : ?>
		<table class="section section--<?php echo $id; ?>" width="100%" cellspacing="10">
			<tr>
				<td style="padding: 5px;">
                    <table cellspacing="0" cellpadding="1" class="sup-line" style="margin-bottom: 9px;"><tr><td></td></tr></table>
                    <h2><?php echo $section_info['label']; ?></h2>
                </td>
			</tr>
			<tr>
				<td>
		            <table cellpadding="5" cellspacing="0" border="0" class="fields-table" style="width: 100%;">
				        <?php echo $content; ?>
		            </table>
				</td>
			</tr>
		</table>
	<?php endif;
endif;
