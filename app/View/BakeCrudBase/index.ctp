<style>
	.souce_a{
		display:none;
	}
	.souce_a pre{
		margin-bottom:100px;
	}
</style>


<h3>
<?php 
	if(!empty($tblName)){
		echo 'テーブル名：'.$tblName;
	}
?>
</h3>


<?php echo $this->Form->create('BakeCrudBase', array('url' => '#'));
	
	echo $this->Form->input("tehon_proj_fp", array(
			'id' => 'tehon_proj_fp',
			'type' => 'text',
			'value' => $conf['tehon_proj_fp'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- お手本プロジェクトフルパス --',
			'style'=>'width:300px',
	));
	
	echo $this->Form->input("tehon_code_c", array(
			'id' => 'tehon_code_c',
			'type' => 'text',
			'value' => $conf['tehon_code_c'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- お手本コード(キャメル記法） --',
			'style'=>'width:200px',
	));
	
	echo $this->Form->input("tehon_wamei", array(
			'id' => 'tehon_wamei',
			'type' => 'text',
			'value' => $conf['tehon_wamei'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- お手本和名 --',
			'style'=>'width:200px',
	));
	
	echo "<br>";
	
	echo $this->Form->input("out_proj_fp", array(
			'id' => 'out_proj_fp',
			'type' => 'text',
			'value' => $conf['out_proj_fp'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- 出力先プロジェクトフルパス --',
			'style'=>'width:300px',
	));


	echo $this->Form->input("db_name", array(
			'id' => 'db_name',
			'type' => 'text',
			'value' => $conf['db_name'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- DB名 --',
			'title'=>'省略時は当システムのデフォルトDB',
	));
	
	echo $this->Form->input("tbl_name", array(
			'id' => 'tbl_name',
			'type' => 'text',
			'value' => $conf['tbl_name'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- テーブル名 --',
	));
	
	echo $this->Form->input("out_wamei", array(
			'id' => 'out_wamei',
			'type' => 'text',
			'value' => $conf['out_wamei'],
			'label' => false,
			'div' => false,
			'placeholder' => '-- 出力モデル和名 --',
			'style'=>'width:200px',
	));
echo $this->Form->submit('作成');
echo $this->Form->end(); 
?>


<?php 
if(empty($data)){
	return;
}
?>
<br><hr>








<p class="text-warning">
	コントローラの全フィールド定義
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#init_crud_base').toggle(300)" />
</p>
<div id = "init_crud_base" class = "souce_a">
<pre><code>
<?php 
echo $data['init_crud_base'];
?>
</code></pre>
</div>


<p>
	検索条件定義
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#kj_define').toggle(300)" />
</p>
<div id = "kj_define" class = "souce_a">
<pre><code>
<?php 
echo $data['kj_define'];
?>
</code></pre>
</div>




<p>
	検索条件バリデーション
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#kj_valid').toggle(300)" />
</p>
<div id = "kj_valid" class = "souce_a">
<pre><code>
<?php 
echo $data['kj_valid'];
?>
</code></pre>
</div>




<p>
	フィールドデータ
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#field_data').toggle(300)" />
</p>
<div id = "field_data" class = "souce_a">
<pre><code>
<?php 
echo $data['field_data'];
?>
</code></pre>
</div>




<p>
	編集フィールド定義
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#edit_field').toggle(300)" />
</p>
<div id = "edit_field" class = "souce_a">
<pre><code>
<?php 
echo $data['edit_field'];
?>
</code></pre>
</div>




<p>
	編集フィールドバリデーション
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#edit_validation').toggle(300)" />
</p>
<div id = "edit_validation" class = "souce_a">
<pre><code>
<?php 
echo $data['edit_validation'];
?>
</code></pre>
</div>




<p>
	モデルのWHERE
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#kj_conditions').toggle(300)" />
</p>
<div id = "kj_conditions" class = "souce_a">
<pre><code>
<?php 
echo $data['kj_conditions'];
?>
</code></pre>
</div>




<p>
	検索条件の入力フォーム
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#kj_input').toggle(300)" />
</p>
<div id = "kj_input" class = "souce_a">
<pre><code>
<?php 
echo $data['kj_input'];
?>
</code></pre>
</div>




<p>
	一覧テーブル
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#field_table').toggle(300)" />
</p>
<div id = "field_table" class = "souce_a">
<pre><code>
<?php 
echo $data['field_table'];
?>
</code></pre>
</div>




<p>
	プロパティプレビュー： 詳細/登録結果
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#detail_preview').toggle(300)" />
</p>
<div id = "detail_preview" class = "souce_a">
<pre><code>
<?php 
echo $data['detail_preview'];
?>
</code></pre>
</div>




<p>
	編集ページの入力フォーム
	<input type="button" class="btn btn-info btn-xs" value="表示" onclick="$('#edit_input').toggle(300)" />
</p>
<div id = "edit_input" class = "souce_a">
<pre><code>
<?php 
echo $data['edit_input'];
?>
</code></pre>
</div>


<br>
<aside>
	ダンプデータ
	<input type="button" class="btn btn-default btn-xs" value="表示" onclick="$('#table_data').toggle(300)" />
</aside>
<div id = "table_data" class = "souce_a">
<?php echo $this->Html->createHtmlTable($data['table_data'],['列','型']); ?>
</div>









<time>2016-8-23</time>




