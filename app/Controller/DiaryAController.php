<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 日誌A
 * 
 * 日誌A画面では日誌A一覧を検索閲覧、および編集ができます。
 * 
 * 
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class DiaryAController extends CrudBaseController {

	/// 名称コード
	public $name = 'DiaryA';
	
	/// 使用しているモデル
	public $uses = array('DiaryA','CrudBase','AnyJson');
	
	/// オリジナルヘルパーの登録
	public $helpers = array('CrudBase');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='DiaryA.diary_date';
	
	/// デフォルトソートタイプ	  0:昇順 1:降順
	public $defSortType=1;
	
	/// 検索条件のセッション保存フラグ
	public $kj_session_flg=true;
	
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
	
		parent::beforeFilter();
	
		$this->initCrudBase();// フィールド関連の定義をする。
	
	}

	/**
	 * indexページのアクション
	 *
	 * indexページでは日誌A一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
	public function index() {
		

		$res=$this->index_before('DiaryA',$this->request->data);//indexアクションの共通先処理(CrudBaseController)
		$kjs=$res['kjs'];//検索条件情報
		$paginations=$res['paginations'];//ページネーション情報
		
		// SQLインジェクション対策用のサニタイズをする。
		App::uses('Sanitize', 'Utility');
		$kjs = Sanitize::clean($kjs, array('encode' => false));

		//一覧データを取得
		$data=$this->DiaryA->findData($kjs,$paginations['page_no'],$paginations['limit'],$paginations['find_order']);

		$res=$this->index_after($kjs);//indexアクションの共通後処理
		

		
		

		$this->set(array(
				'title_for_layout'=>'日誌A',
				'data'=> $data,
		));
		
		//当画面系の共通セット
		$this->setCommon();


	}

	/**
	 * 詳細画面
	 * 
	 * 日誌A情報の詳細を表示します。
	 * この画面から入力画面に遷移できます。
	 * 
	 */
	public function detail() {
		
		$res=$this->edit_before('DiaryA');
		$ent=$res['ent'];
	

		$this->set(array(
				'title_for_layout'=>'日誌A・詳細',
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

		$res=$this->edit_before('DiaryA');
		$ent=$res['ent'];

		$this->set(array(
				'title_for_layout'=>'日誌A・編集',
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
		$res=$this->reg_before('DiaryA');
		$ent=$res['ent'];
		
		$regMsg="<p id='reg_msg'>更新しました。</p>";

		//オリジナルバリデーション■■■□□□■■■□□□■■■□□□
		//$xFlg=$this->validDiaryA();
		$xFlg=true;
		if($xFlg==false){
			//エラーメッセージと一緒に編集画面へ、リダイレクトで戻る。
			$this->errBackToEdit("オリジナルバリデーションのエラー");
		}
		
		//★DB保存
		$this->DiaryA->begin();//トランザクション開始
		$ent=$this->DiaryA->saveEntity($ent);//登録
		$this->DiaryA->commit();//コミット

		$this->set(array(
				'title_for_layout'=>'日誌A・登録完了',
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
	
		// JSON文字列をパースしてエンティティを取得する
		$json=$_POST['key1'];
		$ent = json_decode($json,true);
	
	
// 		// アップロードファイルが存在すればエンティティにセットする。
// 		$upload_file = null;
// 		if(!empty($_FILES["upload_file"])){
// 			$upload_file = $_FILES["upload_file"]["name"];
// 			$ent['diary_a_fn'] = $upload_file;
// 		}
	
	
		// 更新ユーザーなど共通フィールドをセットする。
		$ent = $this->setCommonToEntity($ent);
	
		// エンティティをDB保存
		$this->DiaryA->begin();
		$ent = $this->DiaryA->saveEntity($ent);
		$this->DiaryA->commit();//コミット
	
	//■■■□□□■■■□□□■■■□□□
// 		if(!empty($upload_file)){
			
// 			// ファイルパスを組み立て
// 			$upload_file = $_FILES["upload_file"]["name"];
// 			$ffn = "game_rs/app{$id}/app_icon/{$fn}";
			
// 			// 一時ファイルを所定の場所へコピー（フォルダなければ自動作成）
// 			$this->copyEx($_FILES["upload_file"]["tmp_name"], $ffn);
	
	
// 		}
	
		//$ent=Sanitize::clean($ent, array('encode' => true));//サニタイズ（XSS対策）
	
		$json_data=json_encode($ent,true);//JSONに変換
	
		return $json_data;
	}
	
	
	
	
	
	
	
	/**
	 * 削除登録
	 *
	 * @note
	 * Ajaxによる削除登録。
	 * 物理削除でなく無効フラグをONにする方式。
	 */
	public function ajax_delete(){
		App::uses('Sanitize', 'Utility');
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
	
		// JSON文字列をパースしてエンティティを取得する
		$json=$_POST['key1'];
		$ent = json_decode($json,true);

		// 削除用のエンティティを取得する
		$ent = $this->getEntForDelete($ent['id']);
	
		// エンティティをDB保存
		$this->DiaryA->begin();
		$ent = $this->DiaryA->saveEntity($ent);
		$this->DiaryA->commit();//コミット
	
	
		$ent=Sanitize::clean($ent, array('encode' => true));//サニタイズ（XSS対策）
	
		$json_data=json_encode($ent);//JSONに変換
	
		return $json_data;
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
		 
		
		//セッションから読取
		$kjs=$this->Session->read('diary_a_kjs');
		
		
		//DBからデータ取得
		$data=$this->DiaryA->findData($kjs,null,null,null);
		if(empty($data)){
			return array();
		}
	
		return $data;
	}
	

	/**
	 * 当画面系の共通セット
	 */
	private function setCommon(){
		$diary_aGroupList = array(1=>'ペルシャ',2=>'ボンベイ',3=>'三毛',4=>'シャム',5=>'雉トラ',6=>'スフィンクス');
		
		// 新バージョンであるかチェックする。
		$new_version_flg = $this->checkNewPageVersion($this->this_page_version);
		
		$this->set(array(
				'header' => 'header_demo',
				'diary_aGroupList' => $diary_aGroupList,
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

		
		
		
		
		/// 検索条件情報の定義
		$this->kensakuJoken=array(		

			array('name'=>'kj_id','def'=>null),
			array('name'=>'kj_category','def'=>null),
			array('name'=>'kj_diary_date1','def'=>null),
			array('name'=>'kj_diary_date2','def'=>null),
			array('name'=>'kj_diary_date_ym','def'=>null),
			array('name'=>'kj_diary_dt','def'=>null),
			array('name'=>'kj_diary_note','def'=>null),
			array('name'=>'kj_delete_flg','def'=>null),
			array('name'=>'kj_update_user','def'=>null),
			array('name'=>'kj_ip_addr','def'=>null),
			array('name'=>'kj_created','def'=>null),
			array('name'=>'kj_modified','def'=>null),
			array('name'=>'kj_limit','def'=>10),
		
		);
		
		
		
		
		
		/// 検索条件のバリデーション
		$this->kjs_validate=array(

			'kj_id' => array(
				'naturalNumber'=>array(
					'rule' => array('naturalNumber', true),
					'message' => 'IDは数値を入力してください',
					'allowEmpty' => true
				),
			),

			'kj_category'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 16),
					'message' => 'カテゴリは16文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'kj_diary_date1'=> array(
				'rule' => array( 'date', 'ymd'),
				'message' => '日誌日付は日付形式【yyyy-mm-dd】で入力してください。',
				'allowEmpty' => true
			),

			'kj_diary_date2'=> array(
				'rule' => array( 'date', 'ymd'),
				'message' => '日誌日付は日付形式【yyyy-mm-dd】で入力してください。',
				'allowEmpty' => true
			),

			'kj_diary_dt'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', ),
					'message' => '日誌日時は文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'kj_diary_note'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 1000),
					'message' => '日誌は1000文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'kj_update_user'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 50),
					'message' => '更新者は50文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'kj_ip_addr'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 40),
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


		);
		
		
		
		
		
		///フィールドデータ
		$this->field_data=array('def'=>array(	

			'id'=>array(
				'name'=>'ID', // HTMLテーブルの列名
				'row_order'=>'DiaryA.id', // SQLでの並び替えコード
				'clm_sort_no'=>0, // 列の並び順
				'clm_show'=>1, // HTMLテーブルの列名
			),
			'category'=>array(
				'name'=>'カテゴリ',
				'row_order'=>'DiaryA.category',
				'clm_sort_no'=>1,
				'clm_show'=>1,
			),
			'diary_date'=>array(
				'name'=>'日誌日付',
				'row_order'=>'DiaryA.diary_date',
				'clm_sort_no'=>2,
				'clm_show'=>1,
			),
			'diary_dt'=>array(
				'name'=>'日誌日時',
				'row_order'=>'DiaryA.diary_dt',
				'clm_sort_no'=>3,
				'clm_show'=>0,
			),
			'diary_note'=>array(
				'name'=>'日誌',
				'row_order'=>'DiaryA.diary_note',
				'clm_sort_no'=>4,
				'clm_show'=>1,
			),
			'delete_flg'=>array(
				'name'=>'無効フラグ',
				'row_order'=>'DiaryA.delete_flg',
				'clm_sort_no'=>5,
				'clm_show'=>0,
			),
			'update_user'=>array(
				'name'=>'更新者',
				'row_order'=>'DiaryA.update_user',
				'clm_sort_no'=>6,
				'clm_show'=>0,
			),
			'ip_addr'=>array(
				'name'=>'IPアドレス',
				'row_order'=>'DiaryA.ip_addr',
				'clm_sort_no'=>7,
				'clm_show'=>0,
			),
			'created'=>array(
				'name'=>'生成日時',
				'row_order'=>'DiaryA.created',
				'clm_sort_no'=>8,
				'clm_show'=>0,
			),
			'modified'=>array(
				'name'=>'更新日',
				'row_order'=>'DiaryA.modified',
				'clm_sort_no'=>9,
				'clm_show'=>0,
			),
	
		));
		
		
		
		
		
		/// 編集エンティティ定義
		$this->entity_info=array(		

			array('name'=>'id','def'=>null),
			array('name'=>'category','def'=>null),
			array('name'=>'diary_date','def'=>null),
			array('name'=>'diary_dt','def'=>null),
			array('name'=>'diary_note','def'=>null),
			array('name'=>'delete_flg','def'=>0),
			array('name'=>'ip_addr','def'=>null),
		
		);
		
		
		
		
		
		/// 編集用バリデーション
		$this->edit_validate=array(

			'category'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 16),
					'message' => 'カテゴリは16文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'diary_date'=> array(
				'rule' => array( 'date', 'ymd'),
				'message' => '日誌日付は日付形式【yyyy-mm-dd】で入力してください。',
				'allowEmpty' => true
			),

			'diary_dt'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', ),
					'message' => '日誌日時は文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'diary_note'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 1000),
					'message' => '日誌は1000文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'ip_addr'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 40),
					'message' => 'IPアドレスは40文字以内で入力してください',
					'allowEmpty' => true
				),
			),


		);
		
		
		
		
		 
	}
	
	
	
	
	
	
	
	
	


}