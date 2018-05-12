
<h1>ファイル名一括変更ツール</h1>


<?php echo $this->Form->create('DataSet', array('url' => '#'));?>


<?php
	echo $this->Form->input("img_path", array(
		'type' => 'text',
		'value' => $img_path,
		'label' => false,
		'div' => false,
		'placeholder' => '-- 画像パス --',
	));
?>




<?php echo $this->Form->submit('画像リスト表示', array(
		'name' => 'imgListSubmit',
		'class'=>'btn btn-success',
		'div'=> false,
));
?>

<?php 
if($renameFlg ==true){

	echo $this->Form->submit('ファイル名変更', array(
			'name' => 'renameSubmit',
			'class'=>'btn btn-danger',
			'div'=> false,
	));
}
?>



<?php echo $this->Form->end(); ?>
<br>

<aside>
	Cakeの場合のパス指定例<br>
	/zss_rec/app/webroot/img/n2016/135<br>
</aside>
<br>

<table class='table'>
	<thead><tr><th>現在ファイル名</th><th>ファイル変更フラグ</th><th>変更ファイル名</th></tr></thead>
	<tbody>
	<?php 
	foreach($data as $ent){
		
		$strRenameFlg = "OK";
		if(!empty($ent['rename_flg'])){
			$strRenameFlg = "<span style='color:red'>変更せよ</span>";
			
		}
		echo "<tr>".
			"<td>{$ent['fn']}</td>".
			"<td>{$strRenameFlg}</td>".
			"<td>{$ent['fn_chg']}</td>";
	}
	?>
	</tbody>

</table>


