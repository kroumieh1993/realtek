( function( $ ) {
    'use strict';

    $( function() {
        $( document ).on( 'click', '.js-es-create-page', function() {
            var $button = $( this );
            $button.prop( 'disabled', 'disabled' );
            $.post( ajaxurl, $( this ).data( 'request' ), function( response ) {
                response = response || {};

                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }
            }, 'json' ).fail( function() {
                Estatik_Admin.renderNotification( "<div class='es-notification es-notification--error'>Saving error. Please, contact estatik support.</div>" );
            } ).always( function() {
                $button.addClass( 'es-hidden' );
                $button.removeProp( 'disabled' ).removeAttr( 'disabled' );
            } );

            return false;
        } );

        $( '[name="es_settings[map_marker_type]"]' ).on( 'change', function() {
            var value = $( '[name="es_settings[map_marker_type]"]:checked' ).val();

            if ( 'price' === value ) {
                $( '#es-map-zoom-limit-container' ).removeClass('es-hidden');
                $( '.es-field__is_clusters_enabled, #es-cluster-container, .es-field__is_single_map_marker_enabled, #es-single-marker-container' ).addClass('es-hidden');
            } else {
                $( '.es-field__is_clusters_enabled, .es-field__is_single_map_marker_enabled, #es-single-marker-container' ).removeClass('es-hidden');
                $( '#es-field-is_clusters_enabled' ).trigger( 'change' );
            }
        } ).trigger( 'change' );

        $( '[name="es_settings[listings_layout]"]' ).on( 'change', function() {
            if ( $( this ).is( ':checked' ) ) {
                $( '.es-field__is_layout_switcher_enabled .es-field__label' ).html( $( '#es-field-is_layout_switcher_enabled' ).data( $( this ).val() + '-label' ) );
            }
        } ).trigger( 'change' );

        $( '[name="es_settings[agents_layout]"]' ).on( 'change', function() {
            if ( $( this ).is( ':checked' ) ) {
                $( '.es-field__is_agents_layout_switcher_enabled .es-field__label' ).html( $( '#es-field-is_agents_layout_switcher_enabled' ).data( $( this ).val() + '-label' ) );
            }
        } ).trigger( 'change' );

        [ {single: 'agent', plural: 'agents'}, {single: 'agency', plural: 'agencies'} ].forEach( function( item ) {
            var $default_sorting_field = $( '#es-field-' + item.plural + '_default_sorting_option' );

            $( '#es-field-is_' + item.single + '_rating_enabled' ).on( 'change', function() {
                var $field = $( '#es-field-' + item.plural + '_sorting_options-highest_rating' );

                if ( $( this ).is( ':checked' ) ) {
                    $field.closest( '.es-field' ).removeClass( 'es-hidden' );
                } else {
                    $field.removeProp( 'checked' ).removeAttr( 'checked' );
                    $field.closest( '.es-field' ).addClass( 'es-hidden' );
                    $default_sorting_field.find( '[value="highest_rating"]' ).addClass( 'es-hidden' );
                    $default_sorting_field.trigger( 'change' );
                }
            } ).trigger( 'change' );

            $( '#es-field-is_' + item.single + '_comments_enabled' ).on( 'change', function() {
                var $field = $( '#es-field-' + item.plural + '_sorting_options-reviews' );

                if ( $( this ).is( ':checked' ) ) {
                    $field.closest( '.es-field' ).removeClass( 'es-hidden' );
                } else {
                    $field.removeProp( 'checked' ).removeAttr( 'checked' );
                    $field.closest( '.es-field' ).addClass( 'es-hidden' );
                    $default_sorting_field.find( '[value="reviews"]' ).addClass( 'es-hidden' );
                    $default_sorting_field.trigger( 'change' );
                }
            } ).trigger( 'change' );

            $default_sorting_field.on( 'change', function() {
                if ( $default_sorting_field.find( 'option:selected' ).hasClass( 'es-hidden' ) ) {
                    $default_sorting_field.find( 'option:not(.es-hidden)' ).eq(0).prop( 'selected', 'selected' ).attr( 'selected', 'selected' );
                }
            } );
        } );
    } );
} )( jQuery );
