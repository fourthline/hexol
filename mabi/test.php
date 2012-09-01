<?php



require_once("get_today.php");


$str = today_mission_string();




print $str . "\n";

print delete_flc_section( $str ) . "\n";


?>
