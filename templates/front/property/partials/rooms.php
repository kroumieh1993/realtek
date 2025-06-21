<div class="es-table-wrap">
    <table class="es-table">
        <thead>
        <tr>
            <?php

            /**
             * @var $rooms array
             */

            $rooms_field = es_property_get_field_info( 'rooms' );
            $rooms_field = es_filter_rooms_fields( $rooms_field['fields'], $rooms );

            foreach ( $rooms_field as $field_key => $field_info ) : ?>
                <?php if ( ! empty( $field_info['is_table_col'] ) ) : ?>
                    <th><?php echo apply_filters( 'es_rooms_table_col_heading', $field_info['label'], $field_key ); ?></th>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ( $rooms as $key => $room ) : ?>
            <tr>
                <?php foreach ( $rooms_field as $field_key => $field_info ) : ?>
                    <?php if ( ! empty( $field_info['is_table_col'] ) ) :
                        $value = ! empty( $room[ $field_key ] ) ? $room[ $field_key ] : ''; ?>
                        <td>
	                        <?php echo apply_filters( 'es_rooms_table_col_value', $value, $room, $field_key ); ?>
                        </td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>