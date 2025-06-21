( function( $ ) {
    'use strict';

    /**
     *
     * @param $field
     * @param $dep_field
     */
    function loadFieldLocations( $field, $dep_field ) {
        var data = {
            action: 'es_get_locations',
            nonce: Estatik.nonce.nonce_locations,
            types: $field.data( 'address-components' )
        };

        var placeholder = $field.data( 'placeholder' );

        if ( placeholder ) {
            $field.html( "<option>" + placeholder + "</option>" );
        } else {
            $field.html('');
        }

        if ( typeof $dep_field !== 'undefined' ) {
            data.dependency_id = $dep_field.val();
        }

        $.get( Estatik.settings.ajaxurl, data, function( response ) {
            if ( response ) {
                Object.keys( response ).map(function(objectKey, index) {
                    var label = response[objectKey];

                    if ( $field.data('value') && $field.data('value') == objectKey ) {
                        $field.append( "<option value='" + objectKey + "' selected>" + label + "</option>" );
                    } else {
                        $field.append( "<option value='" + objectKey + "'>" + label + "</option>" );
                    }
                });

                if ( $field.data('value') ) {
                    $field.trigger( 'change' );
                }
            }
        }, 'json' );
    }

    $( function() {
        $( '.es-property-form .js-es-tabs' ).on(  'tab_changed', function() {
            var $active_tab = $( this ).find( '.js-es-tabs__nav .active' );

            if ( ! $active_tab.prev().length ) {
                $( this ).find( '.js-es-nav-link[data-type="prev"]' ).css( 'visibility', 'hidden' );
            } else {
                $( this ).find( '.js-es-nav-link[data-type="prev"]' ).css( 'visibility', 'visible' );
            }

            if ( ! $active_tab.next().length ) {
                $( this ).find( '.js-es-nav-link[data-type="next"]' ).addClass( 'es-hidden' );
                $( this ).find( '.js-es-save-listing-secondary' ).removeClass( 'es-hidden' );
                $( this ).find( '.js-es-save-listing' ).css( 'visibility', 'hidden' );
            } else {
                $( this ).find( '.js-es-nav-link[data-type="next"]' ).removeClass( 'es-hidden' );
                $( this ).find( '.js-es-save-listing-secondary' ).addClass( 'es-hidden' );
                $( this ).find( '.js-es-save-listing' ).css( 'visibility', 'visible' );
            }
        } ).trigger( 'tab_changed' );

        $( '.js-es-nav-link' ).click( function( e ) {
            var direction = $( this ).data( 'type' );

            var $active_tab = $( this ).closest( '.js-es-tabs' ).find( '.js-es-tabs__nav .active' );

            if ( direction === 'prev' ) {
                if ( $active_tab.prev().length ) {
                    $active_tab.prev().find( 'a' ).trigger( 'click' );
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $( $active_tab.prev().find( 'a' ).data( 'tab' ) ).offset().top - 100
                    }, 500);
                }
            } else {
                if ( $active_tab.next().length ) {
                    $active_tab.next().find( 'a' ).trigger( 'click' );
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $( $active_tab.next().find( 'a' ).data( 'tab' ) ).offset().top - 120
                    }, 500);
                }
            }

            e.preventDefault();
            return false;
        } );

        $( '.js-es-agency' ).each( function() {
            $( this ).select2( {
                width: 'style',
                placeholder: $( '.js-es-agency' ).data( 'placeholder' ),
            } );
        } );

        $( document ).on( 'click', '.js-es-delete-property', function() {
            var $el = $( this );
            var property_id = $el.data( 'entity-id' );
            var data = {
                action: 'es_management_delete_property_popup',
                _nonce: Estatik.nonce.delete_property_popup,
                property_id: property_id
            };

            if ( property_id ) {
                $.get( Estatik.settings.ajaxurl, data, function( response ) {
                    $.magnificPopup.open( {
                        items: { src: response.message },
                        type:'inline',
                        midClick: true,
                        mainClass: 'es-magnific',
                        closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                        callbacks: {
                            beforeOpen: function () {
                                $.magnificPopup.close();
                            },
                        }
                    } );

                    setTimeout( function() {
                        EstatikProperties.initCarousel( $( document ).find( '.es-magnific-popup--delete-action' ) );
                    } );
                }, 'json' );
            }

            return false;
        } );

        $( document ).on( 'click', '.js-es-delete-selected-listings', function() {
            var $checkboxes = $( '.js-es-table tbody .es-column--_manage-checkbox input[type=checkbox]:checked' );
            var post_ids = [];
            var action = $( this ).data( 'action' );

            $checkboxes.each( function() {
                post_ids.push( $( this ).val() );
            } );

            if ( post_ids.length ) {
                var url = new URL( window.location.href );
                url.searchParams.append( 'action', action );
                url.searchParams.append( 'post_ids', post_ids );
                url.searchParams.append( '_nonce', $( this ).data( 'nonce' ) );

                window.location.href = url.href;
            }
        } );

        $( document ).on( 'click', '.js-es-delete-bulk, .js-es-duplicate-bulk', function() {
            var tr = EstatikManagement.tr;
            var action = $( this ).data( 'action' );

            var $checkboxes = $( '.js-es-table tbody .es-column--_manage-checkbox input[type=checkbox]:checked' );

            if ( $checkboxes.length === 1 && 'delete' === action ) {
                $( '.js-es-delete-property[data-entity-id="' + $checkboxes.val() + '"]' ).trigger( 'click' );
            } else {
                var markup = "<div class='es-magnific-popup es-magnific-popup--delete-homes'>" +
                    "<h4>" + tr[action + '_homes'].replace( '%s', $checkboxes.length ) + "</h4>" +
                    "<div class='es-magnific-buttons'>" +
                    "<a href='#' class='es-btn es-btn--default js-es-close-popup'>" + tr.cancel + "</a>" +
                    "<a href='#' data-nonce='" + $( this ).data( 'nonce' ) + "' data-action='" + $( this ).data( 'action' ) + "' class='es-btn es-btn--secondary js-es-close-popup js-es-delete-selected-listings'><span class='es-icon es-icon_trash'></span>" + tr[action + "_homes_btn"] + "</a>" +
                    "</div>";

                $.magnificPopup.open( {
                    closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                    mainClass: 'es-magnific',
                    items: { src: markup },
                    type: 'inline'
                } );
            }

            return false;
        } );
    } );

    window.esLoadFieldLocations = loadFieldLocations;
} )( jQuery );