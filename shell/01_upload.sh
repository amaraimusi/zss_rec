#!/bin/sh
echo 'ソースコードを差分アップロードします。'

rsync -auvz ../app amaraimusi@amaraimusi.sakura.ne.jp:www/zss_rec

echo "------------ 送信完了"
cmd /k