#!/bin/sh
echo 'sqlファイルをサーバーに送信します。'

scp zss_rec.sql amaraimusi@amaraimusi.sakura.ne.jp:www/zss_rec/shell
echo "------------ 送信完了"
cmd /k