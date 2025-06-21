( function( $ ) {
    'use strict';

    $( function() {
        $( document ).on( 'change', '.js-es-preferred-radio', function() {
            $( '.js-es-preferred-radio:checked' ).not( this ).removeProp( 'checked' ).removeAttr( 'checked' );
        } );

        $( document ).on( 'click', '.js-es-entity-form.post-type-agent #publish', function(e) {
            var $pwd_field = $( '.js-es-user-pwd' );
            var $pwd_wrap = $pwd_field.closest( '.es-field' );
            $pwd_field.trigger( 'keyup' );

            if ( ! $( '#es-user-pwd' ).hasClass( 'es-hidden' ) ) {
                // console.log($pwd_wrap.find( '.es-validate-item' ).length, $pwd_wrap.find( '.es-validate-item--active' ).length);
                if ( $pwd_wrap.find( '.es-validate-item' ).length === $pwd_wrap.find( '.es-validate-item--active' ).length ) {
                    return true;
                } else {
                    $pwd_field[0].setCustomValidity( EstatikAgentMetabox.tr.invalid_password_format );
                    $pwd_field[0].reportValidity();
                    e.preventDefault();
                    return false;
                }
            }
        } );

        // Validate user password field.
        $( document ).on( 'keyup change', '.js-es-user-pwd, .js-es-user-email, #title[name=post_title]', function() {
            var $form = $( this ).closest( '#post' );
            var $pwd_field = $form.find( '.js-es-user-pwd' );
            var val = $pwd_field.val();
            var $pwd_wrap = $pwd_field.closest( '.es-field' );
            var $email_field = $form.find( '.js-es-user-email' );

            if ( $form.find( '.js-es-has-user[type=checkbox]' ).is( ':checked' ) && ! $pwd_field.hasClass( 'js-es-user-exists' ) ) {
                var name = $form.find( '[name=post_title]' );
                var regExp = /[a-zA-Z0-9]/g;

                $pwd_wrap.find( '.es-validate-item__contain' ).toggleClass( 'es-validate-item--active', name !== val && val !== $email_field.val() );
                $pwd_wrap.find( '.es-validate-item__length' ).toggleClass( 'es-validate-item--active', val.length >= 8 );
                $pwd_wrap.find( '.es-validate-item__char' ).toggleClass( 'es-validate-item--active', regExp.test( val ) );
            }
        } );

        $( document ).on( 'change', '.js-es-has-user[type=checkbox]', function() {
            var $pwd_field = $( this ).closest( 'form' ).find( '.js-es-user-pwd' );
            var $email_field = $( this ).closest( 'form' ).find( '.js-es-user-email' );
            var $label = $email_field.closest( '.es-field' ).find( '.es-field__label' );

            if ( $( this ).is( ':checked' ) ) {
                $email_field.prop( 'required', 'required' ).attr( 'required', 'required' );

                if ( ! $label.find( '.es-required' ).length ) {
                    $label.append( "<span class='es-required'> *</span>" );
                }

                if ( $pwd_field.hasClass( 'js-es-user-exists' ) ) {
                    $pwd_field
                        .removeProp( 'required' ).removeAttr( 'required' )
                        .removeProp( 'disabled' ).removeAttr( 'disabled' );
                } else {
                    $pwd_field.removeProp( 'disabled' ).removeAttr( 'disabled' );
                }
            } else {
                $email_field.removeProp( 'required' ).removeAttr( 'required' );
                $label.find( '.es-required' ).remove();

                $pwd_field
                    .prop( 'required', 'required' ).attr( 'required', 'required' )
                    .prop( 'disabled', 'disabled' ).attr( 'disabled', 'disabled' );
            }
        } );

        $( '.js-es-has-user[type=checkbox]' ).trigger( 'change' );
    } );
} )( jQuery );
