<?php
/**
 * join bot. 接続してきたユーザに、オペレータ権限付与を行う。
 *
 * @author     たんらる
 * @since      2012/05/20
 */

require_once("config.php");
require_once("Net/SmartIRC.php");


function sjis( $str ) {
	return mb_convert_encoding($str, "SJIS", "UTF-8");
}
function jis( $str ) {
	return mb_convert_encoding($str, "JIS", "UTF-8");
}


class mybot
{
	private $irc;
	private $channel1;
	private $channel2;
	
	private $update_enable = true;

	function __construct( $ch1, $ch2 ) {
		$this->irc = &new Net_SmartIRC();
		$this->irc->setChannelSyncing( true );
		$this->channel1 = jis( $ch1 );
		$this->channel2 = jis( $ch2 );
	}
	
	function connect() {
		$this->irc->setDebug(8127);

		$this->irc->connect( IRC_SERVER, IRC_PORT );
		$this->irc->login( IRC_NICKNAME, sjis( IRC_REALNAME ), 0, IRC_USERNAME);
		
		// regist remote commands
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, PREFIX.'>topic:.+', $this, 'e_topic');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, PREFIX.'>update', $this, 'e_update');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, PREFIX.'>quit', $this, 'quit');
		
		// auto naruto
		$this->irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '.*', $this, 'e_naruto');
		
		// local timer
		$this->irc->registerTimehandler( BOT_COMMAND_TIMER, $this, 'exec_bot_command');
		$this->irc->registerTimehandler( CONNECTION_TIMER, $this, 'timer');
		
		$this->irc->join( $this->channel1);
		if ( IRC_JOIN_CH2 == true ) {
			$this->irc->join( $this->channel2);
		}
		
		$this->irc->listen();
		$this->irc->disconnect();
	}
	
	function e_update( &$irc, &$data ) {
		$pos = strpos( $data->message, "enable" );
		if ( $pos !== false ) {
			$this->update_enable = true;
		}
		
		$pos = strpos( $data->message, "disable" );
		if ( $pos !== false ) {
			$this->update_enable = false;
		}
		
		$this->update();
	}
	
	function update() {
		if ( $this->update_enable == true ) {
			$this->notice( "update enable." );
			
			// Todayミッション更新
			exec( UPDATE_COMMAND );
		} else {
			$this->notice( "update disable." );
		}
	}
	
	function e_topic( &$irc, &$data ) {
		$str = mb_convert_encoding( $data->message, 'UTF-8', 'auto' );
		$pattern = PREFIX.'>topic:';
		$pos = strpos( $str, $pattern ) + strlen( $pattern );
		$this->topic( substr( $str, $pos ) );
	}
	
	function topic( $string ) {
		$this->irc->setTopic( $this->channel1, sjis( $string ) );
	}
	
	function message( $string ) {
		$this->irc->message( SMARTIRC_TYPE_CHANNEL, $this->channel1, sjis( $string ) );
	}
	
	function notice( $string ) {
		$this->irc->message( SMARTIRC_TYPE_NOTICE, $this->channel1, sjis( $string ) );
	}
	
	function notice2( $string ) {
		$this->irc->message( SMARTIRC_TYPE_NOTICE, $this->channel2, sjis( $string ) );
	}

	function quit() {
		$this->irc->quit( QUIT_MESSAGE );
	}
	
	function e_naruto( &$irc, &$data ) {
		$this->naruto( $data );
	}
	
	function naruto( &$data ) {
		if ($data->nick == $this->irc->_nick)
			return;
		
		if ( user_allow( $data->nick ) ) {
			$this->irc->op($data->channel, $data->nick);
		}
	}
	
 	function timer() {
		$this->irc->getTopic( $this->channel1 );
	} 
	
	/**
	 * ローカルコマンドの実行
	 */
	function exec_bot_command() {
		parse_command($command, $param);

		switch ( $command ) {
		case "quit":
			$this->quit();
			break;
		case "update":
			$this->update();
			break;
		case "join":
			if ( $param !== null ) {
				$this->irc->join(array( jis($param) ));
			}
			break;
		case "part":
			if ( $param !== null ) {
				$this->irc->part(array( jis($param) ));
			}
			break;
		case "naruto":
			$data->channel = $this->channel1;
			$data->nick = $param;
			$this->naruto( $data );
			break;
		case "topic":
			$this->topic( $param );
			break;
		case "message":
			$this->message( $param );
			break;
		case "notice":
			$this->notice( $param );
			break;
		case "notice2":
			$this->notice2( $param );
			break;
		}
	}
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


file_put_contents( BOT_COMMAND_FILE, "" );       // clear command file

$bot = &new mybot( IRC_CHANNEL1, IRC_CHANNEL2 );
$bot->connect();

?>

