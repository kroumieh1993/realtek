( function( $ ) {
    'use strict';

    $( function() {
        var is_empty_main_button, $sortable_plans_xhr;
        var tr = EstatikSubscription.tr;

        // Save settings form. Uses on data manager and settings page.
        $( document ).on( 'submit', '.js-es-plan-form', function() {
            var $form = $( this );
            var $submit_btn = $form.find( '.js-es-save-plan' );
            $submit_btn.prop( 'disabled', 'disabled' ).addClass( 'es-preload' );

            $.post( ajaxurl, $( this ).serialize(), function( response ) {
                if ( response.message ) {
                    Estatik_Admin.renderNotification( response.message );
                }

                if ( response.id ) {
                    var url = new URL( window.location );
                    url.searchParams.set( 'plan_id', response.id );
                    window.history.pushState( {}, '', url.toString() );
                    $form.find( '.js-es-plan-id' ).val( response.id );
                }
            }, 'json' ).fail( function() {
                Estatik_Admin.renderNotification( "<div class='es-notification es-notification--error'>Saving error. Please, contact estatik support.</div>" );
            } ).always( function() {
                $submit_btn.removeProp( 'disabled' ).removeAttr( 'disabled' ).removeClass( 'es-preload' );
                $( 'html,body' ).animate( { scrollTop: 0 }, 'slow' );
            } );

            return false;
        } );

        $( '.js-es-plan-name' ).change( function() {
            var $btn = $( '#es-field-main_button' );

            if ( $( this ).val() ) {
                if ( typeof is_empty_main_button === 'undefined' ) {
                    is_empty_main_button = +$btn.val().length;
                }

                if ( ! is_empty_main_button ) {
                    $btn.val( 'Buy ' + $( this ).val() );
                }
            }
        } );

        $( '.js-es-is-limited' ).change( function() {
            var $field_wrap = $( $( this ).data( 'field' ) );

            if ( + $( this ).val() ) {
                $field_wrap.removeClass( 'es-hidden' );
                $field_wrap.find( 'input' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
            } else {
                $field_wrap.addClass( 'es-hidden' );
                $field_wrap.find( 'input' ).prop( 'disabled', 'disabled' ).attr( 'disabled', 'disabled' );
            }
        } );

        $( '.js-es-is-limited:checked' ).trigger( 'change' );

        $( '.js-es-plans-table tbody' ).sortable( {
            update: function( event, ui ) {
                var $el = $( event.target );

                var data = {
                    action: 'es_save_plans_order',
                    ordered_ids: [],
                    _wpnonce: EstatikSubscription.nonces.save_plans_order
                };

                $el.find( 'tr' ).each( function() {
                    var $tr = $( this );
                    data.ordered_ids.push( $tr.data( 'plan-id' ) );
                } );

                if ( $sortable_plans_xhr ) $sortable_plans_xhr.abort();

                $sortable_plans_xhr = $.post( ajaxurl, data, function( response ) {
                    response = response || {};
                    if ( response.message ) {
                        Estatik_Admin.renderNotification( response.message );
                    }
                }, 'json' ).fail( function() {

                } ).always( function() {

                } );
            }
        } );
        
        $( document ).on( 'click', '.js-es-confirm-delete', function( e ) {
            e.preventDefault();

            $.estatikPopup( {
                inline_html: "<p class='es-center es-popup-text'>" + $( this ).data( 'message' ) + "</p>" +
                    "<div class='es-popup__buttons es-center'>" +
                    "<button class='js-es-popup__close es-btn es-btn es-btn--link'>" + tr.cancel + "</button>" +
                    "<a href='" + $( this ).prop( 'href' ) + "' class='js-es-fields-builder-remove-section es-btn es-btn--secondary'>" + tr.remove  + "</a></div>"
            } ).open();
        } );
    } );
} )( jQuery );
