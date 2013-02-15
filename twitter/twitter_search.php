<?php
/** 
 * 1分おきに、ツイッターの検索結果から1ツイートをとってくる standalone式スクリプト
 * RTは除外。
 * option:
 *   -s<keyword> 検索キーワード (必須)
 *   -t<sec> ループ間隔
 *   -o<filename> 出力先のコマンドファイル
 *   -i<initialID> 検索開始ID
 */
 
require_once("oauth_config.php");

define( "FILEOUTPUT_PREFIX", "notice:twitter>" );

$options = getopt("i:t:o:s:");
main( $options["s"], $options["t"], $options["o"], $options["i"] );


function main($keyword, $sec, $filename, $initialID)
{
	if ( !isset($keyword) ) {
		echo " not set keyword ( -s<keyword> )\n";
		return;
	}
	
	if ( isset($initialID) ) {
		$obj = new TweetSearch($keyword, $initialID);
	} else {
		$obj = new TweetSearch($keyword);
	}
	
	// 基準ツイート取得
	echo $obj->nextTweet()->toString->__invoke() . "\n";
	
	if ( !isset($sec) ) {
		return;
	}
	
	// ループ間隔
	$time = intval($sec);
	
	while (true) {
		$tweet = $obj->nextTweet();
		if ( $tweet === null ) {
			echo " no tweet.\n";
		} else if ( isset($tweet->retweeted_status) ) { // retweet判定＊
			echo " *** " . $tweet->toString->__invoke() . "\n";
		} else {
			$text = $tweet->toString->__invoke() . "\n";
			
			if ( isset($filename) ) {   // -oオプション
				file_put_contents( $filename, FILEOUTPUT_PREFIX.$text, FILE_APPEND );
				echo "[fileoutput>".$filename."] ".FILEOUTPUT_PREFIX.$text;
			} else {
				echo $text;
			}
		}
		
		sleep($time);
	}
}



class TweetSearch {
    private $since_id = null;
    private $oauth = null;
    private $keyword = null;
    
    /**
     * @param $keyword  Twitter APIに投げる検索キーワード q
     * @param $id       Twitter APIくっつける sinceID
     */
    public function __construct($keyword, $id=null) {
    	$this->oauth = new OAuth( CONSUMER_KEY, CONSUMER_SECRET );
		$this->oauth->setToken( ACCESS_TOKEN, ACCESS_TOKEN_SECRET );
		$this->oauth->disableSSLChecks();

    	$this->since_id = $id;
    	$this->keyword = $keyword;
    }
    
    /**
     * 新しいツイートを1つ取得します。
     * 前回取得したツイートは返しません。
     * 複数のツイートを取得した場合は、一番古いものを返します。
     * 
     * @return 取得したツイートの1つのstatus.
     */
    public function nextTweet() {
    	if ($this->since_id == null) {   // 前回取得したツイートがない場合。
			$statuses = $this->searchTweet(1);
			$tweet = $statuses[0];
		} else {                         // 前回取得したツイートに続ける場合。
			$statuses = $this->searchTweet(15, $this->since_id);
			$count = count( $statuses );
			if ( $count == 0 ) {
				return null;
			}
		
			$tweet = $statuses[ $count - 1 ];
		}
		
		$this->since_id = $tweet->id_str;
		
		$tweet->toString = function() use ($tweet) {
			$time = new DateTime( $status->created_at );
			$time->setTimeZone( new DateTimeZone("Asia/Tokyo") );

			$text = $time->format("H:i") . " @" . $tweet->user->screen_name . " " . $tweet->text;
			$text = ereg_replace("\r|\n"," ",$text);
			return $text;
		};

		
		return $tweet;
	}
	
	
	
	private function searchTweet($count=1, $since=null) {
		$url = "https://api.twitter.com/1.1/search/tweets.json";
		$params["q"] = urlencode( $this->keyword );
		$params["count"] = $count;
		$params["result_type"] = "recent";
		if ($since !== null) {
			$params["since_id"] = $since;
		}
		
		$this->oauth->fetch( $url, $params );
		$contents = json_decode( $this->oauth->getLastResponse() );
		
		echo "count->".count($contents->statuses)."\n";
		
		return $contents->statuses;
	}
}



?>