<?php
$this->CrudBase->setModelName('DiaryK');

$this->assign('css', $this->Html->css(array(
	'CrudBase/reg',
)));
?>

<h2>日誌・登録完了</h2>
<?php
$this->Html->addCrumb("トップ",'/');
$this->Html->addCrumb("日誌",'/diary_k');
$this->Html->addCrumb("詳細",'/diary_k/detail?id='.$ent['id']);
$this->Html->addCrumb("入力",'/diary_k/edit?id='.$ent['id']);
$this->Html->addCrumb("登録完了");
echo $this->Html->getCrumbs(" > ");
?>

<?php

if(!empty($regMsg)){
	echo $regMsg;
}


?>

<style>
	#forms1 td{padding:4px;}

	#forms1 td:nth-child(1) {
    	color:#535353;
	}
	#forms1 td:nth-child(2) {
    	color:#143c7c;
	}
</style>

<hr>


<table id="forms1">

	<?php 
	// --- Start detail_preview	

	$this->CrudBase->tpId($ent['id'],'ＩＤ');
	$this->CrudBase->tpPlain($ent['diary_date'],'日誌日付');
	$this->CrudBase->tpNote($ent['diary_note'],'日誌');
	
	// --- End detail_preview
	?>


</table>

<hr>
<?php
	$rtnUrl= $this->Html->webroot.'diary_k';
	$refixUrl=$this->Html->webroot."diary_k/edit?id={$ent['id']}";
?>
<ul class="ul_side_by_side" >
	<li><a href='<?php echo $rtnUrl ?>' >一覧に戻る</a></li>
	<li><a href='<?php echo $refixUrl ?>' class='btn btn-warning btn-xs'>再修正</a></li>
</ul>
<hr />









