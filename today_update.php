<?php
/**
 * bot.php向け、影情報更新のローカルコマンドを発行する。
 *
 * @author     たんらる
 * @since      2012/12/27
 */


require_once("mabi/get_today.php");

$str = today_mission_string();

print "topic:" . $str;

?>

