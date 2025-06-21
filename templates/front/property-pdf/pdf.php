<html lang="<?php es_get_locale(); ?>">
<head>
    <title><?php es_the_title(); ?></title>
    <style>
        .es-price-container {
            display: block;
            height: 50px;
            line-height: 50px;
            color: #fff;
            background-color: <?php echo ests( 'main_color' ) ?>;
            font-size: 22px;
            letter-spacing: 0.05em;
            white-space: nowrap;
            text-align: left;
        }

        .property-excerpt {
            line-height: 1.7;
        }

        .sup-line {
            width: 35px;
            height: 3px;
            display: block;
            background-color: <?php echo ests( 'main_color' ) ?>;
        }

        #basic-fields {
            background: <?php echo ests( 'pdf_flyer_layout' ) == 'single_photo' ? '#BDBDBD' : ests( 'main_color' ); ?>;
            vertical-align: top;
            padding: 15px 20px;
            margin: 0;
            text-decoration: none;
            color: #fff;
        }

        #basic-fields a {
            color: #fff;
            text-decoration: none;
        }

        .basic-field {
            font-size: 13px;
            color: #FFFFFF;
        }

        #property-map {
            margin-top: 10px;
        }

        .site-url {
            font-weight: 300;
            font-size: 12px;
            line-height: 13px;
            color: #BDBDBD;
        }

        .entry-title {
            font-weight: bold;
            font-size: 19px;
            line-height: 1.1;
            color: #424242;
            text-transform: uppercase;
            margin: 0;
        }

        .es-table {
            font-size: 10px;
            border: 0;
            width: 100%;
            table-layout: initial;
        }

        .es-table tbody, .es-table td, .es-table tr, .es-table th, .es-table thead {
            font-weight: normal;
            border: 0;
            margin: 0;
            padding: 0;
        }

        .es-table tbody tr:nth-child(odd) {
            background: #fff;
        }

        .es-table tbody tr:nth-child(even) {
            background: rgba(236, 239, 241, 0.5);
        }

        .es-table thead tr {
            background: #263238;
        }

        .es-table thead th {
            padding: 0.625em;
            white-space: nowrap;
            color: #fff;
            font-size: 1.2em;
        }

        .es-table tbody {
            color: #263238;
        }

        .es-table tbody td {
            vertical-align: top;
            padding: 20px 7px;
            font-size: 1.2em;
            line-height: 1.67;
        }

        #footer {
            width: 100%;
            background: #C4C4C4;
        }

        .es-pdf-logo {
            width: auto;
            height: 120px;
            padding: 10px;
        }

        #footer .contact-label {
            font-size: 13px;
            color: #424242;
        }

        #footer .contact-value {
            font-weight: 300;
            font-size: 13px;
            color: #212121;
        }
    </style>
</head>

<body>
    <?php if ( $layout = ests( 'pdf_flyer_layout' ) ) :
        include es_locate_template( sprintf( 'front/property-pdf/partials/%s.php', $layout ) );
        include es_locate_template( 'front/property-pdf/partials/sections-list.php' );
    endif; ?>
</body>
</html>