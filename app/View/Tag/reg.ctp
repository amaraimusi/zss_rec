<?php

	if($noData==true){
		echo "NO DATA<br />";
		echo "<a href='{$this->Html->webroot}edit_list'>return</a>";
		die();

	}

?>

<style>

	.labels2{
		float:left;
		width:100px;
		margin-right:16px;
	}
	.inputs{
		float:left;
	}

	.dates{
		color:#808080;
	}

	.d_flg_true{
		color:#23d6e4;
	}

	.d_flg_false{
		color:#b4b4b4;
	}
</style>
<script type="text/javascript">

	$(document).ready(function(){


	});



</script>

<h2>タブ</h2>
<?php
	$this->Html->addCrumb("管理者トップ", "/admin");
	$this->Html->addCrumb("一覧",'/tag');
	$this->Html->addCrumb("入力",'/tag/edit?id='.$ent['id']);
	$this->Html->addCrumb("登録完了");
	echo $this->Html->getCrumbs(" > ");
?>


<?php
if(!empty($regMsg)){
	echo $regMsg;
}
?>


<div>
	<div class="labels2">ID</div>
	<div class="inputs"><?php echo $ent['id']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">タブ名</div>
	<div class="inputs"><?php echo $ent['name']; ?> </div> <div style="clear:both"></div>
</div>



<div>
	<div class="labels2">削除フラグ</div>

	<?php
	//有無(削除フラグ）の出力
	if($ent['del_flg']==0){
		echo "<span class='d_flg_true'>有効</span>";
	}else{
		echo "<span class='d_flg_false'>無効</span>";
	}
	?>
</div>






<br /><br /><br /><br />
<?php
	//一覧に戻る
	$rtnUrl= $this->Html->webroot.'tag#'.$ent['id'];

	echo "<a href='{$rtnUrl}' >一覧に戻る</a>";
?>

<span>　　　　</span>
<a href="<?php echo $this->Html->webroot.'tag/edit?id='.$ent['id'];?>">再修正する</a>

<hr />









