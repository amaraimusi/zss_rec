<?php
$this->CrudBase->setModelName('Knowledge');

// CSSファイルのインクルード
$cssList = $this->CrudBase->getCssList();
$cssList[] = 'Knowledge/index'; // 当画面専用CSS
$this->assign('css', $this->Html->css($cssList));

// JSファイルのインクルード
$jsList = $this->CrudBase->getJsList();
$jsList[] = 'Knowledge/LearnCounter.js';
$jsList[] = 'Knowledge/index'; // 当画面専用JavaScrip
$jsList[] = 'CrudBase/AjaxLoginWithCake.js';
$this->assign('script', $this->Html->script($jsList,array('charset'=>'utf-8')));

?>

<div id="h2_div">
	<div><h2>心得</h2></div>
	<div id="ajax_login_with_cake"></div><!-- 認証用 -->
	<div id="btn_mode_m" style="display:none"><a href="?a=1&mode=2" class="btn btn-warning btn-xs" >管理者モード</a></div>
	<div id="btn_mode_l" style="display:none"><a href="?a=1&mode=1" class="btn btn-warning btn-xs" >覚えモード</a></div>
</div>

<div id="learn_index" class="btn-group" style="display:none">
	<a href="?a=1" class="btn btn-info btn-xs" >全て</a>
	<a href="?a=1&kj_kl_category=1" class="btn btn-info btn-xs" >霊的</a>
	<a href="?a=1&kj_kl_category=2" class="btn btn-info btn-xs" >一般</a>
	<a href="?a=1&kj_kl_category=4" class="btn btn-info btn-xs" >文章</a>
	<a href="?a=1&kj_kl_category=5" class="btn btn-info btn-xs" >宣教</a>
</div>


<div id="err" class="text-danger"><?php echo $errMsg;?></div>

<div id="func_div" style="display:none">
<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("心得メイン");
	echo $this->Html->getCrumbs(" > ");
?>

<?php echo $this->element('CrudBase/crud_base_new_page_version');?>


<div id="func_btns" >
	<button type="button" onclick="$('#detail_div').toggle(300);" class="btn btn-default">
		<span class="glyphicon glyphicon-cog"></span></button>
	<a href="<?php echo $home_url; ?>" class="btn btn-info" title="この画面を最初に表示したときの状態に戻します。（検索状態、列並べの状態を初期状態に戻します。）">
		<span class="glyphicon glyphicon-certificate"  ></span></a>
	<?php $this->CrudBase->newBtn();// 新規入力ボタンを作成 ?>
	<a href="kl_category" class="btn btn-primary btn-sm" >心得カテゴリー</a>
	
</div>
<div style="clear:both"></div>


<!-- 検索条件入力フォーム -->
<?php echo $this->Form->create('Knowledge', array('url' => true )); ?>
<div style="clear:both"></div>

<div id="detail_div" style="display:none">
	
	<?php 
	
	// --- CBBXS-1004
		$this->CrudBase->inputKjId($kjs);
		$this->CrudBase->inputKjText($kjs,'kj_kl_text','心得テキスト');
		$this->CrudBase->inputKjText($kjs,'kj_xid','XID');
		$this->CrudBase->inputKjSelect($kjs,'kj_kl_category','ネコ種別',$klCategoryList); 
		$this->CrudBase->inputKjText($kjs,'kj_contents_url','内容URL');
		$this->CrudBase->inputKjText($kjs,'kj_doc_name','文献名');
		$this->CrudBase->inputKjText($kjs,'kj_doc_text','文献テキスト');
		$this->CrudBase->inputKjText($kjs,'kj_dtm','学習日時');
		$this->CrudBase->inputKjText($kjs,'kj_next_dtm','次回日時');
		$this->CrudBase->inputKjNouislider($kjs,'level','学習レベル');
		$this->CrudBase->inputKjHidden($kjs,'kj_sort_no');
		$this->CrudBase->inputKjDeleteFlg($kjs);
		$this->CrudBase->inputKjText($kjs,'kj_update_user','更新ユーザー');
		$this->CrudBase->inputKjText($kjs,'kj_ip_addr','IPアドレス');
		$this->CrudBase->inputKjCreated($kjs);
		$this->CrudBase->inputKjModified($kjs);

	// --- CBBXE
	
	$this->CrudBase->inputKjLimit($kjs);
	echo $this->element('CrudBase/crud_base_cmn_inp');

	echo $this->Form->submit('検索', array('name' => 'search','class'=>'btn btn-success','div'=>false,));
	
	echo $this->element('CrudBase/crud_base_index');
	
	$csv_dl_url = $this->html->webroot . 'knowledge/csv_download';
	$this->CrudBase->makeCsvBtns($csv_dl_url);
	?>

</div><!-- detail_div -->
<?php echo $this->Form->end()?>

</div><!-- func_div -->

<div style="margin-top:8px;">
	<div style="display:inline-block">
		<?php echo $pages['page_index_html'];//ページ目次 ?>
	</div>
	<div style="display:inline-block">件数:<?php echo $data_count ?></div>
</div>


<div id="crud_base_auto_save_msg" style="height:20px;" class="text-success"></div>
<!-- 一覧テーブル -->
<table id="knowledge_tbl" border="1"  class="table table-striped table-bordered table-condensed">

<thead style="display:none">
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

foreach($data as $i=>$ent){

	echo "<tr id=i{$ent['id']}>";
	// CBBXS-1005
	$this->CrudBase->tdId($ent,'id',array('checkbox_name'=>'pwms'));
	$this->Knowledge->tdKlText($ent);
	$this->CrudBase->tdStr($ent,'xid');
	$this->CrudBase->tdList($ent,'kl_category',$klCategoryList);
	$this->CrudBase->tdStr($ent,'contents_url');
	$this->CrudBase->tdStr($ent,'doc_name');
	$this->CrudBase->tdNote($ent,'doc_text');
	$this->CrudBase->tdPlain($ent,'dtm');
	$this->CrudBase->tdPlain($ent,'next_dtm');
	$this->CrudBase->tdPlain($ent,'level');
	$this->CrudBase->tdPlain($ent,'sort_no');
	$this->CrudBase->tdDeleteFlg($ent,'delete_flg');
	$this->CrudBase->tdStr($ent,'update_user');
	$this->CrudBase->tdStr($ent,'ip_addr');
	$this->CrudBase->tdPlain($ent,'created');
	$this->CrudBase->tdPlain($ent,'modified');
	// CBBXE
	
	
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

<div id='pwms_w' style="display:none">
	<?php echo $this->element('CrudBase/crud_base_pwms'); // 複数選択による一括処理 ?>
</div>

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
    	<input type="hidden" name="xid" value='' >
    	<input type="hidden" name="dtm" value='' >
    	<input type="hidden" name="next_dtm" value='' >
    	<input type="hidden" name="level" value=0 >
	</div>
	<table><tbody>

		<!-- CBBXS-1006 -->
		<tr><td>心得テキスト： </td><td>
			<textarea name="kl_text"></textarea>
			<label class="text-danger" for="kl_text"></label>
		</td></tr>

		<tr><td>カテゴリ: </td><td>
			<?php $this->CrudBase->selectX('kl_category',null,$klCategoryList,null);?>
			<label class="text-danger" for="kl_category"></label>
		</td></tr>

		<tr><td>内容URL: </td><td>
			<input type="text" name="contents_url" class="valid" value=""  maxlength="1024" title="1024文字以内で入力してください" />
			<label class="text-danger" for="contents_url"></label>
		</td></tr>

		<tr><td>文献名: </td><td>
			<input type="text" name="doc_name" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="doc_name"></label>
		</td></tr>

		<tr><td>文献テキスト： </td><td>
			<textarea name="doc_text"></textarea>
			<label class="text-danger" for="doc_text"></label>
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
	<button type="button"  onclick="editReg();" class="btn btn-success">
		<span class="glyphicon glyphicon-ok"></span>
	</button>
	<table><tbody>

		<!-- CBBXS-1007 -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		<tr><td>心得テキスト： </td><td>
			<textarea name="kl_text"></textarea>
			<label class="text-danger" for="kl_text"></label>
		</td></tr>
		<tr><td>XID: </td><td>
			<input type="text" name="xid" class="valid" value=""  maxlength="32" title="32文字以内で入力してください" />
			<label class="text-danger" for="xid"></label>
		</td></tr>

		<tr><td>カテゴリ: </td><td>
			<?php $this->CrudBase->selectX('kl_category',null,$klCategoryList,null);?>
			<label class="text-danger" for="kl_category"></label>
		</td></tr>

		<tr><td>内容URL: </td><td>
			<input type="text" name="contents_url" class="valid" value=""  maxlength="1024" title="1024文字以内で入力してください" />
			<label class="text-danger" for="contents_url"></label>
		</td></tr>

		<tr><td>文献名: </td><td>
			<input type="text" name="doc_name" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="doc_name"></label>
		</td></tr>

		<tr><td>文献テキスト： </td><td>
			<textarea name="doc_text"></textarea>
			<label class="text-danger" for="doc_text"></label>
		</td></tr>
		<tr><td>学習日時: </td><td>
			<input type="text" name="dtm" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="dtm"></label>
		</td></tr>
		<tr><td>次回日時: </td><td>
			<input type="text" name="next_dtm" class="valid" value=""  pattern="([0-9]{4})(/|-)([0-9]{1,2})(/|-)([0-9]{1,2}) [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}" title="日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)" />
			<label class="text-danger" for="next_dtm"></label>
		</td></tr>
		<tr><td>学習レベル: </td><td>
			<input type="text" name="level" class="valid" value=""  pattern="^[+-]?([0-9]*[.])?[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="level"></label>
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
		

		<tr><td>心得メイン名: </td><td>
			<span class="knowledge_name"></span>
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
		

		<tr><td>心得メイン名: </td><td>
			<span class="knowledge_name"></span>
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
	<input id="kl_category_json" type="hidden" value='<?php echo $kl_category_json; ?>' />
	<input id="data_json" type="hidden" value='<?php echo $data_json; ?>' />
	<input id="mode" type="hidden" value="<?php echo $mode; ?>" />
	<!-- CBBXE -->
</div>



<!-- ヘルプ用  -->
<div id="help_x_w" style="display:none">
	<input type="button" class="btn btn-info btn-sm" onclick="$('#help_x').toggle()" value="ヘルプ" />
	<div id="help_x" class="help_x" style="display:none">
		<h2>ヘルプ</h2>
	
		<?php echo $this->element('CrudBase/crud_base_help');?>
	
	</div>
</div>






















