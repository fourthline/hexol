<?php
/**
 * join bot. 接続してきたユーザに、オペレータ権限付与を行う。補助機能で、影情報更新も。
 *
 * @author     たんらる
 * @since      2012/05/20
 */

require_once("config.php");
require_once("Net/SmartIRC.php");
require_once("mabi/get_today.php");


function sjis( $str ) {
	$result = mb_convert_encoding($str, "SJIS", "UTF-8");
	
	return $result;
}


class mybot
{
	function update(&$irc) {
		$str = $str = today_mission_string();
		
		print " [ ".date( DATE_COOKIE ). " ] " . "update topic: \"" . $str . "\"\n";
		$irc->setTopic( IRC_CHANNEL, sjis( $str ) );
	}
		
	function quit(&$irc) {
		$irc->quit( QUIT_MESSAGE );
	}
	
	function naruto(&$irc, &$data) {
		if ($data->nick == $irc->_nick)
			return;
		
		if ( user_allow( $data->nick ) ) {
			$irc->op($data->channel, $data->nick);
		}
	}
	
 	function timer(&$irc) {
		$irc->getTopic( IRC_CHANNEL );
	} 
	
	function exec_bot_command(&$irc) {
		parse_command($command, $param);

		switch ( $command ) {
		case "quit":
			$this->quit( $irc );
			break;
		case "update":
			$this->update( $irc );
			break;
		case "getList":
			$irc->getList();
			break;
		case "naruto":
			$data->channel = IRC_CHANNEL;
			$data->nick = $param;
			$this->naruto( $irc, $data );
		break;
		}
	}
}

function bot_main()
{
	$bot = &new mybot();
	$irc = &new Net_SmartIRC();
//	$irc->setDebug(SMARTIRC_DEBUG_ALL);
	$irc->setDebug(8127);
	
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, PREFIX.'>update', $bot, 'update');
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, PREFIX.'>quit', $bot, 'quit');
	$irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '.*', $bot, 'naruto');
	$irc->registerTimehandler( BOT_COMMAND_TIMER, $bot, 'exec_bot_command');
	$irc->registerTimehandler( CONNECTION_TIMER, $bot, 'timer');

	$irc->connect( IRC_SERVER, IRC_PORT );
	$irc->login( IRC_NICKNAME, sjis( IRC_REALNAME ), 0, IRC_USERNAME);
	$irc->join(array( sjis( IRC_CHANNEL )));

	$irc->listen();
	$irc->disconnect();
}

function parse_command(&$command, &$param)
{
	$command = file_get_contents( BOT_COMMAND_FILE );
	if ( command ) {
		$command = strtok( $command, "\r\n" );
		
		$pos = strpos( $command, ":" );
		if ( $pos ) {
			$param = substr( $command, $pos+1 );
			$command = substr( $command, 0, $pos );
		} else {
			$param = null;
		}
		
	}
	file_put_contents( BOT_COMMAND_FILE, "" );
}

function user_allow( $s ) {
  $user_list = split( "[ ,\t\r\n]", file_get_contents( USER_ALLOW_CONF ) );


  foreach( $user_list as $user ) {
    if ( $user && ($s === $user) ) {
      return TRUE;
    }
  }

  return FALSE;
}


file_put_contents( BOT_COMMAND_FILE, "" );
bot_main();

?>

