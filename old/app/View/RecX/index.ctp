<?php
$this->CrudBase->setModelName('RecX');

// CSSファイルのインクルード
$cssList = $this->CrudBase->getCssList();
$this->assign('css', $this->Html->css($cssList));

// JSファイルのインクルード
$jsList = $this->CrudBase->getJsList();
$jsList[] = 'RecX/index'; // 当画面専用JavaScript
$this->assign('script', $this->Html->script($jsList,array('charset'=>'utf-8')));

?>




<h2>農業記録X</h2>

農業記録Xの検索閲覧および編集する画面です。<br>
<br>

<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("農業記録X");
	echo $this->Html->getCrumbs(" > ");
?>

<?php echo $this->element('CrudBase/crud_base_new_page_version');?>
<div id="err" class="text-danger"><?php echo $errMsg;?></div>


<div id="func_btns" >
	<button type="button" onclick="$('#detail_div').toggle(300);" class="btn btn-default">
		<span class="glyphicon glyphicon-cog"></span></button>
	<a href="<?php echo $home_url; ?>" class="btn btn-info" title="この画面を最初に表示したときの状態に戻します。（検索状態、列並べの状態を初期状態に戻します。）">
		<span class="glyphicon glyphicon-certificate"  ></span></a>
	<?php $this->CrudBase->newBtn();// 新規入力ボタンを作成 ?>
</div>
<div style="clear:both"></div>


<!-- 検索条件入力フォーム -->
<?php echo $this->Form->create('RecX', array('url' => true )); ?>
<div style="clear:both"></div>

<div id="detail_div" style="display:none">
	
	<?php 
	
	// --- CBBXS-1004
		$this->CrudBase->inputKjId($kjs);
		$this->CrudBase->inputKjText($kjs,'kj_rec_title','rec_title');
		$this->CrudBase->inputKjText($kjs,'kj_rec_date','rec_date');
		$this->CrudBase->inputKjText($kjs,'kj_note','note');
		$this->CrudBase->inputKjText($kjs,'kj_category_id2','category_id2');
		$this->CrudBase->inputKjText($kjs,'kj_category_id1','category_id1');
		$this->CrudBase->inputKjText($kjs,'kj_tags','tags');
		$this->CrudBase->inputKjText($kjs,'kj_photo_fn','photo_fn');
		$this->CrudBase->inputKjText($kjs,'kj_photo_dir','写真ディレクトリパス');
		$this->CrudBase->inputKjText($kjs,'kj_ref_url','参照URL');
		$this->CrudBase->inputKjNouislider($kjs,'nendo','nendo');
		$this->CrudBase->inputKjHidden($kjs,'kj_sort_no');
		$this->CrudBase->inputKjNouislider($kjs,'no_a','番号A');
		$this->CrudBase->inputKjNouislider($kjs,'no_b','no_b');
		$this->CrudBase->inputKjText($kjs,'kj_parent_id','親ID');
		$this->CrudBase->inputKjText($kjs,'kj_probe_id','サンプルID');
		$this->CrudBase->inputKjText($kjs,'kj_publish','公開フラグ');
		$this->CrudBase->inputKjText($kjs,'kj_create_date','create_date');
		$this->CrudBase->inputKjText($kjs,'kj_update_date','update_date');

	// --- CBBXE
	
	$this->CrudBase->inputKjLimit($kjs);
	echo $this->element('CrudBase/crud_base_cmn_inp');

	echo $this->Form->submit('検索', array('name' => 'search','class'=>'btn btn-success','div'=>false,));
	
	echo $this->element('CrudBase/crud_base_index');
	
	$csv_dl_url = $this->html->webroot . 'rec_x/csv_download';
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
<table id="rec_x_tbl" border="1"  class="table table-striped table-bordered table-condensed">

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
	$this->CrudBase->tdStr($ent,'rec_title');
	$this->CrudBase->tdPlain($ent,'rec_date');
	$this->CrudBase->tdNote($ent,'note');
	$this->CrudBase->tdPlain($ent,'category_id2');
	$this->CrudBase->tdPlain($ent,'category_id1');
	$this->CrudBase->tdStr($ent,'tags');
	$this->CrudBase->tdStr($ent,'photo_fn');
	$this->CrudBase->tdStr($ent,'photo_dir');
	$this->CrudBase->tdStr($ent,'ref_url');
	$this->CrudBase->tdPlain($ent,'nendo');
	$this->CrudBase->tdPlain($ent,'sort_no');
	$this->CrudBase->tdPlain($ent,'no_a');
	$this->CrudBase->tdPlain($ent,'no_b');
	$this->CrudBase->tdPlain($ent,'parent_id');
	$this->CrudBase->tdPlain($ent,'probe_id');
	$this->CrudBase->tdPlain($ent,'publish');
	$this->CrudBase->tdPlain($ent,'create_date');
	$this->CrudBase->tdPlain($ent,'update_date');

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
		<tr><td>rec_title: </td><td>
			<input type="text" name="rec_title" class="valid" value=""  maxlength="50" title="50文字以内で入力してください" />
			<label class="text-danger" for="rec_title"></label>
		</td></tr>

		<tr><td>rec_date: </td><td>
			<input type="text" name="rec_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="rec_date"></label>
		</td></tr>
		<tr><td>note： </td><td>
			<textarea name="note" ></textarea>
			<label class="text-danger" for="note"></label>
		</td></tr>
		<tr><td>category_id2: </td><td>
			<input type="text" name="category_id2" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="category_id2"></label>
		</td></tr>

		<tr><td>category_id1: </td><td>
			<input type="text" name="category_id1" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="category_id1"></label>
		</td></tr>

		<tr><td>tags: </td><td>
			<input type="text" name="tags" class="valid" value=""  maxlength="255" title="255文字以内で入力してください" />
			<label class="text-danger" for="tags"></label>
		</td></tr>

		<tr><td>photo_fn: </td><td>
			<input type="text" name="photo_fn" class="valid" value=""  maxlength="128" title="128文字以内で入力してください" />
			<label class="text-danger" for="photo_fn"></label>
		</td></tr>

		<tr><td>写真ディレクトリパス: </td><td>
			<input type="text" name="photo_dir" class="valid" value=""  maxlength="128" title="128文字以内で入力してください" />
			<label class="text-danger" for="photo_dir"></label>
		</td></tr>

		<tr><td>参照URL: </td><td>
			<input type="text" name="ref_url" class="valid" value=""  maxlength="2083" title="2083文字以内で入力してください" />
			<label class="text-danger" for="ref_url"></label>
		</td></tr>

		<tr><td>nendo: </td><td>
			<input type="text" name="nendo" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="nendo"></label>
		</td></tr>
		<tr><td>番号A: </td><td>
			<input type="text" name="no_a" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="no_a"></label>
		</td></tr>
		<tr><td>no_b: </td><td>
			<input type="text" name="no_b" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="no_b"></label>
		</td></tr>
		<tr><td>親ID: </td><td>
			<input type="text" name="parent_id" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="parent_id"></label>
		</td></tr>

		<tr><td>サンプルID: </td><td>
			<input type="text" name="probe_id" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="probe_id"></label>
		</td></tr>

		<tr><td>公開フラグ: </td><td>
			<input type="text" name="publish" class="valid" value=""  pattern="^[ -]?[0-9] $" maxlength="11" title="数値（整数数）を入力してください" />
			<label class="text-danger" for="publish"></label>
		</td></tr>
		<tr><td>create_date: </td><td>
			<input type="text" name="create_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="create_date"></label>
		</td></tr>
		<tr><td>update_date: </td><td>
			<input type="text" name="update_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="update_date"></label>
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
		<tr><td>rec_title: </td><td>
			<input type="text" name="rec_title" class="valid" value=""  maxlength="50" title="50文字以内で入力してください" />
			<label class="text-danger" for="rec_title"></label>
		</td></tr>

		<tr><td>rec_date: </td><td>
			<input type="text" name="rec_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="rec_date"></label>
		</td></tr>
		<tr><td>note： </td><td>
			<textarea name="note"></textarea>
			<label class="text-danger" for="note"></label>
		</td></tr>
		<tr><td>category_id2: </td><td>
			<input type="text" name="category_id2" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="category_id2"></label>
		</td></tr>

		<tr><td>category_id1: </td><td>
			<input type="text" name="category_id1" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="category_id1"></label>
		</td></tr>

		<tr><td>tags: </td><td>
			<input type="text" name="tags" class="valid" value=""  maxlength="255" title="255文字以内で入力してください" />
			<label class="text-danger" for="tags"></label>
		</td></tr>

		<tr><td>photo_fn: </td><td>
			<input type="text" name="photo_fn" class="valid" value=""  maxlength="128" title="128文字以内で入力してください" />
			<label class="text-danger" for="photo_fn"></label>
		</td></tr>

		<tr><td>写真ディレクトリパス: </td><td>
			<input type="text" name="photo_dir" class="valid" value=""  maxlength="128" title="128文字以内で入力してください" />
			<label class="text-danger" for="photo_dir"></label>
		</td></tr>

		<tr><td>参照URL: </td><td>
			<input type="text" name="ref_url" class="valid" value=""  maxlength="2083" title="2083文字以内で入力してください" />
			<label class="text-danger" for="ref_url"></label>
		</td></tr>

		<tr><td>nendo: </td><td>
			<input type="text" name="nendo" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="nendo"></label>
		</td></tr>
		<tr><td>番号A: </td><td>
			<input type="text" name="no_a" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="no_a"></label>
		</td></tr>
		<tr><td>no_b: </td><td>
			<input type="text" name="no_b" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="no_b"></label>
		</td></tr>
		<tr><td>親ID: </td><td>
			<input type="text" name="parent_id" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="parent_id"></label>
		</td></tr>

		<tr><td>サンプルID: </td><td>
			<input type="text" name="probe_id" class="valid" value=""  pattern="^[0-9] $" maxlength="11" title="数値（自然数）を入力してください" />
			<label class="text-danger" for="probe_id"></label>
		</td></tr>

		<tr><td>公開フラグ: </td><td>
			<input type="text" name="publish" class="valid" value=""  pattern="^[ -]?[0-9] $" maxlength="11" title="数値（整数数）を入力してください" />
			<label class="text-danger" for="publish"></label>
		</td></tr>
		<tr><td>create_date: </td><td>
			<input type="text" name="create_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="create_date"></label>
		</td></tr>
		<tr><td>update_date: </td><td>
			<input type="text" name="update_date" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="update_date"></label>
		</td></tr>

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
		

		<tr><td>農業記録X名: </td><td>
			<span class="rec_x_name"></span>
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
		

		<tr><td>農業記録X名: </td><td>
			<span class="rec_x_name"></span>
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























