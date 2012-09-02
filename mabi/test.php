<?php



require_once("get_today.php");


$str = today_mission_string();

print $str . "\n";
print strlen( $str ) . "\n";

$str = delete_flc_section( $str );

print $str . "\n";
print strlen( $str ) . "\n";

?>
