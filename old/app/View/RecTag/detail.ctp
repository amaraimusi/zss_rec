
<?php
$this->CrudBase->setModelName('RecTag');

$this->assign('css', $this->Html->css(array('CrudBase/common','CrudBase/detail')));
?>

<h2>記録タグ・詳細</h2>
<?php
$this->Html->addCrumb("トップ",'/');
$this->Html->addCrumb("記録タグ",'/rec_tag');
$this->Html->addCrumb("詳細");
echo $this->Html->getCrumbs(" > ");

if(empty($ent)){
	echo "<div id='no_data'>NO DATA</div>";
	return;
}
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

	$this->CrudBase->tpId($ent['id'],'ID');
	$this->CrudBase->tpPlain($ent['rec_id'],'記録ＩＤ');
	$this->CrudBase->tpPlain($ent['tag_id'],'タブＩＤ');
	$this->CrudBase->tpStr($ent['updated'],'更新日時');
	
	// --- End detail_preview
	?>


</table>

<hr>
<?php
	$rtnUrl= $this->Html->webroot.'rec_tag';
	$refixUrl=$this->Html->webroot."rec_tag/edit?id={$ent['id']}";
?>
<ul class="ul_side_by_side" >
	<li><a href='<?php echo $rtnUrl ?>' >一覧に戻る</a></li>
	<li><a href='<?php echo $refixUrl ?>' class='btn btn-warning btn-xs'>編集</a></li>
</ul>
<div style="clear:both"></div>
<hr />









