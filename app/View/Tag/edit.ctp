<?php

	if($noData==true){
		echo "NO DATA<br />";
		echo "<a href='{$this->Html->webroot}tag_list'>return</a>";
		die();

	}

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

<h2>タグ 編集入力</h2>



<?php


// //一覧に戻る
// $rtnUrl= $this->Html->webroot.'tag_list';
// if(!empty($ent['id'])){

// 	$rtnUrl.='#i'.$ent['id'];
// }

// echo "<a href='{$rtnUrl}' >一覧に戻る</a>";


$this->Html->addCrumb("管理者トップ", "/admin");
$this->Html->addCrumb("一覧",'/tag');
$this->Html->addCrumb("入力");
echo $this->Html->getCrumbs(" > ");

//echo $this->Form->create('Tag', array('url' => 'reg','onsubmit' => 'reload2();'));

//ファイルアップロード対応Form要素
echo $this->Form->create('Tag', array('url' => 'reg','onsubmit' => 'reload2();','type'=>'file', 'enctype' => 'multipart/form-data' ));




if(!empty($errMsg)){

	echo "<div style='color:red'>".$errMsg."</div>";
}




echo $this->Form->input('id', array(
		'value' => $ent['id'],
		'type' => 'hidden',
));



?>

<div class="form_inp_rap"><div class="form_inp_title">タグ名</div>
	<div class="form_inp"><?php
	echo $this->Form->input('name', array(
			'value' => $ent['name'],
			'type' => 'text',
			'placeholder' => '-- タグ名 --',
			'label' => false,
			'div'=>false,
	));
?></div><div style="clear:both"></div></div>





<div class="form_inp_rap"><div class="form_inp_title">削除フラグ</div>
	<div class="form_inp"><?php
	echo $this->Form->input('del_flg', array(
			'checked' => $ent['del_flg'],
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








