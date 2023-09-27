
<script>

</script>
<style>

	input{
		clear:none;
	}
	form div{/* Cakeではclear:bothが入っているため */
		clear:none;
	}

	.kj_btn{
		float:left;
		margin-right:20px;
		margin-left:0px;
	}

	#kjs2{
		display:none;
	}

	.d_flg_true{
		color:#23d6e4;
	}

	.d_flg_false{
		color:#b4b4b4;
	}
</style>


<h2>タグ</h2>

<?php
	$this->Html->addCrumb("管理者トップ", "/admin");
	$this->Html->addCrumb("タグ");
	echo $this->Html->getCrumbs(" > ");
?>
<br>
<div style="color:red"><?php echo $errMsg;?></div>

<div style="margin-top:5px">
	<?php echo $this->Form->create('Tag', array('url' => true ));?>

	<div class="xxx">

		<div style="float:left">
			<?php
			echo $this->Form->input('kj_name', array(
					'value' => $kjs['kj_name'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- タグ（部分一致） --',
					'div'=> false,
			));
			?>
		</div>

		<div style="float:left">
			<?php
			echo $this->Form->input('kj_del_flg', array(
				'type' => 'select',
				'options' => array(0=>'すべて',1=>'無効'),
				'default' => $kjs['kj_del_flg'],
				'label' => false,
				'empty' => '-- 有無 --',
				'div'=> false,
			));
			?>
		</div>
		
		<div style="float:left">
				<?php
			 //表示件数
			 echo $this->Form->input('kj_limit', array(
			 		'type' => 'select',
			 		'options' => array(
							5=>'5件表示',
			 				10=>'10件表示',
			 				20=>'20件表示',
			 				30=>'30件表示',
			 				50=>'50件表示',
			 				100=>'100件表示',
			 				200=>'200件表示',
			 				500=>'500件表示',
			 		),
			 		'empty' => 'すべて表示',
			 		'default' => $kjs['kj_limit'],
			 		'label' => false,
					'style' => 'height:27px',
					'div'=> false,
					'style'=>'float:none;width:100%',
			 ));

			 ?>
		</div>
		<div style="float:left">
			<?php echo $this->Form->submit('検索', array(
				'name' => 'search',
				'class'=>'btn btn-primary',
				'div'=> false,
				));
			?>
		</div>


		<div style="float:left">
			<?php echo $this->Html->link('新規',
				array('controller' => 'Tag', 'action' => 'edit'),
				array('class' => 'btn btn-success',
					'div'=> false,
			));
			?>
		</div>

		<div style="clear:both"></div>

	</div>
	

	<?php echo $this->Form->end()?>

</div>


<br />




<div class="row" style="margin-bottom:5px">
	<div class="col-md-12">
		<?php echo $pages['page_index_html'];//ページ目次 ?>
	</div>
</div>

<table border="1"  class="table table-striped table-bordered table-condensed">

<tbody>
<tr>

	<th><?php echo $pages['sorts']['Tag.id']; ?></th>
	<th><?php echo $pages['sorts']['Tag.name']; ?></th>
	<th><?php echo $pages['sorts']['Tag.del_flg']; ?></th>
	<th><?php echo $pages['sorts']['Tag.updated']; ?></th>
</tr>
<?php

foreach($data as $i=>$ary){

	$ent=$ary['Tag'];
	$editUrl=$this->Html->webroot.'tag/edit?id='.$ent['id'];

	echo "<tr id=i{$ent['id']} value='{$i}' >";
	echo "<td ><a href='{$editUrl}'>{$ent['id']}</a></td>";

	echo "<td >{$ent['name']}</td>";


	//有無の出力
	if($ent['del_flg']==0){
		echo "<td><span class='d_flg_true'>有効</span></td>";
	}else{
		echo "<td><span class='d_flg_false'>無効</span></td>";
	}
	
	
	echo "<td >{$ent['updated']}</td>";

	echo "</tr>\n";

}


?>
</tbody>
</table>


<br />





