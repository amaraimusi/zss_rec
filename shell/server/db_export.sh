#!/bin/bash

echo "DBパスワードを入力してください"
mysqldump -Q -h mysql303.db.sakura.ne.jp -u amaraimusi -p amaraimusi_zss_rec --add-drop-table > www/zss_rec/shell/zss_rec.sql 2> www/zss_rec/shell/dump.error.txt

echo "出力完了"