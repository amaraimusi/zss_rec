<?php
$this->CrudBase->setModelName('DiaryA');

// CSSファイルのインクルード
$cssList = $this->CrudBase->getCssList();
$this->assign('css', $this->Html->css($cssList));

// JSファイルのインクルード
$jsList = $this->CrudBase->getJsList();
$jsList[] = 'DiaryA/index'; // 当画面専用JavaScript
$this->assign('script', $this->Html->script($jsList,array('charset'=>'utf-8')));

?>




<h2>日誌Ａ</h2>


<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("日誌Ａ");
	echo $this->Html->getCrumbs(" > ");
?>



<?php echo $this->element('CrudBase/crud_base_new_page_version');?>
<div id="err" class="text-danger"><?php echo $errMsg;?></div>


<div id="func_btns" class="btn-group" >
	<button type="button" onclick="$('#detail_div').toggle(300);" class="btn btn-default">
		<span class="glyphicon glyphicon-cog"></span></button>
		
	<a href="<?php echo $home_url; ?>" class="btn btn-info" title="この画面を最初に表示したときの状態に戻します。（検索状態、列並べの状態を初期状態に戻します。）">
		<span class="glyphicon glyphicon-certificate"  ></span></a>
		
	<button id="table_transform_tbl_mode" type="button" class="btn btn-default" onclick="tableTransform(0)" style="display:none">
		<span class="glyphicon glyphicon-th" title="一覧の変形・テーブルモード"></span></button>
		
	<button id="table_transform_div_mode" type="button" class="btn btn-default" onclick="tableTransform(1)" >
		<span class="glyphicon glyphicon-th-large" title="一覧の変形・区分モード"></span></button>
		
	<button type="button" class="btn btn-warning" onclick="newInpShow(this);">
		<span class="glyphicon glyphicon-plus-sign" title="新規入力"></span></button>
</div>
<div style="clear:both"></div>


<!-- 検索条件入力フォーム -->
<?php echo $this->Form->create('DiaryA', array('url' => true )); ?>
<div style="clear:both"></div>

<div id="detail_div" style="display:none">
	
	<?php 
	
	// --- CBBXS-1004
		$this->CrudBase->inputKjId($kjs);
		$this->CrudBase->inputKjText($kjs,'kj_category','カテゴリ');
		$this->CrudBase->inputKjMoDateRng($kjs,'kj_diary_date','日誌日付');
		$this->CrudBase->inputKjText($kjs,'kj_diary_dt','日誌日時');
		$this->CrudBase->inputKjText($kjs,'kj_diary_note','日誌');
		$this->CrudBase->inputKjDeleteFlg($kjs);
		$this->CrudBase->inputKjText($kjs,'kj_update_user','更新者');
		$this->CrudBase->inputKjText($kjs,'kj_ip_addr','IPアドレス');
		$this->CrudBase->inputKjCreated($kjs);
		$this->CrudBase->inputKjModified($kjs);

	// --- CBBXE
	
	$this->CrudBase->inputKjLimit($kjs);
	echo $this->element('CrudBase/crud_base_cmn_inp');

	echo $this->Form->submit('検索', array('name' => 'search','class'=>'btn btn-success','div'=>false,));
	
	echo $this->element('CrudBase/crud_base_index');
	
	$csv_dl_url = $this->html->webroot . 'diary_a/csv_download';
	$this->CrudBase->makeCsvBtns($csv_dl_url);
	?>

</div><!-- detail_div -->
<?php echo $this->Form->end()?>


<div style="margin-top:8px;">
	<div style="display:inline-block">
		<?php echo $pages['page_index_html'];//ページ目次 ?>
	</div>
	<div style="display:inline-block">件数:<?php echo $data_count ?></div>
	<div style="display:inline-block">
		<a href="#help_lists" class="livipage btn btn-info btn-xs" title="ヘルプ"><span class="glyphicon glyphicon-question-sign"></span></a></div>
</div>

<div id="crud_base_auto_save_msg" style="height:20px;" class="text-success"></div>
<!-- 一覧テーブル -->
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
	// CBBXS-1005
	$this->CrudBase->tdId($ent,'id',array('checkbox_name'=>'pwms'));
	$this->CrudBase->tdStr($ent,'category');
	$this->CrudBase->tdPlain($ent,'diary_date');
	$this->CrudBase->tdPlain($ent,'diary_dt');
	$this->CrudBase->tdNote($ent,'diary_note');
	$this->CrudBase->tdDeleteFlg($ent,'delete_flg');
	$this->CrudBase->tdStr($ent,'update_user');
	$this->CrudBase->tdStr($ent,'ip_addr');
	$this->CrudBase->tdPlain($ent,'created');
	$this->CrudBase->tdPlain($ent,'modified');

	// CBBXE
	
	$this->CrudBase->tdsEchoForClmSort();// 列並に合わせてTD要素群を出力する
	
	// 行のボタン類
	echo "<td><div class='btn-group' style='display:inline-block'>";
	$id = $ent['id'];
	echo  "<input type='button' value='↑↓' onclick='rowExchangeShowForm(this)' class='row_exc_btn btn btn-info btn-xs' />";
	$this->CrudBase->rowEditBtn($id);
	$this->CrudBase->rowPreviewBtn($id);
	$this->CrudBase->rowCopyBtn($id);
	echo "</div>&nbsp;";
	echo "<div style='display:inline-block'>";
	$this->CrudBase->rowDeleteBtn($ent); // 削除ボタン
	$this->CrudBase->rowEnabledBtn($ent); // 有効ボタン
	echo "&nbsp;";
	$this->CrudBase->rowEliminateBtn($ent);// 抹消ボタン
	echo "</div>";
	echo "</td>";
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
			<button type="button" class="btn btn-primary btn-sm" onclick="closeForm('new_inp')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	<div class="panel-body">
	<div class="err text-danger"></div>
	
	<div style="display:none">
    	<input type="hidden" name="form_type">
    	<input type="hidden" name="row_index">
    	<input type="hidden" name="sort_no">
	</div>
	<table><tbody>

		<!-- CBBXS-1006 -->


		<tr><td>日誌日付: </td><td>
			<input id="new_inp_diary_date" type="text" name="diary_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2})" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
			<label class="text-danger" for="diary_date"></label>
		</td></tr>

		<tr><td>日誌： </td><td>
			<textarea name="diary_note" style="width:400px;height:300px"></textarea>
			<label class="text-danger" for="diary_note"></label>
		</td></tr>

		<!-- CBBXE -->
	</tbody></table>
	

	<button type="button" onclick="newInpReg();" class="btn btn-success">
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
			<button type="button" class="btn btn-primary btn-sm" onclick="closeForm('edit')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	<div style="display:none">
    	<input type="hidden" name="sort_no">
	</div>
	<div class="panel-body">
	<div class="err text-danger"></div>
	<table><tbody>

		<!-- CBBXS-1007 -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>


		<tr><td>日誌日付: </td><td>
			<input id="edit_diary_date" type="text" name="diary_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2})" title="日付形式（Y-m-d）で入力してください(例：2012-12-12)" />
			<label class="text-danger" for="diary_date"></label>
		</td></tr>

		<tr><td>日誌： </td><td>
			<textarea name="diary_note" style="width:400px;height:300px"></textarea>
			<label class="text-danger" for="diary_note"></label>
		</td></tr>
		<tr><td>削除：<input type="checkbox" name="delete_flg" class="valid"  /> </td><td></td></tr>

		<!-- CBBXE -->
	</tbody></table>
	
	

	<button type="button"  onclick="editReg();" class="btn btn-success">
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
			<button type="button" class="btn btn-default btn-sm" onclick="closeForm('delete')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>日誌Ａ名: </td><td>
			<span class="diary_a_name"></span>
		</td></tr>
		
		<tr><td>画像ファイル: </td><td>
			<label for="img_fn"></label><br>
			<img src="" class="img_fn" width="80" height="80" ></img>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="deleteReg();" class="btn btn-danger">
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



<!-- 抹消フォーム -->
<div id="ajax_crud_eliminate_form" class="panel panel-danger">

	<div class="panel-heading">
		<div class="pnl_head1">抹消</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-default btn-sm" onclick="closeForm('eliminate')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>日誌Ａ名: </td><td>
			<span class="diary_a_name"></span>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="eliminateReg();" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span>　抹消する
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_eliminate_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_eliminate_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>


<br />

<!-- 埋め込みJSON -->
<div style="display:none">
	
	<!-- CBBXS-1022 -->

	<!-- CBBXE -->
</div>



<!-- ヘルプ用  -->
<input type="button" class="btn btn-info btn-sm" onclick="$('#help_x').toggle()" value="ヘルプ" />
<div id="help_x" class="help_x" style="display:none">
	<h2>ヘルプ</h2>

	<?php echo $this->element('CrudBase/crud_base_help');?>

</div>























