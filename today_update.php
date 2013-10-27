<?php
/**
 * bot.php向け、影情報更新のローカルコマンドを発行する。
 *
 * @author     たんらる
 * @since      2012/12/27
 */


require_once("mabi/get_today.php");
$todayDate = date( "n/j" );

openlog(__FILE__, LOG_PID, LOG_USER);

for ($i = 1; $i <= 20; $i++) {
  $str = today_mission_string();
  if ( strncmp( $todayDate, $str, strlen( $todayDate ) ) == 0 ) {
    print "topic:" . $str;
    syslog(LOG_INFO, "update ok: ".$str);
    break;
  }
  
  if ($i == 1) {
    print "topic:" . $todayDate . " （不明）";
  }
  
  syslog(LOG_INFO, "retry [".$i."]");
  sleep( 30*60 ); // 30min
}

closelog();
?>

