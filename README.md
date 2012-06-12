# What is hexol
とあるIRCメッセ環境の接続維持を行います。

## Requirements
- php
- SmartIRC
- wget
- cron

## 機能
- IRCへの接続維持 (2分間隔)
- Todayミッション情報をトピックに設定 (cron設定によりローカルコマンド実行)
- joinユーザへのオペレータ権限付与 (allow.confで指定されたユーザ)

## Starting
    $ php bot.php &

## ローカルコマンド
実行環境で使用されるコマンドです。

"config.php" で指定されている "BOT_COMMAND_FILE" がコマンドファイルになります。

現在の設定値は "command"。

- quit (終了)
- update (Today情報更新)
- naruto (オペレータ権限付与, allow.confに登録されているニックネームに対してのみ)

### 使用例
    $ echo "quit" > command
    $ echo "update" > command
    $ echo "naruto:<user>" > command

## リモートコマンド
IRCを使って、特定のメッセージを送ることで実行されるコマンドです。

"config.php" で指定されている "PREFIX" でコマンド接頭が変わります。

現在の設定値は "hexol"。

- quit (終了)
- update (Today情報更新)

### 使用例 (IRCメッセージ)
    hexol>quit
    hexol>update

## cron設定
cron を使用して、AM7:10にupdateローカルコマンドを発行します。

