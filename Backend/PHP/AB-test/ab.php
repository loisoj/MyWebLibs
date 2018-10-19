<?php
$ABbtn = array(
'<div class="greenbtn">Купить</div>' ,
'<div class="redbtn">Купить</div>'
);
$MaxRandom = getrandmax();
$RandomValue = intval( rand( 0 , count( $ABbtn ) - 1 ) );
echo $ABbtn[ $RandomValue ] ;
?>
