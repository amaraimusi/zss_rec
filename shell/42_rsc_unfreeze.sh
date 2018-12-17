#!/bin/sh

echo 'サーバー先でzss_rec_rsc.tar.gzを解凍します。'
ssh -l amaraimusi amaraimusi.sakura.ne.jp "
	cd www/zss_rec/app/webroot;
	pwd;
	tar vxzf zss_rec_rsc.tar.gz;
	exit;
	"

echo "------------ 解凍完了"
cmd /k