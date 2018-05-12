<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 記録X
 * 
 * 記録X画面では記録X一覧を検索閲覧、および編集ができます。
 * 
 * 
 * @date 2016/5/12	新規作成
 * @author k-uehara
 *
 */
class RecXController extends CrudBaseController {

	/// 名称コード
	public $name = 'RecX';
	
	/// 使用しているモデル
	public $uses = array('RecX','CrudBase','RecTag');
	
	/// 当ページ専用ヘルパー
	public $helpers = array('RecX');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='RecX.id';
	
	
	/// デフォルトソートタイプ      0:昇順 1:降順
	public $defSortType=0;
	
	/// 検索条件のセッション保存フラグ
	public $kj_session_flg=true;

	/// 検索条件情報の定義
	public $kensakuJoken=array(
			array('name'=>'kj_rec_date1','def'=>''),
			array('name'=>'kj_rec_date2','def'=>''),
			array('name'=>'kj_category_id1','def'=>''),
			array('name'=>'kj_category_id2','def'=>''),
			array('name'=>'kj_rec_title','def'=>''),
			array('name'=>'kj_note','def'=>''),
			array('name'=>'kj_no_a','def'=>''),
			array('name'=>'kj_no_b','def'=>''),
			array('name'=>'kj_probe_id','def'=>''),
			array('name'=>'kj_tags','def'=>''),
			array('name'=>'kj_tag_id','def'=>''),
			array('name'=>'kj_limit','def'=>30),
	);


	/// 検索条件のバリデーション
	public $kjs_validate = array(


		'kj_rec_title' => array(
				'maxLength'=>array(
						'rule' => array('maxLength', 50),
						'message' => 'タイトルは50文字以内で入力してください。',
						'allowEmpty' => true
				)
		),

		'kj_note' => array(
				'maxLength'=>array(
						'rule' => array('maxLength', 1000),
						'message' => 'ノートは1000文字以内で入力してください。',
						'allowEmpty' => true
				)
		),


		'kj_nendo' => array(
				'maxLength'=>array(
						'rule' => array('maxLength', 4),
						'message' => '年度は4文字で入力してください。',
						'allowEmpty' => true
				),
				'minLength'=>array(
						'rule' => array('minLength', 4),
						'message' => '年度は4文字で入力してください。',
						'allowEmpty' => true
				),
				'naturalNumber'=>array(
						'rule' => array('naturalNumber', true),  // 0以上の整数
						'message' => '年度は数値を入力してください',
						'allowEmpty' => true
				),
		),


		'kj_tags' => array(
				'maxLength'=>array(
						'rule' => array('maxLength', 255),
						'message' => 'タグは255文字以内で入力してください。',
						'allowEmpty' => true
				)
		),


// 		'kj_probe_id' => array(

// 				'naturalNumber'=>array(
// 						'rule' => array('naturalNumber', true),  // 0以上の整数
// 						'message' => '個別番号は数値を入力してください',
// 						'allowEmpty' => true
// 				),
// 		),
			
			
	);

	///フィールドデータ
	public $field_data=array(
		'def'=>array(
			'id'=>array(
				'name'=>'ID',//HTMLテーブルの列名
				'row_order'=>'RecX.id',//SQLでの並び替えコード
				'clm_sort_no'=>0,//列の並び順
				'clm_show'=>1,//初期の列表示    0:非表示    1:表示
			),
			'rec_title'=>array(
				'name'=>'タイトル',
				'row_order'=>'RecX.rec_title',
				'clm_sort_no'=>1,
				'clm_show'=>1,
			),
			'rec_date'=>array(
				'name'=>'記録日付',
				'row_order'=>'RecX.rec_date',
				'clm_sort_no'=>2,
				'clm_show'=>1,
			),
			'note'=>array(
				'name'=>'ノート',
				'row_order'=>'RecX.note',
				'clm_sort_no'=>3,
				'clm_show'=>1,
			),
			'category_id2'=>array(
				'name'=>'カテゴリ2',
				'row_order'=>'RecX.category_id2',
				'clm_sort_no'=>4,
				'clm_show'=>0,
			),
			'category_id1'=>array(
				'name'=>'カテゴリ1',
				'row_order'=>'RecX.category_id1',
				'clm_sort_no'=>5,
				'clm_show'=>1,
			),
			'tags'=>array(
				'name'=>'タグリスト',
				'row_order'=>'RecX.tags',
				'clm_sort_no'=>6,
				'clm_show'=>0,
			),
			'photo_fn'=>array(
				'name'=>'写真ファイル名',
				'row_order'=>'RecX.photo_fn',
				'clm_sort_no'=>7,
				'clm_show'=>0,
			),
			'photo_dir'=>array(
				'name'=>'写真ディレクトリパス',
				'row_order'=>'RecX.photo_dir',
				'clm_sort_no'=>8,
				'clm_show'=>0,
			),
			'ref_url'=>array(
				'name'=>'参照URL',
				'row_order'=>'RecX.ref_url',
				'clm_sort_no'=>9,
				'clm_show'=>0,
			),
			'nendo'=>array(
				'name'=>'年度',
				'row_order'=>'RecX.nendo',
				'clm_sort_no'=>10,
				'clm_show'=>1,
			),
			'sort_no'=>array(
				'name'=>'並び順',
				'row_order'=>'RecX.sort_no',
				'clm_sort_no'=>11,
				'clm_show'=>1,
			),
			'no_a'=>array(
				'name'=>'番号A',
				'row_order'=>'RecX.no_a',
				'clm_sort_no'=>12,
				'clm_show'=>1,
			),
			'no_b'=>array(
				'name'=>'番号B',
				'row_order'=>'RecX.no_b',
				'clm_sort_no'=>13,
				'clm_show'=>0,
			),
			'parent_id'=>array(
				'name'=>'親ID',
				'row_order'=>'RecX.parent_id',
				'clm_sort_no'=>14,
				'clm_show'=>0,
			),
			'probe_id'=>array(
				'name'=>'個体サンプルID',
				'row_order'=>'RecX.probe_id',
				'clm_sort_no'=>15,
				'clm_show'=>0,
			),
			'publish'=>array(
				'name'=>'公開フラグ',
				'row_order'=>'RecX.publish',
				'clm_sort_no'=>16,
				'clm_show'=>0,
			),
			'create_date'=>array(
				'name'=>'生成日',
				'row_order'=>'RecX.create_date',
				'clm_sort_no'=>17,
				'clm_show'=>0,
			),
			'update_date'=>array(
				'name'=>'更新日',
				'row_order'=>'RecX.update_date',
				'clm_sort_no'=>18,
				'clm_show'=>0,
			),
			
		),
		
		
	);

	
	
	public function beforeFilter() {
		$this->Auth->allow(); // 認証と未認証の両方に対応したページする。
		parent::beforeFilter();//基本クラスのメソッドを呼び出し。
	}


	/**
	 * indexページのアクション
	 *
	 * indexページでは記録X一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
    public function index() {

		$res=$this->index_before('RecX',$this->request->data);//indexアクションの共通先処理(CrudBaseController)
		$kjs=$res['kjs'];//検索条件情報
		$paginations=$res['paginations'];//ページネーション情報
		$saveKjFlg = $res['saveKjFlg'];
		
		// 季節ボタンデータを取得する
		$seasonBtnData = $this->RecX->getSeasonBtnData('2012-7-1');
		
		//初期フラグがONである場合、最新30件
		if(!empty($this->request->query['i'])){
			//最新30件アクション
			$ary=$this->new30Action($kjs,$saveKjFlg);
			$pageParam=$ary['pageParam'];
			$paginations=Hash::merge($paginations,$pageParam);
			$kjs=$ary['kjs'];

		}
		

		// 個体リンクをクリックしたとき
		if(!empty($this->request->query['probe_flg'])){
			$defKjs = $this->getDefKjs();
			$kj_probe_id = $kjs['kj_probe_id'];
			$kjs = $defKjs;
			$kjs['kj_probe_id'] = $kj_probe_id;
			
		}

		
		// 季節ボタンクリックの検知
		foreach($seasonBtnData as $seasonBtn){
			$season_name = $seasonBtn['name'];
			if(!empty($this->data[$season_name])){
				
				$season_date1 = str_replace('season','',$season_name);
				$season_date2 = $season_date1;
				$season_date2 = new DateTime($season_date2);
				$season_date2->modify("+3 month");
				$season_date2 = $season_date2->format("Y-m-d").' 23:59:59';
				$season_date1= $season_date1.' 00:00:00';

 				$ary=$this->seasonAction($kjs,$season_date1,$season_date2,$saveKjFlg);
				$pageParam=$ary['pageParam'];
				$paginations=Hash::merge($paginations,$pageParam);
				$kjs=$ary['kjs'];
				
				
				$this->Session->write('rec_x_kjs',$kjs);
			}
		}

		
		
		
		// POSTおよびGETが空ならデフォルトフラグをON
		$def_flg=false;
		if(empty($this->request->data) && empty($this->request->query) ){
			$def_flg=true;
		}
		
		//最新30件ボタンが押された場合の処理
		if(!empty($this->data['new30']) || $def_flg==true){
			//最新30件アクション
			$ary=$this->new30Action($kjs,$saveKjFlg);
			$pageParam=$ary['pageParam'];
			$paginations=Hash::merge($paginations,$pageParam);
			$kjs=$ary['kjs'];
		}
		
		// SQLインジェクション対策用のサニタイズをする。
		App::uses('Sanitize', 'Utility');
		$kjs = Sanitize::clean($kjs, array('encode' => false));
		
	

		//一覧データを取得
    	$data=$this->RecX->findData($kjs,$paginations['page_no'],$paginations['limit'],$paginations['find_order']);

    	// タグ情報のセット
    	foreach($data as $i=> $ent){
    		$id=$ent['id'];
    		$data[$i]['Tags']=$this->RecTag->getTagData($id);
    	}
    	
    

    	 
    	
    	$res=$this->index_after($kjs);//indexアクションの共通後処理
 
    	$datetimeList=$this->createDateTimeList();//日時系検索用のセレクト選択肢

    	// 隠し管理者モードにする。
    	$admin_link_show_flg=0;
    	$auth_flg=0;//認証フラグ 0:未認証 1:認証
    	if(!empty($this->request->query['a'])){
    		$admin_link_show_flg=$this->request->query['a'];
    		

    	}

    	
    	// 認証状態の確認
    	if(!empty($this->Auth->user('id'))){
    		$auth_flg = 1;//認証中
    	}
    	
    	$this->set(array(
    			'kjs'=>$kjs,
    			'datetimeList'=>$datetimeList,
    			'title_for_layout'=>'記録X',
    			'data'=> $data,
    			'seasonBtnData'=> $seasonBtnData,
    			'admin_link_show_flg'=>$admin_link_show_flg,
    			'auth_flg'=>$auth_flg,
    	));



    }
    

    
    /**
     * Ajaxによる登録
     */
    public function ajax_reg(){
    	
    	
    	$this->autoRender = false;//ビュー(ctp)を使わない。
    	
    	// 認証状態の確認
    	if(empty($this->Auth->user('id'))){
    		return 'no_auth';
    	}
    	
    	$json = $_POST['key1'];
    	
    	
    	$dataSet=json_decode($json,true);//JSONデコード
    	$data = $dataSet['data'];
    	$data['id'] = $data['rec_id'];
    	
    	// DBインジェクション対策はなされている模様 Cakeの新機能？

    	
		//★DB保存
    	$this->RecX->begin();//トランザクション開始
    	$rets = $this->RecX->saveAll($data, array('atomic' => false,'validate'=>'false'));
    	$this->RecX->commit();//コミット

    	
    	$json=json_encode($data);//JSONエンコード
    	
    	echo $json;
    }

    /**
     * 最新30件アクション
     * @param array $kjs 検索条件情報
     * @param bool $saveKjFlg 検索条件保存フラグ
     * @return デフォルト検索条件[]
     */
    private function new30Action($kjs,$saveKjFlg){
    
    
    
    	//最終行の日付と、その20件前行の日付を取得
    	$firstDates=$this->RecX->getFirstDates($this->kensakuJoken,30);
    	$defKjs = $this->getDefKjs();
    	$kjs = $defKjs;
    	$kjs['kj_rec_date1']=$firstDates['d1'];
    	$kjs['kj_rec_date2']=$firstDates['d2'];


    	//ページネーションパラメータを取得
    	$pageParam=$this->getPageParamForSubmit($kjs,$saveKjFlg);
    
    	$ary=array('kjs'=>$kjs,'pageParam'=>$pageParam);
    	return $ary;
    }
    

    
    /**
     * //季節ボタンアクション
     * @param array $kjs 検索条件情報
     * @param date $kj_rec_date1 検索日付範囲1
     * @param date $kj_rec_date2 検索日付範囲2
     * @param bool $saveKjFlg 検索条件保存フラグ
     * @return デフォルト検索条件[]
     */
    private function seasonAction($kjs,$kj_rec_date1,$kj_rec_date2,$saveKjFlg){
    
    	$kjs=$this->getDefKjs();//デフォルト検索条件をセット
    	
    	$kjs['kj_rec_date1']=$kj_rec_date1;
    	$kjs['kj_rec_date2']=$kj_rec_date2;
    	$kjs['kj_tag_id'] = null;
    	//$kjs['kj_limit'] = 30;
    
    	//ページネーションパラメータを取得
    	$pageParam=$this->getPageParamForSubmit($kjs,$saveKjFlg);
    
    	$ary=array('kjs'=>$kjs,'pageParam'=>$pageParam);
    	return $ary;
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
    	$fn='rec_x'.$strDate.'.csv';
    
    
    	//CSVダウンロード
    	App::uses('CsvDownloader','Vendor/Wacg');
    	$csv= new CsvDownloader();
    	$csv->output($fn, $data);
    	 
    
    
    }
    
    
    /**
     * //日時系検索用のセレクト選択肢
     */
    private function createDateTimeList(){
    	 
    	$d1=date('Y-m-d');//本日
    	$d2=$this->getBeginningWeekDate($d1);//週初め日付を取得する。
    	$d3 = date('Y-m-d', strtotime("-10 day"));//10日前
    	$d4 = $this->getBeginningMonthDate($d1);//今月一日を取得する。
    	$d5 = date('Y-m-d', strtotime("-30 day"));//30日前
    	$d6 = date('Y-m-d', strtotime("-50 day"));//50日前
    	$d7 = date('Y-m-d', strtotime("-100 day"));//100日前
    	$d8 = date('Y-m-d', strtotime("-180 day"));//180日前
    	$d9 = $this->getBeginningYearDate($d1);//今年元旦を取得する
    	$d10 = date('Y-m-d', strtotime("-365 day"));//365日前
    	 
    	$list= array(
    			$d1=>'本日',
    			$d2=>'今週（日曜日から～）',
    			$d3=>'10日以内',
    			$d4=>'今月（今月一日から～）',
    			$d5=>'30日以内',
    			$d6=>'50日以内',
    			$d7=>'100日以内',
    			$d8=>'半年以内（180日以内）',
    			$d9=>'今年（今年の元旦から～）',
    			$d10=>'1年以内（365日以内）',
    	);
    	 
    
    	return $list;
    	 
    }
    
    
    /**
     * 引数日付の週の週初め日付を取得する。
     * 週初めは日曜日とした場合。
     * @param $ymd
     * @return 週初め
     */
    private function getBeginningWeekDate($ymd) {
    	 
    	$w = date("w",strtotime($ymd));
    	$bwDate = date('Y-m-d', strtotime("-{$w} day", strtotime($ymd)));
    	return $bwDate;
    	 
    }
    
    /**
     * 引数日付から月初めの日付を取得する。
     * @param $ymd
     */
    private function getBeginningMonthDate($ymd) {
    
    	$ym = date("Y-m",strtotime($ymd));
    	$d=$ym.'-01';
    	 
    	return $d;
    
    }
    
    /**
     * 引数日付から元旦日を取得する。
     * @param $ymd
     */
    private function getBeginningYearDate($ymd) {
    
    	$y = date("Y",strtotime($ymd));
    	$d=$y.'-01-01';
    	 
    	return $d;
    
    }
    
    

    



}