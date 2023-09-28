<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 農業記録X
 * 
 * @note
 * 農業記録X画面では農業記録X一覧を検索閲覧、編集など多くのことができます。
 * 
 * @date 2015-9-16 | 2018-4-25 削除のバグを主末井
 * @version 3.0.1
 *
 */
class RecXController extends CrudBaseController {

	/// 名称コード
	public $name = 'RecX';
	
	/// 使用しているモデル
	public $uses = array('RecX','CrudBase');
	
	/// オリジナルヘルパーの登録
	public $helpers = array('CrudBase');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='RecX.sort_no';
	
	/// デフォルトソートタイプ	  0:昇順 1:降順
	public $defSortType=0;
	
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
		
		$this->Auth->allow(); // 認証と未認証の両方に対応したページする。
	
		parent::beforeFilter();
	
		$this->initCrudBase();// フィールド関連の定義をする。
	
	}

	/**
	 * indexページのアクション
	 *
	 * indexページでは農業記録X一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
	public function index() {
		
		// CrudBase共通処理（前）
		$crudBaseData = $this->indexBefore('RecX');//indexアクションの共通先処理(CrudBaseController)
		
		//一覧データを取得
		$data = $this->RecX->findData2($crudBaseData);

		// CrudBase共通処理（後）
		$crudBaseData = $this->indexAfter($crudBaseData);//indexアクションの共通後処理
		
		// CBBXS-1020

		// CBBXE
		
		
	
		
		$this->set($crudBaseData);
		$this->set(array(
			'title_for_layout'=>'農業記録X',
			'data'=> $data,
		));
		
		//当画面系の共通セット
		$this->setCommon();


	}

	/**
	 * 詳細画面
	 * 
	 * 農業記録X情報の詳細を表示します。
	 * この画面から入力画面に遷移できます。
	 * 
	 */
	public function detail() {
		
		$res=$this->edit_before('RecX');
		$ent=$res['ent'];
	

		$this->set(array(
				'title_for_layout'=>'農業記録X・詳細',
				'ent'=>$ent,
		));
		
		//当画面系の共通セット
		$this->setCommon();
	
	}













	/**
	 * 入力画面
	 * 
	 * 入力フォームにて値の入力が可能です。バリデーション機能を実装しています。
	 * 
	 * URLクエリにidが付属する場合は編集モードになります。
	 * idがない場合は新規入力モードになります。
	 * 
	 */
	public function edit() {
		
		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
		$res=$this->edit_before('RecX');
		$ent=$res['ent'];

		$this->set(array(
				'title_for_layout'=>'農業記録X・編集',
				'ent'=>$ent,
		));
		
		//当画面系の共通セット
		$this->setCommon();

	}
	
	 /**
	 * 登録完了画面
	 * 
	 * 入力画面の更新ボタンを押し、DB更新に成功した場合、この画面に遷移します。
	 * 入力エラーがある場合は、入力画面へ、エラーメッセージと共にリダイレクトで戻ります。
	 */
	public function reg(){
		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー
		$res=$this->reg_before('RecX');
		$ent=$res['ent'];
		
		$regMsg="<p id='reg_msg'>更新しました。</p>";

		//オリジナルバリデーション■■■□□□■■■□□□■■■□□□
		//$xFlg=$this->validRecX();
		$xFlg=true;
		if($xFlg==false){
			//エラーメッセージと一緒に編集画面へ、リダイレクトで戻る。
			$this->errBackToEdit("オリジナルバリデーションのエラー");
		}
		
		//★DB保存
		$this->RecX->begin();//トランザクション開始
		$ent=$this->RecX->saveEntity($ent);//登録
		$this->RecX->commit();//コミット

		$this->set(array(
				'title_for_layout'=>'農業記録X・登録完了',
				'ent'=>$ent,
				'regMsg'=>$regMsg,
		));
		
		//当画面系の共通セット
		$this->setCommon();

	}
	
	
	
	
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
		if(empty($this->Auth->user())) return 'Error:login is needed.';// 認証中でなければエラー

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
			$ent['rec_x_fn'] = $upload_file;
		}
	
	
		// 更新ユーザーなど共通フィールドをセットする。
		$ent = $this->setCommonToEntity($ent);
	
		// エンティティをDB保存
		$this->RecX->begin();
		$ent = $this->RecX->saveEntity($ent,$regParam);
		$this->RecX->commit();//コミット

		if(!empty($upload_file)){
			
			// ファイルパスを組み立て
			$upload_file = $_FILES["upload_file"]["name"];
			$ffn = "game_rs/app{$id}/app_icon/{$fn}";
			
			// 一時ファイルを所定の場所へコピー（フォルダなければ自動作成）
			$this->copyEx($_FILES["upload_file"]["tmp_name"], $ffn);
	
	
		}

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
		$this->RecX->begin();
		if($eliminate_flg == 0){
			$ent = $this->RecX->saveEntity($ent,$regParam); // 更新
		}else{
		    $this->RecX->delete($ent['id']); // 削除
		}
		$this->RecX->commit();//コミット
	
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
		$this->RecX->begin();
		$this->RecX->saveAll($data); // まとめて保存。内部でSQLサニタイズされる。
		$this->RecX->commit();

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
		
		$this->csv_fu_base($this->RecX,array('id','rec_x_val','rec_x_name','rec_x_date','rec_x_group','rec_x_dt','note','sort_no'));
		
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
		$fn='rec_x'.$strDate.'.csv';
	
	
		//CSVダウンロード
		App::uses('CsvDownloader','Vendor/Wacg');
		$csv= new CsvDownloader();
		$csv->output($fn, $data);
		 
	
	
	}
	
	

	
	
	//ダウンロード用のデータを取得する。
	private function getDataForDownload(){
		 
		
        //セッションから検索条件情報を取得
        $kjs=$this->Session->read('rec_x_kjs');
        
        // セッションからページネーション情報を取得
        $pages = $this->Session->read('rec_x_pages');

        $page_no = 0;
        $row_limit = 100000;
        $sort_field = $pages['sort_field'];
        $sort_desc = $pages['sort_desc'];

		//DBからデータ取得
	   $data=$this->RecX->findData($kjs,$page_no,$row_limit,$sort_field,$sort_desc);
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
			array('name'=>'kj_rec_title','def'=>null),
			array('name'=>'kj_rec_date','def'=>null),
			array('name'=>'kj_note','def'=>null),
			array('name'=>'kj_category_id2','def'=>null),
			array('name'=>'kj_category_id1','def'=>null),
			array('name'=>'kj_tags','def'=>null),
			array('name'=>'kj_photo_fn','def'=>null),
			array('name'=>'kj_photo_dir','def'=>null),
			array('name'=>'kj_ref_url','def'=>null),
			array('name'=>'kj_nendo1','def'=>null),
			array('name'=>'kj_nendo2','def'=>null),
			array('name'=>'kj_sort_no','def'=>null),
			array('name'=>'kj_no_a1','def'=>null),
			array('name'=>'kj_no_a2','def'=>null),
			array('name'=>'kj_no_b1','def'=>null),
			array('name'=>'kj_no_b2','def'=>null),
			array('name'=>'kj_parent_id','def'=>null),
			array('name'=>'kj_probe_id','def'=>null),
			array('name'=>'kj_publish','def'=>null),
			array('name'=>'kj_create_date','def'=>null),
			array('name'=>'kj_update_date','def'=>null),

			// CBBXE
			
			array('name'=>'row_limit','def'=>50),
				
		);
		
		
		
		
		
		/// 検索条件のバリデーション
		$this->kjs_validate=array(
				
				// CBBXS-1001
				'kj_id' => array(
						'naturalNumber'=>array(
								'rule' => array('naturalNumber', true),
								'message' => 'idは数値を入力してください',
								'allowEmpty' => true
						),
				),
				'kj_rec_title'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => 'rec_titleは50文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_rec_date'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 20),
								'message' => 'rec_dateは20文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_note'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => 'noteは1000文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_category_id2' => array(
						'naturalNumber'=>array(
								'rule' => array('naturalNumber', true),
								'message' => 'category_id2は数値を入力してください',
								'allowEmpty' => true
						),
				),
				'kj_category_id1' => array(
						'naturalNumber'=>array(
								'rule' => array('naturalNumber', true),
								'message' => 'category_id1は数値を入力してください',
								'allowEmpty' => true
						),
				),
				'kj_tags'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => 'tagsは255文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_photo_fn'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => 'photo_fnは128文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_photo_dir'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => '写真ディレクトリパスは128文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_ref_url'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 255),
								'message' => '参照URLは2083文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_nendo1' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => 'nendoは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_nendo2' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => 'nendoは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_sort_no' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => 'sort_noは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_no_a1' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => '番号Aは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_no_a2' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => '番号Aは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_no_b1' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => 'no_bは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_no_b2' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => 'no_bは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_parent_id' => array(
						'naturalNumber'=>array(
								'rule' => array('naturalNumber', true),
								'message' => '親IDは数値を入力してください',
								'allowEmpty' => true
						),
				),
				'kj_probe_id' => array(
						'naturalNumber'=>array(
								'rule' => array('naturalNumber', true),
								'message' => 'サンプルIDは数値を入力してください',
								'allowEmpty' => true
						),
				),
				'kj_publish' => array(
						'custom'=>array(
								'rule' => array( 'custom', '/^[-]?[0-9] ?$/' ),
								'message' => '公開フラグは整数を入力してください。',
								'allowEmpty' => true
						),
				),
				'kj_create_date'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 20),
								'message' => 'create_dateは20文字以内で入力してください',
								'allowEmpty' => true
						),
				),
				'kj_update_date'=> array(
						'maxLength'=>array(
								'rule' => array('maxLength', 20),
								'message' => 'update_dateは20文字以内で入力してください',
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
					'row_order'=>'RecX.id',//SQLでの並び替えコード
					'clm_show'=>1,//デフォルト列表示 0:非表示 1:表示
			),
			'rec_title'=>array(
					'name'=>'rec_title',
					'row_order'=>'RecX.rec_title',
					'clm_show'=>1,
			),
			'rec_date'=>array(
					'name'=>'rec_date',
					'row_order'=>'RecX.rec_date',
					'clm_show'=>1,
			),
			'note'=>array(
					'name'=>'note',
					'row_order'=>'RecX.note',
					'clm_show'=>1,
			),
			'category_id2'=>array(
					'name'=>'category_id2',
					'row_order'=>'RecX.category_id2',
					'clm_show'=>1,
			),
			'category_id1'=>array(
					'name'=>'category_id1',
					'row_order'=>'RecX.category_id1',
					'clm_show'=>1,
			),
			'tags'=>array(
					'name'=>'tags',
					'row_order'=>'RecX.tags',
					'clm_show'=>1,
			),
			'photo_fn'=>array(
					'name'=>'photo_fn',
					'row_order'=>'RecX.photo_fn',
					'clm_show'=>1,
			),
			'photo_dir'=>array(
					'name'=>'写真ディレクトリパス',
					'row_order'=>'RecX.photo_dir',
					'clm_show'=>1,
			),
			'ref_url'=>array(
					'name'=>'参照URL',
					'row_order'=>'RecX.ref_url',
					'clm_show'=>1,
			),
			'nendo'=>array(
					'name'=>'nendo',
					'row_order'=>'RecX.nendo',
					'clm_show'=>1,
			),
			'sort_no'=>array(
					'name'=>'sort_no',
					'row_order'=>'RecX.sort_no',
					'clm_show'=>1,
			),
			'no_a'=>array(
					'name'=>'番号A',
					'row_order'=>'RecX.no_a',
					'clm_show'=>1,
			),
			'no_b'=>array(
					'name'=>'no_b',
					'row_order'=>'RecX.no_b',
					'clm_show'=>1,
			),
			'parent_id'=>array(
					'name'=>'親ID',
					'row_order'=>'RecX.parent_id',
					'clm_show'=>1,
			),
			'probe_id'=>array(
					'name'=>'サンプルID',
					'row_order'=>'RecX.probe_id',
					'clm_show'=>1,
			),
			'publish'=>array(
					'name'=>'公開フラグ',
					'row_order'=>'RecX.publish',
					'clm_show'=>1,
			),
			'create_date'=>array(
					'name'=>'create_date',
					'row_order'=>'RecX.create_date',
					'clm_show'=>1,
			),
			'update_date'=>array(
					'name'=>'update_date',
					'row_order'=>'RecX.update_date',
					'clm_show'=>1,
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