<?php

	if($noData==true){
		echo "NO DATA<br />";
		echo "<a href='{$this->Html->webroot}rec_y_list'>return</a>";
		die();

	}

	//インクルードするcssファイル
	$cssList = array(
			'CrudBase/common',
			'CrudBase/edit',
	);
	
	//インクルードするjavascriptファイル
	$scripts =array(
			'CrudBase/edit',
			'RecY/edit',
	);
	
	//デバッグモードの場合にインクルード
	$debug_mode=Configure::read('debug');
	if($debug_mode == 2){
		$scripts[] = 'auto_inp_form3';
	}
	
	$this->assign('css', $this->Html->css($cssList));
	$this->assign('script', $this->Html->script($scripts));
?>

<style>

	label{
		width:400px;
	}
	#reg_msg{
		color:#257fc4;
		font-size:2em;
	}

	#reg_msg_err{
		color:#dc5925;

	}

	.error-message{
		color:#dc5925;
	}


	.form_inp_rap{
		margin-bottom:25px;
	}
	.form_inp_title{
		width:110px;
		padding-right:10px;
		float:left;
	}
	.form_inp{
		width:240px;
		float:left;
	}
	.form_inp input{
		width:250px;
	}



</style>
<script type="text/javascript">

	$(document).ready(function(){


	});

	//リロード対策用。
	function reload2(){

		var d=new Date();
		$('#reload').val(d);
		return null;
	}



</script>

<h2>記録Y 編集入力</h2>



<?php


// //一覧に戻る
// $rtnUrl= $this->Html->webroot.'rec_y_list';
// if(!empty($ent['id'])){

// 	$rtnUrl.='#i'.$ent['id'];
// }

// echo "<a href='{$rtnUrl}' >一覧に戻る</a>";


$this->Html->addCrumb("管理者トップ", "/admin");
$this->Html->addCrumb("一覧",'/rec_y');
$this->Html->addCrumb("入力");
echo $this->Html->getCrumbs(" > ");

//echo $this->Form->create('RecY', array('url' => 'reg','onsubmit' => 'reload2();'));

//ファイルアップロード対応Form要素
echo $this->Form->create('RecY', array('url' => 'reg','onsubmit' => 'reload2();','type'=>'file', 'enctype' => 'multipart/form-data' ));




if(!empty($errMsg)){

	echo "<div style='color:red'>".$errMsg."</div>";
}




echo $this->Form->input('id', array(
		'value' => $ent['id'],
		'type' => 'hidden',
));



?>

<div class="form_inp_rap"><div class="form_inp_title">タイトル</div>
	<div class="form_inp"><?php
	echo $this->Form->input('rec_title', array(
			'value' => $ent['rec_title'],
			'type' => 'text',
			'placeholder' => '-- タイトル --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">日にち</div>
	<div class="form_inp"><?php
	echo $this->Form->input('rec_date', array(
			'value' => $ent['rec_date'],
			'id' => 'datepicker',
			'type' => 'text',
			'placeholder' => '-- 日にち --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">ノート</div>
	<div class="form_inp"><?php
	echo $this->Form->input('note', array(
			'value' => $ent['note'],
			'type' => 'textarea',
			'placeholder' => '-- ノート --',
			'label' => false,
			'div'=>false,
			'escape'=>false
	));
?></div><div style="clear:both"></div></div>



<div class="form_inp_rap"><div class="form_inp_title">カテゴリ1</div>
	<div class="form_inp"><?php
	echo $this->Form->input('category_id1', array(
			'type' => 'select',
			'options' =>$categoryOptions1,
			'default' => $ent['category_id1'],
			'empty' => '-- カテゴリ1 --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">カテゴリ2</div>
	<div class="form_inp"><?php
	echo $this->Form->input('category_id2', array(
			'type' => 'select',
			'options' =>$categoryOptions2,
			'default' => $ent['category_id2'],
			'empty' => '-- カテゴリ2 --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">タグ</div>
	<div class="form_inp"><?php
	echo $this->Form->input('j_tags', array(
			'value' => $ent['j_tags'],
			'type' => 'text',
			'placeholder' => '-- タグ --',
			'title'=>'複数のタグを入力する場合は「,」でつなぐ',
			'label' => false,
			'div'=>false,
	));
	echo $this->Form->input('j_tags_b',array('type'=>'hidden','value' => $ent['j_tags']));
	echo $this->Form->input('j_tag_ids',array('type'=>'hidden','value' => $ent['j_tag_ids']));
	
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">画像パス</div>
	<div class="form_inp"><?php
	echo $this->Form->input('photo_dir', array(
			'value' => $ent['photo_dir'],
			'type' => 'text',
			'placeholder' => '-- 画像パス --',
			'title'=>'前後を「/」で閉じること',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">写真</div>
	<div class="form_inp"><?php
	echo $this->Form->input('photo_fn', array(
			'value' => $ent['photo_fn'],
			'type' => 'text',
			'placeholder' => '-- 写真 --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">参考サイトURL</div>
	<div class="form_inp"><?php
	echo $this->Form->input('ref_url', array(
			'value' => $ent['ref_url'],
			'type' => 'text',
			'placeholder' => '-- URL --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">年度</div>
	<div class="form_inp"><?php
	echo $this->Form->input('nendo', array(
			'value' => $ent['nendo'],
			'type' => 'text',
			'placeholder' => '-- 年度 --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">並び順</div>
	<div class="form_inp"><?php
	echo $this->Form->input('sort_no', array(
			'value' => $ent['sort_no'],
			'type' => 'text',
			'placeholder' => ' - ',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">番号A</div>
	<div class="form_inp"><?php
	echo $this->Form->input('no_a', array(
			'value' => $ent['no_a'],
			'type' => 'text',
			'placeholder' => ' - ',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">番号B</div>
	<div class="form_inp"><?php
	echo $this->Form->input('no_b', array(
			'value' => $ent['no_b'],
			'type' => 'text',
			'placeholder' => ' - ',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">親記録ID</div>
	<div class="form_inp"><?php
	echo $this->Form->input('parent_id', array(
			'value' => $ent['parent_id'],
			'type' => 'text',
			'label' => '親記録ID',
			'placeholder' => '-- 親記録ID --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>


<div class="form_inp_rap"><div class="form_inp_title">公開フラグ</div>
	<div class="form_inp"><?php
	echo $this->Form->input('publish', array(
			'checked' => $ent['publish'],
			'type' => 'checkbox',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>




<?php

//モード
echo $this->Form->input('mode', array(
		'value' => $mode,
		'type' => 'hidden',
));

echo "<input type='hidden' id='reload' name='reload' value='' />\n";//リロードチェック用

//モードからサブミットボタン名を決め、サブミットボタンを生成
$submitName=null;
if($mode=='new'){
	$submitName="新規追加";
}else{
	$submitName="更新";
}
echo $this->Form->submit($submitName, array(
		'name' => 'reg'
));


echo $this->Form->end()

?>
<hr />








