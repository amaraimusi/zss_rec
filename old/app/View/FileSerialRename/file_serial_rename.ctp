<?php
	
	$this->assign('css', $this->Html->css(array(
			'FileSerialRename/file_serial_rename',
	)));
	
	$this->assign('script', $this->Html->script(array(
			'FileSerialRename/file_serial_rename',
	)));
?>


<p>File name change in the serial number</p>
version 1.0 2016-8-30<br>
<br>



<?php echo $this->Form->create('FileSerialRename', array('url' => '#'));?>


<?php
	if($phase == 'init'){
		echo $this->Form->input("fp", array(
			'id' => 'fp',
			'type' => 'text',
			'value' => $param['fp'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- 画像フォルダのフルパス --',
			'style' => 'width:500px',
		));
		

		echo $this->Form->input("hinagata", array(
				'id' => 'hinagata',
				'type' => 'text',
				'value' => $param['hinagata'],
				'label' => false,
				'div' => false,
				'placeholder' => '-- ひな形 --',
				'style' => 'width:100px',
				'onblur' => "validHinagata()",
		));
	}else{
		echo '<div>'.$param['fp'].'</div>';
		echo '<div>'.$param['hinagata'].'</div>';
	}
	
	
	

?>
<table>
	<tr>
	<td><strong>ソート</strong></td>
	<td style="padding-left:40px">
	<?php 
	if($phase == 'init'){
		
		echo $this->Form->input('sort_field', array(
				'id' => 'sort_field',
				'legend' => false,
				'type' => 'radio',
				'value'=>$param['sort_field'],//初期選択値
				'options' => array('file_name'=>'ファイル名','update_dt'=>'更新日時')
		));
	}else{
		echo "並びフィールド：".$param['sort_field'];
	}
	?>
	</td>
	<td style="padding-left:40px">
	<?php 
	if($phase == 'init'){
		echo $this->Form->input('sort_asc', array(
				'id' => 'sort_asc',
				'legend' => false,
				'type' => 'radio',
				'value'=>$param['sort_asc'],
				'options' => array('0'=>'昇順','1'=>'降順')
		));
	}else{
		echo "並び順：".$param['sort_asc'];
	}
	
	?>
	</td>
	</tr>
</table>
<br><br>

<?php 
if($phase == 'init'){
	echo $this->Form->submit('ファイル一覧を表示',['name'=>'submit_file_list','class'=>'btn btn-success','id'=>'submit_file_list']);
	echo $this->Form->submit('セッションクリア',['name'=>'submit_session_clear','class'=>'btn btn-danger btn-xs']);
}else if($phase == 'file_list'){
	echo "<a href='file_serial_rename' class='btn btn-default btn-xs'>最初から</a>";
	echo $this->Form->submit('名前変更',['name'=>'submit_rename','class'=>'btn btn-danger']);
}else if($phase == 'rename'){
	echo "<a href='file_serial_rename' class='btn btn-default btn-xs'>最初から</a>";
}
?>

<?php echo $this->Form->end(); ?>



<?php if(!empty($fileData)){?>

<h3>ファイルデータ</h3>
<table class="table" style="font-size:0.8em">
<thead>
	<tr>
	<?php 
	$ent = $fileData[0];
	foreach($ent as $field=>$v){
		echo "<th>{$field}</th>";
	}
	?>
	</tr>
</thead>
<tbody>
	<?php 
	foreach($fileData as $ent){
		echo '<tr>';
		foreach($ent as $v){
			echo "<td>{$v}</td>";
		}
		echo "</tr>\n";
	}
	?>
</tbody>
</table>


<?php }?>



<?php 
if(!empty($success)){
	echo "<div id='success'>ファイル名を変更しました。(renameフォルダ内）</div>";
}
?>









