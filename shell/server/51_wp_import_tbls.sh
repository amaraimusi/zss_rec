#!/bin/bash

echo 'さくらスタンダードのコントロールパネルで先にデータベースを作成してくだい'
echo "DBパスワードを入力してください"
read pw

mysql -h mysql303.db.sakura.ne.jp -u amaraimusi -p$pw -B amaraimusi_zss_rec < www/zss_rec/shell/wp_tbls.sql
echo "出力完了"