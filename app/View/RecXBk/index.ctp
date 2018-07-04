<?php
	
	$this->assign('css', $this->Html->css(array(
			'jquery-ui.min',
			'CrudBase/common',				//CRUD共通
			'CrudBase/index',				//CRUD index共通
	)));
	
	$this->assign('script', $this->Html->script(array(
			'jquery-ui.min',
			'SectionEditK.js',
			'RecX/index'					//当画面専用JavaScript
	),array('charset'=>'utf-8')));
?>

<script>
	//詳細検索フォームの表示切替
	function show_kj_detail(){
		$("#kjs2").toggle();
		$('.season_btn_toggle').toggle();
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


	

	.container {
	    max-width: 100%;
	}
	.section{
		font-size:16px;
	}
	.section label{
		color:#c1544f;
	}
	
	.rec_title{
		display:inline-block;
		font-weight:bold;
		border:none;
	}


	.note_label{
		color:#7878bc
	}
	.rec_date{
		color:#037900
	}
	.note{
		padding:10px;
	}
	.btn1{
		float:left;
	}
	.season_btn_toggle{
		display:none;
	}
	.sec_footer{
		color:#29400a;
	}
	.sec_footer div{
		display:inline-block;
		margin-right:15px;
	}
	.sec_inp_edit{
		display:inline-block;
		min-width:50px;
		border:solid 2px #61bdd1;
	}
	

</style>



<?php
	$this->Html->addCrumb("トップ", "/top");
	$this->Html->addCrumb("記録X");
	echo $this->Html->getCrumbs(" > ");
?>
<br>
<div style="color:red"><?php echo $errMsg;?></div>

<?php echo $this->Form->create('RecX', array('url' => true ));?>

<div>
	<div class="btn1">
		
		<button type="button"  onclick="show_kj_detail()" class="btn btn-primary">
			<span class="glyphicon glyphicon-align-justify"></span>
		</button>
	</div>
	<div class="btn1">
		<?php 
			echo $this->Form->submit('最新30件', array(
				'name' => 'new30',
				'class'=>'btn btn-success',
				'div'=> false,
				'style'=>'float:none;',
				));
		?>
	</div>
	
	<?php 
	
		$seasonBtnCnt = count($seasonBtnData);
		$counter=0;
		foreach($seasonBtnData as $seasonBtn){
			echo '<div class="btn1">';
			
			$season_btn_toggle='';
			if($seasonBtnCnt - $counter > 4){
				$season_btn_toggle='season_btn_toggle ';
			}
			echo $this->Form->submit($seasonBtn['label_name'], array(
					'name' => $seasonBtn['name'],
					'class'=>$season_btn_toggle.'btn btn-success',
					'div'=> false,
					'rec_title'=>$seasonBtn['f_date'].'から三ヶ月間',
					'style'=>'float:none;',
			));
			echo '</div>';
			$counter++;
		}
	?>

	
	<div class="btn1 btn-group" style="margin-left:10px">
	
		<?php 
		if($auth_flg==1){
			echo "<a href='{$this->Html->webroot}users/logout' class='btn btn-primary btn-xs'>ログアウト</a>";
			echo "<a href = '/zss_rec/probe?page_no=0&limit=50&sort=Probe.id&sort_type=1' class='btn btn-info btn-xs' target='blank' >固体</a>";
		}
		if($auth_flg==0 && $admin_link_show_flg==1){
			echo "<a href='{$this->Html->webroot}users/login' class='btn btn-primary btn-xs'>ログイン</a>";
		}
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
					'id' => 'kj_rec_date1',
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
					'id' => 'kj_rec_date2',
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- 日付（終） --',
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
					'id' => 'kj_rec_title',
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
					'id' => 'kj_note',
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
					'id' => 'kj_tags',
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
					'id' => 'kj_tag_id',
					'value' => $kjs['kj_tag_id'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- タグID --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			?>
		</div>

		<div class="col-md-2">
			<?php

			echo $this->Form->input('kj_probe_id', array(
					'id' => 'kj_probe_id',
					'value' => $kjs['kj_probe_id'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- 個別ＩＤ --',
					'div'=> false,
					'style'=>'float:none;width:100%',
			));
			
			?>
		</div>

		
		<?php 
		if($auth_flg == 1){
			
			echo $this->Form->input('kj_category_id1', array(
					'id' => 'kj_category_id1',
					'value' => $kjs['kj_category_id1'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- カテゴリID1 --',
					'div'=> false,
					
			));
			echo $this->Form->input('kj_category_id2', array(
					'id' => 'kj_category_id2',
					'value' => $kjs['kj_category_id2'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- カテゴリID2 --',
					'div'=> false,
					
			));
			echo $this->Form->input('kj_no_a', array(
					'id' => 'kj_no_a',
					'value' => $kjs['kj_no_a'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- 番号Ａ --',
					'div'=> false,
					
			));
			echo $this->Form->input('kj_no_b', array(
					'id' => 'kj_no_b',
					'value' => $kjs['kj_no_b'],
					'type' => 'text',
					'label' => false,
					'placeholder' => '-- 番号Ｂ --',
					'div'=> false,
					
			));
		}
		?>
		

		<div class="col-md-3">
				<?php
			 //表示件数
			 echo $this->Form->input('kj_limit', array(
					'id' => 'kj_limit',
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


		<div class="kj_div" style="margin-top:5px">
			<input type="button" value="リセット" title="検索入力を初期に戻します" onclick="resetKjs()" class="btn btn-primary btn-xs" />
		</div>


		<a href="?ini=1&sc=1" class="btn btn-danger btn-xs">セッションクリア</a>


	</div>

	<table border="1"  class="table table-striped table-bordered table-condensed">

		<thead>
		<tr>

			<th><?php echo $pages['sorts']['RecX.id']; ?></th>
			<th><?php echo $pages['sorts']['RecX.rec_date']; ?></th>
		</tr>
		</thead>
	</table>

	</div>
</div><!-- $kjs2 -->
<?php echo $this->Form->end()?>


<input id="auth_flg" type="hidden" value="<?php echo $auth_flg; ?>" />
<div id="defKjsJson" style="display:none"><?php echo $defKjsJson ?></div>
<br />




<div style="margin-bottom:5px">
	<?php echo $pages['page_index_html'];//ページ目次 ?>
</div>

<?php

	if(!empty($pages['page_prev_link'])){
		echo "<a href='{$pages['page_prev_link']}' class='btn btn-primary btn-lg'>前へ</a>\n";
	}

	if(!empty($pages['page_next_link'])){
		echo "<a href='{$pages['page_next_link']}' class='btn btn-primary btn-lg'>次へ</a>\n";
	}
?>

<?php
foreach($data as $i=>$ent){

	// 画像パス
	$img_fn="";
 	if(!empty($ent['photo_fn'])){
 		$img_fn='/photos/halther'.$ent['photo_dir'].$ent['photo_fn'];
 	}

 	
 	// タグ要素
 	$tagsElm = $this->RecX->createTagsElm($auth_flg,$ent['Tags']);
 	
 	// 個番要素
 	$probeElm = $this->RecX->createProbeElm($auth_flg,$ent['probe_id'],$ent['probe_name'],$ent['probe_note']);
 	
 	// 編集モード用の入力要素CSS
 	$sie = '';
 	if(!empty($auth_flg)){
 		$sie = 'sec_inp_edit';
 	}
 	
 	echo 	"<div id='section{$ent['id']}' class='section row'>".
 			" 	<div class='col-md-6'>".
 			" 		<img class='img-responsive' src='{$img_fn}'>".
 			" 	</div>".
 			" 	<div class='col-md-6'>".
 			" 		<div class='note_label'>ID:{$ent['id']}".
 			" 		<input type='hidden' class='rec_id' value='{$ent['id']}' />".
 			" 			<span class='rec_date'>{$ent['rec_date']}　</span>".
 			" 			<div class='rec_title {$sie}'>{$ent['rec_title']}</div>".
 			" 		</div>".
 			" ".
 			" 		<div class='note {$sie}'>{$ent['note']}</div>".
 			" 		<div class='sec_footer'>".
 			" 			<div >{$tagsElm}</div>".
 			" 			{$probeElm}".
 			" 		</div>".
 			" 	</div>".
 			" </div><hr>";


}


?>



<?php

	if(!empty($pages['page_prev_link'])){
		echo "<a href='{$pages['page_prev_link']}' class='btn btn-primary btn-lg'>前へ</a>\n";
	}

	if(!empty($pages['page_next_link'])){
		echo "<a href='{$pages['page_next_link']}' class='btn btn-primary btn-lg'>次へ</a>\n";
	}
?>






<br />





