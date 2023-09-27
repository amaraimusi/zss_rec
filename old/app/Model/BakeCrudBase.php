<?php
App::uses('Model', 'Model');

/**
 * CRUD自動生成処理のロジック
 * 
 * @note
 * 
 * 
 * CRUD支援ツールを応用
 * CrudBaseに対応した、いくつかのパラメータを自動生成する。
 * 支援ツールなので完璧なパラメータを作成するツールではない。
 * 共通フィールドを除き、セレクトボックスには対応しない。
 * 文字列検索のルールとして、50文字以内なら完全一致、超えるなら部分一致検索とする。
 * Tiny Intなどフラグ系および、コメントに「フラグ」が、フラグ型データと見なす。
 * コメントの末尾が「額」、「金」、「費」であるなら金額用と判定する。
 * 日付系は自動的に範囲検索になる。
 * 
 * @date 2016-8-24
 * @version 1.0
 * @author k-uehara
 *
 */
class BakeCrudBase  extends Model{
	
	public $useTable = false;
	
	private $_strLenRule = 256;//文字数基準
	
	
	/**
	 * CRUD自動生成
	 * @param array $conf 設定データ
	 */
	public function autoCreate($conf){
		
		// パラメータデータをDBテーブルから作成する。
		$paramData = $this->getParamData($conf,false);
		
		// CRUDソースファイル群を出力先プロジェクトへ書きだす
		$this->writeCrud($conf,$paramData);
		

		
	}
	
	
	
	
	/**
	 * CRUDソースファイル群を出力先プロジェクトへ書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeCrud($conf,$paramData){
		
		
		// コントローラのソースコードを書きだす
		$this->writeForController($conf,$paramData);
		
		// モデルのソースコードを書きだす
		$this->writeForModel($conf,$paramData);
		
		// ビューindexのソースコードを書きだす
		$this->writeForViewIndex($conf,$paramData);
		
		// ビューeditのソースコードを書きだす
		$this->writeForViewEdit($conf,$paramData);
		
		// ビューdetailのソースコードを書きだす
		$this->writeForViewDetail($conf,$paramData);
		
		// ビューregのソースコードを書きだす
		$this->writeForViewReg($conf,$paramData);
		
// 		// css/edit.cssのソースコードを書きだす
// 		$this->writeForViewCssEdit($conf,$paramData);
		
		// js/index.jsのソースコードを書きだす
		$this->writeForViewJsIndex($conf,$paramData);
		
		// js/edit.jsのソースコードを書きだす
		$this->writeForViewJsEdit($conf,$paramData);
		
		
		

	}
	
	/**
	 * コントローラのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForController($conf,$paramData){
		
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード

		
		// お手本コントローラフルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'Controller'.DS.$tehon_code_c.'Controller.php';
		
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);

		
		// コードなどの置換
		$text = $this->replaceCode($text,$conf);

		
		// 置換：検索条件定義
		$kj_define = $paramData['kj_define'];
		$mark1 = "this->kensakuJoken=array(";
		$mark2 = ");";
		$text = $this->replaceHasami($text,$mark1,$mark2,$kj_define);// 2つの印文字に挟まれた文字を置換する
		

		// 置換：検索条件のバリデーション
		$kj_valid = $paramData['kj_valid'];
		$mark1 = "this->kjs_validate=array(";
		$mark2 = ");";
		$text = $this->replaceHasami($text,$mark1,$mark2,$kj_valid);
		

		// 置換：フィールドデータ
		$field_data = $paramData['field_data'];
		$mark1 = "this->field_data=array('def'=>array(";
		$mark2 = "));";
		$text = $this->replaceHasami($text,$mark1,$mark2,$field_data);
		

		// 置換：編集エンティティ定義
		$edit_field = $paramData['edit_field'];
		$mark1 = "this->entity_info=array(";
		$mark2 = ");";
		$text = $this->replaceHasami($text,$mark1,$mark2,$edit_field);
		

		// 置換：編集用バリデーション
		$edit_validation = $paramData['edit_validation'];
		$mark1 = "this->edit_validate=array(";
		$mark2 = ");";
		$text = $this->replaceHasami($text,$mark1,$mark2,$edit_validation);

		// 出力コントローラのフルパス
		$o_ctrl_fp = $out_proj_fp.DS.'app'.DS.'Controller'.DS.$out_code_c.'Controller.php';

		
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	


	/**
	 * モデルのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForModel($conf,$paramData){
		
		
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
		
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'Model'.DS.$tehon_code_c.'.php';
		
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
		

		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
		
 		// 置換：検索条件
 		$kj_conditions = $paramData['kj_conditions'];
		$mark1 = "// --- Start kjConditions";
		$mark2 = "// --- End kjConditions";
		$text = $this->replaceHasami($text,$mark1,$mark2,$kj_conditions);// 2つの印文字に挟まれた文字を置換する

		
		// 出力のフルパス
		$o_ctrl_fp = $out_proj_fp.DS.'app'.DS.'Model'.DS.$out_code_c.'.php';
		
		
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	
	
	
	
	/**
	 * ビューindexのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewIndex($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
		
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'View'.DS.$tehon_code_c.DS.'index.ctp';
		
		
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
		

		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
		
		// 置換：kjs1
		$mark1 = "<div id=\"kjs1\">";
		$mark2 = "</div>";
		$text = $this->replaceHasami($text,$mark1,$mark2,"\r\n\t");// 2つの印文字に挟まれた文字を置換する

		
  		// 置換：kjs2
  		$kj_input = $paramData['kj_input'];
		$mark1 = "// --- Start kj_input";
		$mark2 = "// --- End kj_input";
		$text = $this->replaceHasami($text,$mark1,$mark2,$kj_input);

		
  		// 置換：一覧テーブル
  		$field_table = $paramData['field_table'];
		$mark1 = "// --- Start field_table";
		$mark2 = "// --- End field_table";
		$text = $this->replaceHasami($text,$mark1,$mark2,$field_table);

		
  		// 置換：Ajax新規入力フォーム
  		$ajax_form_new = $paramData['ajax_form_new'];
		$mark1 = "<!-- Start ajax_form_new_start -->";
		$mark2 = "<!-- Start ajax_form_new_end -->";
		$text = $this->replaceHasami($text,$mark1,$mark2,$ajax_form_new);

		
  		// 置換：Ajax編集フォーム
  		$ajax_form_edit = $paramData['ajax_form_edit'];
		$mark1 = "<!-- Start ajax_form_edit_start -->";
		$mark2 = "<!-- Start ajax_form_edit_end -->";
		$text = $this->replaceHasami($text,$mark1,$mark2,$ajax_form_edit);

		
		
  		// ディレクトリを作成
  		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'View'.DS.$out_code_c;
  		
		// ディレクトリが存在しないなら作成
  		if (!is_dir($o_ctrl_dir)){
  			mkdir($o_ctrl_dir);
  		}
  		

		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'index.ctp';
		
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	
	
	
	
	/**
	 * ビューeditのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewEdit($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
		
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'View'.DS.$tehon_code_c.DS.'edit.ctp';
		
		
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
		

		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
		

		
		// 置換：編集入力フォーム
		$edit_input = $paramData['edit_input'];
		$mark1 = "// --- Start edit_input";
		$mark2 = "// --- End edit_input";
		$text = $this->replaceHasami($text,$mark1,$mark2,$edit_input);

		// ディレクトリを作成
		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'View'.DS.$out_code_c;
		
		// ディレクトリが存在しないなら作成
			if (!is_dir($o_ctrl_dir)){
			mkdir($o_ctrl_dir);
		}


		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'edit.ctp';
		
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	
	
	
	
	/**
	 * ビューdetailのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewDetail($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
		
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'View'.DS.$tehon_code_c.DS.'detail.ctp';
		
		
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
		

		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
		

		
		// 置換：編集入力フォーム
		$detail_preview = $paramData['detail_preview'];
		$mark1 = "// --- Start detail_preview";
		$mark2 = "// --- End detail_preview";
		$text = $this->replaceHasami($text,$mark1,$mark2,$detail_preview);

		// ディレクトリを作成
		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'View'.DS.$out_code_c;
		
		// ディレクトリが存在しないなら作成
			if (!is_dir($o_ctrl_dir)){
			mkdir($o_ctrl_dir);
		}


		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'detail.ctp';
		
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	
	
	
	
	/**
	 * ビューregのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewReg($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
		
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'View'.DS.$tehon_code_c.DS.'reg.ctp';
		
		
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
		

		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
		

		
		// 置換：編集入力フォーム
		$detail_preview = $paramData['detail_preview'];
		$mark1 = "// --- Start detail_preview";
		$mark2 = "// --- End detail_preview";
		$text = $this->replaceHasami($text,$mark1,$mark2,$detail_preview);

		// ディレクトリを作成
		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'View'.DS.$out_code_c;
		
		// ディレクトリが存在しないなら作成
			if (!is_dir($o_ctrl_dir)){
			mkdir($o_ctrl_dir);
		}


		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'reg.ctp';
		
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	

	/**
	 * css/edit.cssのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewCssEdit($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
	
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'webroot'.DS.'css'.DS.$tehon_code_c.DS.'edit.css';
	
	
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
	
	
		// コードなどの置換
		$text = $this->replaceCode($text,$conf);

		
		// ディレクトリを作成
		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'webroot'.DS.'css'.DS.$out_code_c;
		
		
		// ディレクトリが存在しないなら作成
		if (!is_dir($o_ctrl_dir)){
			mkdir($o_ctrl_dir);
		}
	
	
		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'edit.css';
	
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	


	
	/**
	 * js/index.jsのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewJsIndex($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
	
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'webroot'.DS.'js'.DS.$tehon_code_c.DS.'index.js';
		
	
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
	
	
		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
	
	
	
		// 置換：ex1
		$mark1 = "// --- Start ex1";
		$mark2 = "// --- End ex1";
		$text = $this->replaceHasami($text,$mark1,$mark2,"\r\n");
	
		// 置換：ex2
		$mark1 = "// --- Start ex2";
		$mark2 = "// --- End ex2";
		$text = $this->replaceHasami($text,$mark1,$mark2,"\r\n\t");
		
		// 置換：index.js日付機能のソースコード
		$js_index_date = $paramData['js_index_date'];
		$mark1 = "// --- Start js_index_date";
		$mark2 = "// --- End js_index_date";
		$text = $this->replaceHasami($text,$mark1,$mark2,$js_index_date);
		

		// ディレクトリを作成
		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'webroot'.DS.'js'.DS.$out_code_c;
	
		// ディレクトリが存在しないなら作成
		if (!is_dir($o_ctrl_dir)){
			mkdir($o_ctrl_dir);
		}
	
	
		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'index.js';
	
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	


	
	/**
	 * js/edit.jsのソースコードを書きだす
	 * @param array $conf 設定データ
	 * @param array $paramData パラメータデータ
	 */
	private function writeForViewJsEdit($conf,$paramData){
		$tehon_proj_fp = $conf['tehon_proj_fp']; // お手本プロジェクトフルパス
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$out_proj_fp = $conf['out_proj_fp']; // 出力先プロジェクトフルパス
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
	
		// お手本フルパスを取得
		$t_ctrl_fp = $tehon_proj_fp.DS.'app'.DS.'webroot'.DS.'js'.DS.$tehon_code_c.DS.'edit.js';
		
	
		// ソースファイルからテキストを取得する
		$text = $this->loadSource($t_ctrl_fp);
	
	
		// コードなどの置換
		$text = $this->replaceCode($text,$conf);
	

		// 置換：index.js日付機能のソースコード
		$js_edit_date = $paramData['js_edit_date'];
		$mark1 = "// --- Start js_edit_date";
		$mark2 = "// --- End js_edit_date";
		$text = $this->replaceHasami($text,$mark1,$mark2,$js_edit_date);
		

		// ディレクトリを作成
		$o_ctrl_dir = $out_proj_fp.DS.'app'.DS.'webroot'.DS.'js'.DS.$out_code_c;
	
		// ディレクトリが存在しないなら作成
		if (!is_dir($o_ctrl_dir)){
			mkdir($o_ctrl_dir);
		}
	
	
		// 出力のフルパス
		$o_ctrl_fp = $o_ctrl_dir.DS.'edit.js';
	
		// ソースコードを書きだす
		$this->writeSource($o_ctrl_fp,$text);
	}
	
	
	
	
	
	/**
	 * コードなどの置換
	 * @param string $text ソーステキスト
	 * @param array $conf 設定データ
	 */
	private function replaceCode($text,$conf){
		
		$tehon_code_c = $conf['tehon_code_c']; // お手本コード
		$tehon_code_s = $conf['tehon_code_s']; // お手本コード
		$tehon_wamei = $conf['tehon_wamei']; // お手本和名
		$out_code_c = $conf['out_code_c']; // 出力モデルコード
		$out_code_s = $conf['out_code_s']; // 出力モデルコード
		$out_wamei = $conf['out_wamei']; // 出力モデル和名
		
		// モデルコードや和名を置換する
		$text = str_replace($tehon_code_c, $out_code_c, $text);
		$text = str_replace($tehon_code_s, $out_code_s, $text);
		$text = str_replace($tehon_wamei, $out_wamei, $text);
		
		return $text;
	}
	
	
	/**
	 * テキストファイルに書き出す
	 *
	 * @param $txtFn テキストファイル名
	 * @param $str 文字列
	 * @return なし
	 */
	function writeSource($txtFn, $str) {
	
		// ファイルを追記モードで開く
		$fp = fopen ( $txtFn, 'ab' );
	
		// ファイルを排他ロックする
		flock ( $fp, LOCK_EX );
	
		// ファイルの中身を空にする
		ftruncate ( $fp, 0 );
	
		// データをファイルに書き込む
		fwrite ( $fp, $str );
	
		// ファイルを閉じる
		fclose ( $fp );
	}
	
	
	/**
	 * ソースファイルからテキスト（文字列を取得する）
	 * @param string $fn ソースファイル名
	 */
	private function loadSource($fn) {
	
		// 引数のiniファイル名が空、もしくは存在しなければ、なら、nullを返して終了
		if (! $fn) {
			return null;
		}
	
		$str = null;
		$fn=mb_convert_encoding($fn,'SJIS','UTF-8');
		if (!is_file($fn)){
			return null;
		}
	
		if ($fp = fopen ( $fn, "r" )) {
			$data = array ();
			while ( false !== ($line = fgets ( $fp )) ) {
				$str .= mb_convert_encoding ( $line, 'utf-8', 'utf-8,sjis,euc_jp,jis' );
			}
		}
		fclose ( $fp );
	
		return $str;
	}
	
	/**
	 * パラメータデータをDBテーブルから作成する
	 * 
	 * @param array $conf 設定データ
	 * @param bool $withHead true:ヘッド部分を一緒に作成   false:定義部分のみ
	 * @return パラメータデータ
	 */
	private function getParamData($conf,$withHead=true){
		
		$dbName = $conf['db_name']; // DB名
		$tblName = $conf['tbl_name']; // テーブル名
		
		if(!empty($dbName)){
			$this->changeDbName($dbName);// データベース名を指定して、DB変更する。
		}
		
		$data = $this->getFieldData($tblName);

		$modelName = $this->convModelName($tblName); // テーブル名からモデル名を作成する

		$data = $this->setProtoField($data); // プロトフィールドにセット
		
		$data = $this->convRangeDate($data); // 日付系フィールドを範囲検索ように分割する
		
		$data = $this->addLimit($data); // limitフィールドを追加する
		
		$data = $this->classifying($data); // フィールドの分類
		
		$data = $this->setStrLen($data); // 文字列系の文字数をセット
		
		$kj_define = $this->createKjDefine($data,$withHead); // 検索条件定義の作成
		
		$kj_valid = $this->createKjValidation($data,$withHead); // 検索条件バリデーションのソースコード作成
		
		$kj_conditions = $this->createKjConditions($data,$modelName); // WHEREのソースコード作成
		
		$kj_input = $this->createKjInput($data); // 検索条件入力フォームのソースコード作成
		
		$field_data = $this->createFieldData($data,$modelName,$withHead); // フィールドデータのソースコード作成
		
		$field_table = $this->createFieldTable($data); // 一覧テーブルのソースコードを作成
		
		$ajax_form_new = $this->getAjaxFormNew($data); // Ajax新規入力フォームのソースコードを作成
		
		$ajax_form_edit = $this->getAjaxFormEdit($data); // Ajax編集フォームのソースコードを作成

		$detail_preview = $this->createDetailPreview($data); // 詳細ページのプレビューソースコードを作成
		
		$edit_input = $this->createEditInput($data); // 編集入力フォームのソースコード作成
		
		$edit_field = $this->createEditField($data,$withHead); // 編集フィールド定義のソースコードを作成
		
		$edit_validation = $this->createEditValidation($data,$withHead); // 編集バリデーションのソースコード作成
		
		$js_index_date = $this->createJsIndexDate($data); // index.js日付機能のソースコードを作成
		
		$js_edit_date = $this->createJsEditDate($data); // edit.js日付機能のソースコードを作成
		
		
		

		$codes['table_data'] = $data;
		$codes['kj_define'] = $kj_define;
		$codes['kj_valid'] = $kj_valid;
		$codes['kj_conditions'] = $kj_conditions;
		$codes['kj_input'] = $kj_input;
		$codes['field_data'] = $field_data;
		$codes['field_table'] = $field_table;
		$codes['ajax_form_new'] = $ajax_form_new;
		$codes['ajax_form_edit'] = $ajax_form_edit;
		$codes['detail_preview'] = $detail_preview;
		$codes['edit_input'] = $edit_input;
		$codes['edit_field'] = $edit_field;
		$codes['edit_validation'] = $edit_validation;
		$codes['js_index_date'] = $js_index_date;
		$codes['js_edit_date'] = $js_edit_date;
		
		
		$init_crud_base = $this->createInitCrudBase($codes); // コントローラの全フィールド定義
		$codes['init_crud_base'] = $init_crud_base;
		
		
		return $codes;
		
	}
	
	
	
	/**
	 * コントローラの全フィールド定義
	 * @param array $codes コードリスト
	 * @return コントローラの全フィールド定義ソースコード
	 */
	private function createInitCrudBase($codes){
		
		// 出力項目一覧
		$whiteList = array(
			'kj_define',
			'kj_valid',
			'field_data',
			'edit_field',
			'edit_validation',
		);
		
		
		$str = "private function initCrudBase(){";
		foreach($whiteList as $key){
			
			
			$code = $codes[$key];
			
			$code = str_replace('public $', '$this->', $code);
			$str .= $code;
			
			$str .= "\n\n\n\n";
			
			
		}
		$str.="}";
		
		return $str;
	}
	 


	
	
	
	
	/**
	 * テーブル名からフィールドデータを取得する
	 *
	 * @param string $tbl テーブル名
	 * @return array フィールドデータ
	 */
	private function getFieldData($tbl){
		

		
		$sql="SHOW FULL COLUMNS FROM {$tbl}";
	
		//SQLを実行してデータを取得
		$data=$this->query($sql);
		
		//構造変換
		if(!empty($data)){
			$data=Hash::extract($data, '{n}.COLUMNS');
		}
	
		return $data;
	}
	
	
	// データベース名を指定して、DB変更する。
	private function changeDbName($dbName,$DbConfig='default') {
		$this->setDataSource($DbConfig);
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$db->reconnect(array('database' => $dbName));
	}
	
	
	/**
	 * 編集バリデーションのソースコード作成
	 * @param array $data フィールドデータ
	 * @param bool $withHead true:ヘッド部分を一緒に作成   false:定義部分のみ
	 * @return string 編集バリデーションのソースコード
	 */
	private function createEditValidation($data,$withHead=true){
		$ary=array();
		
		if($withHead){
			$ary[] = "	/// 編集用バリデーション";
			$ary[] = "	\$this->edit_validate = array(";
			
		}
		$ary[] = "\r\n";
		
		// 対象外フィールド
		$excludeds = array('id','delete_flg','update_user','update_ip_addr','created','modified','limit');
		
		foreach($data as $ent){

			// フィールド名
			$field = $ent['proto_field'];
			
			// 対象外フィールドは飛ばす
			if(in_array($field, $excludeds)){
				continue;
			}
			
			if(!empty($ent['range2_flg'])){
				continue;
			}
				
			// 和名を取得
			$wamei = $ent['Comment'];
			if(empty($wamei)){
				$wamei = $field;
			}
		
				
			// int系
			if($ent['type_b'] == 'int'){
		
				$ary[] =
				"			'{$field}' => array(\n".
				"				'naturalNumber'=>array(\n".
				"					'rule' => array('naturalNumber', true),\n".
				"					'message' => '{$wamei}は数値を入力してください',\n".
				"					'allowEmpty' => true\n".
				"				),\n".
				"			),\n";
			}
		
				
			// string系
			if($ent['type_b'] == 'string'){
		
				$str_len = $ent['str_len'];
		
				$ary[] =
				"			'{$field}'=> array(\n".
				"				'maxLength'=>array(\n".
				"					'rule' => array('maxLength', {$str_len}),\n".
				"					'message' => '{$wamei}は{$str_len}文字以内で入力してください',\n".
				"					'allowEmpty' => true\n".
				"				),\n".
				"			),\n";
			}
		
				
			// date系
			if($ent['type_b'] == 'date'){
		
				$ary[] =
				"			'{$field}'=> array(\n".
				"				'rule' => array( 'date', 'ymd'),\n".
				"				'message' => '{$wamei}は日付形式【yyyy-mm-dd】で入力してください。',\n".
				"				'allowEmpty' => true\n".
				"			),\n";
			}
		
				
			// float系
			if($ent['type_b'] == 'float'){
		
				$ary[] =
				"			'{$field}'=> array(\n".
				"				'range'=>array(\n".
				"					'rule' => array('range', -100000000,100000000),\n".
				"					'message' => '{$wamei}は数値を入力してください。（小数可、最大10億）',\n".
				"					'allowEmpty' => true,\n".
				"				),\n".
				"			),\n";
			}
		
				
			// datetime系
			if($ent['type_b'] == 'datetime'){
		
				$ary[] =
				"			'{$field}'=> array(\n".
				"				'maxLength'=>array(\n".
				"					'rule' => array('maxLength', 20),\n".
				"					'message' => '{$wamei}は20文字以内で入力してください',\n".
				"					'allowEmpty' => true\n".
				"				),\n".
				"			),\n";
			}
				
		
		
		}
		
		if($withHead){
			$ary[] = "	);";
		}
		$ary[] = "\r\n\t\t";
		
		$code = join("\r\n",$ary);
		
		
		
		return $code;		
	}
	
	
	/**
	 * index.js日付機能のソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string index.js日付機能のソースコード
	 */
	private function createJsIndexDate($data){
		
		// 日付系フィールドが存在しないなら、空文字を返す。
		$dfPre = false;// 日付フィールド存在フラグ
		foreach($data as $ent){
			if($ent['type_b'] == 'date'){
				$dfPre = true;
				break;
			}
		}
		if($dfPre == false){
			return "";
		}
		
		$ary=array();
		
		$ary[] = "\r\n";
		$ary[]="//jQuery UIカレンダーの組み込みと日本語化(Layouts/default.js)";
		$ary[]="datepicker_ja();";
		
		
		foreach($data as $ent){
			
			$field = $ent['proto_field'];
			if($ent['type_c']=='date1'){
				$ary[] = "$('#kj_{$field}1').datepicker({";
				$ary[] = "	dateFormat:'yy-mm-dd'";
				$ary[] = "});";
				$ary[] = "$('#kj_{$field}2').datepicker({";
				$ary[] = "	dateFormat:'yy-mm-dd'";
				$ary[] = "});";
				$ary[] = "ympicker_tukishomatu('kj_{$field}_ym','kj_{$field}1','kj_{$field}2');// 年月選択により月初日、月末日らのテキストボックスを連動させる。";
				$ary[] = "";
			}
		}

		

		
		$ary[] = "\r\n\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		
		return $code;		
	}
	
	
	/**
	 * edit.js日付機能のソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string index.js日付機能のソースコード
	 */
	private function createJsEditDate($data){
		
		// 日付系フィールドが存在しないなら、空文字を返す。
		$dfPre = false;// 日付フィールド存在フラグ
		foreach($data as $ent){
			if($ent['type_b'] == 'date'){
				$dfPre = true;
				break;
			}
		}
		if($dfPre == false){
			return "";
		}
		
		$ary=array();
		
		$ary[] = "\r\n";
		$ary[]="//jQuery UIカレンダーの組み込みと日本語化(Layouts/default.js)";
		$ary[]="datepicker_ja();";
		
		
		foreach($data as $ent){
			
			$field = $ent['proto_field'];
			if($ent['type_c']=='date1'){
				$ary[] = "$('#{$field}').datepicker({";
				$ary[] = "	dateFormat:'yy-mm-dd'";
				$ary[] = "});";
				$ary[] = "";
			}
		}

		

		
		$ary[] = "\r\n\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		
		return $code;		
	}
	
	
	
	
	
	
	
	/**
	 * 編集フィールド定義のソースコードを作成
	 * @param array $data フィールドデータ
	 * @param bool $withHead true:ヘッド部分を一緒に作成   false:定義部分のみ
	 * @return string 編集フィールド定義のソースコード
	 */
	private function createEditField($data,$withHead=true){
		$ary=array();
		
		if($withHead){
			$ary[] = "/// 編集エンティティ定義";
			$ary[] = "\$this->entity_info=array(";
		}
		$ary[] = "\r\n";

		// 対象外フィールド
		$excludeds = array('update_user','update_ip_addr','created','modified','limit');
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
				
		
			$field = $ent['proto_field'];
				
			// 対象外フィールドは飛ばす
			if(in_array($field, $excludeds)){
				continue;
			}
		
			// デフォルトの値
			$def='null';
			if($ent['type_c'] == 'delete_flg'){
				$def='0';
			}
			
			$ary[] = "	array('name'=>'{$field}','def'=>{$def}),";
			
				
		}
		
		if($withHead){
			$ary[] = ");";
		}
		$ary[] = "\r\n\t\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		return $code;		
	}
	
	
	
	
	
	
	/**
	 * 編集入力フォームのソースコード作成
	 * @param array $data フィールドデータ
	 * @return string 編集入力フォームのソースコード
	 */
	private function createEditInput($data){
		$ary=array();
		$ary[] = "\r\n";
		
		
		// 対象外フィールド
		$excludeds = array('id','update_user','update_ip_addr','created','modified','limit');
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
			

			$field = $ent['proto_field'];
			
			// 対象外フィールドは飛ばす
			if(in_array($field, $excludeds)){
				continue;
			}


			// 和名を取得
			$wamei = $ent['Comment'];
			if(empty($wamei)){
				$wamei = $ent['Field'];;
			}
			
			// 文字サイズを取得する(varcharのサイズ)
			$str_len = 0;
			if(!empty($ent['str_len'])){
				$str_len = $ent['str_len'];
			}
			
			// 入力フォーム幅の取得
			$width = 150;
			if($ent['type_c'] == 'string'){
				$width = 300;
			}
			
			
			
			
			
			if($field == 'delete_flg'){
				$ary[] = "\$this->CrudBase->editDeleteFlg(\$ent,\$mode);";
			}
			
			// 文字サイズが指定サイズを超えるならテキストエリアとする。
			elseif($str_len > $this->_strLenRule){
				$ary[] = "\$this->CrudBase->editTextArea(\$ent,'{$field}','{$wamei}');";
			}

			// その他はテキストボックスとする
			else{
				$ary[] = "\$this->CrudBase->editText(\$ent,'{$field}','{$wamei}',{$width});";
			}
		
		}

		$ary[] = "\r\n\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		return $code;		
	}
	
	
	
	

	/**
	 * 詳細ページのプレビューソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string プレビューのソースコード
	 */
	private function createDetailPreview($data){
		$ary=array();
		$ary[] = "\r\n";
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
		
			if($ent['type_c'] == 'limit'){
				continue;
			}
			
			$field = $ent['proto_field'];
			
			// 和名を取得
			$name = $ent['Comment'];
			if(empty($name)){
				$name = $ent['Field'];;
			}
			
			if($ent['type_c'] == 'id'){
				$ary[] = "\$this->CrudBase->tpId(\$ent['id'],'{$name}');";
			}
				
			elseif($ent['type_c'] == 'date' || $ent['type_c'] == 'float' || $ent['type_c'] == 'int'){
				$ary[] = "\$this->CrudBase->tpPlain(\$ent['{$field}'],'{$name}');";
			}
				
			elseif($ent['type_c'] == 'string'){
				if($ent['str_len'] <= $this->_strLenRule){
					$ary[] = "\$this->CrudBase->tpStr(\$ent['{$field}'],'{$name}');";
				}else{
					$ary[] = "\$this->CrudBase->tpNote(\$ent['{$field}'],'{$name}');";
				}
			
			}
				
			elseif($ent['type_c'] == 'money'){
				$ary[] = "\$this->CrudBase->tpMoney(\$ent['{$field}'],'{$name}');";
			}
				
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = "\$this->CrudBase->tpDeleteFlg(\$ent['delete_flg'],'有無');";
			}
				
			elseif($ent['type_c'] == 'created' || $ent['type_c'] == 'modified' || $ent['type_c'] == 'datetime'){
				$ary[] = "\$this->CrudBase->tpPlain(\$ent['{$field}'],'{$name}');";
			}
				
			else{
				$ary[] = "\$this->CrudBase->tpPlain(\$ent['{$field}'],'{$name}');";
			}

		
		}
		
		
		$ary[] = "\r\n\t";
		
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		return $code;
	}
	
	
	
	
	

	/**
	 * 一覧テーブルのソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string 一覧テーブルのソースコード
	 */
	private function createFieldTable($data){
		$ary=array();
		$ary[] = "\r\n";
		

		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
				
			if($ent['type_c'] == 'limit'){
				continue;
			}
			
			$field = $ent['proto_field'];
				
			if($ent['type_c'] == 'id'){
				$ary[] = "\$this->CrudBase->tdId(\$ent,'id');";
			}
			
			elseif($ent['type_c'] == 'date' || $ent['type_c'] == 'float' || $ent['type_c'] == 'int'){
				$ary[] = "\$this->CrudBase->tdPlain(\$ent,'{$field}');";
			}
			
			elseif($ent['type_c'] == 'string'){
				if($ent['str_len'] <= $this->_strLenRule){
					$ary[] = "\$this->CrudBase->tdStr(\$ent,'{$field}');";
				}else{
					$ary[] = "\$this->CrudBase->tdNote(\$ent,'{$field}');";
				}
				
			}
			
			elseif($ent['type_c'] == 'money'){
				$ary[] = "\$this->CrudBase->tdMoney(\$ent,'{$field}');";
			}
			
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = "\$this->CrudBase->tdDeleteFlg(\$ent,'delete_flg');";
			}
			
			elseif($ent['type_c'] == 'created' || $ent['type_c'] == 'modified' || $ent['type_c'] == 'datetime'){
				$ary[] = "\$this->CrudBase->tdPlain(\$ent,'{$field}');";
			}
			
			else{
				$ary[] = "\$this->CrudBase->tdPlain(\$ent,'{$field}');";
			}
		

				
		}
		
		
		$ary[] = "\r\n\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		return $code;
	}
	
	
	
	
	
	/**
	 * Ajax新規入力フォームのソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string Ajax新規入力フォームのソースコード
	 */
	private function getAjaxFormNew($data){
		$ary=array();
		$ary[] = "\r\n";
		
		// 対象外リスト
		$exempts = array('id','limit','delete_flg','update_user','user_agent','ip_addr','created','modified');
	
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
			
			$field = $ent['proto_field'];
			if(in_array($field, $exempts)){
				continue;
			}

			
			
			// 和名を取得
			$wamei = $ent['Comment'];
			if(empty($wamei)){
				$wamei = $field;
			}
			
			// 必須フラグを取得
			$req = true;
			if($ent['Null'] == 'YES'){
				$req = false;
			}
			
			
		
			// int系
			if($ent['type_b'] == 'int'){
			
				$ary[] =
					"		<tr><td>{$wamei}: </td><td>\n".
					"				<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"^[0-9]+$\" maxlength=\"11\" title=\"数値を入力してください\" />\n".
					"				<label class=\"text-danger\" for=\"{$field}\"></label>\n".
					"			</td></tr>\n";
			}

				
			// string系
			if($ent['type_b'] == 'string'){
			
 				$str_len = $ent['str_len'];
			
				$ary[] =
					"		<tr><td>{$wamei}: </td><td>\n".
					" 			<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  maxlength=\"{$str_len}\" title=\"{$str_len}文字以内で入力してください\" />\n".
					" 			<label class=\"text-danger\" for=\"{$field}\"></label>\n".
					" 		</td></tr>\n";
			}
			
				
			// date系
			if($ent['type_b'] == 'date'){
			
				$ary[] =
						"		<tr><td>{$wamei}: </td><td>\n".
						" 			<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"\d{4}-\d{2}-\d{2}\" title=\"日付形式（Y-m-d）で入力してください(例：2012-12-12)\" />\n".
						" 			<label class=\"text-danger\" for=\"{$field}\"></label>\n".
						" 		</td></tr>\n";
			}
			
				
			// float系
			if($ent['type_b'] == 'float'){
				$ary[] =
				"		<tr><td>{$wamei}: </td><td>\n".
				"				<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"[+-]?[0-9]+[\.]?[0-9]*([eE][+-])?[0-9]*\" maxlength=\"11\" title=\"数値を入力してください\" />\n".
				"				<label class=\"text-danger\" for=\"{$field}\"></label>\n".
				"			</td></tr>\n";

			}
			
				
			// datetime系
			if($ent['type_b'] == 'datetime'){
			
				$ary[] = 
					"		<tr><td>{$wamei}: </td><td>\n".
					" 			<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\" title=\"日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)\" />\n".
					" 			<label class=\"text-danger\" for=\"{$field}\"></label>\n".
					" 		</td></tr>\n";

			}
				

		
		
		
		}
		
		
		$ary[] = "\r\n\t";
		

		$code = join("\r\n",$ary);
		
		return $code;		
	}
	
	
	
	
	
	/**
	 * Ajax編集フォームのソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string Ajax編集フォームのソースコード
	 */
	private function getAjaxFormEdit($data){
		$ary=array();
		$ary[] = "\r\n";
		
		// 対象外リスト
		$exempts = array('limit','update_user','user_agent','ip_addr','created','modified');
	
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
			
			$field = $ent['proto_field'];
			if(in_array($field, $exempts)){
				continue;
			}

			
			
			// 和名を取得
			$wamei = $ent['Comment'];
			if(empty($wamei)){
				$wamei = $field;
			}
			
			// 必須フラグを取得
			$req = true;
			if($ent['Null'] == 'YES'){
				$req = false;
			}
			
			// id
			if($ent['type_c'] == 'id'){
				$ary[] = 
					"		<tr><td>ID: </td><td>\n".
					" 			<span class=\"id\"></span>\n".
					" 		</td></tr>\n";
			}
			
			// 無効フラグ
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = 
					"		<tr><td>無効： </td><td>\n".
					" 			<input type=\"checkbox\" name=\"delete_flg\" class=\"valid\"  />\n".
					" 		</td></tr>\n";
			}
		
			// int系
			elseif($ent['type_b'] == 'int'){
			
				$ary[] =
					"		<tr><td>{$wamei}: </td><td>\n".
					"				<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"^[0-9]+$\" maxlength=\"11\" title=\"数値を入力してください\" />\n".
					"				<label class=\"text-danger\" for=\"{$field}\"></label>\n".
					"			</td></tr>\n";
			}

				
			// string系
			if($ent['type_b'] == 'string'){
			
 				$str_len = $ent['str_len'];
			
				$ary[] =
					"		<tr><td>{$wamei}: </td><td>\n".
					" 			<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  maxlength=\"{$str_len}\" title=\"{$str_len}文字以内で入力してください\" />\n".
					" 			<label class=\"text-danger\" for=\"{$field}\"></label>\n".
					" 		</td></tr>\n";
			}
			
				
			// date系
			if($ent['type_b'] == 'date'){
			
				$ary[] =
						"		<tr><td>{$wamei}: </td><td>\n".
						" 			<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"\d{4}-\d{2}-\d{2}\" title=\"日付形式（Y-m-d）で入力してください(例：2012-12-12)\" />\n".
						" 			<label class=\"text-danger\" for=\"{$field}\"></label>\n".
						" 		</td></tr>\n";
			}
			
				
			// float系
			if($ent['type_b'] == 'float'){
				$ary[] =
				"		<tr><td>{$wamei}: </td><td>\n".
				"				<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"[+-]?[0-9]+[\.]?[0-9]*([eE][+-])?[0-9]*\" maxlength=\"11\" title=\"数値を入力してください\" />\n".
				"				<label class=\"text-danger\" for=\"{$field}\"></label>\n".
				"			</td></tr>\n";

			}
			
				
			// datetime系
			if($ent['type_b'] == 'datetime'){
			
				$ary[] = 
					"		<tr><td>{$wamei}: </td><td>\n".
					" 			<input type=\"text\" name=\"{$field}\" class=\"valid\" value=\"\"  pattern=\"\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\" title=\"日時形式（Y-m-d H:i:s）で入力してください(例：2012-12-12 12:12:12)\" />\n".
					" 			<label class=\"text-danger\" for=\"{$field}\"></label>\n".
					" 		</td></tr>\n";

			}
				

		
		
		
		}
		
		
		$ary[] = "\r\n\t";
		

		$code = join("\r\n",$ary);
		
		return $code;		
	}
	
	
	
	
	

	/**
	 * フィールドデータのソースコード作成
	 * @param array $data フィールドデータ
	 * @param string $modelName モデル名
	 * @param bool $withHead true:ヘッド部分を一緒に作成   false:定義部分のみ
	 * @return string フィールドデータのソースコード
	 */
	private function createFieldData($data,$modelName,$withHead=true){
		$ary=array();
		
		if($withHead){
			$ary[] = "///フィールドデータ";
			$ary[] = "public \$field_data=array(";
			$ary[] = "	'def'=>array(";
		}
		$ary[] = "\r\n";
		
		$clm_show_c = 0; // 列表示カウンター
		$clm_show_max = 8; // 最大列表示数
		$firstLoop = true; // 初回ループフラグ
		$clm_sort_no = 0; // 列並び番号
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
			
			if($ent['type_c'] == 'limit'){
				continue;
			}


			// 列表示カウンターが最大列表示数以下、文字数(varchar)が文字数基準以下なら、列表示フラグをONにする
			$clm_show=0; // 列表示フラグ
			$str_len = 0;
			if(!empty($ent['str_len'])){
				$str_len = $ent['str_len'];
			}
			if($clm_show_c < $clm_show_max && $str_len <= $this->_strLenRule){
				$clm_show = 1;
			}
		
			
			if($clm_show == 1){
				$clm_show_c++;
			}
			
			// 先頭のみコメント付きにする。
			$cmm1='';$cmm2='';$cmm3='';$cmm4='';
			if($firstLoop == true){
				$cmm1 = " // HTMLテーブルの列名";
				$cmm2 = " // SQLでの並び替えコード";
				$cmm3 = " // 列の並び順";
				$cmm4 = " // 初期の列表示   0:非表示   1:表示";
			}
			$firstLoop = false;
			
			
			// 和名を取得
			$name = $ent['Comment'];
			if(empty($name)){
				$name = $ent['Field'];;
			}
			
			// フィールド名を取得
			$field = $ent['Field'];
			

			
			// フィールドデータのソースコードを組み立てる
			$ary[] = "		'{$field}'=>array(";
			$ary[] = "			'name'=>'{$name}',{$cmm1}";
			$ary[] = "			'row_order'=>'{$modelName}.{$field}',{$cmm2}";
			$ary[] = "			'clm_sort_no'=>{$clm_sort_no},{$cmm3}";
			$ary[] = "			'clm_show'=>{$clm_show},{$cmm1}";
			$ary[] = "		),";
			
			$clm_sort_no ++;
			
		}
		

		
		if($withHead){
			$ary[] = "	);";
			$ary[] = ");";
		}
		$ary[] = "\r\n\t\t";
		
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		return $code;
	}
	
	
	
	
	
	/**
	 * 検索条件入力フォームのソースコード作成
	 * 
	 * @param array $data フィールドデータ
	 * @return string 入力フォームのソースコード
	 */
	private function createKjInput($data){
		$ary=array();
		$ary[] = "\r\n";
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
			
			// 和名を取得
			$name = $ent['Comment'];
			if(empty($name)){
				$name = $ent['Field'];;
			}
			
			
			if($ent['type_c'] == 'id'){
				$ary[] = "\$this->CrudBase->inputKjId(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'date1'){
				$ary[] = "\$this->CrudBase->inputKjNengetu(\$kjs,'kj_{$ent['proto_field']}','{$name}'); ";
			}
			
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = "\$this->CrudBase->inputKjDeleteFlg(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'created'){
				$ary[] = "\$this->CrudBase->inputKjCreated(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'modified'){
				$ary[] = "\$this->CrudBase->inputKjModified(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'limit'){
				$ary[] = "\$this->CrudBase->inputKjLimit(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'limit'){
				$ary[] = "\$this->CrudBase->inputKjLimit(\$kjs); ";
			}
			
			else{
				$width=120;
				if($ent['str_len'] > $this->_strLenRule){
					$width = 240;
				}
				
				$ary[] = "\$this->CrudBase->inputKjText(\$kjs,'kj_{$ent['kj_field']}','{$name}',{$width});" ;
				
			}
			
			
			
		}
		
		$ary[] = "\r\n\t\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t\t".$v;
		}
		
		$code = join("\r\n",$ary);
		
		return $code;
	}
	
	
	



	/**
	 *  WHEREのソースコード作成
	 * @param array $data フィールドデータ
	 * @param string $modelName モデル名
	 * @return string WHEREのソースコード
	 */
	private function createKjConditions($data,$modelName){
		$ary=array();
		
		$ary[] = "\r\n";

		foreach($data as $ent){
	
			if($ent['type_c'] == 'limit'){
				continue;
			}
	
			$field = $ent['proto_field'];
			$kj_field = $ent['kj_field'];
	
			if($ent['type_c'] == 'date1'){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} >= '{\$kjs['kj_{$kj_field}']}'\";";
				$ary[] = "	}";
			}
	
			elseif($ent['type_c'] == 'date2'){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} <= '{\$kjs['kj_{$kj_field}']}'\";";
				$ary[] = "	}";
			}
	
			elseif($ent['type_c'] == 'string' && $ent['str_len'] > $this->_strLenRule){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} LIKE '%{\$kjs['kj_{$kj_field}']}%'\";";
				$ary[] = "	}";
			}
	
			elseif($ent['type_c'] == 'flg'){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}']) || \$kjs['kj_{$kj_field}'] ==='0' || \$kjs['kj_{$kj_field}'] ===0){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} = {\$kjs['kj_{$kj_field}']}\";";
				$ary[] = "	}";
			}
	
			elseif($ent['type_c'] == 'kj_delete_flg'){
				$ary[] = "	if(!empty(\$kjs['kj_delete_flg']) || \$kjs['kj_delete_flg'] ==='0' || \$kjs['kj_delete_flg'] ===0){";
				$ary[] = "		\$cnds[]=\"{$modelName}.delete_flg = {\$kjs['kj_delete_flg']}\";";
				$ary[] = "	}";
			}
	
			elseif($ent['type_c'] == 'created' || $ent['type_c'] == 'modified'){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$kj_{$kj_field}=\$kjs['kj_{$kj_field}'].' 00:00:00';";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} >= '{\$kj_{$kj_field}}'\";";
				$ary[] = "	}";
			}
	
			else{
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} = '{\$kjs['kj_{$kj_field}']}'\";";
				$ary[] = "	}";
			}

		}
	

		$ary[] = "\r\n\t\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
	
		
		$code = join("\r\n",$ary);
	
		return $code;
	
	}
	
	
	

	/**
	 * 検索条件バリデーションのソースコード作成
	 * @param array $data フィールドデータ
	 * @param bool $withHead true:ヘッド部分を一緒に作成   false:定義部分のみ
	 * @return string バリデーションのソースコード
	 */
	private function createKjValidation($data,$withHead=true){
		
		$ary=array();
		
		if($withHead){
			$ary[] = "	/// 検索条件のバリデーション";
			$ary[] = "	public \$kjs_validate = array(";
		}
		
		$ary[] = "\r\n";
				
		foreach($data as $ent){
			
			
			// limitのバリデーションは無し
			if($ent['type_c'] == 'limit'){
				continue;
			}
			
			// 削除フラグのバリデーションは無し
			if($ent['type_c'] == 'delete_flg'){
				continue;
			}
			
			//フラグ系のバリデーションは無し
			if($ent['type_c'] == 'flg'){
				continue;
			}
			
			
			
			// フィールド名
			$field = $ent['kj_field'];
			
			// 和名を取得
			$name = $ent['Comment'];
			if(empty($name)){
				$name = $field;
			}
		
			
			// int系
			if($ent['type_b'] == 'int'){
				
				$ary[] =
					"			'kj_{$field}' => array(\n".
					"				'naturalNumber'=>array(\n".
					"					'rule' => array('naturalNumber', true),\n".
					"					'message' => '{$name}は数値を入力してください',\n".
					"					'allowEmpty' => true\n".
					"				),\n".
					"			),\n";
			}
		
			
			// string系
			if($ent['type_b'] == 'string'){
				
				$str_len = $ent['str_len'];
				
				$ary[] =
					"			'kj_{$field}'=> array(\n".
					"				'maxLength'=>array(\n".
					"					'rule' => array('maxLength', {$str_len}),\n".
					"					'message' => '{$name}は{$str_len}文字以内で入力してください',\n".
					"					'allowEmpty' => true\n".
					"				),\n".
					"			),\n";
			}
		
			
			// date系
			if($ent['type_b'] == 'date'){
				
				$ary[] =
					"			'kj_{$field}'=> array(\n".
					"				'rule' => array( 'date', 'ymd'),\n".
					"				'message' => '{$name}は日付形式【yyyy-mm-dd】で入力してください。',\n".
					"				'allowEmpty' => true\n".
					"			),\n";
			}
		
			
			// float系
			if($ent['type_b'] == 'float'){
				
				$ary[] =
					"			'kj_{$field}'=> array(\n".
					"				'range'=>array(\n".
					"					'rule' => array('range', -100000000,100000000),\n".
					"					'message' => '{$name}は数値を入力してください。（小数可、最大10億）',\n".
					"					'allowEmpty' => true,\n".
					"				),\n".
					"			),\n";
			}
		
			
			// datetime系
			if($ent['type_b'] == 'datetime'){
				
				$ary[] =
					"			'kj_{$field}'=> array(\n".
					"				'maxLength'=>array(\n".
					"					'rule' => array('maxLength', 20),\n".
					"					'message' => '{$name}は20文字以内で入力してください',\n".
					"					'allowEmpty' => true\n".
					"				),\n".
					"			),\n";
			}
			
	

		}
		
		if($withHead){
			$ary[] = "	);";
		}
		$ary[] = "\r\n\t\t";
		


		$code = join("\r\n",$ary);
		
		
		
		return $code;
		
	}
	
	

	/**
	 * 検索条件定義の作成
	 * 
	 * @param array $data フィールドデータ
	 * @param bool $withHead true:ヘッド部分を一緒に作成   false:定義部分のみ
	 * @return string 検索条件定義のソースコード
	 */
	private function createKjDefine($data,$withHead=true){
		
		$ary=array();
		$ary[] = "\r\n";
		
		if($withHead){
			$ary[] = "/// 検索条件定義";
			$ary[] = "public \$kensakuJoken=array(";
		}

		
		
		foreach($data as $ent){
			$field = $ent['kj_field'];
			$def = 'null';
			if($field == 'limit'){
				$def = '50';
			}
			
			$ary[] = "	array('name'=>'kj_{$field}','def'=>{$def}),";
			
			// 年月用
			if($ent['type_c'] == 'date2'){
				$proto_field = $ent['proto_field'];
				$ary[] = "	array('name'=>'kj_{$proto_field}_ym','def'=>null),";
			}
		}
		if($withHead){
			$ary[] = ");";
		}
		
		$ary[] = "\r\n\t\t";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t\t".$v;
		}
		

		$code = join("\r\n",$ary);
		
		
		
		return $code;
		
	}
	

	
	

	/**
	 * 文字列系の文字数をセット
	 * 
	 * @param array $data フィールドデータ
	 * @return array 文字数をセット後のフィールドデータ
	 */
	private function setStrLen($data){
		
		foreach($data as $i => $ent){
			if($ent['Type']=='text'){
				$data[$i]['str_len'] = 1000;
			}else{
				$str = $ent['Type'];
		
				$re = '/(\()(.*)(\))/';
				preg_match($re, $str,$match);
				$str2=null;
				if(!empty($match[2])){
					$str2 = $match[2];
				}
				
				$data[$i]['str_len'] = $str2;
			}
			
		}
		

		return $data;
	}
	
	
	

	/**
	 * フィールドの分類
	 * 
	 * @param array $data フィールドデータ
	 * @return array 分類後のフィールドデータ
	 */
	private function classifying($data){
		
		
		// 共通フィールドの分類
		foreach($data as $i => $ent){
			if($ent['Field'] == 'id'){
				$ent['type_b'] = 'int';
				$ent['type_c'] = 'id';
				$data[$i] = $ent;
			}
			
			if($ent['Field'] == 'delete_flg'){
				$ent['type_b'] = 'int';
				$ent['type_c'] = 'delete_flg';
				$data[$i] = $ent;
			}
			
			elseif($ent['Field'] == 'created'){
				$ent['type_b'] = 'datetime';
				$ent['type_c'] = 'created';
				$data[$i] = $ent;
			}
			
			elseif($ent['Field'] == 'modified'){
				$ent['type_b'] = 'datetime';
				$ent['type_c'] = 'modified';
				$data[$i] = $ent;
			}
			
			elseif($ent['Field'] == 'limit'){
				$ent['type_b'] = 'int';
				$ent['type_c'] = 'limit';
				$data[$i] = $ent;
			}

		}
		
		
		
		// 型から分類
		foreach($data as $i => $ent){
			if(isset($ent['type_b'])){
				continue;
			}
			
			$typ=$ent['Type'];
			
			$typ=substr($typ,0,(strpos($typ,'(')));
	
			if($typ == 'datetime'){
				$ent['type_b'] = 'datetime';
				$ent['type_c'] = 'datetime';
				$data[$i] = $ent;
			}
			
			elseif($typ == 'date'){
				$ent['type_b'] = 'date';
				$ent['type_c'] = 'date';
				$data[$i] = $ent;
			}
			
			elseif($typ == 'float' || $typ == 'double' || $typ == 'decimal' || $typ == 'numeric'){
				$ent['type_b'] = 'float';
				$ent['type_c'] = 'float';
				$data[$i] = $ent;
			}
			
			elseif(strpos($typ,'decimal') !== false){
				
				$ent['type_b'] = 'float';
				$ent['type_c'] = 'float';
				$data[$i] = $ent;
			}
			
			elseif($typ == 'text'){
			
				$ent['type_b'] = 'string';
				$ent['type_c'] = 'string';
			}
			
			elseif(strpos($typ,'char') !== false){
				
				$ent['type_b'] = 'string';
				$ent['type_c'] = 'string';
				$data[$i] = $ent;
			}
			
			elseif(strpos($typ,'tinyint')!==false){
				$ent['type_b'] = 'int';
				$ent['type_c'] = 'flg';
				$data[$i] = $ent;
			}
			
			elseif(strpos($typ,'int')!==false){
				$ent['type_b'] = 'int';
				$ent['type_c'] = 'int';
				$data[$i] = $ent;
			}
			
			
			else{
				$ent['type_b'] = 'string';
				$ent['type_c'] = 'string';
				$data[$i] = $ent;
				
			}
			
			

			
			
		}
		
		
		// 未セットがあればnullをセット
		foreach($data as $i => $ent){
			if(empty($ent['type_b'])){
				$ent['type_b'] =null;
				$data[$i] = $ent;
			}
			if(empty($ent['type_c'])){
				$ent['type_c'] =null;
				$data[$i] = $ent;
			}
		}
		
		
		
		// コメントから分類
		foreach($data as $i => $ent){
			
			if($ent['type_c'] == 'int' || $ent['type_c'] == 'float'){
			
				$l_str1=mb_substr($ent['Comment'],-1);
				if($l_str1 == '額' || $l_str1 == '金' || $l_str1 == '費'){
					
					$ent['type_c'] = 'money';
					$data[$i] = $ent;
				}
				
				
				$l_str3=mb_substr($ent['Comment'],-3);
				if($l_str3 == 'フラグ' || $l_str3 == 'ﾌﾗｸﾞ'){
					$ent['type_c'] = 'flg';
					$data[$i] = $ent;
				}
				
			}
			

		}
		

		return $data;
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 * limitフィールドを追加する
	 * 
	 * @param array $data フィールドデータ
	 * @return array limitフィールド追加後のフィールドデータ
	 */
	private function addLimit($data){
		
		$data[] = array(
				'Field' => 'limit',
				'kj_field' => 'limit',
				'proto_field' => 'limit',
				'Type' => 'int(11)',
				'Collation' => null,
				'Null' => 'YES',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
				'Privileges' => 'select,insert,update,references',
				'Comment' => '表示件数',
		);
		
		return $data;
	}
	
	
	
	

	/**
	 * 日付系フィールドを範囲検索ように分割する
	 * 
	 * X日付 → X日付1 と X日付2 に分解する
	 * 
	 * @param array $data フィールドデータ
	 * @return array 日付系分割後のフィールドデータ
	 */
	private function convRangeDate($data){
		$data2 = array();
		foreach($data as $ent){
			if($ent['Type'] == 'date'){
				
				$ent1 = $ent;
				$ent1['kj_field'] = $ent1['Field'].'1';
				$ent1['type_b'] = 'date';
				$ent1['type_c'] = 'date1';
				$data2[] = $ent1;
				
				$ent2 = $ent;
				$ent2['kj_field'] = $ent2['Field'].'2';
				$ent2['type_b'] = 'date';
				$ent2['type_c'] = 'date2';
				$ent2['range2_flg'] = 1;
				$data2[] = $ent2;
				

				
				
			}else{
				$ent['kj_field'] = $ent['Field'];
				$data2[]=$ent;
			}
		}
		
		
		return $data2;
	}
	
	
	/**
	 * プロトフィールドへのセット
	 * 
	 * @param フィールドデータ  $data
	 * @return プロトフィールドセット後のフィールドデータ
	 */
	private function setProtoField($data){
		foreach($data as $i => $ent){
			$data[$i]['proto_field'] = $ent['Field'];
		}
		return $data;
	}
	
	
	/**
	 * テーブル名からモデル名を作成する
	 * @param string $tblName テーブル名
	 * @return string モデル名
	 */
	private function convModelName($tblName){
		
		// 末尾の一文字を削る（sの除去）
		$modelName = mb_substr($tblName,0,mb_strlen($tblName)-1);
		
		// キャメル記法に変換する
		$modelName = $this->camelize($modelName); 
		
		return $modelName;
	}
	
	
	/**
	 * キャメルケースにスネークケースから変換する
	 *
	 * 先頭も大文字になる。
	 *
	 * @param string $str スネークケースの文字列
	 * @return キャメルケースの文字列
	 */
	private function camelize($str) {
		$str = strtr($str, '_', ' ');
		$str = ucwords($str);
		return str_replace(' ', '', $str);
	}

	
	
	
	/**
	 * 2つの印文字に挟まれた文字を置換する
	 * @param string $targetStr 対象文字
	 * @param string $mark1 印文字1
	 * @param string $mark2 印文字2
	 * @param string $replaceStr 置き換え文字
	 * @return 置換後の文字
	 *
	 * @note
	 * 印文字が対象文字に存在しない場合は、置換は行わず、対象文字をそのまま返す。
	 */
	private function replaceHasami($targetStr,$mark1,$mark2,$replaceStr){
	
		if(empty($targetStr)){
			return $targetStr;
		}
	
		$a1 = mb_strpos($targetStr,$mark1);
		if($a1===false){
			return $targetStr;
		}
	
		$markLen1 = mb_strlen($mark1);
		$s1 = mb_substr($targetStr,0,$a1 + $markLen1);
	
		$targetStrLen = mb_strlen($targetStr);
		$s2 = mb_substr($targetStr,$a1 + $markLen1);
	
		$a2 = mb_strpos($s2,$mark2);
		if($a2===false){
			return $targetStr;
		}
		$s2 = mb_substr($s2,$a2);
	
		$s3 = $s1.$replaceStr.$s2;
	
		return $s3;
	}

	
	
	
	
}



















