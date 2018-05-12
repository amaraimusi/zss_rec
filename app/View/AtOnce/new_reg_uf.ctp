<h2>一括新規登録 | 一意フィールド指定型</h2>

<div style="color:#0ca57e"><?php echo $msg ?></div>

<?php 
echo $this->Form->create(null, array('url' => '#'));

echo $this->Form->input("no_a", array(
	'type' => 'text',
	'value' => $param['no_a'],
	'label' => false,
	'placeholder' => '-- 番号A --',
));

echo $this->Form->input("nendo", array(
	'type' => 'text',
	'value' => $param['nendo'],
	'label' => false,
	'placeholder' => '-- 年度 --',
));


?>

<br>

<?php 
echo $this->Form->submit('データ表示',array('name'=>'show_data_btn','class'=>'btn btn-success','div'=>false));
if(!empty($data)){
	echo $this->Form->submit('一括登録',array('name'=>'reg_at_once_btn','class'=>'btn btn-danger','div'=>false));
}
?>

<button type="button" class="btn btn-primary btn-xs" onclick="$('#gadgets').toggle(200)"><span class="glyphicon glyphicon-wrench"></span></button>
<div id='gadgets' style="display:none">
<?php 
echo $this->Form->submit('データを戻す',array('name'=>'restore_btn','class'=>'btn btn-danger btn-xs'));
echo $this->Form->submit('セッションクリア',array('name'=>'ses_clear_btn','class'=>'btn btn-danger btn-xs'));
?>
</div>

<?php echo $this->Form->end(); ?>

<?php if(!empty($data)){ ?>
<table class="table">
	<thead><tr>
	<?php 
		foreach($whiteList as $field){
			echo "<th>{$field}</th>";
		}
	?>
	</tr></thead>
	<tbody>
	<?php 
		foreach($data as $ent){
			echo "<tr>";
			foreach($whiteList as $field){
				$v = $ent[$field];
				echo "<td>{$v}</td>";
			}
			echo "</tr>";
		}
	?>
	</tbody>
</table>
<?php } ?>










