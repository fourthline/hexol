<?php
/**
 * bot.php向け、影情報更新のローカルコマンドを発行する。
 *
 * @author     たんらる
 * @since      2012/12/27
 */


require_once("mabi/get_today.php");
$todayDate = date( "n/j" );

for ($i = 1; $i <= 5; $i++) {
  $str = today_mission_string();
  if ( strncmp( $todayDate, $str, strlen( $todayDate ) ) == 0 ) {
    print "topic:" . $str;
    break;
  }
  
  print "notice2:update_retry( ".$i." )";
  sleep( 30*60 ); // 30min
}


?>

