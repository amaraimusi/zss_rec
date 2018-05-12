<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 個別サンプル
 * 
 * 個別サンプル画面では個別サンプル一覧を検索閲覧、および編集ができます。
 * 
 * 
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class ProbeController extends CrudBaseController {

	/// 名称コード
	public $name = 'Probe';
	
	/// 使用しているモデル
	public $uses = array('Probe','CrudBase');
	
	/// オリジナルヘルパーの登録
	public $helpers = array('CrudBase');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='Probe.id';
	
	/// デフォルトソートタイプ	  0:昇順 1:降順
	public $defSortType=0;
	
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



	public function beforeFilter() {
	
		parent::beforeFilter();
	
		$this->initCrudBase();// フィールド関連の定義をする。
	
	}

	/**
	 * indexページのアクション
	 *
	 * indexページでは個別サンプル一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
	public function index() {
		

		$res=$this->index_before('Probe',$this->request->data);//indexアクションの共通先処理(CrudBaseController)
		$kjs=$res['kjs'];//検索条件情報
		$paginations=$res['paginations'];//ページネーション情報
		
		// SQLインジェクション対策用のサニタイズをする。
		App::uses('Sanitize', 'Utility');
		$kjs = Sanitize::clean($kjs, array('encode' => false));

		//一覧データを取得
		$data=$this->Probe->findData($kjs,$paginations['page_no'],$paginations['limit'],$paginations['find_order']);

		$res=$this->index_after($kjs);//indexアクションの共通後処理

		$this->set(array(
				'title_for_layout'=>'個別サンプル',
				'data'=> $data,
		));
		
		//当画面系の共通セット
		$this->setCommon();


	}

	/**
	 * 詳細画面
	 * 
	 * 個別サンプル情報の詳細を表示します。
	 * この画面から入力画面に遷移できます。
	 * 
	 */
	public function detail() {
		
		$res=$this->edit_before('Probe');
		$ent=$res['ent'];
	

		$this->set(array(
				'title_for_layout'=>'個別サンプル・詳細',
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

		$res=$this->edit_before('Probe');
		$ent=$res['ent'];

		$this->set(array(
				'title_for_layout'=>'個別サンプル・編集',
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
		$res=$this->reg_before('Probe');
		$ent=$res['ent'];

		$regMsg="<p id='reg_msg'>更新しました。</p>";

		//オリジナルバリデーション■■■□□□■■■□□□■■■□□□
		//$xFlg=$this->validProbe();
		$xFlg=true;
		if($xFlg==false){
			//エラーメッセージと一緒に編集画面へ、リダイレクトで戻る。
			$this->errBackToEdit("オリジナルバリデーションのエラー");
		}
		
		//★DB保存
		$this->Probe->begin();//トランザクション開始
		$ent=$this->Probe->saveEntity($ent);//登録
		$this->Probe->commit();//コミット

		$this->set(array(
				'title_for_layout'=>'個別サンプル・登録完了',
				'ent'=>$ent,
				'regMsg'=>$regMsg,
		));
		
		//当画面系の共通セット
		$this->setCommon();

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
		
		//列名配列を取得
		$clms=array_keys($data[0]);
	
		//データの先頭行に列名配列を挿入
		array_unshift($data,$clms);
	
	
		//CSVファイル名を作成
		$date = new DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='probe'.$strDate.'.csv';
	
	
		//CSVダウンロード
		App::uses('CsvDownloader','Vendor/Wacg');
		$csv= new CsvDownloader();
		$csv->output($fn, $data);
		 
	
	
	}
	
	
	/**
	 * 業務型CSVのダウンロード
	 */
	public function csv_download2(){
	
		$this->autoRender = false;//ビューを使わない。
	
		//ダウンロード用のデータを取得する。
		$data=$this->getDataForDownload();
		if(empty($data)){
			return 'NO DATA';
		}
	
		//表記変換情報を取得
		$showInfos = $this->getShowInfos();
		 
		//CSVデータを加工する
		$dq_flg=0;//0:ダブルクォートで括らない
		$data=$this->prosCsvData($data,'probe',$dq_flg,$showInfos);
		 
	
		//CSVファイル名を作成
		$date = new DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='probe_w_'.$strDate.'.csv';
	
	
		//CSVダウンロード
		App::uses('CsvDownloader','Vendor/Wacg');
		$csv= new CsvDownloader();
		$csv->output($fn, $data);
	
	
	}
	
	/**
	 * 業務Excel型CSVのダウンロード
	 */
	public function csv_download3(){
	
		$this->autoRender = false;//ビューを使わない。
	
		//ダウンロード用のデータを取得する。
		$data=$this->getDataForDownload();
		if(empty($data)){
			return 'NO DATA';
		}
		 
		//表記変換情報を取得
		$showInfos = $this->getShowInfos();
	
		//CSVデータを加工する
		$dq_flg=1;//1:ダブルクォートで括る
		$data=$this->prosCsvData($data,'probe',$dq_flg,$showInfos);
	
	
	
		//CSVファイル名を作成
		$date = new DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='probe_ew_'.$strDate.'.csv';
	
	
		//CSVダウンロード
		App::uses('CsvDownloader','Vendor/Wacg');
		$csv= new CsvDownloader();
		$csv->output($fn, $data);
	
	
	}
	
	
	//ダウンロード用のデータを取得する。
	private function getDataForDownload(){
		 
		
		//セッションから読取
		$kjs=$this->Session->read('probe_kjs');
		
		
		//DBからデータ取得
		$data=$this->Probe->findData($kjs,null,null,null);
		if(empty($data)){
			return array();
		}
	
		return $data;
	}
	
	
	/**
	 * 表記変換情報を取得
	 * @return 表記変換情報
	 */
	private function getShowInfos(){
	
	
		$probeGroupList = array(1=>'ペルシャ',2=>'ボンベイ',3=>'三毛',4=>'シャム',5=>'雉トラ',6=>'スフィンクス');
	
		//表記変換情報を定義
		$showInfos=array(
				'id' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'probe_val' => array(
						'show_csv'=>CB_FLD_NULL_ZERO,
						'group_list'=>null,
				),
				'probe_name' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'probe_date' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'probe_group' => array(
						'show_csv'=>null,
						'group_list'=>$probeGroupList,
				),
				'probe_dt' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'note' => array(
						'show_csv'=>CB_FLD_TA_CSV,
						'group_list'=>null,
				),
				'delete_flg' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'update_user' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'ip_addr' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'created' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
				'modified' => array(
						'show_csv'=>null,
						'group_list'=>null,
				),
 
	
		);
		 
		return $showInfos;
	
	}

	/**
	 * 当画面系の共通セット
	 */
	private function setCommon(){
		$probeGroupList = array(1=>'ペルシャ',2=>'ボンベイ',3=>'三毛',4=>'シャム',5=>'雉トラ',6=>'スフィンクス');
		$this->set(array(
				'header' => 'header_demo',
				'probeGroupList' => $probeGroupList,
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
			array('name'=>'kj_probe_name','def'=>null),
			array('name'=>'kj_probe_note','def'=>null),
			array('name'=>'kj_modified','def'=>null),
			array('name'=>'kj_limit','def'=>50),
		
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

			'kj_probe_name'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 50),
					'message' => '個体名は50文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'kj_probe_note'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 1000),
					'message' => '個体説明は1000文字以内で入力してください',
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
				'row_order'=>'Probe.id', // SQLでの並び替えコード
				'clm_sort_no'=>0, // 列の並び順
				'clm_show'=>1, // HTMLテーブルの列名
			),
			'probe_name'=>array(
				'name'=>'個体名',
				'row_order'=>'Probe.probe_name',
				'clm_sort_no'=>1,
				'clm_show'=>1,
			),
			'probe_note'=>array(
				'name'=>'個体説明',
				'row_order'=>'Probe.probe_note',
				'clm_sort_no'=>2,
				'clm_show'=>0,
			),
			'modified'=>array(
				'name'=>'更新日',
				'row_order'=>'Probe.modified',
				'clm_sort_no'=>3,
				'clm_show'=>1,
			),
	
		));
		
		
		
		
		
		/// 編集エンティティ定義
		$this->entity_info=array(		

			array('name'=>'id','def'=>null),
			array('name'=>'probe_name','def'=>null),
			array('name'=>'probe_note','def'=>null),
		
		);
		
		
		
		
		
		/// 編集用バリデーション
		$this->edit_validate=array(

			'probe_name'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 50),
					'message' => '個体名は50文字以内で入力してください',
					'allowEmpty' => true
				),
			),

			'probe_note'=> array(
				'maxLength'=>array(
					'rule' => array('maxLength', 1000),
					'message' => '個体説明は1000文字以内で入力してください',
					'allowEmpty' => true
				),
			),


		);
		
		
		
		
		 
	}
	
	
	
	
	
	
	
	
	


}