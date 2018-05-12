<?php
$this->CrudBase->setModelName('Probe');

$this->assign('css', $this->Html->css(array(
		'clm_show_hide',				//列表示切替
		'ympicker_rap',					//年月ダイアログ
		'nouislider.min',				//数値範囲入力スライダー・noUiSlider
		'NoUiSliderRap',				//noUiSliderのラップ
		'CrudBase/common',				//CRUD共通
		'CrudBase/index'				//CRUD indexページ共通
)));

$this->assign('script', $this->Html->script(array(
		'clm_show_hide',				//列表示切替
		'date_ex',						//日付関連関数集
		'jquery.ui.ympicker',			//年月選択ダイアログ
		'ympicker_rap',					//年月選択ダイアログのラップ
		'nouislider.min',				//数値範囲入力スライダー・noUiSlider
		'NoUiSliderRap',				//noUiSliderのラップ
		'livipage',						//ページ内リンク先プレビュー
		'CrudBase/index',				//CRUD indexページ共通
		'Probe/index'					//当画面専用JavaScript
),array('charset'=>'utf-8')));
	
	
?>




<h2>個体</h2>

CakePHP Ver: <?php echo Configure::version(); ?><br>
CrudBase Ver: <?php echo $version; ?><br>

個体の検索閲覧および編集する画面です。<br>
<br>

<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("個体");
	echo $this->Html->getCrumbs(" > ");
?>
<br>
<div style="color:red"><?php echo $errMsg;?></div>

<div style="margin-top:5px">
	<?php 
		echo $this->Form->create('Probe', array('url' => true ));
		
	
	?>


	<div id="kjs1">
	</div><!-- kjs1 -->
	
	<div style="clear:both"></div>
	
	<div id="kjs2">
		
		
		<?php 
		
		// --- Start kj_input		

		$this->CrudBase->inputKjId($kjs); 
		$this->CrudBase->inputKjText($kjs,'kj_probe_name','probe_name',240);
		$this->CrudBase->inputKjText($kjs,'kj_hatake_id','畑ID',120);
		$this->CrudBase->inputKjText($kjs,'kj_rx','畑相対位置X',120);
		$this->CrudBase->inputKjText($kjs,'kj_ry','畑相対位置Y',120);
		$this->CrudBase->inputKjText($kjs,'kj_probe_note','probe_note',240);
		$this->CrudBase->inputKjDeleteFlg($kjs); 
		$this->CrudBase->inputKjModified($kjs); 
		$this->CrudBase->inputKjLimit($kjs); 
		
		// --- End kj_input
		
		echo $this->element('CrudBase/crud_base_index');
		?>

		


		


		

		

	</div><!-- kjs2 -->

	<div id="func_btns">
		<div class="kj_div">
			<div class="btn-group">
				<input type="button" value="ﾘｾｯﾄ" title="検索入力を初期に戻します" onclick="resetKjs()" class="btn btn-primary" />
				<?php echo $this->Form->submit('検索', array(
						'name' => 'search',
						'class'=>'btn btn-success',
						'div'=>false,
				));
				?>
			</div>
		</div>
		<div class="kj_div" style="margin-top:8px">
			<div class="btn-group">
				<input type="button" value="詳細" onclick="show_kj_detail()" class="btn btn-primary btn-sm" />
				<a href="<?php $this->html->webroot ?>probe/csv_download" class="btn btn-success btn-sm" title="データベース型フォーマットのCSVファイル">
					CSV1<span class="glyphicon glyphicon-download"></span>
				</a>
				<a href="<?php $this->html->webroot ?>probe/csv_download2" class="btn btn-success btn-sm" title="列表示切替、列並替に対応したCSVファイル">
					CSV2<span class="glyphicon glyphicon-download"></span>
				</a>
				<a href="<?php $this->html->webroot ?>probe/csv_download3" class="btn btn-success btn-sm" title="Excel型フォーマットのCSVファイル">
					CSV3<span class="glyphicon glyphicon-download"></span>
				</a>
				<a href="<?php $this->Html->webroot ?>probe/edit" class="btn btn-warning btn-sm">
					新規入力
				</a>
				<a href="#help_csv" class="livipage btn btn-info btn-sm" title="ヘルプ">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
				
			</div>
		</div>




	</div>
	<div style="clear:both"></div>
	<?php echo $this->Form->end()?>

	
</div>


<br />

<div id="total_div">
	<table><tr>
		<td>件数:<?php echo $data_count ?></td>
		<td><a href="#help_lists" class="livipage btn btn-info btn-xs" title="ヘルプ"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	</tr></table>
</div>


<div style="margin-bottom:5px">
	<?php echo $pages['page_index_html'];//ページ目次 ?>
</div>


<table id="probe_tbl" border="1"  class="table table-striped table-bordered table-condensed">

<thead>
<tr>

	<?php

		foreach($field_data as $ent){
			$row_order=$ent['row_order'];
			echo "<th>{$pages['sorts'][$row_order]}</th>";
		}

	?>

</tr>
</thead>
<tbody>
<?php

foreach($data as $i=>$ent){

	echo "<tr>";
	// --- Start field_table	

	$this->CrudBase->tdId($ent['id']);
	$this->CrudBase->tdNote($ent['probe_name']);
	$this->CrudBase->tdPlain($ent['hatake_id']);
	$this->CrudBase->tdPlain($ent['rx']);
	$this->CrudBase->tdPlain($ent['ry']);
	$this->CrudBase->tdNote($ent['probe_note']);
	$this->CrudBase->tdDeleteFlg($ent['delete_flg']);
	$this->CrudBase->tdPlain($ent['modified']);
	
	// --- End field_table
	echo "</tr>";
}

?>
</tbody>
</table>


<br />




<!-- ヘルプ用  -->
<input type="button" class="btn btn-info btn-sm" onclick="$('#help_x').toggle()" value="ヘルプ" />
<div id="help_x" class="help_x" style="display:none">
	<h2>ヘルプ</h2>

	<?php echo $this->element('CrudBase/crud_base_help');?>


</div>



