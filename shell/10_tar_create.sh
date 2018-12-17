#!/bin/sh

cd ../../
echo '作業ディレクトリ'
pwd
echo 'zss_recを圧縮開始'
tar cvzf zss_rec.tar.gz zss_rec
echo 'zss_rec.tar.gzを作成'
echo "------------ 終わり"
cmd /k