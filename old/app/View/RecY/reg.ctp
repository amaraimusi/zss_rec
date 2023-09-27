<?php

	if($noData==true){
		echo "NO DATA<br />";
		echo "<a href='{$this->Html->webroot}edit_list'>return</a>";
		die();

	}
	
	$this->assign('css', $this->Html->css(array(
			'CrudBase/common',
			'CrudBase/reg',
	)));
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

<h2>記録Y</h2>
<?php
	$this->Html->addCrumb("管理者トップ", "/admin");
	$this->Html->addCrumb("一覧",'/rec_y');
	$this->Html->addCrumb("入力",'/rec_y/edit?id='.$ent['id']);
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
	<div class="labels2">記録2</div>
	<div class="inputs"><?php echo $ent['rec_title']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">記録日</div>
	<div class="inputs"><?php echo $ent['rec_date']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">ノート</div>
	<div class="inputs"><?php echo $ent['note']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">カテゴリ1</div>
	<div class="inputs"><?php echo $ent['category_id1']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">カテゴリ2</div>
	<div class="inputs"><?php echo $ent['category_id2']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">タグ</div>
	<div class="inputs"><?php echo $ent['tags']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">写真</div>
	<div class="inputs"><?php echo $ent['photo_fn']; ?> </div> <div style="clear:both"></div>
	<div class="inputs"><img src="<?php echo $this->Html->webroot.'img'.$ent['photo_dir'].$ent['photo_fn'] ?>" /></div><div style="clear:both"></div>
</div>

<div>
	<div class="labels2">参照リンク</div>
	<div class="inputs"><?php echo $ent['ref_url']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">並び順</div>
	<div class="inputs"><?php echo $ent['sort_no']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">番号A</div>
	<div class="inputs"><?php echo $ent['no_a']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">番号B</div>
	<div class="inputs"><?php echo $ent['no_b']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">親ID</div>
	<div class="inputs"><?php echo $ent['parent_id']; ?> </div> <div style="clear:both"></div>
</div>

<div>
	<div class="labels2">公開</div>

	<?php
	//有無(削除フラグ）の出力
	$publish="";
	if($ent['publish']==1){
		$publish="<span class='d_flg_true'>公開</span>";
	}else{
		$publish="<span class='d_flg_false'>非公開</span>";
	}
	?>
	<div class="inputs"><?php echo $publish; ?> </div> <div style="clear:both"></div>
</div>






<br /><br /><br /><br />
<?php
	//一覧に戻る
	$rtnUrl= $this->Html->webroot.'rec_y#'.$ent['id'];

	echo "<a href='{$rtnUrl}' >一覧に戻る</a>";
?>

<span>　　　　</span>
<a href="<?php echo $this->Html->webroot.'rec_y/edit?id='.$ent['id'];?>">再修正する</a>

<hr />









