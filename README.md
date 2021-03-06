# What is hexol
とあるIRCメッセ環境の接続維持を行います。

## Requirements
- php
- SmartIRC
- cron

## 機能
- IRCへの接続維持 (2分間隔)
- Todayミッション情報をトピックに設定 (cron設定によりローカルコマンド実行。チャンネル1のみ。)
- メッセージ送信（チャンネル1のみ。通知は、チャンネル1と2が使用可能です。）
- joinユーザへのオペレータ権限付与 (allow.confで指定されたユーザ。接続中のチャンネルすべて。)
- 任意のチャンネルへの接続

## Starting
    $ php bot.php &

## リモートコマンド
IRCを使って、特定のメッセージを送ることで実行されるコマンドです。

"config.php" で指定されている "PREFIX" でコマンド接頭が変わります。

現在の設定値は "hexol"。

- quit (終了)
- update (Today情報更新)
- topic (トピックの設定)

### 使用例 (IRCメッセージ)
    hexol>quit
    hexol>update
    hexol>update enable
    hexol>update disable
    hexol>topic:<text>


## ローカルコマンド
実行環境で使用されるコマンドです。

"config.php" で指定されている "BOT_COMMAND_FILE" がコマンドファイルになります。

現在の設定値は "command"。

- quit (終了)
- update (Today情報更新)
- naruto (オペレータ権限付与, allow.confに登録されているニックネームに対してのみ)
- join
- part
- topic
- message
- notice

### 使用例
    $ echo "quit" > command
    $ echo "update" > command
    $ echo "naruto:<user>" > command
    $ echo "topic:<text>" > command
    $ echo "message:<text>" > command
    $ echo "join:#<ch>" > command
    $ echo "part:#<ch>" > command


## cron設定
cron を使用して、AM7:10にupdateローカルコマンドを発行します。

