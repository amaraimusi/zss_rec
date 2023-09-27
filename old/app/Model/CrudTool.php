<?php
App::uses('Model', 'Model');

/**
 * CRUD自動生成ツール(検索条件まわり）
 * 
 * @note
 * CrudBaseに対応した、いくつかのパラメータを自動生成する。
 * 
 * 支援ツールなので完璧なパラメータを作成するツールではない。
 * 共通フィールドを除き、セレクトボックスには対応しない。
 * 文字列検索のルールとして、50文字以内なら完全一致、超えるなら部分一致検索とする。
 * Tiny Intなどフラグ系および、コメントに「フラグ」が、フラグ型データと見なす。
 * コメントの末尾が「額」、「金」、「費」であるなら金額用と判定する。
 * 日付系は自動的に範囲検索になる。
 * 
 * @date 2016-7-27 | 2016-8-4
 * @version 1.1.3
 * @author k-uehara
 *
 */
class CrudTool  extends Model{
	
	public $useTable = false;
	
	private $_strLenRule = 50;//文字数基準
	
	
	/**
	 * 検索条件のパラメータ情報を自動生成する。
	 * 
	 * 各システムで共通するパターンを自動で作成する。
	 * 
	 * @param string $tblName DBテーブル名
	 * @param string $dbName DB名（省略時は当システムのデフォルトDB)
	 * @return 検索条件のパラメータ情報
	 */
	public function autoCreate($tblName,$dbName=null){
		
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
		
		$kj_define = $this->createKjDefine($data); // 検索条件定義の作成
		
		$kj_valid = $this->createKjValidation($data); // 検索条件バリデーションのソースコード作成
		
		$kj_conditions = $this->createKjConditions($data,$modelName); // WHEREのソースコード作成
		
		$kj_input = $this->createKjInput($data); // 検索条件入力フォームのソースコード作成
		
		$field_data = $this->createFieldData($data,$modelName); // フィールドデータのソースコード作成
		
		$field_table = $this->createFieldTable($data); // 一覧テーブルのソースコードを作成
		
		$detail_preview = $this->createDetailPreview($data); // 詳細ページのプレビューソースコードを作成
		
		$edit_input = $this->createEditInput($data); // 編集入力フォームのソースコード作成
		
		$edit_field = $this->createEditField($data); // 編集フィールド定義のソースコードを作成
		
		$edit_validation = $this->createEditValidation($data); // 編集バリデーションのソースコード作成
		

		$codes['table_data'] = $data;
		$codes['kj_define'] = $kj_define;
		$codes['kj_valid'] = $kj_valid;
		$codes['kj_conditions'] = $kj_conditions;
		$codes['kj_input'] = $kj_input;
		$codes['field_data'] = $field_data;
		$codes['field_table'] = $field_table;
		$codes['detail_preview'] = $detail_preview;
		$codes['edit_input'] = $edit_input;
		$codes['edit_field'] = $edit_field;
		$codes['edit_validation'] = $edit_validation;
		
		
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
	 * @return string 編集バリデーションのソースコード
	 */
	private function createEditValidation($data){
		$ary=array();
		
		$ary[] = "	/// 編集用バリデーション";
		$ary[] = "	\$this-&gt;edit_validate = array(";
		
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
				"		'{$field}' => array(\n".
				"			'naturalNumber'=>array(\n".
				"				'rule' => array('naturalNumber', true),\n".
				"				'message' => '{$wamei}は数値を入力してください',\n".
				"				'allowEmpty' => true\n".
				"			),\n".
				"		),\n";
			}
		
				
			// string系
			if($ent['type_b'] == 'string'){
		
				$str_len = $ent['str_len'];
		
				$ary[] =
				"		'{$field}'=> array(\n".
				"			'maxLength'=>array(\n".
				"				'rule' => array('maxLength', {$str_len}),\n".
				"				'message' => '{$wamei}は{$str_len}文字以内で入力してください',\n".
				"				'allowEmpty' => true\n".
				"			),\n".
				"		),\n";
			}
		
				
			// date系
			if($ent['type_b'] == 'date'){
		
				$ary[] =
				"		'{$field}'=> array(\n".
				"			'rule' => array( 'date', 'ymd'),\n".
				"			'message' => '{$wamei}は日付形式【yyyy-mm-dd】で入力してください。',\n".
				"			'allowEmpty' => true\n".
				"		),\n";
			}
		
				
			// float系
			if($ent['type_b'] == 'float'){
		
				$ary[] =
				"		'{$field}'=> array(\n".
				"			'range'=>array(\n".
				"				'rule' => array('range', -100000000,100000000),\n".
				"				'message' => '{$wamei}は数値を入力してください。（小数可、最大10億）',\n".
				"				'allowEmpty' => true,\n".
				"			),\n".
				"		),\n";
			}
		
				
			// datetime系
			if($ent['type_b'] == 'datetime'){
		
				$ary[] =
				"		'{$field}'=> array(\n".
				"			'maxLength'=>array(\n".
				"				'rule' => array('maxLength', 20),\n".
				"				'message' => '{$wamei}は20文字以内で入力してください',\n".
				"				'allowEmpty' => true\n".
				"			),\n".
				"		),\n";
			}
				
		
		
		}
		
		
		$ary[] = "	);";
		
		
		$code = join("\n",$ary);
		
		
		
		return $code;		
	}
	
	
	
	
	
	
	
	/**
	 * 編集フィールド定義のソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string 編集フィールド定義のソースコード
	 */
	private function createEditField($data){
		$ary=array();
		
		$ary[] = "/// 編集エンティティ定義";
		$ary[] = "\$this-&gt;entity_info=array(";

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
			
			$ary[] = "	array('name'=&gt;'{$field}','def'=&gt;{$def}),";
			
				
		}
		
		$ary[] = ");";
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\n",$ary);
		
		return $code;		
	}
	
	
	
	
	
	
	/**
	 * 編集入力フォームのソースコード作成
	 * @param array $data フィールドデータ
	 * @return string 編集入力フォームのソースコード
	 */
	private function createEditInput($data){
		$ary=array();
		
		
		
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
				$ary[] = "\$this-&gt;CrudBase-&gt;editDeleteFlg(\$ent,\$mode);";
			}
			
			// 文字サイズが指定サイズを超えるならテキストエリアとする。
			elseif($str_len > $this->_strLenRule){
				$ary[] = "\$this-&gt;CrudBase-&gt;editTextArea(\$ent,'{$field}','{$wamei}');";
			}

			// その他はテキストボックスとする
			else{
				$ary[] = "\$this-&gt;CrudBase-&gt;editText(\$ent,'{$field}','{$wamei}',{$width});";
			}
		
		}

		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\n",$ary);
		
		return $code;		
	}
	
	
	
	

	/**
	 * 詳細ページのプレビューソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string プレビューのソースコード
	 */
	private function createDetailPreview($data){
		$ary=array();

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
				$ary[] = "\$this-&gt;CrudBase-&gt;tpId(\$ent['id'],'{$name}');";
			}
				
			elseif($ent['type_c'] == 'date' || $ent['type_c'] == 'float' || $ent['type_c'] == 'int'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tpPlain(\$ent['{$field}'],'{$name}');";
			}
				
			elseif($ent['type_c'] == 'string'){
				if($ent['str_len'] <= $this->_strLenRule){
					$ary[] = "\$this-&gt;CrudBase-&gt;tpStr(\$ent['{$field}'],'{$name}');";
				}else{
					$ary[] = "\$this-&gt;CrudBase-&gt;tpNote(\$ent['{$field}'],'{$name}');";
				}
			
			}
				
			elseif($ent['type_c'] == 'money'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tpMoney(\$ent['{$field}'],'{$name}');";
			}
				
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tpDeleteFlg(\$ent['delete_flg'],'有無');";
			}
				
			elseif($ent['type_c'] == 'created' || $ent['type_c'] == 'modified' || $ent['type_c'] == 'datetime'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tpPlain(\$ent['{$field}'],'{$name}');";
			}
				
			else{
				$ary[] = "\$this-&gt;CrudBase-&gt;tpPlain(\$ent['{$field}'],'{$name}');";
			}

		
		}
		
		
		
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\n",$ary);
		
		return $code;
	}
	
	
	
	
	

	/**
	 * 一覧テーブルのソースコードを作成
	 * @param array $data フィールドデータ
	 * @return string 一覧テーブルのソースコード
	 */
	private function createFieldTable($data){
		$ary=array();
		

		
		$ary[] = "echo \"&lt;tr id=i{\$ent['id']} &gt;\";";
		
		foreach($data as $ent){
			if(!empty($ent['range2_flg'])){
				continue;
			}
				
			if($ent['type_c'] == 'limit'){
				continue;
			}
			
			$field = $ent['proto_field'];
				
			if($ent['type_c'] == 'id'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tdId(\$ent['id']);";
			}
			
			elseif($ent['type_c'] == 'date' || $ent['type_c'] == 'float' || $ent['type_c'] == 'int'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tdPlain(\$ent['{$field}']);";
			}
			
			elseif($ent['type_c'] == 'string'){
				if($ent['str_len'] <= $this->_strLenRule){
					$ary[] = "\$this-&gt;CrudBase-&gt;tdStr(\$ent['{$field}']);";
				}else{
					$ary[] = "\$this-&gt;CrudBase-&gt;tdNote(\$ent['{$field}']);";
				}
				
			}
			
			elseif($ent['type_c'] == 'money'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tdMoney(\$ent['{$field}']);";
			}
			
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tdDeleteFlg(\$ent['delete_flg']);";
			}
			
			elseif($ent['type_c'] == 'created' || $ent['type_c'] == 'modified' || $ent['type_c'] == 'datetime'){
				$ary[] = "\$this-&gt;CrudBase-&gt;tdPlain(\$ent['{$field}']);";
			}
			
			else{
				$ary[] = "\$this-&gt;CrudBase-&gt;tdPlain(\$ent['{$field}']);";
			}
		

				
		}
		
		
		$ary[] = "echo '&lt;/tr&gt;';";
		$ary[] = "";
		
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\n",$ary);
		
		return $code;
	}
	
	
	

	/**
	 * フィールドデータのソースコード作成
	 * @param array $data フィールドデータ
	 * @param string $modelName モデル名
	 * @return string フィールドデータのソースコード
	 */
	private function createFieldData($data,$modelName){
		$ary=array();
		
		$ary[] = "///フィールドデータ";
		$ary[] = "public \$field_data=array(";
		$ary[] = "	'def'=&gt;array(";
		
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
			$ary[] = "		'{$field}'=&gt;array(";
			$ary[] = "			'name'=&gt;'{$name}',{$cmm1}";
			$ary[] = "			'row_order'=&gt;'{$modelName}.{$field}',{$cmm2}";
			$ary[] = "			'clm_sort_no'=&gt;{$clm_sort_no},{$cmm3}";
			$ary[] = "			'clm_show'=&gt;{$clm_show},{$cmm1}";
			$ary[] = "		),";
			
			$clm_sort_no ++;
			
		}
		
		$ary[] = "	),";
		$ary[] = ");";
		$ary[] = "";
		
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\n",$ary);
		
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
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjId(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'date1'){
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjNengetu(\$kjs,'kj_{$ent['proto_field']}','{$name}'); ";
			}
			
			elseif($ent['type_c'] == 'delete_flg'){
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjDeleteFlg(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'created'){
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjCreated(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'modified'){
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjModified(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'limit'){
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjLimit(\$kjs); ";
			}
			
			elseif($ent['type_c'] == 'limit'){
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjLimit(\$kjs); ";
			}
			
			else{
				$width=120;
				if($ent['str_len'] > $this->_strLenRule){
					$width = 240;
				}
				
				$ary[] = "\$this-&gt;CrudBase-&gt;inputKjText(\$kjs,'kj_{$ent['kj_field']}','{$name}',{$width});" ;
				
			}
			
			
			
		}
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		
		$code = join("\n",$ary);
		
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

		foreach($data as $ent){
	
			if($ent['type_c'] == 'limit'){
				continue;
			}
	
			$field = $ent['proto_field'];
			$kj_field = $ent['kj_field'];
	
			if($ent['type_c'] == 'date1'){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} &gt;= '{\$kjs['kj_{$kj_field}']}'\";";
				$ary[] = "	}";
			}
	
			elseif($ent['type_c'] == 'date2'){
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} &lt;= '{\$kjs['kj_{$kj_field}']}'\";";
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
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} &gt;= '{\$kj_{$kj_field}}'\";";
				$ary[] = "	}";
			}
	
			else{
				$ary[] = "	if(!empty(\$kjs['kj_{$kj_field}'])){";
				$ary[] = "		\$cnds[]=\"{$modelName}.{$field} = '{\$kjs['kj_{$kj_field}']}'\";";
				$ary[] = "	}";
			}

		}
	

		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
	
		$code = join("\n",$ary);
	
		return $code;
	
	}
	
	
	

	/**
	 * 検索条件バリデーションのソースコード作成
	 * @param array $data フィールドデータ
	 * @return string バリデーションのソースコード
	 */
	private function createKjValidation($data){
		
		$ary=array();
		
		$ary[] = "	/// 検索条件のバリデーション";
		$ary[] = "	public \$kjs_validate = array(";
				
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
					"		'kj_{$field}' => array(\n".
					"			'naturalNumber'=>array(\n".
					"				'rule' => array('naturalNumber', true),\n".
					"				'message' => '{$name}は数値を入力してください',\n".
					"				'allowEmpty' => true\n".
					"			),\n".
					"		),\n";
			}
		
			
			// string系
			if($ent['type_b'] == 'string'){
				
				$str_len = $ent['str_len'];
				
				$ary[] =
					"		'kj_{$field}'=> array(\n".
					"			'maxLength'=>array(\n".
					"				'rule' => array('maxLength', {$str_len}),\n".
					"				'message' => '{$name}は{$str_len}文字以内で入力してください',\n".
					"				'allowEmpty' => true\n".
					"			),\n".
					"		),\n";
			}
		
			
			// date系
			if($ent['type_b'] == 'date'){
				
				$ary[] =
					"		'kj_{$field}'=> array(\n".
					"			'rule' => array( 'date', 'ymd'),\n".
					"			'message' => '{$name}は日付形式【yyyy-mm-dd】で入力してください。',\n".
					"			'allowEmpty' => true\n".
					"		),\n";
			}
		
			
			// float系
			if($ent['type_b'] == 'float'){
				
				$ary[] =
					"		'kj_{$field}'=> array(\n".
					"			'range'=>array(\n".
					"				'rule' => array('range', -100000000,100000000),\n".
					"				'message' => '{$name}は数値を入力してください。（小数可、最大10億）',\n".
					"				'allowEmpty' => true,\n".
					"			),\n".
					"		),\n";
			}
		
			
			// datetime系
			if($ent['type_b'] == 'datetime'){
				
				$ary[] =
					"		'kj_{$field}'=> array(\n".
					"			'maxLength'=>array(\n".
					"				'rule' => array('maxLength', 20),\n".
					"				'message' => '{$name}は20文字以内で入力してください',\n".
					"				'allowEmpty' => true\n".
					"			),\n".
					"		),\n";
			}
			
	

		}
		
		$ary[] = "	);";

		$code = join("\n",$ary);
		
		
		
		return $code;
		
	}
	
	

	/**
	 * 検索条件定義の作成
	 * 
	 * @param array $data フィールドデータ
	 * @return string 検索条件定義のソースコード
	 */
	private function createKjDefine($data){
		
		$ary=array();
		$ary[] = "/// 検索条件定義";
		$ary[] = "public \$kensakuJoken=array(";

		
		
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
		$ary[] = ");";
		
		
		// インデント1
		foreach($ary as $i=>$v){
			$ary[$i] = "\t".$v;
		}
		

		$code = join("\n",$ary);
		
		
		
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
			$str = $ent['Type'];
	
			$re = '/(\()(.*)(\))/';
			preg_match($re, $str,$match);
			$str2=null;
			if(!empty($match[2])){
				$str2 = $match[2];
			}
			
			$data[$i]['str_len'] = $str2;
			
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

	

	
	
	
	
}



















