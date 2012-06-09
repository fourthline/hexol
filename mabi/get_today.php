<?php
/**
 * http://weather.erinn.biz/today.rss.php からToday影ミッションの情報を取得します。
 *
 * @author     たんらる
 * @since      2012/05/20
 */
 
function get_mission( $string ) {
  $start_index = strpos($string, "<title>") + 7;
  $end_index = strpos($string, "</title>");

  $mission = substr($string, $start_index, $end_index-$start_index);

  return $mission;
}

function get_date_string( $string ) {
  $date_start = strpos($string, "<dc:date>") + 9;
  $date_end = strpos($string, "</dc:date>");
  
  $date_obj = substr($string, $date_start, $date_end);
  $date = date_parse($date_obj);

  $date_string = $date['month'].'/'.$date['day'];

  return $date_string;
}

function replace_mission_string( $string) {
  $array = array(
    'タルティーン「' => '【たる】',
    'タラ「' => '【たら】',
    '時間無制限' => '無制限',
    '」' => '',
    
    'クラッグカウを退治' => 'クラッグカウ',
    'スリアブクィリンの岩石' => 'クィリン',
    'シャドウウォーリアを退治' => 'シャドウォーリア',
    'ポウォールコマンダーを退治 I' => 'ポコマ I',
    'ポウォールコマンダーを退治 II' => 'ポコマ II',
    'シャドウウィザードを退治' => 'シャドウィザ',
    'タルティーン制圧戦 I' => '制圧戦 I',
    'タルティーン制圧戦 II' => '制圧戦 II',
    'タルティーン防御戦' => '防御戦',
    '偵察兵救出' => '偵察兵',
    'ドレンの頼み' => 'ドレン',
    '遭遇戦' => '遭遇戦',
    '生贄' => '生贄',
    '挑発' => '挑発',
    
    '影差す都市' => '影差す',
    '背後の敵' => '背後',
    '彼らのやり方' => '彼らの',
    '残された闇' => '残やみ',
    'コリブ渓谷の守護' => 'コリブ渓谷',
    'ワインの香り' => 'ワイン',
    '遠征隊召集' => '遠征隊',
    '影の世界の硫黄クモ' => '硫黄クモ',
    'ポウォールの襲撃' => '襲撃',
    'また別の錬金術師たち' => 'また別',
    'パルホロンの幽霊' => '幽霊',
  );
  
  foreach($array as $key => $value) {
    $string = str_replace($key, $value, $string);
  }

  return $string;
}


function today_mission_string() {

  exec("wget http://weather.erinn.biz/today.rss.php");
  $rss = file_get_contents("today.rss.php");
  exec("rm today.rss.php");
  
  $contents = strstr($rss, "<item rdf:about=\"http://weather.erinn.biz/today.php\">");

  $mission = get_mission( $contents );

  $date = get_date_string( $contents );
  
  $string = $date . replace_mission_string( $mission );

  return $string;
}


?>