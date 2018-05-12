<?php 

$this->assign('css', $this->Html->css(array(
		'ThumMaker/index'
)));

$this->assign('script', $this->Html->script(array(
		'ThumMaker/thum_maker_model',
		'ThumMaker/index'
),array('charset'=>'utf-8')));
?>

<div id="err" class="text-danger"></div>
<div>
	<div class="inp_wrap1">
		<input type="text" name="orig_dp" value="" placeholder='-- 原寸画像フォルダパス (バックスラッシュ区切り）--' autocomplete="on" style="width:100%" />
	</div>
	<div class="inp_wrap1">
		<input type="text" name="thum_dp" value="" placeholder='-- サムネイル画像フォルダパス(バックスラッシュ区切り） --' autocomplete="on" style="width:100%" />
		<input type="button" value="auto set" class="btn btn-default btn-xs" onclick="thumDpAutoSet()" />
	</div>
	<div class="inp_wrap1">
		<input type="text" name="thum_width" value="" placeholder='横幅' autocomplete="on" style="width:50px" />
		<input type="text" name="thum_heith" value="100" placeholder='縦幅' autocomplete="on" style="width:50px" />
	</div>
	
	<div class="inp_wrap1">
		<input id="show_img_list_btn" type="button" value="オリジナル画像パス一覧" onclick="showImgList()" class="btn btn-success"/>
		<input id="make_thum_btn" type="button" value="サムネイル作成" onclick="makeThum()" class="btn btn-danger" style="display:none"/>
		<span id="res" class="text-success"></span>
	</div>
</div>


<br>

<table id="tbl1" class='table'>
	<thead><tr><th>原寸画像ファイル</th><th>サムネイル画像ファイル</th></tr></thead>
	<tbody>

	</tbody>

</table>


