<?php

include 'phpqrcode.php';
//$text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false
QRcode::png( $_GET['link'], false, QR_ECLEVEL_L, 2.4, 0 );
