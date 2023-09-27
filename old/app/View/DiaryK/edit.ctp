<?php
$this->CrudBase->setModelName('DiaryK');

if($noData==true ){
	echo "NO PAGE<br />";
	echo "<a href='{$this->Html->webroot}client'>Please back</a>";
	die();

}

//インクルードするcssファイル
$cssList = array(
		'CrudBase/edit',
);

//インクルードするjavascriptファイル
$scripts =array(
		'CrudBase/edit',
		'DiaryK/edit',
);

//デバッグモードの場合にインクルード
$debug_mode=Configure::read('debug');
if($debug_mode == 2){
	$scripts[] = 'auto_inp_form3';
}

$this->assign('css', $this->Html->css($cssList));
$this->assign('script', $this->Html->script($scripts));


?>


<h2>日誌・入力</h2>
<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("日誌",'/diary_k');
	if($mode=='edit'){
		$this->Html->addCrumb("詳細",'/diary_k/detail?id='.$ent['id']);
	}
	$this->Html->addCrumb("入力");
	echo $this->Html->getCrumbs(" > ");
?>

<div style="color:red"><?php echo $errMsg;?></div>



<style>
	#forms1 td{
		padding:4px;
	}

</style>

<?php



echo $this->Form->create('DiaryK', array('url' => 'reg','onsubmit' => 'reload2();', ));
echo $this->element('CrudBase/crud_base_edit_form');

echo $this->Form->input('id', array('value' => $ent['id'],'type' => 'hidden',));

?>




<table id="forms1">

	<?php 
	// --- Start edit_input	

	$this->CrudBase->editText($ent,'diary_date','日誌日付',150);
	$this->CrudBase->editTextArea($ent,'diary_note','日誌');
	
	// --- End edit_input
	?>

	


	


	<tr>
		<td>
		<?php
		//モードからサブミットボタン名を決め、サブミットボタンを生成
		$submitName=null;
		if($mode=='new'){
			$submitName="新規追加";
		}else{
			$submitName="更新";
		}
		echo $this->Form->submit($submitName, array(
				'name' => 'reg',
				'class'=>'btn btn-success',
		));

		?>
		</td>
		<td></td>
	</tr>
</table>

<?php echo $this->Form->end(); ?>


<div style="margin-top:50px">
	<?php
	//編集モードの場合、各フィールド値を表示
	if($mode=='edit'){
		echo $this->CrudBase->updateInfo($ent);

	}
	?>
</div>

<hr />










