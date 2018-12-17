#!/bin/sh

echo '作業ディレクトリ'
pwd

echo "ローカルDBのパスワードを入力してください"
read pw

echo 'SQLをエクスポートします。'
mysqldump -uroot -p$pw zss_rec > zss_rec.sql
echo 'エクスポートしました。'

echo 'SQLファイルをサーバーに転送します。'
scp zss_rec.sql amaraimusi@amaraimusi.sakura.ne.jp:www/zss_rec/shell
echo '転送しました。'

echo "------------ 終わり"
cmd /k