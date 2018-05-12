<?php
$this->CrudBase->setModelName('Probe');

$this->assign('css', $this->Html->css(array(
	'CrudBase/common',
	'CrudBase/reg',
)));
?>

<h2>個体・登録完了</h2>
<?php
$this->Html->addCrumb("トップ",'/');
$this->Html->addCrumb("個体",'/probe');
$this->Html->addCrumb("詳細",'/probe/detail?id='.$ent['id']);
$this->Html->addCrumb("入力",'/probe/edit?id='.$ent['id']);
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

	$this->CrudBase->tpId($ent['id'],'id');
	$this->CrudBase->tpNote($ent['probe_name'],'probe_name');
	$this->CrudBase->tpPlain($ent['hatake_id'],'畑ID');
	$this->CrudBase->tpPlain($ent['rx'],'畑相対位置X');
	$this->CrudBase->tpPlain($ent['ry'],'畑相対位置Y');
	$this->CrudBase->tpNote($ent['probe_note'],'probe_note');
	$this->CrudBase->tpDeleteFlg($ent['delete_flg'],'有無');
	$this->CrudBase->tpPlain($ent['modified'],'更新日時');
	
	// --- End detail_preview
	?>


</table>

<hr>
<?php
	$rtnUrl= $this->Html->webroot.'probe';
	$refixUrl=$this->Html->webroot."probe/edit?id={$ent['id']}";
?>
<ul class="ul_side_by_side" >
	<li><a href='<?php echo $rtnUrl ?>' >一覧に戻る</a></li>
	<li><a href='<?php echo $refixUrl ?>' class='btn btn-warning btn-xs'>再修正</a></li>
</ul>
<hr />









