<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 日誌Ａ
 * 
 * @note
 * 日誌Ａ画面では日誌Ａ一覧を検索閲覧、編集など多くのことができます。
 * 
 * @date 2015-9-16 | 2018-7-14
 * @version 4.0
 *
 */
class DiaryAController extends CrudBaseController {

	/// 名称コード
	public $name = 'DiaryA';
	
	/// 使用しているモデル
	public $uses = array('DiaryA','CrudBase');
	
	/// オリジナルヘルパーの登録
	public $helpers = array('CrudBase');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='DiaryA.diary_date';
	
	/// デフォルトソートタイプ	  0:昇順 1:降順
	public $defSortType = 1;
	
	/// 検索条件情報の定義
	public $kensakuJoken=array();

	/// 検索条件のバリデーション
	public $kjs_validate = array();

	///フィールドデータ
	public $field_data=array();

	/// 編集エンティティ定義
	public $entity_info=array();

	/// 編集用バリデーション
	public $edit_validate = array();
	
	// 当画面バージョン (バージョンを変更すると画面に新バージョン通知とクリアボタンが表示されます。）
	public $this_page_version = '1.9.1'; 



	public function beforeFilter() {

// 		// 未ログイン中である場合、未認証モードの扱いでページ表示する。
// 		if(empty($this->Auth->user())){
// 			$this->Auth->allow(); // 未認証モードとしてページ表示を許可する。
// 		}
	
		parent::beforeFilter();
	
		$this->initCrudBase();// フィールド関連の定義をする。
	
	}

	/**
	 * indexページのアクション
	 *
	 * indexページでは日誌Ａ一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
	public function index() {
		
		// CrudBase共通処理（前）
		$crudBaseData = $this->indexBefore('DiaryA');//indexアクションの共通先処理(CrudBaseController)
		
		//一覧データを取得
		$data = $this->DiaryA->findData2($crudBaseData);

		// CrudBase共通処理（後）
		$crudBaseData = $this->indexAfter($crudBaseData);//indexアクションの共通後処理
		
		// CBBXS-1020

		// CBBXE
		
// 		// ■■■□□□■■■□□□■■■□□□テスト
// 		App::uses('DbExport','Vendor/Wacg');
// 		App::uses('DaoForCake','Model');
// 		$dao = new DaoForCake();
// 		$dbExp = new DbExport();
// 		$dbExp->test($dao);
		
	
		
		$this->set($crudBaseData);
		$this->set(array(
			'title_for_layout'=>'日誌Ａ',
			'data'=> $data,
		));
		
		//当画面系の共通セット
		$this->setCommon();


	}

	/**
	 * 詳細画面
	 * 
	 * 日誌Ａ情報の詳細を表示します。
	 * この画面から入力画面に遷移できます。
	 * 
	 */
	public function detail() {
		
		$res=$this->edit_before('DiaryA');
		$ent=$res['ent'];
	

		$this->set(array(
				'title_for_layout'=>'日誌Ａ・詳細',
				'ent'=>$ent,
		));
		
		//当画面系の共通セット
		$this->setCommon();
	
	}













// 	/**
// 	 * 入力画面
// 	 * 
// 	 * 入力フォームにて値の入力が可能です。バリデーション機能を実装しています。
// 	 * 
// 	 * URLクエリにidが付属する場合は編集モードになります。
// 	 * idがない場合は新規入力モードになります。
// 	 * 
// 	 */
// 	public function edit() {
		
// 		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
// 		$res=$this->edit_before('DiaryA');
// 		$ent=$res['ent'];

// 		$this->set(array(
// 				'title_for_layout'=>'日誌Ａ・編集',
// 				'ent'=>$ent,
// 		));
		
// 		//当画面系の共通セット
// 		$this->setCommon();

// 	}
	
// 	 /**
// 	 * 登録完了画面
// 	 * 
// 	 * 入力画面の更新ボタンを押し、DB更新に成功した場合、この画面に遷移します。
// 	 * 入力エラーがある場合は、入力画面へ、エラーメッセージと共にリダイレクトで戻ります。
// 	 */
// 	public function reg(){
		
		
// 		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
// 		$res=$this->reg_before('DiaryA');
// 		$ent=$res['ent'];
		
// 		$regMsg="<p id='reg_msg'>更新しました。</p>";

// 		//オリジナルバリデーション■■■□□□■■■□□□■■■□□□
// 		//$xFlg=$this->validDiaryA();
// 		$xFlg=true;
// 		if($xFlg==false){
// 			//エラーメッセージと一緒に編集画面へ、リダイレクトで戻る。
// 			$this->errBackToEdit("オリジナルバリデーションのエラー");
// 		}
		
// 		//★DB保存
// 		$this->DiaryA->begin();//トランザクション開始
// 		$ent=$this->DiaryA->saveEntity($ent);//登録
// 		$this->DiaryA->commit();//コミット

// 		$this->set(array(
// 				'title_for_layout'=>'日誌Ａ・登録完了',
// 				'ent'=>$ent,
// 				'regMsg'=>$regMsg,
// 		));
		
// 		//当画面系の共通セット
// 		$this->setCommon();

// 	}
	
	
	
	
	/**
	 * DB登録
	 *
	 * @note
	 * Ajaxによる登録。
	 * 編集登録と新規入力登録の両方に対応している。
	 */
	public function ajax_reg(){
		App::uses('Sanitize', 'Utility');
		$this->autoRender = false;//ビュー(ctp)を使わない。
		$errs = array(); // エラーリスト
		
		// 認証中でなければエラー
		if(empty($this->Auth->user())){
			return 'Error:login is needed.';// 認証中でなければエラー
		}
		
		// 未ログインかつローカルでないなら、エラーアラートを返す。
		if(empty($this->Auth->user()) && $_SERVER['SERVER_NAME']!='localhost'){
			return '一般公開モードでは編集登録はできません。';
		}
		
		// JSON文字列をパースしてエンティティを取得する
		$json=$_POST['key1'];
		$ent = json_decode($json,true);
		
		// 登録パラメータ
		$reg_param_json = $_POST['reg_param_json'];
		$regParam = json_decode($reg_param_json,true);

		// アップロードファイルが存在すればエンティティにセットする。
		$upload_file = null;
		if(!empty($_FILES["upload_file"])){
			$upload_file = $_FILES["upload_file"]["name"];
			$ent['diary_a_fn'] = $upload_file;
		}

	
		// 更新ユーザーなど共通フィールドをセットする。
		$ent = $this->setCommonToEntity($ent);
	
		// エンティティをDB保存
		$this->DiaryA->begin();
		$ent = $this->DiaryA->saveEntity($ent,$regParam);
		$this->DiaryA->commit();//コミット

		if(!empty($upload_file)){
			
			// ファイルパスを組み立て
			$upload_file = $_FILES["upload_file"]["name"];
			$ffn = "game_rs/app{$id}/app_icon/{$fn}";
			
			// 一時ファイルを所定の場所へコピー（フォルダなければ自動作成）
			$this->copyEx($_FILES["upload_file"]["tmp_name"], $ffn);
	
	
		}
		
		if($errs) $ent['err'] = implode("','",$errs); // フォームに表示するエラー文字列をセット

		

		$json_data=json_encode($ent,true);//JSONに変換
	
		return $json_data;
	}
	
	
	
	
	
	
	
	/**
	 * 削除登録
	 *
	 * @note
	 * Ajaxによる削除登録。
	 * 削除更新でだけでなく有効化に対応している。
	 * また、DBから実際に削除する抹消にも対応している。
	 */
	public function ajax_delete(){
		App::uses('Sanitize', 'Utility');
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
	
		// JSON文字列をパースしてエンティティを取得する
		$json=$_POST['key1'];
		$ent0 = json_decode($json,true);
		
		// 登録パラメータ
		$reg_param_json = $_POST['reg_param_json'];
		$regParam = json_decode($reg_param_json,true);

		// 抹消フラグ
		$eliminate_flg = 0;
		if(isset($regParam['eliminate_flg'])) $eliminate_flg = $regParam['eliminate_flg'];
		
		// 削除用のエンティティを取得する
		$ent = $this->getEntForDelete($ent0['id']);
		$ent['delete_flg'] = $ent0['delete_flg'];
	
		// エンティティをDB保存
		$this->DiaryA->begin();
		if($eliminate_flg == 0){
			$ent = $this->DiaryA->saveEntity($ent,$regParam); // 更新
		}else{
		    $this->DiaryA->delete($ent['id']); // 削除
		}
		$this->DiaryA->commit();//コミット
	
		$ent=Sanitize::clean($ent, array('encode' => true));//サニタイズ（XSS対策）
		$json_data=json_encode($ent);//JSONに変換
	
		return $json_data;
	}
	
	
	/**
	* Ajax | 自動保存
	* 
	* @note
	* バリデーション機能は備えていない
	* 
	*/
	public function auto_save(){
		
		App::uses('Sanitize', 'Utility');
		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
		
		$this->autoRender = false;//ビュー(ctp)を使わない。
		
		$json=$_POST['key1'];
		
		$data = json_decode($json,true);//JSON文字を配列に戻す
		
		// データ保存
		$this->DiaryA->begin();
		$this->DiaryA->saveAll($data); // まとめて保存。内部でSQLサニタイズされる。
		$this->DiaryA->commit();

		$res = array('success');
		
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	

	
	
	/**
	 * CSVインポート | AJAX
	 *
	 * @note
	 *
	 */
	public function csv_fu(){
		$this->autoRender = false;//ビュー(ctp)を使わない。
		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
		
		$this->csv_fu_base($this->DiaryA,array('id','diary_a_val','diary_a_name','diary_a_date','diary_a_group','diary_a_dt','img_fn','note','sort_no'));
		
	}
	



	
	



	/**
	 * CSVダウンロード
	 *
	 * 一覧画面のCSVダウンロードボタンを押したとき、一覧データをCSVファイルとしてダウンロードします。
	 */
	public function csv_download(){
		$this->autoRender = false;//ビューを使わない。
	
		//ダウンロード用のデータを取得する。
		$data = $this->getDataForDownload();
		
		
		// ユーザーエージェントなど特定の項目をダブルクォートで囲む
		foreach($data as $i=>$ent){
			if(!empty($ent['user_agent'])){
				$data[$i]['user_agent']='"'.$ent['user_agent'].'"';
			}
		}

		
		
		//列名配列を取得
		$clms=array_keys($data[0]);
	
		//データの先頭行に列名配列を挿入
		array_unshift($data,$clms);
	
	
		//CSVファイル名を作成
		$date = new DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='diary_a'.$strDate.'.csv';
	
	
		//CSVダウンロード
		App::uses('CsvDownloader','Vendor/Wacg');
		$csv= new CsvDownloader();
		$csv->output($fn, $data);
		 
	
	
	}
	
	

	
	
	//ダウンロード用のデータを取得する。
	private function getDataForDownload(){
		 
		
        //セッションから検索条件情報を取得
        $kjs=$this->Session->read('diary_a_kjs');
        
        // セッションからページネーション情報を取得
        $pages = $this->Session->read('diary_a_pages');

        $page_no = 0;
        $row_limit = 100000;
        $sort_field = $pages['sort_field'];
        $sort_desc = $pages['sort_desc'];

		//DBからデータ取得
	   $data=$this->DiaryA->findData($kjs,$page_no,$row_limit,$sort_field,$sort_desc);
		if(empty($data)){
			return array();
		}
	
		return $data;
	}
	

	/**
	 * 当画面系の共通セット
	 */
	private function setCommon(){

		
		// 新バージョンであるかチェックする。
		$new_version_flg = $this->checkNewPageVersion($this->this_page_version);
		
		$this->set(array(
				'header' => 'header_demo',
				'new_version_flg' => $new_version_flg, // 当ページの新バージョンフラグ   0:バージョン変更なし  1:新バージョン
				'this_page_version' => $this->this_page_version,// 当ページのバージョン
		));
	}
	

	/**
	 * CrudBase用の初期化処理
	 *
	 * @note
	 * フィールド関連の定義をする。
	 *
	 *
	 */
	private function initCrudBase(){

		
		// CBBXS-3001 

		// CBBXE
		
		
		/// 検索条件情報の定義
		$this->kensakuJoken=array(
		
			// CBBXS-1000 
			array('name'=>'kj_id','def'=>null),
			array('name'=>'kj_category','def'=>null),
			array('name'=>'kj_diary_date1','def'=>null),
			array('name'=>'kj_diary_date2','def'=>null),
			array('name'=>'kj_diary_dt','def'=>null),
			array('name'=>'kj_diary_note','def'=>null),
			array('name'=>'kj_delete_flg','def'=>0),
			array('name'=>'kj_update_user','def'=>null),
			array('name'=>'kj_ip_addr','def'=>null),
			array('name'=>'kj_created','def'=>null),
			array('name'=>'kj_modified','def'=>null),

			// CBBXE
			
			array('name'=>'row_limit','def'=>50),
				
		);
		
		
		
		
		
		/// 検索条件のバリデーション
		$this->kjs_validate=array(
				
				// CBBXS-1001
				'kj_id' => array(
						'naturalNumber'=>array(
								'rule' => array('naturalNumber', true),
								'message' => 'IDは数値を入力してください',
								'allowEmpty' => true
						),
				),
				'kj_category'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => 'カテゴリは16文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_diary_date1'=> array(
						'rule' => array( 'date', 'ymd'),
						'message' => '日誌日付【範囲1】は日付形式【yyyy-mm-dd】で入力してください。',
						'allowEmpty' => true
				),
				'kj_diary_date2'=> array(
						'rule' => array( 'date', 'ymd'),
						'message' => '日誌日付【範囲2】は日付形式【yyyy-mm-dd】で入力してください。',
						'allowEmpty' => true
				),
				'kj_diary_dt'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 20),
								'message' => '日誌日時は20文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_diary_note'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => '日誌は0文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_update_user'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => '更新者は50文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_ip_addr'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => 'IPアドレスは40文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_created'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 20),
								'message' => '生成日時は20文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_modified'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 20),
								'message' => '更新日は20文字以内で入力してください',
								'allowEmpty' => true
						),
				),

				// CBBXE
		);
		
		
		
		
		
		///フィールドデータ
		$this->field_data = array('def'=>array(
		
			// CBBXS-1002
			'id'=>array(
					'name'=>'ID',//HTMLテーブルの列名
					'row_order'=>'DiaryA.id',//SQLでの並び替えコード
					'clm_show'=>1,//デフォルト列表示 0:非表示 1:表示
			),
			'category'=>array(
					'name'=>'カテゴリ',
					'row_order'=>'DiaryA.category',
					'clm_show'=>0,
			),
			'diary_date'=>array(
					'name'=>'日誌日付',
					'row_order'=>'DiaryA.diary_date',
					'clm_show'=>1,
			),
			'diary_dt'=>array(
					'name'=>'日誌日時',
					'row_order'=>'DiaryA.diary_dt',
					'clm_show'=>0,
			),
			'diary_note'=>array(
					'name'=>'日誌',
					'row_order'=>'DiaryA.diary_note',
					'clm_show'=>1,
			),
			'delete_flg'=>array(
					'name'=>'無効フラグ',
					'row_order'=>'DiaryA.delete_flg',
					'clm_show'=>0,
			),
			'update_user'=>array(
					'name'=>'更新者',
					'row_order'=>'DiaryA.update_user',
					'clm_show'=>0,
			),
			'ip_addr'=>array(
					'name'=>'IPアドレス',
					'row_order'=>'DiaryA.ip_addr',
					'clm_show'=>1,
			),
			'created'=>array(
					'name'=>'生成日時',
					'row_order'=>'DiaryA.created',
					'clm_show'=>0,
			),
			'modified'=>array(
					'name'=>'更新日',
					'row_order'=>'DiaryA.modified',
					'clm_show'=>0,
			),

			// CBBXE
		));

		// 列並び順をセットする
		$clm_sort_no = 0;
		foreach ($this->field_data['def'] as &$fEnt){
			$fEnt['clm_sort_no'] = $clm_sort_no;
			$clm_sort_no ++;
		}
		unset($fEnt);

		 
	}



}