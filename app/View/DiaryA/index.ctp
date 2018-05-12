<?php
$this->CrudBase->setModelName('DiaryA');

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
		'DiaryA/index'					//当画面専用JavaScript
),array('charset'=>'utf-8')));
	
	
?>




<h2>日誌A</h2>

日誌Aの検索閲覧および編集する画面です。<br>
<br>

<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("日誌A");
	echo $this->Html->getCrumbs(" > ");
?>

<?php echo $this->element('CrudBase/crud_base_new_page_version');?>
<div id="err" class="text-danger"><?php echo $errMsg;?></div>


<!-- 検索条件入力フォーム -->
<div style="margin-top:5px">
	<?php 
		echo $this->Form->create('DiaryA', array('url' => true ));
	?>

	
	<div style="clear:both"></div>
	
	<div id="detail_div" style="display:none">
		
		<?php 
		
		// --- Start kj_input		

		$this->CrudBase->inputKjId($kjs); 
		$this->CrudBase->inputKjText($kjs,'kj_category','カテゴリ',120);
		$this->CrudBase->inputKjNengetu($kjs,'kj_diary_date','日誌日付'); 
		$this->CrudBase->inputKjText($kjs,'kj_diary_dt','日誌日時',120);
		$this->CrudBase->inputKjText($kjs,'kj_diary_note','日誌',240);
		$this->CrudBase->inputKjDeleteFlg($kjs); 
		$this->CrudBase->inputKjText($kjs,'kj_update_user','更新者',120);
		$this->CrudBase->inputKjText($kjs,'kj_ip_addr','IPアドレス',120);
		$this->CrudBase->inputKjCreated($kjs); 
		$this->CrudBase->inputKjModified($kjs); 
		$this->CrudBase->inputKjLimit($kjs); 
		
		// --- End kj_input
		echo $this->Form->submit('検索', array('name' => 'search','class'=>'btn btn-success','div'=>false,));
		
		echo $this->element('CrudBase/crud_base_index');
		?>

	</div><!-- detail_div -->

	<div id="func_btns" >
		
			<div style="text-align: right">
				<button type="button" onclick="$('#detail_div').toggle(300);" class="btn btn-default btn-sm">
					<span class="glyphicon glyphicon-cog"></span>
				</button>
				<button type="button" class="btn btn-warning btn-sm" onclick="niShowRap(this);">新規入力</button>

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

<!-- 一覧 -->
<table id="diary_a_tbl" border="1"  class="table table-striped table-bordered table-condensed">

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
	$this->CrudBase->tdStr($ent,'category');
	$this->CrudBase->tdPlain($ent,'diary_date');
	$this->CrudBase->tdStr($ent,'diary_dt');
	$this->CrudBase->tdStrRN($ent,'diary_note');
	$this->CrudBase->tdDeleteFlg($ent,'delete_flg');
	$this->CrudBase->tdStr($ent,'update_user');
	$this->CrudBase->tdStr($ent,'ip_addr');
	$this->CrudBase->tdPlain($ent,'created');
	$this->CrudBase->tdPlain($ent,'modified');
	
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
	<div class="err text-danger"></div>
	<table><tbody>

		<!-- Start ajax_form_new_start -->

		<tr><td>カテゴリ: </td><td>
			<select name="category" id="ni_category">
				<option value="mountain">mountain</option>
				<option value="diary" selected>diary</option>
				<option value="hatake">hatake</option>
				<option value="osie">osie</option>
				<option value="other">other</option>
			</select>
 		</td></tr>

		<tr><td>日誌日付: </td><td>
 			<input type="text" id="ni_diary_date" name="diary_date" class="valid" value=""  />
 			<label class="text-danger" for="diary_date"></label>
 		</td></tr>

		<tr><td>日誌日時: </td><td>
 			<input type="text" id="ni_diary_dt" name="diary_dt" class="valid" value=""  maxlength="" title="文字以内で入力してください" />
 			<label class="text-danger" for="diary_dt"></label>
 		</td></tr>

		<tr><td colspan="2">
 			<textarea name="diary_note" class="valid" maxlength="1000" title="1000文字以内で入力してください" style="width:100%;height:200px"></textarea>
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
	<div class="err text-danger"></div>
	<table><tbody>

		<!-- Start ajax_form_edit_start -->

		<tr><td>ID: </td><td>
 			<span class="id"></span>
 		</td></tr>

		<tr><td>カテゴリ: </td><td>
			<select name="category" id="ni_category">
				<option value="mountain">mountain</option>
				<option value="diary" selected>diary</option>
				<option value="hatake">hatake</option>
				<option value="osie">osie</option>
				<option value="other">other</option>
			</select>
 		</td></tr>

		<tr><td>日誌日付: </td><td>
 			<input type="text" id="edit_diary_date" name="diary_date" class="valid" value=""   />
 			<label class="text-danger" for="diary_date"></label>
 		</td></tr>

		<tr><td>日誌日時: </td><td>
 			<input type="text" id="edit_diary_dt" name="diary_dt" class="valid" value=""  maxlength="" title="文字以内で入力してください" />
 			<label class="text-danger" for="diary_dt"></label>
 		</td></tr>

		<tr><td colspan="2">
 			<textarea name="diary_note" class="valid" maxlength="1000" title="1000文字以内で入力してください" style="width:100%;height:200px"></textarea>
 			<label class="text-danger" for="diary_note"></label>
 		</td></tr>

		<tr><td>無効： </td><td>
 			<input type="checkbox" name="delete_flg" class="valid"  />
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
		

		<tr><td>日誌A名: </td><td>
			<span class="diary_a_name"></span>
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























