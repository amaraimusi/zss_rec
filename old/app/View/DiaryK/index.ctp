<?php
$this->CrudBase->setModelName('DiaryK');

$this->assign('css', $this->Html->css(array(
		'clm_show_hide',				//列表示切替
		'ympicker_rap',					//年月ダイアログ
		'nouislider.min',				//数値範囲入力スライダー・noUiSlider
		'NoUiSliderRap',				//noUiSliderのラップ
		'CrudBase/index'				//CRUD indexページ共通
)));

$this->assign('script', $this->Html->script(array(
		'clm_show_hide',				//列表示切替
		'date_ex',						//日付関連関数集
		'jquery.ui.ympicker',			//年月選択ダイアログ
		'ympicker_rap',					//年月選択ダイアログのラップ
		'nouislider.min',				//数値範囲入力スライダー・noUiSlider
		'NoUiSliderRap',				//noUiSliderのラップ
		'AjaxCRUD',						//AjaxによるCRUD
		'livipage',						//ページ内リンク先プレビュー
		'ProcessWithMultiSelection',	//一覧のチェックボックス複数選択による一括処理
		'CrudBase/index',				//CRUD indexページ共通
		'DiaryK/index'					//当画面専用JavaScript
),array('charset'=>'utf-8')));
	
	
?>




<h2>日誌</h2>

日誌の検索閲覧および編集する画面です。<br>
<br>

<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("日誌");
	echo $this->Html->getCrumbs(" > ");
?>

<?php echo $this->element('CrudBase/crud_base_new_page_version');?>
<div id="err" class="text-danger"><?php echo $errMsg;?></div>


<!-- 検索条件入力フォーム -->
<div style="margin-top:5px">
	<?php 
		echo $this->Form->create('DiaryK', array('url' => true ));
	?>

	
	<div style="clear:both"></div>
	
	<div id="detail_div" style="display:none">
		
		<?php 
		
		// --- Start kj_input		

		$this->CrudBase->inputKjId($kjs); 
		$this->CrudBase->inputKjNengetu($kjs,'kj_diary_date','日誌日付'); 
		$this->CrudBase->inputKjText($kjs,'kj_diary_note','日誌',240);
		$this->CrudBase->inputKjLimit($kjs); 
		
		// --- End kj_input
		echo $this->Form->submit('検索', array('name' => 'search','class'=>'btn btn-success','div'=>false,));
		
		echo $this->element('CrudBase/crud_base_index');
		?>

	</div><!-- detail_div -->

	<div id="func_btns" >
		
			<div class="line-left">
				<button type="button" onclick="$('#detail_div').toggle(300);" class="btn btn-default btn-sm">
					<span class="glyphicon glyphicon-cog"></span>
				</button>

			</div>
			
			<div class="line-middle"></div>
			
			<div class="line-right">
				<?php 
					// 新規入力ボタンを作成
					$newBtnOption = array(
							'scene'=>'<span class="glyphicon glyphicon-plus"></span>追加'
					);
					$this->CrudBase->newBtn($newBtnOption);
				?>

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


<table id="diary_k_tbl" border="1"  class="table table-striped table-bordered table-condensed">

<thead>
<tr>
	<?php
	foreach($field_data as $ent){
		$row_order=$ent['row_order'];
		echo "<th class='{$ent['id']}'>{$pages['sorts'][$row_order]}</th>";
	}
	?>
	<th></th>
</tr>
</thead>
<tbody>
<?php

// td要素出力を列並モードに対応させる
$this->CrudBase->startClmSortMode($field_data);

foreach($data as $i=>$ent){


	
	echo "<tr id=i{$ent['id']}>";
	// --- Start field_table	

	$this->CrudBase->tdId($ent,'id');
	$this->CrudBase->tdPlain($ent,'diary_date');
	$this->CrudBase->tdStr($ent,'diary_note');
	
	// --- End field_table
	
	$this->CrudBase->tdsEchoForClmSort();// 列並に合わせてTD要素群を出力する
	
	// 行のボタン類
	echo "<td><div class='btn-group'>";
	$id = $ent['id'];
	$this->CrudBase->rowEditBtn($id);
	$this->CrudBase->rowPreviewBtn($id);
	$this->CrudBase->rowDeleteBtn($id);
	echo "</div></td>";
	
	echo "</tr>";
}

?>
</tbody>
</table>

<?php echo $this->element('CrudBase/crud_base_pwms'); // 複数選択による一括処理 ?>

<!-- 新規入力フォーム -->
<div id="ajax_crud_new_inp_form" class="panel panel-primary">

	<div class="panel-heading">
		<div class="pnl_head1">新規入力</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-primary btn-sm" onclick="ajaxCrud.closeForm('new_inp')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	<div class="panel-body">
	<table><tbody>

		<!-- Start ajax_form_new_start -->

		<tr><td>日誌日付: </td><td>
 			<input id="diary_date_n" type="text" name="diary_date" class="valid" value=""  pattern="\d{4}-\d{2}-\d{2}" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
 			<label class="text-danger" for="diary_date"></label>
 		</td></tr>

		<tr><td>日誌: </td><td>
 			<textarea name="diary_note" class="valid" maxlength="1000" title="1000文字以内で入力してください" style="width:400px;height:400px"></textarea>
 			<label class="text-danger" for="diary_note"></label>
 		</td></tr>


	<!-- Start ajax_form_new_end -->
	</tbody></table>
	

	<button type="button" onclick="newInpRegRap();" class="btn btn-success">
		<span class="glyphicon glyphicon-ok"></span>
	</button>

	</div><!-- panel-body -->
</div>



<!-- 編集フォーム -->
<div id="ajax_crud_edit_form" class="panel panel-primary">

	<div class="panel-heading">
		<div class="pnl_head1">編集</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-primary btn-sm" onclick="ajaxCrud.closeForm('edit')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	<div class="panel-body">
	<table><tbody>

		<!-- Start ajax_form_edit_start -->

		<tr><td>ID: </td><td>
 			<span class="id"></span>
 		</td></tr>

		<tr><td>日誌日付: </td><td>
 			<input  id="diary_date_e" type="text" name="diary_date" class="valid" value=""  pattern="\d{4}-\d{2}-\d{2}" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
 			<label class="text-danger" for="diary_date"></label>
 		</td></tr>

		<tr><td>日誌: </td><td>
 			<textarea name="diary_note" class="valid" maxlength="1000" title="1000文字以内で入力してください" style="width:400px;height:400px"></textarea>
 			<label class="text-danger" for="diary_note"></label>
 		</td></tr>


	<!-- Start ajax_form_edit_end -->
	</tbody></table>
	
	

	<button type="button"  onclick="editRegRap();" class="btn btn-success">
		<span class="glyphicon glyphicon-ok"></span>
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_edit_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_edit_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>



<!-- 削除フォーム -->
<div id="ajax_crud_delete_form" class="panel panel-danger">

	<div class="panel-heading">
		<div class="pnl_head1">削除</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-default btn-sm" onclick="ajaxCrud.closeForm('delete')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>日誌名: </td><td>
			<span class="diary_k_name"></span>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="ajaxCrud.deleteReg();" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span>　削除する
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_delete_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_delete_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>


<br />





<!-- ヘルプ用  -->
<input type="button" class="btn btn-info btn-sm" onclick="$('#help_x').toggle()" value="ヘルプ" />
<div id="help_x" class="help_x" style="display:none">
	<h2>ヘルプ</h2>

	<?php echo $this->element('CrudBase/crud_base_help');?>


</div>























