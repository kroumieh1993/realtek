( function( $ ) {
    'use strict';

    /**
     * Number.prototype.format(n, x, s, c)
     *
     * @param n length of decimal
     * @param x: length of whole part
     * @param s: sections delimiter
     * @param c: decimal delimiter
     */
    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };

    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    // Set bulk actions container width equals wp content width.
    $( window ).resize( function() {
        $( '.es-actions-container' ).each( function() {
            $( this ).width( +$( this ).parent().width() - parseInt( $( this ).css( 'padding-left' ) ) - parseInt( $( this ).css( 'padding-right' ) ) );
        } );
    } ).trigger( 'resize' );

    /**
     *
     * @param $table
     */
    function esToggleBulkActions( $table ) {
        var $checkboxes = $table.find( 'tbody .es-column--_manage-checkbox input[type=checkbox]:checked' );
        var $container = $( '.es-actions-container' );

        if ( $checkboxes.length ) {
            $container.removeClass( 'es-hidden' );
        } else {
            $container.addClass( 'es-hidden' );
        }

        $container.find( '.js-es-selected-num' ).html( $checkboxes.length );

        $( window ).trigger( 'resize' );
    }

    var Formatter = {

        /**
         *
         * @param value
         * @returns {*}
         */
        price: function( value ) {
            var pos = Realtek.settings.currency_position;
            var dec = Realtek.settings.currency_dec;
            var sup = Realtek.settings.currency_sup;
            var sign = Realtek.settings.currency_sign || Realtek.settings.currency;
            var space = ! sign ? ' ' : '';
            var price_format = sup + dec;

            var dec_num = sup === ' ' || sup === ',' || sup === '.' ? 0 : 2;
            dec_num = price_format === ',.' || price_format === '.,' ? 2 : dec_num;

            value = +(value).format( dec_num, sup , dec );

            return pos === 'after' ? value + space + sign : sign + space + value;
        }
    };

    /**
     * Responsive manager.
     *
     * @type {{init: init, breakpoints: *, initLayout: initLayout}}
     */
    var Responsinator = {
        breakpoints: Realtek.settings.responsive_breakpoints,
        initialized: false,

        /**
         * @return void
         */
        init: function(_context, $item ) {
            if ( _context ) {
                Responsinator.initLayout( _context, $item );
                // $( window ).resize( ( Responsinator._initLoopLayout )( _context, $item ) );
            } else if ( ! this.initialized ) {
                for ( var context in Responsinator.breakpoints ) {
                    Responsinator.initLayout( context, $item );
                    $( window ).resize( ( Responsinator._initLoopLayout )( context, $item ) );
                }
                this.initialized = true;
            }
        },

        /**
         * Init layout classes.
         *
         * @param context
         * @param $item
         */
        initLayout: function( context, $item ) {
            if ( Responsinator.breakpoints.hasOwnProperty( context ) ) {
                var item = Responsinator.breakpoints[ context ];
                var $el = $item || $( item.selector );

                if ( $el.length ) {
                    var breakpoints = item.breakpoints;
                    var classes = Object.keys( breakpoints );

                    $el.each( function() {
                        var $container = $( this );
                        var desktop_layout = $container.data( 'layout' ) || 'es-listings--list';
                        var current_layout = 'es-listings--list';
                        var container_width = $container.width();
                        var className, layout;

                        switch ( context ) {
                            case 'listings':
                                for ( var i = 1; i <= 6; i++ ) {
                                    if ( $container.hasClass( 'es-listings--grid-' + i ) ) {
                                        current_layout = 'es-listings--grid-' + i;
                                        break;
                                    }
                                }

                                var $wrapper = $container.closest( '.js-es-properties' );
                                var is_half_map = $wrapper.find( '.es-properties__map--visible' ).length;

                                for ( className in breakpoints ) {
                                    if ( className.includes( '--list' ) && desktop_layout.includes( '--grid' ) ) {
                                        continue;
                                    }

                                    if ( breakpoints.hasOwnProperty( className ) && breakpoints[className].min <= breakpoints[desktop_layout].min ) {
                                        if ( breakpoints[desktop_layout].min <= container_width ) {
                                            $container.removeClass( 'es-listings--list-sm' ).removeClass( current_layout ).addClass( desktop_layout );
                                        } else if ( breakpoints[ className ].min < container_width && breakpoints[desktop_layout].min > container_width ) {
                                            $container.removeClass( 'es-listings--list-sm' ).removeClass( current_layout ).addClass( className );

                                            if ( ! is_half_map ) {
                                                $container.closest( '.js-es-listings__wrap-inner' ).find( '.js-es-change-layout' ).removeClass( 'es-btn--active' );

                                                if ( className.indexOf( '--grid' ) !== -1 ) {
                                                    $container.closest( '.js-es-listings__wrap-inner' ).find( '.es-control__grid .js-es-change-layout' ).addClass( 'es-btn--active' );
                                                } else if ( className.indexOf( '--list' ) !== -1 ) {
                                                    $container.closest( '.js-es-listings__wrap-inner' ).find( '.es-control__list .js-es-change-layout' ).addClass( 'es-btn--active' );
                                                } else {
                                                    $container.closest( '.js-es-listings__wrap-inner' )
                                                        .find( '.js-es-change-layout[data-layout="' + className.replace( 'es-listings--', '' ) + '"]' )
                                                        .addClass( 'es-btn--active' );
                                                }
                                            }
                                            break;
                                        }
                                    }
                                }

                                container_width = is_half_map ? $wrapper.width() : container_width;

                                if ( breakpoints.hasOwnProperty( 'es-listings--list-sm' ) && container_width <= breakpoints['es-listings--list-sm'].min ) {
                                    $container.closest( '.js-es-listings__wrap-inner' ).find( '.es-control__list' ).hide();
                                } else {
                                    $container.closest( '.js-es-listings__wrap-inner' ).find( '.es-control__list' ).show();
                                }

                                var $navbar = $container.closest( '.js-es-properties' ).find( '.js-es-listings-filter' ) ;

                                if ( $navbar.length ) {
                                    Responsinator.init( 'listings-navbar', $navbar );
                                }
                                break;

                            case 'single-entity':
                                layout = $container.data( 'layout' );

                                classes.forEach( function( className ) {
                                    $container.removeClass( className );
                                } );

                                var $request_btn = $( '.js-es-btn--request-form' );

                                for( className in breakpoints ) {
                                    if ( breakpoints.hasOwnProperty( className ) && breakpoints[ className ].min > container_width ) {
                                        $container.addClass( className );

                                        if ( className === 'es-entity-single--xl' ) {
                                            $container.find( '.js-es-entity__single-block .js-es-control .es-btn' )
                                                .removeClass( 'es-btn--icon' )
                                                .addClass( 'es-btn--big' )
                                                .removeClass( 'es-btn--medium' );
                                        } else {
                                            $container.find( '.js-es-entity__single-block .js-es-control .es-btn' )
                                                .addClass( 'es-btn--icon' )
                                                .addClass( 'es-btn--medium' )
                                                .removeClass( 'es-btn--big' );
                                        }

                                        if ( className !== 'es-entity-single--lg' && className !== 'es-entity-single--xl' ) {
                                            if ( $( '.es-request-form' ).length ) {
                                                $request_btn.removeClass( 'es-hidden' );
                                            }
                                        } else {
                                            $request_btn.addClass( 'es-hidden' );
                                        }

                                        break;
                                    }
                                }

                                break;

                            case 'single-property':
                                layout = $container.data( 'layout' );

                                classes.forEach( function( className ) {
                                    $container.removeClass( className );
                                } );

                                var isMaxBreakpoint = false;

                                for( className in breakpoints ) {
                                    isMaxBreakpoint = className === 'es-single--xl' && breakpoints[ className ].min < container_width;
                                    if ( ( breakpoints.hasOwnProperty( className ) && breakpoints[ className ].min > container_width ) || isMaxBreakpoint ) {
                                        $container.addClass( className );

                                        if ( 'single-tiled-gallery' === layout || 'single-slider' === layout ) {
                                            if ( className === 'es-single--xl' ) {
                                                $container.find( '.js-es-single-property-layout .js-es-control .es-btn' )
                                                    .removeClass( 'es-btn--icon' )
                                                    .addClass( 'es-btn--big' )
                                                    .removeClass( 'es-btn--medium' );
                                            } else {
                                                $container.find( '.js-es-single-property-layout .js-es-control .es-btn' )
                                                    .addClass( 'es-btn--icon' )
                                                    .addClass( 'es-btn--medium' )
                                                    .removeClass( 'es-btn--big' );
                                            }
                                        }

                                        if ( 'single-left-slider' === layout ) {
                                            if ( className === 'es-single--lg' ) {
                                                $container.find( '.js-es-single-property-layout .js-es-control .es-btn' )
                                                    .removeClass( 'es-btn--icon' )
                                                    .removeClass( 'es-btn--medium' )
                                                    .addClass( 'es-btn--big' );
                                            } else {
                                                $container.find( '.js-es-single-property-layout .js-es-control .es-btn' )
                                                    .addClass( 'es-btn--icon' )
                                                    .addClass( 'es-btn--medium' )
                                                    .removeClass( 'es-btn--big' );
                                            }
                                        }

                                        break;
                                    }
                                }
                                break;

                            default:
                                classes.forEach( function( className ) {
                                    $container.removeClass( className );
                                } );

                                classes.some( function( className ) {
                                    if ( breakpoints.hasOwnProperty( className ) ) {
                                        var min_condition = ( breakpoints[ className ].hasOwnProperty( 'min' ) && breakpoints[ className ].min <= container_width ) || ! breakpoints[ className ].hasOwnProperty( 'min' );
                                        var max_condition = ( breakpoints[ className ].hasOwnProperty( 'max' ) && breakpoints[ className ].max >= container_width ) || ! breakpoints[ className ].hasOwnProperty( 'max' );

                                        if ( min_condition && max_condition ) {
                                            var triggerData = {
                                                className: className,
                                                container: $container,
                                                context: context
                                            };

                                            $container.trigger( 'es_before_layout_changed', triggerData );
                                            $container.addClass( className );
                                            $container.trigger( 'es_after_layout_changed', triggerData );
                                            return true;
                                        }
                                    }
                                } );
                        }
                    } );
                }
            }
        },

        /**
         *
         * @param context
         * @param $item
         * @returns {Function}
         * @private
         */
        _initLoopLayout: function( context, $item ) {
            return function() {
                Responsinator.initLayout( context, $item );
            };
        }
    };

    window.es_initialize_recaptcha = function() {
        $( '.js-g-recaptcha' ).each( function() {
            var $object = $( this );
            var widget_id = grecaptcha.render( $object.attr( 'id' ), {
                "sitekey": Realtek.settings.recaptcha_site_key,
                "callback": function ( token ) {
                    $object.closest( 'form' ).find( '.g-recaptcha-response' ).val( token );
                }
            } );

            $object.data( 'recaptcha-id', widget_id );
        } );
    };

    /**
     * @param $context
     */
    function searchMoreFieldsInit( $context ) {
        $context = $context || $( '.js-es-search--advanced' );
        $context.each( function() {
            var $container = $( this );
            var $fields_to_hide = $container.find( 'form>.js-search-field-container:nth-child(n+12)' ).toggleClass( 'es-hidden' );
            if ( $fields_to_hide.length ) {
                $container.find( '.js-es-search-more' ).removeClass( 'es-hidden' );
            }
        } );
    }

    /**
     * Initialize single marker google map.
     *
     * @return void
     */
    function initSingleMap() {
        $( '.js-es-property-map' ).each( function() {
            var map = $( this ).get(0);
            var lat = $( this ).data( 'latitude' );
            var lon = $( this ).data( 'longitude' );

            initMap( map, lon, lat, Realtek.settings.single_property_map_zoom );
        } );
    }

    /**
     * Init map func.
     * @param map
     * @param lon
     * @param lat
     * @param zoom
     */
    function initMap( map, lon, lat, zoom ) {
        if ( lat && lon && map && typeof google !== 'undefined' && typeof google.maps !== 'undefined' ) {
            // Initialize google map.
            zoom = zoom || 16;

            var options = {
                center: {lat: +lat, lng: +lon},
                draggable: true,
                zoom: +zoom
            };

            if ( ! Realtek.settings.can_zoom ) {
                if ( Realtek.settings.map_zoom_min ) {
                    options.minZoom = +Realtek.settings.map_zoom_min;
                }

                if ( Realtek.settings.map_zoom_max ) {
                    options.maxZoom = +Realtek.settings.map_zoom_max;
                }
            }

            // Initialize google map.
            var map_instance = new google.maps.Map( map , options );

            // Add property marker.
            new google.maps.Marker( {
                position: map_instance.getCenter(),
                map: map_instance,
            } );
        }
    }

    window.esInitMap = initMap;

    /**
     * Resize recaptcha.
     *
     * @param $captchaElements
     */
    function resizeCaptcha( $captchaElements ) {
        var captchaResized = false;
        var captchaWidth = 304;
        var captchaHeight = 78;

        $captchaElements.each( function() {
            var $captchaEl = $( this );
            var captchaWrapper = $captchaEl.closest( '.es-recaptcha-wrapper' );

            if ( $captchaEl.is(":visible") ) {
                if (captchaWrapper.width() >= captchaWidth) {
                    if (captchaResized) {
                        $captchaEl.css('transform', '').css('-webkit-transform', '').css('-ms-transform', '').css('-o-transform', '').css('transform-origin', '').css('-webkit-transform-origin', '').css('-ms-transform-origin', '').css('-o-transform-origin', '');
                        captchaWrapper.height(captchaHeight);
                    }
                } else {
                    var scale = (1 - (captchaWidth - captchaWrapper.width()) * (0.05/15));
                    $captchaEl.css('transform', 'scale('+scale+')').css('-webkit-transform', 'scale('+scale+')').css('-ms-transform', 'scale('+scale+')').css('-o-transform', 'scale('+scale+')').css('transform-origin', '0 0').css('-webkit-transform-origin', '0 0').css('-ms-transform-origin', '0 0').css('-o-transform-origin', '0 0');
                    captchaWrapper.height(captchaHeight * scale);
                    if (captchaResized === false) captchaResized = true;
                }
            }
        } );
    }

    /**
     * Set dropdown labels for simple & main search layouts.
     *
     * @param $search_container
     */
    function initSearchDropdownLabels( $search_container ) {
        var form_data = $search_container.find( 'form' ).serializeObject();
        var collapsed_value = [];
        var $collapsed_container = $search_container.find( '.js-es-search-nav__item--more' );
        var $collapsed_reset = $collapsed_container.find( '.js-es-search-nav__reset' );
        var $collapsed_open = $collapsed_container.find( '.js-es-search-nav__open' );

        $search_container.find( '.js-es-search-nav__item--more input:checked, .js-es-search-nav__item--more select' ).each( function() {
            var value = $( this ).val();

            if ( value && value.length ) {
                collapsed_value.push( value );
            }
        } );

        if ( collapsed_value.length ) {
            $collapsed_reset.removeClass( 'es-hidden' );
            $collapsed_open.addClass( 'es-hidden' );
        } else {
            $collapsed_reset.addClass( 'es-hidden' );
            $collapsed_open.removeClass( 'es-hidden' );
        }

        $( $search_container ).find( '.js-es-search-nav__single-item' ).each( function() {
            var field_data = $( this ).data();
            var is_range_mode = $( this ).data( 'range-enabled' );
            var $label = $( this ).find( '.js-es-search-nav__label' );
            var $reset = $( this ).find( '.js-es-search-nav__reset' );
            var $open = $( this ).find( '.js-es-search-nav__open' );

            $reset.addClass( 'es-hidden' );
            $open.removeClass( 'es-hidden' );

            $label.html( field_data.placeholder );
            var single_unit, plural_unit, unit, value;

            if ( is_range_mode ) {
                var min_value = form_data[ 'min_' + field_data.field ];
                var max_value = form_data[ 'max_' + field_data.field ];
                var from_value = form_data[ 'from_' + field_data.field ];

                if ( typeof min_value === 'object' ) {
                    min_value = Math.max.apply(null, min_value);
                }

                var $min_field = $( this ).find( '[name="min_' + field_data.field + '"]' );
                var $max_field = $( this ).find( '[name="max_' + field_data.field + '"]' );
                var min_value_label = $min_field.length && $min_field.prop( 'tagName' ).toLowerCase() === 'select' ? $min_field.find( 'option:selected' ).html() : min_value;
                var max_value_label = $max_field.length && $max_field.prop( 'tagName' ).toLowerCase() === 'select' ? $max_field.find( 'option:selected' ).html() : max_value;

                single_unit = $min_field.data( 'single_unit' );
                plural_unit = $min_field.data( 'plural_unit' );

                if ( min_value || max_value || from_value ) {
                    $reset.removeClass( 'es-hidden' );
                    $open.addClass( 'es-hidden' );
                }

                if ( min_value && ! max_value ) {
                    unit = min_value > 1 ? plural_unit : single_unit;
                    value = ! unit ? min_value_label + '+' : min_value + '+';
                } else if ( ! min_value && max_value ) {
                    unit = max_value > 1 ? plural_unit : single_unit;
                    value = ! unit ? 0 + ' - ' + max_value_label : 0 + ' - ' + max_value;
                } else if ( min_value && max_value ) {
                    unit = max_value > 1 ? plural_unit : single_unit;
                    value = ! unit ? min_value_label + ' - ' + max_value_label : min_value + ' - ' + max_value;
                } else if ( from_value ) {
                    unit = from_value > 1 ? plural_unit : single_unit;
                    value = ! unit ? min_value_label + '+' : from_value + '+';
                }

                if ( value ) {
                    unit = unit ? " " + unit : '';
                    $label.html( value + unit );
                }
            } else {
                var $fields = $( this ).find( 'input, select' );

                $fields.each( function() {
                    var $field = $( this );

                    value = form_data[ $field.prop( 'name' ) ];
                    single_unit = $field.data( 'single_unit' );
                    plural_unit = $field.data( 'plural_unit' );

                    if ( value ) {
                        $reset.removeClass( 'es-hidden' );
                        $open.addClass( 'es-hidden' );
                        var field_type = $field.prop( 'type' ).toLowerCase();
                        var label_rendered = false;

                        switch ( field_type ) {
                            case 'select':
                            case 'select-one':
                                unit = value > 1 ? plural_unit : single_unit;
                                $label.html( $field.find( 'option:selected' ).html() + unit );
                                label_rendered = true;
                                break;

                            case 'select-multiple':
                                var l = [];
                                $field.find( 'option:selected' ).each( function( i, option ) {
                                    l.push( $( option ).html() );
                                } );
                                $label.html( l.join( ', ' ) );
                                label_rendered = true;
                                break;

                            case 'radio':
                            case 'checkbox':
                                var $input = $field.closest( '.es-field--multiple-checks' ).find( 'input:checked' );
                                $input = $input.length ? $input : $field.closest( '.es-field--radio-bordered' ).find( 'input:checked' );
                                $input = $input.length ? $input : $field.closest( '.es-field--checkboxes-bordered' ).find( 'input:checked' );

                                if ( $input.length ) {
                                    if ( $input.length === 1 ) {
                                        single_unit = $input.data( 'single_unit' );
                                        plural_unit = $input.data( 'plural_unit' );

                                        var values = [];

                                        $input.each( function() {
                                            values.push( $( this ).closest( 'div' ).find( '.es-field__label' ).html() );
                                        } );

                                        value = values.join( ', ' );
                                        unit = value > 1 ? plural_unit : single_unit;
                                    } else {
                                        label_rendered = true;
                                        $label.html( field_data.placeholder + ' (' + $input.length + ')' );
                                    }
                                }
                                break;

                            default:
                        }

                        if ( ! label_rendered ) {
                            unit = unit ? ' ' + unit : '';
                            $label.html( value + unit );
                        }
                    }
                } );
            }
        } );
    }

    /**
     *
     * @param $field
     * @param parent_id
     */
    function esLoadLocation( $field, parent_id ) {
        var request_data = {
            action: 'es_get_locations',
            nonce: Realtek.nonce.get_locations,
            dependency_id: parent_id,
            types: $field.data( 'address-components' )
        };

        $.get( Realtek.settings.ajaxurl, request_data, function( response ) {
            $field.html('<option value="">' + $field.data( 'placeholder' ) + '</option>');
            if ( response ) {
                $field.removeProp( 'disabled' ).removeAttr( 'disabled' );
                Object.keys( response ).map(function( objectKey, index ) {
                    var label = response[objectKey];
                    var values = $field.data( 'value' );

                    if ( values ) {
                        if ( typeof values === 'string' ) {
                            values = values.split(',');
                        }

                        if ( typeof values === "object" && values.includes( objectKey ) ) {
                            $field.append( "<option value='" + objectKey + "' selected>" + label + "</option>" );
                        } else if ( +values === +objectKey ) {
                            $field.append( "<option value='" + objectKey + "' selected>" + label + "</option>" );
                        } else {
                            $field.append( "<option value='" + objectKey + "'>" + label + "</option>" );
                        }
                    } else {
                        $field.append( "<option value='" + objectKey + "'>" + label + "</option>" );
                    }
                });

                if ( $field.data( 'value' ) ) {
                    initSearchDropdownLabels( $field.closest( '.js-es-search' ) );
                }
            }
        }, 'json' );
    }

    /**
     * Initialize base location search field.
     *
     * @param $context
     */
    function initSearchBaseLocation( $context ) {
        $context = $context || $( '.js-es-search' );
        $context.each( function() {
            var priority = Realtek.settings.search_locations_init_priority;
            for ( var i in priority ) {
                var $field = $( '.js-es-search-field--' + priority[i] );
                if ( $field.length ) {
                    esLoadLocation( $field );
                    break;
                }
            }
        } );
    }

    /**
     * Set request form country phone code.
     *
     * @param country_code
     */
    function setRequestFormPhoneCode( country_code ) {
        var local_storage = window.localStorage;
        var $field = $( '.js-es-request-form' ).find( '.js-es-phone-field' );
        if ( $field.find( 'option[value="' + country_code + '"]' ).length ) {
            $field.val( country_code ).trigger( 'change' );
        } else {
            $field.val( '' ).trigger( 'change' );
        }

        local_storage.setItem( 'country_code', country_code );
    }

    /**
     * Init select2 fields for search properties form
     *
     * @return void
     */
    function initSelect2( $context ) {
        $( '.js-es-search select', $context ).each( function() {
            if ( ! $( this ).hasClass( 'select2-hidden-accessible' ) ) {
                var attr = $( this ).attr( 'multiple' );
                var $parent = $( this ).parent();

                if ( typeof attr !== typeof undefined && attr !== false ) {
                    $( this ).select2( {
                        // width: '100%',
                        tags: true,
                        dropdownCssClass: "es-select2__dropdown es-select2__dropdown--positioning",
                        tokenSeparators: [','],
                        dropdownParent: $parent,
                    } );
                } else {
                    $( this ).select2( {
                        // width: '100%',
                        placeholder: $( this ).data( 'placeholder' ),
                        dropdownCssClass: "es-select2__dropdown es-select2__dropdown--positioning",
                        allowClear: true,
                        dropdownParent: $parent,
                    } );
                }
            }
        } );
    }

    function initRequestFormPhoneCode() {
        if ( typeof Realtek.settings !== 'undefined' && +Realtek.settings.request_form_geolocation_enabled && $( '.js-es-request-form' ).length ) {
            var local_storage = window.localStorage;
            var country_code = local_storage.getItem( 'country_code' );

            if ( country_code ) {
                setRequestFormPhoneCode( country_code );
            } else {
                setRequestFormPhoneCode( Realtek.settings.country );

                if ( typeof google !== 'undefined' && typeof google.hasOwnProperty( 'maps' ) && navigator.geolocation ) {
                    navigator.geolocation.getCurrentPosition( function( position ) {
                        var location = { lat: +position.coords.latitude, lng: position.coords.longitude };

                        if ( ! location.lat || typeof google.maps.Geocoder == 'undefined' ) return;

                        var geocoder = new google.maps.Geocoder();

                        geocoder.geocode( { location: location }, function( results, status ) {
                            if ( status === 'OK' && results.hasOwnProperty( 0 ) ) {
                                var country_code = results[0].address_components.find( function( element ) {
                                    var types = element.types;

                                    for ( var i in types ) {
                                        if ( types[i] === 'country' ) {
                                            return element;
                                        }
                                    }
                                } );

                                if ( country_code ) {
                                    setRequestFormPhoneCode( country_code.short_name );
                                }
                            }
                        } );
                    } );
                }
            }
        }
    }

    function initPropertiesSlideshow() {
        $( '.js-es-p-slideshow' ).not( '.es-p-slideshow--initialized' ).each(  function() {
            var $slideshow = $( this );
            $slideshow.addClass( 'es-p-slideshow--initialized' );
            var $info_slider = $slideshow.find( '.js-es-p-slideshow__info-slider' );
            var $images_slider = $slideshow.find( '.js-es-p-slideshow__images-slider' );
            var $pager_slider = $slideshow.find( '.js-es-p-slideshow__pager-slider' );
            var $progress = $slideshow.find( '.js-es-slick-progress' );
            var $bar = $progress.find( '.es-progress' );

            var time = 5, isPause, tick, percentTime, config;

            function startProgressbar() {
                resetProgressbar();
                percentTime = 0;
                isPause = false;
                tick = setInterval(interval, 10);
            }

            function resetProgressbar() {
                $bar.css( { width: 0 + '%' } );
                clearTimeout(tick);
            }

            function interval() {
                isPause = $images_slider.hasClass( 'es-slick-paused' );

                if ( isPause === false ) {
                    percentTime += 1 / (time + 0.1);
                    $bar.css( { width: percentTime + "%" } );

                    if (percentTime >= 100) {
                        $images_slider.slick('slickNext');
                        startProgressbar();
                    }
                }
            }

            if ( $info_slider.length ) {
                config = $info_slider.data( 'slick-config' );
                $info_slider.not( '.slick-initialized' ).slick( config );
            }

            if ( $images_slider.length ) {
                config = $images_slider.data( 'slick-config' );
                config.autoplaySpeed = time * 1000;

                if ( config.autoplay ) {
                    startProgressbar();
                }

                $images_slider.not( '.slick-initialized' ).slick( config ).on("beforeChange", function() {
                    if ( config.autoplay ) {
                        resetProgressbar();
                        startProgressbar();
                    }
                    $bar.css( { width: 100 + "%" } );
                } ).on( 'mouseover', function() {
                    $images_slider.addClass( 'es-slick-paused' );
                } ).on( 'mouseleave', function() {
                    $images_slider.removeClass( 'es-slick-paused' );
                } );
            }

            if ( $pager_slider.length ) {
                config = $pager_slider.data( 'slick-config' );
                config.focusOnSelect = true;

                if ( $slideshow.hasClass( 'es-p-slideshow--bottom-previews' ) ) {
                    if ( $slideshow.hasClass( 'es-p-slideshow--md' ) ) {
                        config.slidesToShow = 5;
                    }

                    if ( $slideshow.hasClass( 'es-p-slideshow--sm' ) ) {
                        config.slidesToShow = 4;
                    }

                    if ( $slideshow.hasClass( 'es-p-slideshow--xs' ) ) {
                        config.slidesToShow = 2;
                    }
                }

                $pager_slider.not( '.slick-initialized' ).slick( config );
            }
        } );
    }

    function initLocationsCarousel() {
        $( '.js-es-locations-slick' ).each( function() {
            var $wrapper = $( this );
            var slides_num = 4;

            if ( $wrapper.hasClass( 'es-locations-grid--md' ) ) {
                slides_num = 3;
            }

            if ( $wrapper.hasClass( 'es-locations-grid--sm' ) ) {
                slides_num = 2;
            }

            if ( $wrapper.hasClass( 'es-locations-grid--xs' ) ) {
                slides_num = 1;
            }

            if ( $wrapper.hasClass( 'slick-initialized' ) ) {
                $wrapper.slick( 'unslick' );
            }

            $wrapper.slick( {
                slidesToShow: slides_num,
                arrows: true,
                prevArrow: "<button class='slick-prev'><span class='es-icon es-icon_chevron-left slick-prev'></span></button>",
                nextArrow: "<button class='slick-next'><span class='es-icon es-icon_chevron-right slick-next'></span></button>",
            } );
        } );
    }

    /**
     * Display magnific error popup.
     *
     * @param message
     */
    function displayErrorPopup( message ) {
        var markup = "<div class='es-magnific-popup es-ajax-form-popup es-magnific-popup--error'>" +
            "<span class='es-icon es-icon_close'></span><h4>" + Realtek.tr.error + "</h4>" +
            "<p style='text-align: center;'>" + message + "</p>";

        $.magnificPopup.open( {
            items: { src: markup },
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
    }

    window.initLocationsCarousel = initLocationsCarousel;

    var Compare = function( data ) {
        this.data = data;

        return this;
    };

    Compare.prototype.initSliders = function() {
        var $listingsSlider = this.data.container.find( '.js-es-compare__listings-slider' );
        var $listingsValuesSlider = this.data.container.find( '.js-es-compare__listings-values-slider' );
        var listingsCount = +$listingsSlider.find( '.js-es-listing' ).length;

        var sliderConfig = {
            slidesToShow: 3,
            arrows: true,
            swipe: false,
            asNavFor: $listingsValuesSlider,
            prevArrow: "<button class='slick-prev'><span class='es-icon es-icon_chevron-left'></span></button>",
            nextArrow: "<button class='slick-next'><span class='es-icon es-icon_chevron-right'></span></button>",
            // fade: true,
            // cssEase: 'linear'
        };

        if ( this.data.className === 'es-compare--lg' || this.data.className === 'es-compare--sm' ) {
            sliderConfig.slidesToShow = 2;
        } else if ( this.data.className === 'es-compare--xs' ) {
            sliderConfig.slidesToShow = 1;
        }

        if ( sliderConfig.slidesToShow > 1 ) {
            sliderConfig.slidesToShow = +listingsCount < 3 ? listingsCount : sliderConfig.slidesToShow;
        }

        if ( $listingsSlider.hasClass( 'slick-initialized' ) ) {
            $listingsSlider.slick( 'unslick' );
            $listingsValuesSlider.slick( 'unslick' );
        }

        $listingsSlider.slick( sliderConfig );

        sliderConfig.asNavFor = $listingsSlider;
        sliderConfig.arrows = false;
        $listingsValuesSlider.slick( sliderConfig );

        $( '.js-es-compare-listing .slick-arrow' ).on('click', function () {
            var direction = $( this ).hasClass( 'slick-next' ) ? 'slickNext' : 'slickPrev';
            $( this ).closest( '.js-es-compare-listing' ).find( '.js-es-slick' ).slick( direction );
        } );
    };

    Compare.prototype.calculateColumnsSizes = function() {
        var _this = this;

        this.data.container.find( '.js-es-compare__field' ).each( function() {
            var heights = [];
            var $fieldColumn = $( this );
            $fieldColumn.css( 'min-height', 'initial' );
            var $valuesColumns = _this.data.container.find( '.js-es-compare__listing-value[data-field="' + $fieldColumn.data( 'field' ) + '"]' );
            $valuesColumns.css( 'min-height', 'initial' );

            heights.push( +$fieldColumn.outerHeight() );

            $valuesColumns.each( function() {
                heights.push( +$( this ).outerHeight() );
            } );

            var maxHeight = Math.max.apply( Math, heights );
            $fieldColumn.css( 'min-height', maxHeight + 'px' );
            $valuesColumns.css( 'min-height', maxHeight + 'px' );
        } );
    };

    Compare.prototype.init = function() {
        var _this = this;
        this.initSliders();

        if ( typeof this.calculateTriggerInitialzied === 'undefined' ) {
            $( window ).on( 'resize', function() {
                _this.calculateColumnsSizes();
            } );

            setTimeout( function() {
                _this.calculateColumnsSizes();
            }, 500 );
            this.calculateTriggerInitialzied = true;
        }

        return this;
    };

    $( function() {
        var $disabled_forms = $( '.js-es-form-enable-on-change' );
        var autocomplemeXHR;
        var compareInstances = [];

        Responsinator.init();
        searchMoreFieldsInit();

        initSingleMap();
        initSearchBaseLocation();
        initRequestFormPhoneCode();
        initPropertiesSlideshow();

        $( document ).on( 'change', '.js-es-preferred-radio', function() {
            $( '.js-es-preferred-radio:checked' ).not( this ).removeProp( 'checked' ).removeAttr( 'checked' );
        } );

        $( '.js-es-compare-wrapper' ).on( 'es_after_layout_changed', function( e, data ) {
            if ( 'compare' === data.context ) {
                compareInstances.push( new Compare( data ).init() );
            }
        } );

        $( '.js-es-select2' ).each( function() {
            var $el = $( this );
            var config = {};

            if ( $el.attr( 'multiple' ) ) {
                config.tags = true;
            }

            $el.select2( config );
        } );

        $( document ).on( 'click', '.js-es-agent-item img', function( e ) {
            var $item = $( this ).closest( '.js-es-agent-item' );
            var $field = $item.find( '.js-es-agent-checkbox' );

            if ( $item.hasClass( 'es-agent-item--active' ) ) {
                $item.removeClass( 'es-agent-item--active' );
                $field.removeAttr( 'checked' ).removeProp( 'checked' );
            } else {
                $item.addClass( 'es-agent-item--active' );
                $field.prop( 'checked', 'checked' ).prop( 'checked', 'checked' );
            }

            e.preventDefault();
        } );

        $( window ).on( 'resize', function() {
            initLocationsCarousel();
        } ).trigger( 'resize' );

        $( document ).on( 'click', '.js-es-btn--contact', function() {
            var $btn = $( this );
            $btn.addClass( 'es-btn--preload' ).prop( 'disabled', 'disabled' ).attr( 'disabled', 'disabled' );
            var data = { action: 'es_contact_request_form', entity_id: $btn.data( 'id' ) };

            $.get( Realtek.settings.ajaxurl, data, function( response ) {
                response = response || {};
                if ( response.status === 'success' ) {
                    $.magnificPopup.open( {
                        items: { src: response.message },
                        type:'inline',
                        midClick: true,
                        mainClass: 'es-magnific es-magnific--contact',
                        closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                        callbacks: {
                            beforeOpen: function () {
                                $.magnificPopup.close();
                            },
                            open: function() {
                                var $popup = $( this.content[0] );
                                var $field = $popup.find( '[name="g-recaptcha-response"]' );
                                if ( $field.length ) {
                                    window.grecaptcha.execute( Realtek.settings.recaptcha_site_key, { action: 'request_form' }).then(function (token) {
                                        $field.val( token );
                                    });
                                }
                            }
                        }
                    } );
                } else if ( response.status === 'error' ) {

                } else {

                }
            }, 'json' ).always( function() {
                $btn.removeClass( 'es-btn--preload' ).removeProp( 'disabled', 'disabled' ).removeAttr( 'disabled', 'disabled' );
            } );

            // $.magnificPopup.open( {
            //     type: 'inline',
            //     midClick: true,
            //     mainClass: 'es-magnific',
            //     closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
            //     ajax: {
            //         settings: {
            //             url: Realtek.settings.ajaxurl + '?action=es_contact_request_form&entity_id=' + $btn.data( 'id' ),
            //         },
            //     },
            //     callbacks: {
            //         parseAjax: function() {
            //             $btn.removeClass( 'es-btn--preload' );
            //         },
            //         beforeOpen: function () {
            //             $.magnificPopup.close();
            //         },
            //     }
            // } );

            return false;
        } );

        setTimeout( initSelect2, 50 );

        $( document ).on( 'elementor/popup/show', function( e, id, defaults ) {
            if ( $( defaults.$element ).find( '.js-es-search select' ).length ) {
                initSelect2( $( defaults.$element ) );
            }

            initSearchBaseLocation( $( defaults.$element ) );
        } );

        $disabled_forms.each( function() {
            $( this ).data( 'hash', $( this ).serialize() );
        } );

        $( document ).on( 'click', '.js-es-search [type=reset]', function(e) {
            e.stopPropagation();
            e.preventDefault();
            var $form = $( this ).closest( 'form' );

            $form.find( 'input,select' ).each( function() {
                var $field = $( this );
                var type = $( this ).prop( 'type' );
                if ( type === 'radio' || type === 'checkbox' ) {
                    $field.removeProp( 'checked' ).removeAttr( 'checked' );
                    var $any_field = $( this ).closest( '.js-search-field-container' ).find( 'input[value=""]' );

                    if ( $any_field.length ) {
                        $any_field.prop( 'checked', 'checked' ).trigger( 'change' );
                    } else {
                        $field.trigger( 'change' );
                    }
                } else {
                    if ( $( this ).hasClass( 'select2-hidden-accessible' ) ) {
                        $( this ).val([]).trigger( 'change' );
                    } else {
                        $( this ).val('').trigger( 'change' );
                    }
                }
            } );
        } );

        $disabled_forms.on( 'input', 'input,select,textarea', function() {
            var $form =  $( this ).closest( 'form' );
            var hash = $form.serialize();
            var def_hash = $form.data( 'hash' );

            if ( hash !== def_hash || $form.find( '[type=file]' ).val().length ) {
                $form.find( '[type=submit]' ).removeAttr( 'disabled' ).removeProp( 'disabled' );
                $form.data( 'changed', 1 );
            } else {
                $form.find( '[type=submit]' ).attr( 'disabled', 'disabled' ).prop( 'disabled', 'disabled' );
                $form.data( 'changed', 0 );
            }
        } );

        $( '.js-es-confirm-by-pwd' ).on( 'input', 'input,select,textarea', function() {
            var $form =  $( this ).closest( 'form' );
            var $confirm_field = $form.find( '.js-es-confirm-field' );

            if ( $form.data( 'changed' ) ) {
                $confirm_field.removeClass( 'es-hidden' );
            } else {
                $confirm_field.addClass( 'es-hidden' );
            }
        } );

        $( document ).on( 'es-ajax-form-submit-success', function( e, data ) {
            data = data || {};

            if ( typeof data.form !== 'undefined' && data.response ) {
                var response = data.response;
                if ( $( data.form ).hasClass( 'es-property-form' ) && response.status === 'success' ) {
                    var url = new URL( window.location );
                    url.searchParams.delete( 'screen' );
                    url.searchParams.set( 'screen', 'edit-property' );
                    url.searchParams.set( 'property_id', response.post_id );
                    $( data.form ).find( '[name="post_id"]' ).val( response.post_id );
                    window.history.pushState( {}, '', url.toString() );
                }
            }
        } );

        $( document ).on( 'click', '.js-es-save-listing, .js-es-save-listing-secondary', function() {
            var $form = $( this ).closest( 'form' );
            $form.find('input, textarea, select').filter('textarea[required]:hidden, [required]:hidden, [type=url]:hidden, [type=email]:hidden').each( function() {
                if ( ! $(this)[0].checkValidity() ) {
                    var tabId = $( this ).closest('.js-es-tabs__content').attr( 'id' );
                    $form.find( '.js-es-tabs .js-es-tabs__nav [data-tab="#' + tabId + '"]' ).trigger( 'click' );
                    return false;
                }
            } );
        } );

        $( '.js-es-ajax-form' ).on( 'submit', function() {
            var $form = $( this );
            var $submit_btn = $form.find( '.es-btn[type=submit]' );

            $submit_btn.attr( 'disabled', 'disabled' ).prop( 'disabled', 'disabled' );

            var formData = new FormData( $form[0] );
            var $files = $form.find( 'input[type=file]' );

            if ( $files.length ) {
                $files.each( function() {
                    var file_node = $( this )[0];

                    if ( file_node.files.length ) {
                        formData.append( $( this ).attr( 'name' ), file_node.files );
                    }
                } );
            }

            $.ajax( {
                url: Realtek.settings.ajaxurl,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function( response ) {
                    response = response || {};

                    $( document ).trigger( 'es-ajax-form-submit-success', {
                        response: response,
                        form: $form
                    } );

                    if ( response.response_view === 'popup' ) {
                        if ( response.message ) {
                            $.magnificPopup.open( {
                                closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                                mainClass: 'es-magnific',
                                items: { src: response.message },
                                type: 'inline'
                            } );
                        }
                    }

                    if ( response.status === 'success' && $form.hasClass( 'js-es-form-enable-on-change' ) ) {
                        $form.find( '.js-es-confirm-field' ).addClass( 'es-hidden' ).find( '[type=password]' ).val( '' );
                        $form.data( 'hash', $form.serialize() );
                        $form.data( 'changed', 0 );
                        $form.trigger( 'input' );
                    }

                    if ( response.redirect_timeout && response.redirect_url ) {
                        setTimeout( function() {
                            window.location.href = response.redirect_url;
                        }, +response.redirect_timeout );
                    }
                },
            } ).always( function() {
                if ( ! $form.hasClass( 'js-es-form-enable-on-change' ) ) {
                    $submit_btn.removeProp( 'disabled' ).removeAttr( 'disabled' );
                }
            } );

            return false;
        } );

        var $singleDescription = $( '.js-es-full-description-link' );

        // Initialize single description show|hide button.
        if ( $singleDescription.length ) {
            $singleDescription.each( function() {
                var $wrapper = $( this ).closest( '.es-entity-field' );
                var height = $wrapper.find( '.es-entity-field__value' ).height();
                if ( height > 90 ) {
                    $wrapper.addClass( 'es-entity-field--post_content--collapsed' );
                    $wrapper.find( '.js-es-full-description-link' ).removeClass( 'es-hidden' );
                }
            } );
        }

        $( '.js-es-auth__login-form .es-field__input' ).keyup( function() {
            var $wrapper = $( this ).closest( '.js-es-auth__login-form' );
            var $login = $wrapper.find( '[name="es_user_login"]' );
            var $pwd = $wrapper.find( '[name="es_user_password"]' );

            if ( $login.val().length && $pwd.val().length ) {
                $wrapper.find( '.js-es-btn--login' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
            } else {
                $wrapper.find( '.js-es-btn--login' ).prop( 'disabled', 'disabled' );
            }
        } ).trigger( 'change' );

        setTimeout( function() {
            $( '.js-es-auth__login-form .es-field__input' ).trigger( 'keyup' );
        }, 800 );

        $( document ).on( 'click', '.js-es-select-text-click', function() {
            this.setSelectionRange( 0, this.value.length );
        } );

        $( document ).ajaxComplete( function() {
            initSelect2();
        } );

        $( '.js-es-scroll-to' ).click( function() {
            var scroll_offset = $( $( this ).attr( 'href' ) ).offset().top - 80;
            $([document.documentElement, document.body]).animate( {
                scrollTop: scroll_offset || 0
            }, 1000 );

            return false;
        } );

        $( '.js-es-mobile-gallery' ).on( 'init reInit afterChange', function( event, slick, currentSlide ) {
            var i = (currentSlide ? currentSlide : 0) + 1;
            $( '.js-es-mobile-gallery__pager' ).text( i + '/' + slick.slideCount );
        } ).slick( {
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            arrows: true,
            rtl: Realtek.settings.is_rtl,
            adaptiveHeight: true,
            prevArrow: '<span class="es-icon es-icon_chevron-left slick-arrow slick-prev"></span>',
            nextArrow: '<span class="es-icon es-icon_chevron-right slick-arrow slick-next"></span>',
        } );

        $( document ).on( 'change', '.js-es-search-field[data-address-components]', function() {
            var $field = $( this );
            var $wrap = $( this ).closest( '.js-es-search' );
            var dep_fields = $field.data( 'dependency-fields' );
            var $dep_field;

            if ( dep_fields ) {
                dep_fields.forEach( function( i ) {
                    $dep_field = $wrap.find( '.js-es-search-field--' + i );
                    esLoadLocation( $dep_field, $field.val() );
                } );
            }
        } );

        $( document ).on( 'click', '.js-es-search-more', function() {
            searchMoreFieldsInit( $( this ).closest( '.js-es-search--advanced' ) );

            return false;
        } );

        $( document ).on( 'click', '.js-es-search-nav__reset', function( e ) {
            e.stopPropagation();
            e.preventDefault();
            $( this ).closest( '.js-es-search-nav__item' ).find( 'input,select' ).each( function() {
                var $field = $( this );
                var type = $( this ).prop( 'type' );
                if ( type === 'radio' || type === 'checkbox' ) {
                    $field.removeProp( 'checked' ).removeAttr( 'checked' );
                    var $any_field = $( this ).closest( '.js-search-field-container' ).find( 'input[value=""]' );

                    if ( $any_field.length ) {
                        $any_field.prop( 'checked', 'checked' ).trigger( 'change' );
                    } else {
                        $field.trigger( 'change' );
                    }
                } else {
                    if ( $( this ).hasClass( 'select2-hidden-accessible' ) ) {
                        $( this ).val([]).trigger( 'change' );
                    } else {
                        $( this ).val('').trigger( 'change' );
                    }
                }
            } );
        } );

        $( document ).on( 'click', '.js-es-remove-saved-search', function() {
            var $btn = $( this );
            var $wrapper = $btn.closest( '#saved-searches' );
            var $items_wrapper = $btn.closest( '.es-saved-searches' );
            var $item_wrapper = $btn.closest( '.js-es-saved-search' );

            $( this ).addClass( 'es-btn--preload' );

            $.post( Realtek.settings.ajaxurl, {
                action: 'es_remove_saved_search',
                hash: $( this ).data('hash'),
                nonce: Realtek.nonce.saved_search
            }, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    if ( $item_wrapper.length ) {
                        $item_wrapper.fadeOut( 400, function() {
                            $item_wrapper.remove();

                            if ( ! $items_wrapper.find( '.js-es-saved-search' ).length ) {
                                $wrapper.find( '.js-es-no-posts' ).removeClass( 'es-hidden' );
                                $items_wrapper.remove();
                            }
                        } );
                    }
                } else {
                    alert( response.message );
                }
            }, 'json' ).fail( function() {
                alert( Realtek.tr.unknown_error );
            } ).always( function() {
                $btn.removeClass( 'es-btn--preload' );
            } );
            return false;
        } );

        $( document ).on( 'change', '.js-es-search--main input, .js-es-search--main select, .js-es-search--simple input, .js-es-search--simple select', function() {
            initSearchDropdownLabels( $( this ).closest( '.js-es-search' ) );
        } );

        $( '.js-es-search--main, .js-es-search--simple' ).each( function() {
            initSearchDropdownLabels( $( this ) );
        } );

        $( document ).on( 'change', '.js-es-search .js-es-search-field', function() {
            var field_name = $( this ).data('base-name');
            var $wrapper = $( this ).closest( '.js-es-search' );
            var value = $( this ).is( ':checked' ) ? $( this ).val() : false;
            value = $( this ).prop( 'tagName' ).toLowerCase() === 'select' ? $( this ).val() : value;

            if ( ( field_name === 'bedrooms' || field_name === 'bathrooms' ) && value ) {
                if ( $( this ).prop( 'tagName' ).toLowerCase() === 'select' ) {
                    $wrapper.find( 'input[name="from_' + field_name + '"]:checked' ).removeProp('checked').removeAttr( 'checked' ).trigger( 'change' );
                } else {
                    $wrapper.find( 'select[name="min_' + field_name + '"], select[name="max_' + field_name + '"]' ).val('').trigger( 'change' );
                }
            }
        } ) ;

        $( document ).on( 'change', '.js-es-search textarea, .js-es-search input, .js-es-search select', function() {
            var $btn = $( this ).closest( '.js-es-search' ).find( '.js-es-save-search' );
            $btn.removeProp( 'disabled' ).removeAttr( 'disabled' ).html( $btn.data( 'label' ) );
        } );

        $( document ).on( 'click', '.js-es-save-search', function() {
            var $btn = $( this );
            var data = $btn.closest( 'form' ).serialize();
            data += '&action=es_save_search&nonce=' + $btn.data( 'nonce' );

            $btn.prop( 'disabled', 'disabled' );

            $.post( Realtek.settings.ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    $btn.html( response.message );
                }
            }, 'json' );

            return false;
        } );

        $( document ).on( 'change', '.js-es-password-field', function() {
            var val = $( this ).val();
            var email_val = $( this ).data( 'email' ) ?
                $( this ).data( 'email' ) : $( this ).closest( 'form' ).find( '.es-field__es_user_email input' ).val();
            var $list = $( this ).closest( '.es-field, .js-es-field' ).find( '.es-field__validate-list' );
            var validate1 = false;
            var validate2 = false;
            var validate3 = false;

            if ( val && val.length ) {
                if ( email_val.length && email_val !== val ) {
                    $list.find( '.es-validate-item__contain' ).addClass( 'es-validate-item--active' );
                    validate1 = true;
                } else {
                    $list.find( '.es-validate-item__contain' ).removeClass( 'es-validate-item--active' );
                    validate1 = false;
                }

                if ( val.length >= 8 ) {
                    validate2 = true;
                    $list.find( '.es-validate-item__length' ).addClass( 'es-validate-item--active' );
                } else {
                    validate2 = false;
                    $list.find( '.es-validate-item__length' ).removeClass( 'es-validate-item--active' );
                }

                var regExp = /[a-zA-Z0-9]/g;

                if ( regExp.test( val ) ) {
                    validate3 = true;
                    $list.find( '.es-validate-item__char' ).addClass( 'es-validate-item--active' );
                } else {
                    validate3 = false;
                    $list.find( '.es-validate-item__char' ).removeClass( 'es-validate-item--active' );
                }
            }

            if ( validate1 && validate2 && validate3 ) {
                $( this ).closest( 'form' ).find( '[type=submit]' ).removeProp( 'disabled' ).removeAttr( 'disabled' );
            } else {
                $( this ).closest( 'form' ).find( '[type=submit]' ).prop( 'disabled', 'disabled' );
            }
        } );

        $( '.js-es-password-field' ).trigger( 'change' );

        $( document ).on( 'click', '.js-es-auth-item__switcher', function() {
            var $wrapper = $( this ).closest( '.js-es-auth' );
            var auth_item = $( this ).data( 'auth-item' );
            $wrapper.find( '.es-auth__item' ).addClass( 'es-auth__item--hidden' );
            $wrapper.find( '.es-auth__' + auth_item ).removeClass( 'es-auth__item--hidden' );

            window.RealtekFramework.initFields( $wrapper );

            resizeCaptcha( $( '.es-recaptcha-wrapper .js-g-recaptcha' ) );
            return false;
        } );

        $( document ).on( 'click', '.js-return-false', function() {
            return false;
        } );

        $( document ).mouseup(function(e){
            var $container = $( ".js-es-autocomplete" );

            // If the target of the click isn't the container
            if( ! $container.is( e.target ) && $container.has( e.target ).length === 0 ){
                $container.remove();
            }
        });

        if ( typeof Realtek.settings !== 'undefined' && Realtek.settings.address_autocomplete_enabled ) {
            $( document ).on( 'click', '.js-autocomplete-item', function() {
                var $field = $( this ).closest( '.es-field, .js-es-field, .js-search-field-container' ).find( 'input' );
                $field.val( $( this ).data( 'query' ) ).trigger( 'focusout' );
                $( this ).closest( '.js-es-autocomplete' ).remove();

                return false;
            } );

            $( document ).on( 'keyup', '.js-es-address', function() {
                var value = $( this ).val();
                var $field = $( this );
                $field.focus();

                $field.closest( 'div' ).find( '.js-es-autocomplete' ).remove();

                if ( typeof autocomplemeXHR !== 'undefined' ) {
                    autocomplemeXHR.abort();
                }

                if ( value.length >= 2 ) {
                    autocomplemeXHR = $.get( Realtek.settings.ajaxurl, {
                        q: value,
                        action: 'es_search_address_components',
                    }, function( response ) {
                        response = response || {};
                        if ( response.status === 'success' ) {
                            $( response.content ).insertAfter( $field );
                        }
                    }, 'json' ).fail( function() {

                    } );
                }
            } );
        }

        $( document ).on( 'change', '.js-es-submit-on-form-change input, .js-es-submit-on-form-change textarea, .js-es-submit-on-form-change select', function() {
            var $form = $( this ).closest( 'form' );
            $form.submit();
        } );

        $( document ).on( 'change', '.js-es-submit-on-change', function() {
            $( this ).closest( 'form' ).submit();
        } );

        if ( typeof ClipboardJS !== 'undefined' ) {
            new ClipboardJS( '.js-es-property-copy', {
                container: $( '#es-share-popup' )[0]
            } );

            $( document ).on( 'click', '.js-es-copy', function() {
                var $link = $( this );

                if ( ! $link.hasClass( 'es-copy--active' ) ) {
                    var copied_label = $link.data( 'copied' ) || 'Copied';
                    var temp_label = $link.html();
                    $link.addClass( 'es-copy--active' );

                    if ( copied_label ) {
                        $link.html( copied_label );
                        setTimeout( function() {
                            $link.html( temp_label );
                            $link.removeClass( 'es-copy--active' );
                        } , 4000 );
                    }
                }

                return false;
            } );
        }

        $( document ).on( 'click', '.es-btn--active.js-es-wishlist--confirm, .es-wishlist-link--active.js-es-wishlist--confirm', function() {
            var $btn = $( this );
            var tr = Realtek.tr;
            var entity = $btn.data( 'entity' );
            var message = tr['remove_saved_' + entity];

            var markup = "<div class='es-magnific-popup es-ajax-form-popup'>" +
                "<h4>" + message + "</h4>" +
                "<a href='#' class='es-btn es-btn--default js-es-close-popup'>" + tr.cancel + "</a>" +
                "<a href='#' class='es-btn es-btn--secondary js-es-close-popup js-es-delete-wishlist-item'>" + tr.remove + "</a>" +
                "</div>";

            $.magnificPopup.open( {
                closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                mainClass: 'es-magnific',
                items: { src: markup },
                type: 'inline'
            } );

            $( document ).on( 'click', '.js-es-delete-wishlist-item', function() {
                $btn.removeClass( 'js-es-wishlist--confirm' ).trigger( 'click' );
                return false;
            } );

            e.preventDefault();
        } );

        $( document ).on( 'click', '[data-confirm-message]', function(e) {
            var message = $( this ).data( 'confirm-message' );
            var title = $( this ).data( 'confirm-title' );
            var button_label = $( this ).data( 'confirm-button' ) || Realtek.tr.got_it;
            var cancel_button_label = $( this ).data( 'confirm-cancel-button' ) || Realtek.tr.cancel;
            var icon = $( this ).data( 'confirm-button-icon' ) || 'es-icon es-icon_circle-x';

            var markup = "<div class='es-magnific-popup es-magnific-popup--confirm'>" +
                "<h4>" + title + "</h4>" +
                "<p>" + message + "</p>" +
                "<div class='es-magnific-buttons'>" +
                "<a href='#' class='es-btn es-btn--default js-es-close-popup'>" + cancel_button_label + "</a>" +
                "<a href='" + $( this ).attr( 'href' ) + "' class='es-btn es-btn--secondary'><span class='" + icon + "'></span>" + button_label + "</a>" +
                "</div>";

            $.magnificPopup.open( {
                closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                mainClass: 'es-magnific',
                items: { src: markup },
                type: 'inline'
            } );

            e.preventDefault();
        } );

        $( document ).on( 'click', '.js-es-compare-del-all', function() {
            $( '.js-es-compare-wrapper' ).addClass( 'es-hidden' );
            $( '.js-es-compare__listings-slider .slick-slide:not(.slick-cloned) .js-es-compare-del-entity' ).trigger( 'click' );
            $( '.js-es-compare-empty' ).removeClass( 'es-hidden' );
            return false;
        } );

        $( document ).on( 'click', '.js-es-compare-fields-switcher', function() {
            var $link = $( this );
            var $wrapper = $link.closest( '.js-es-compare-wrapper' );

            if ( ! $link.hasClass( 'js-compare-filtered' ) ) {
                $wrapper.find( '.js-es-compare__listing-value' ).each( function() {
                    var field = $( this ).data( 'field' );
                    var equal = true;
                    var prev_value = null;

                    $wrapper.find( '.js-es-compare__listing-value[data-field="' + field + '"]' ).each( function() {
                        if ( prev_value === null ) {
                            prev_value = $( this ).html();
                        } else if ( prev_value !== $( this ).html() ) {
                            equal = false;
                        }
                    } );

                    if ( equal ) {
                        $wrapper.find( '.js-es-compare__listing-value[data-field="' + field + '"], .js-es-compare__field[data-field="' + field + '"]' ).addClass( 'es-hidden' );
                    }
                } );
            } else {
                $wrapper.find( '.js-es-compare__listing-value, .js-es-compare__field' ).removeClass( 'es-hidden' );
            }

            $link.toggleClass( 'js-compare-filtered' );

            return false;
        } );

        $( document ).on( 'click', '.js-es-compare-del-entity', function() {
            var $link = $( this );
            var entity_id = $link.data('entity-id');
            var $wrapper = $link.closest( '.js-es-compare-wrapper' );
            $link.addClass( 'es-preload-link--preload' );

            var data = {
                post_id: entity_id,
                action: 'es_compare_delete_action',
            };

            $.post( Realtek.settings.ajaxurl, data, function ( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    $wrapper.find( '.js-es-listing-' + entity_id ).remove();
                    compareInstances.forEach( function( compareInstance ) {
                        compareInstance.init();
                    } );
                } else if ( response.status === 'error' ) {
                    displayErrorPopup( response.message );
                } else {
                    displayErrorPopup( Realtek.tr.unknown_error );
                }
            }, 'json' ).always( function() {
                $link.removeClass( 'es-preload-link--preload' );
                if ( ! $wrapper.find( '.js-es-compare-listing' ).length ) {
                    $( '.js-es-compare-wrapper' ).addClass( 'es-hidden' );
                    $( '.js-es-compare-empty' ).removeClass( 'es-hidden' );
                }
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-compare', function() {
            var $el = $( this );
            var data = {
                post_id: $el.data( 'id' ),
                action: 'es_compare_action',
            };

            if ( $el.hasClass( 'es-btn' ) ) {
                $el.addClass( 'es-btn--preload' );
            } else {
                $el.addClass( 'es-wishlist-link--preload' );
            }

            $.post( Realtek.settings.ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    if ( $el.hasClass( 'es-btn' ) ) {
                        $el.toggleClass( 'es-btn--active' );
                    } else {
                        $el.toggleClass( 'es-compare-link--active' );
                    }

                    var $tooltip = $el.parent().find( '.js-es-tooltip' );

                    $tooltip.removeClass( 'es-hidden' );

                    setTimeout( function() {
                        $tooltip.addClass( 'es-hidden' );
                    }, 5000 );

                    $el.removeClass( 'js-es-compare' )
                        .attr( 'href', response.compare_page_url )
                        .prop( 'href', response.compare_page_url );
                } else if ( response.status === 'error' ) {
                    displayErrorPopup( response.message );
                } else {
                    displayErrorPopup( Realtek.tr.unknown_error );
                }
            }, 'json' ).always( function() {
                $el.removeClass( 'es-btn--preload' ).removeClass( 'es-wishlist-link--preload' );
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-wishlist:not(.js-es-wishlist--confirm)', function() {
            var $el = $( this );
            var data = {
                post_id: $el.data( 'id' ),
                action: 'es_wishlist_action',
                entity: $el.data( 'entity' ),
            };

            var $item_wrapper = $el.closest( '.es-post-entity' );
            var $items_wrapper = $el.closest( '.js-es-entities__wrap_inner' );
            var $wrapper = $el.closest( '#saved-homes, #saved-agents, #saved-agencies' );

            if ( $el.hasClass( 'es-btn' ) ) {
                $el.addClass( 'es-btn--preload' );
            } else {
                $el.addClass( 'es-wishlist-link--preload' );
            }

            $.post( Realtek.settings.ajaxurl, data, function( response ) {
                response = response || {};

                if ( response.status === 'success' ) {
                    if ( $el.hasClass( 'es-btn' ) ) {
                        $el.toggleClass( 'es-btn--active' );
                    } else {
                        $el.toggleClass( 'es-wishlist-link--active' );
                    }
                }

                if ( $wrapper.length ) {
                    if ( ! $el.hasClass( 'es-wishlist-link--active' ) && $item_wrapper.length ) {
                        $item_wrapper.fadeOut( 400, function() {
                            $item_wrapper.remove();

                            if ( ! $items_wrapper.find( '.es-post-entity' ).length ) {
                                $wrapper.find( '.js-es-no-posts' ).removeClass( 'es-hidden' );
                                $items_wrapper.remove();
                            }
                        } );
                    }
                }

            }, 'json' ).always( function() {
                $el.removeClass( 'es-btn--preload' ).removeClass( 'es-wishlist-link--preload' );
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-fields-list__copy', function( e ) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        } );

        resizeCaptcha( $( '.es-recaptcha-wrapper .js-g-recaptcha' ) );

        $( window ).on('resize', function() {
            resizeCaptcha( $( '.es-recaptcha-wrapper .js-g-recaptcha' ) );
        } );

        $( document ).on( 'click', '.js-es-popup-link', function() {
            $.magnificPopup.close();
            var $link = $( this );

            var id = $( this ).data( 'popup-id' ) || $( this ).attr( 'href' );

            $.magnificPopup.open( {
                items: { src: id },
                type:'inline',
                midClick: true,
                mainClass: 'es-magnific',
                closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                callbacks: {
                    beforeOpen: function () {
                        $( id ).trigger( 'popup_before_open', {
                            popup_id: id,
                            link: $link,
                        } );
                        $.magnificPopup.close();
                    },
                }
            } );

            return false;
        } );

        if ( $( '.js-es-single' ).length ) {
            var magnific_popup = {
                delegate: 'a.js-es-image',
                type: 'image',
                infinite: false,
                tLoading: 'Loading image #%curr%...',
                mainClass: 'es-property-magnific',
                closeMarkup: '<button class="es-btn es-btn--default es-btn--transparent mfp-close">%title%</button>',
                tClose: '<span class="es-mfg-close-ico" data-trigger-click=".mfp-close">&#x2715</span> ' + Realtek.tr.close,
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    preload: [0, 5],
                    tCounter: '%curr% / %total%',
                    arrowMarkup: '<span class="es-mfp-arrow es-mfp-arrow-%dir% es-btn es-btn--default es-btn--icon">%title%</span>',

                    tPrev: '<span class="es-icon es-icon_chevron-left mfp-prevent-close"></span>',
                    tNext: '<span class="es-icon es-icon_chevron-right mfp-prevent-close"></span>'
                },
                image: {
                    titleSrc: function( item ) {
                        return item.el.attr( 'title' );
                    },
                    markup: '<div class="mfp-top-bar">' +
                        '<div class="mfp-top-bar__inner">' +
                        '<div class="mfp-close"></div>' +
                        '<div class="mfp-counter"></div>' +
                        '<div class="mfp-control">' + Realtek.single.control + '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="mfp-figure">' +
                        '<div class="mfp-img"></div>' +
                        '<div class="mfp-title"></div>' +
                        '</div>' +
                        '</div>'
                }
            };

            var lightbox_disabled = +Realtek.settings.is_lightbox_disabled;

            if ( ! lightbox_disabled ) {
                $('.js-es-images, .js-es-property-gallery').magnificPopup( magnific_popup );
            }

            magnific_popup.delegate = '.slick-slide:not(.slick-cloned) a.js-es-image';
            $('.js-es-slider__image').magnificPopup(magnific_popup);
        }

        $( '.js-es-slider' ).each( function() {
            var $wrapper = $( this );

            var $slider = $wrapper.find( '.js-es-slider__image' );
            var $pager = $wrapper.find( '.js-es-slider__pager' );
            var $page_info = $wrapper.find( '.es-slider__page-info .es-slider__page-info-text' );

            $slider.on( 'init reInit afterChange', function( event, slick, currentSlide, nextSlide ) {
                var i = (currentSlide ? currentSlide : 0) + 1;
                $page_info.text(i + '/' + slick.slideCount);
                $slider.removeClass( 'slick-hidden' );
            } ).slick( {
                arrows: true,
                prevArrow: '<span class="es-icon es-icon_chevron-left slick-arrow slick-prev"></span>',
                nextArrow: '<span class="es-icon es-icon_chevron-right slick-arrow slick-next"></span>',
                asNavFor: $pager,
                adaptiveHeight: true,
            } );

            $pager.slick( {
                arrows: false,
                dots: false,
                infinite: true,
                asNavFor: $slider,
                slidesToScroll: 1,
                slidesToShow: 5,
                focusOnSelect: true,
                slide: 'div',
                rows: 0,
                responsive: [
                    {
                        breakpoint: 1130,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 780,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 320,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            } );
        } );

        $( document ).on( 'click', '.js-es-search-nav > li > a', function() {
            $( '.js-es-search-nav > li' ).not( $( this ).closest( 'li' ) ).removeClass( 'active' );
            $( this ).closest( 'li' ).toggleClass( 'active' );
            return false;
        } );

        $( document ).click( function( event ) {
            var $target = $( event.target );
            if ( ! $target.closest( '.js-es-search-nav' ).length ) {
                $( '.js-es-search-nav > li' ).removeClass( 'active' );
            }

            $( '.js-es-tooltip' ).addClass( 'es-hidden' );
        } );

        $( '.js-es-search__collapse-link' ).click( function() {
            $( this ).closest( '.js-es-search' ).find( '.es-search-nav' ).toggleClass( 'es-search-nav--show' );

            return false;
        } );

        $( '.js-es-search-field--es_type, .js-es-search-field--es_category' ).change( function() {
            var value_type, value_category;
            var $field = $( this );
            var $search_container = $field.closest( '.js-es-search' );

            if ( ! $search_container.data( 'same-price' ) ) {
                var $type = $search_container.find( '.js-es-search-field--es_type' );
                var $category = $search_container.find( '.js-es-search-field--es_category' );
                var prices_list = $search_container.find( '.js-es-search-field--price' ).data( 'prices-list' );

                var $price_min = $search_container.find( '.js-es-search-field--price-min' );
                var $price_max = $search_container.find( '.js-es-search-field--price-max' );

                if ( $type.length && $type.prop( 'tagName' ).toLowerCase() === 'select' ) {
                    value_type = $type.val();
                } else {
                    value_type = $search_container.find( '.js-es-search-field--es_type:checked' ).val();
                }

                if ( $category.length && $category.prop( 'tagName' ).toLowerCase() === 'select' ) {
                    value_category = $category.val();
                } else {
                    value_category = $search_container.find( '.js-es-search-field--es_category:checked' ).val();
                }

                value_category = value_category ? value_category : '';
                value_type = value_type ? value_type : '';

                if ( prices_list && prices_list.length ) {
                    for ( var i in prices_list ) {
                        if ( prices_list[i].category === value_category && prices_list[i].type === value_type ) {
                            var min_list = prices_list[i].min_prices_list;
                            var max_list = prices_list[i].max_prices_list;

                            $price_min.html('<option></option>');
                            $price_max.html('<option></option>');

                            for ( var j in min_list ) {
                                $price_min.append( new Option( min_list[j], j ) );
                            }

                            for ( var k in max_list ) {
                                $price_max.append( new Option( max_list[k], k ) );
                            }

                            break;
                        }
                    }
                }
            }
        } );

        $( document ).on( 'submit', '.js-es-request-form', function() {
            var $submit_btn = $( this ).find( '.js-es-request-form-submit' );
            $submit_btn.prop( 'disabled', 'disabled' );
            var $response_container = $( this ).closest( '.es-request-form' ).find( '.js-es-request-form__response' );
            $response_container.html( false );
            var $form = $( this );

            $.post( Realtek.settings.ajaxurl, $( this ).serialize(), function( response ) {
                if ( response.message ) {
                    $.magnificPopup.open( {
                        closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                        mainClass: 'es-magnific',
                        items: { src: response.message },
                        type: 'inline'
                    } );
                }

                if ( response.status === 'success' ) {
                    $submit_btn.closest( 'form' )[0].reset();
                    initRequestFormPhoneCode();
                }
            }, 'json' ).always( function() {
                $submit_btn.removeProp( 'disabled' ).removeAttr( 'disabled' );

                if ( typeof grecaptcha !== 'undefined' && $form.find( '.js-g-recaptcha' ).length ) {
                    if ( Realtek.settings.recaptcha_version === 'v2' ) {
                        grecaptcha.reset( $form.find( '.js-g-recaptcha' ).data( 'recaptcha-id' ) );
                    }
                }
            } );

            return false;
        } );

        $( document ).on( 'click', '.js-es-close-popup', function() {
            $.magnificPopup.close();
            return false;
        } );

        $( '.js-es-toggle-class' ).click( function() {
            $( $( this ).data( 'container' ) ).toggleClass( $( this ).data( 'class' ) );
            return false;
        } );

        try {
            if ( window.location.hash ) {
                if ( $( window.location.hash ).length ) {
                    if ( $( window.location.hash ).hasClass( 'es-magnific-popup' ) ) {
                        $.magnificPopup.open({
                            items: {
                                src: window.location.hash
                            },
                            type:'inline',
                            midClick: true,
                            mainClass: 'es-magnific',
                            closeMarkup: '<span class="es-icon es-icon_close mfp-close"></span>',
                            callbacks: {
                                beforeOpen: function () {
                                    $.magnificPopup.close();
                                }
                            }
                        });
                    }
                }
            }
        } catch (e) {}

        $( '.js-es-table-check-all' ).on( 'change', function() {
            var $table = $( this ).closest( '.js-es-table' );
            var $checkboxes = $table.find( 'tbody [type=checkbox]' );

            if ( $( this ).is( ':checked' ) ) {
                $checkboxes.prop( 'checked', 'checked' ).attr( 'checked', 'checked' ).trigger( 'change' );
            } else {
                $checkboxes.removeProp( 'checked' ).removeAttr( 'checked', 'checked' ).trigger( 'change' );
            }

            esToggleBulkActions( $table );
        } );

        $( '.js-es-table tbody .es-column--_manage-checkbox' ).on( 'change', 'input[type=checkbox]', function() {
            var $table = $( this ).closest( '.js-es-table' );
            var $tr = $( this ).closest( 'tr' );

            if ( $( this ).is( ':checked' ) ) {
                $tr.addClass( 'active' );
            } else {
                $tr.removeClass( 'active' );
            }

            esToggleBulkActions( $table );
        } );

        $( document ).on( 'submit', '.js-es-saved-search-form', function(e) {
            var $form = $( this );
            e.preventDefault();

            $.post( Realtek.settings.ajaxurl, $form.serialize(), function( response ) {
                $form.find( '.js-es-saved-search__notify' ).html( response.message );
                setTimeout( function() {
                    $form.find( '.js-es-saved-search__notify' ).html('');
                }, 3000 );
            }, 'json' );
        } );
    } );

    window.RealtekResponsinator = Responsinator;
    window.RealtekFormatter = Formatter;
    window.initPropertiesSlideshow = initPropertiesSlideshow;
} )( jQuery );
