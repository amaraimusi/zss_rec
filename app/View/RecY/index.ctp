<?php
	
	$this->assign('css', $this->Html->css(array(
			'CrudBase/common',				//CRUD共通
			'CrudBase/index'				//CRUD index共通
	)));
	
	$this->assign('script', $this->Html->script(array(
			'RecY/index'					//当画面専用JavaScript
	),array('charset'=>'utf-8')));
?>

<script>
	//詳細検索フォームの表示切替
	function show_kj_detail(){
		$("#kjs2").toggle();
	}
</script>
<style>



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

	.sec1{
		border-style:solid;
		border-width:2px;
		border-color:#dac5fe;
		padding:10px;
		margin-bottom:50px;

	}
	.note_label{
		color:#7878bc
	}
	.date{
		color:#037900
	}
	.note{
		padding:10px;
	}
	.btn1{
		float:left;
	}

</style>



<?php
	$this->Html->addCrumb("管理者トップ", "/admin");
	$this->Html->addCrumb("記録Y");
	echo $this->Html->getCrumbs(" > ");
?>
<br>
<div style="color:red"><?php echo $errMsg;?></div>

<?php echo $this->Form->create('RecY', array('url' => true ));?>

<div>
	<div class="btn1">
		<input type="button" value="検索入力" onclick="show_kj_detail()" class="btn btn-primary" />
	</div>
	<div class="btn1">
		<?php echo $this->Form->submit('最新30件', array(
				'name' => 'new30',
				'class'=>'btn btn-success',
				'div'=> false,
				'style'=>'float:none;',
				));
		?>
	</div>
	<div class="btn1">
		<?php echo $this->Form->submit('2014夏', array(
				'name' => 'season1',
				'class'=>'btn btn-success',
				'div'=> false,
				'title'=>'7-9',
				'style'=>'float:none;',
				));
		?>
	</div>
	<div class="btn1">
		<?php echo $this->Form->submit('2014秋', array(
				'name' => 'season2',
				'class'=>'btn btn-success',
				'div'=> false,
				'title'=>'10-12',
				'style'=>'float:none;',
				));
		?>
	</div>
	<div class="btn1">
		<?php echo $this->Form->submit('2015冬', array(
				'name' => 'season3',
				'class'=>'btn btn-success',
				'div'=> false,
				'title'=>'1-3',
				'style'=>'float:none;',
				));
		?>
	</div>
	<div class="btn1">
		<?php echo $this->Form->submit('2015春', array(
				'name' => 'season4',
				'class'=>'btn btn-success',
				'div'=> false,
				'title'=>'4-6',
				'style'=>'float:none;',
				));
		?>
	</div>
	<div style="clear:both"></div>
</div>

<div id="kjs2">
	<div class="row">


		<div class="col-md-2">
			<?php
			//日付範囲1
			echo $this->Form->input('kj_rec_date1', array(
					'value' => $kjs['kj_rec_date1'],
					'id' => 'datepicker',
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- 日付（始） --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			 ?>
		</div>
		<div class="col-md-2">
			<?php
			//日付範囲2
			echo $this->Form->input('kj_rec_date2', array(
					'value' => $kjs['kj_rec_date2'],
					'id' => 'datepicker2',
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- 日付（終） --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			 ?>
		</div>

		<div class="col-md-3">
			<?php

			echo $this->Form->input('kj_category_id1', array(
				'type' => 'select',
				'options' => $categoryOptions,
				'default' => $kjs['kj_category_id1'],
				'label' => false,
				'empty' => '-- カテゴリ1 --',
				'div'=> false,
				'style'=>'float:none;width:100%',
			));

			?>
		</div>

		<div class="col-md-3">
			<?php

			echo $this->Form->input('kj_category_id2', array(
				'type' => 'select',
				'options' => $categoryOptions,
				'default' => $kjs['kj_category_id2'],
				'label' => false,
				'empty' => '-- カテゴリ2 --',
				'div'=> false,
				'style'=>'float:none;width:100%',
			));

			?>
		</div>


	</div>


	<div class="row" >

		<div class="col-md-3">
			<?php
			echo $this->Form->input('kj_rec_title', array(
					'value' => $kjs['kj_rec_title'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- タイトル（部分一致） --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			?>
		</div>

		<div class="col-md-2">
			<?php
			//ノート
			echo $this->Form->input('kj_note', array(
					'value' => $kjs['kj_note'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- ノート --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			?>
		</div>
		<div class="col-md-2">
			<?php
			//タグリスト
			echo $this->Form->input('kj_tags', array(
					'value' => $kjs['kj_tags'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- タグ --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			?>
		</div>

		<div class="col-md-2">
			<?php
			//タグID
			echo $this->Form->input('kj_tag_id', array(
					'value' => $kjs['kj_tag_id'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- タグID --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			?>
		</div>

		<div class="col-md-3">
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
			 		),
			 		'default' => $kjs['kj_limit'],
			 		'label' => false,
					'div'=> false,
					'style'=>'float:none;width:100%',
			 ));

			 ?>
		</div>
		
		<div class="kj_div" style="margin-top:4px;">
			<?php
			echo $this->Form->input("saveKjFlg",array(
				'type'=>'checkbox',
				'value' => 1,
				'checked'=>$saveKjFlg,
				'label'=>'検索入力保存',
				'div'=>false,
			));
			?>
		</div>
		
		<div class="kj_div" style="margin-top:5px">
			<input type="button" value="リセット" title="検索入力を初期に戻します" onclick="resetKjs()" class="btn btn-primary btn-xs" />
		</div>

		<div class="col-md-2">
			<?php echo $this->Form->submit('検索', array(
				'name' => 'search',
				'class'=>'btn btn-success',
				'div'=> false,
				'style'=>'float:none;',
				));
		?>





	</div>

	<table border="1"  class="table table-striped table-bordered table-condensed">

		<thead>
		<tr>

			<th><?php echo $pages['sorts']['RecY.id']; ?></th>
			<th><?php echo $pages['sorts']['RecY.rec_date']; ?></th>
		</tr>
		</thead>
	</table>

	</div>
</div><!-- $kjs2 -->
<?php echo $this->Form->end()?>




<br />




<div style="margin-bottom:30px">
	<?php echo $pages['page_index_html'];//ページ目次 ?>
</div>


<?php

foreach($data as $i=>$ary){

 	$ent=$ary['RecY'];

	echo "<div id='{$ent['id']}' class='row'>";
	echo "<div class='col-md-6'>";
	if(!empty($ent['photo_fn'])){
		$img_fn=$this->Html->webroot.'img'.$ent['photo_dir'].$ent['photo_fn'];
		echo "<img class='img-responsive' src='{$img_fn}' />";
	}
	echo "</div>";

	$tag_text="";
	if(!empty($data[$i]['Tags'])){
		$tag_text="タグ：";
		foreach($data[$i]['Tags'] as $tag_ent){
			$tag_text.="<a href='rec_y?tag_search=1&kj_tag_id={$tag_ent['tag_id']}' target='_blank'>{$tag_ent['name']}</a>     ";
		}
	}

	echo "<div class='col-md-6'>
			<div class='note_label'>ID:{$ent['id']}
				<span class='date'>    {$ent['rec_date']}</span>
				<strong> {$ent['title']}</strong>
			</div>
			<div class='note_label'>
				{$tag_text}
			</div>

			<div class='note'>{$ent['note']}</div>
			<div><a href='rec_y/edit?id={$ent['id']}' class='btn btn-warning btn-sm' >編集</a></div>
		</div>";

	echo "</div><hr>";




}


?>

<?php

	if(!empty($page_prev_link)){
		echo "<a href='{$page_prev_link}' class='btn btn-primary btn-lg'>前へ</a>\n";
	}

	if(!empty($page_next_link)){
		echo "<a href='{$page_next_link}' class='btn btn-primary btn-lg'>次へ</a>\n";
	}
?>






<br />





