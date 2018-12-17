#!/bin/sh
echo 'サーバー側のshファイルをサーバーに送信します。'

scp -r server amaraimusi@amaraimusi.sakura.ne.jp:www/zss_rec/shell/

echo 'サーバー側のshファイルをサーバーに送信しました。'
cmd /k