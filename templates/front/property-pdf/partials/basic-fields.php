<?php $fields = apply_filters( 'es_property_pdf_sidebar_fields', array(
	'bedrooms', 'bathrooms', 'es_type', 'es_category', 'es_status', 'area' ) );
$num_fields = apply_filters( 'es_property_pdf_sidebar_num_fields', 8 ); ?>

<table id="basic-fields" width="100%">
    <tr>
        <td>
            <ul>
		        <?php if ( $fields ) : $i = 0; ?>
			        <?php foreach ( $fields as $field ) :
                        $field_config = es_property_get_field_info( $field );
                        if ( empty( $field_config['is_pdf_visible'] ) || empty( $field_config ) ) continue;
				        if ( ! ( $value = es_get_the_formatted_field( $field ) ) ) continue; ?>
				        <?php if ( $value && $i < $num_fields ) : ?>
                        <li class="basic-field">
                            <?php echo strip_tags( $value ); ?>
                        </li>
                        <table style="height: 7px;"><tr><td></td></tr></table>
				        <?php $i++; endif; ?>
			        <?php endforeach; ?>
		        <?php endif; ?>
            </ul>
        </td>
    </tr>
</table>
