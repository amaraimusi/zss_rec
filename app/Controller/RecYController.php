<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 記録Y
 * 
 * 記録Y画面では記録Y一覧を検索閲覧、および編集ができます。
 * 
 * 
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class RecYController extends CrudBaseController {

	/// 名称コード
	public $name = 'RecY';
	
	/// 使用しているモデル
	public $uses = array('RecY','Category','Category2','RecTag');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='RecY.id';
	
	/// デフォルトソートタイプ      0:昇順 1:降順
	public $defSortType=0;
	
	/// 検索条件のセッション保存フラグ
	public $kj_session_flg=true;

	///検索条件定義
	public $kensakuJoken=array(
			array('name'=>'kj_rec_date1','def'=>null),
			array('name'=>'kj_rec_date2','def'=>null),
			array('name'=>'kj_category_id1','def'=>null),
			array('name'=>'kj_category_id2','def'=>null),
			array('name'=>'kj_rec_title','def'=>null),
			array('name'=>'kj_note','def'=>null),
			array('name'=>'kj_tags','def'=>null),
			array('name'=>'kj_tag_id','def'=>null),
			array('name'=>'kj_limit','def'=>30),
	);


	///検索条件のバリデーション
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
	);
	
	
	/// 一覧列情報（ソート機能付き）
	public $table_fields=array(
			'RecY.id'=>'ID',
			'RecY.rec_title'=>'タイトル',
			'RecY.rec_date'=>'記録日付',
			'RecY.note'=>'説明',
			'RecY.category_id2'=>'カテゴリ2',
			'RecY.category_id1'=>'カテゴリ1',
			'RecY.tags'=>'タグ',
			'RecY.photo_fn'=>'写真ファイル名',
			'RecY.photo_dir'=>'写真ディレクトリ',
			'RecY.ref_url'=>'参照URL',
			'RecY.nendo'=>'年度',
			'RecY.sort_no'=>'ソート番号',
			'RecY.no_a'=>'番号A',
			'RecY.no_b'=>'番号B',
			'RecY.parent_id'=>'親ID',
			'RecY.publish'=>'公開フラグ',
			'RecY.create_date'=>'生成日',
			'RecY.update_date'=>'更新日',
				
	);
	
	///編集エンティティ定義
	public $entity_info=array(
	
			array('name'=>'id','def'=>null),
			array('name'=>'rec_title','def'=>null),
			array('name'=>'rec_date','def'=>null),
			array('name'=>'note','def'=>null),
			array('name'=>'category_id2','def'=>null),
			array('name'=>'category_id1','def'=>null),
			array('name'=>'tags','def'=>null),
			array('name'=>'photo_fn','def'=>null),
			array('name'=>'photo_dir','def'=>null),
			array('name'=>'ref_url','def'=>null),
			array('name'=>'nendo','def'=>null),
			array('name'=>'sort_no','def'=>null),
			array('name'=>'no_a','def'=>null),
			array('name'=>'no_b','def'=>null),
			array('name'=>'parent_id','def'=>null),
			array('name'=>'publish','def'=>null),
			array('name'=>'j_tags','def'=>null),
			array('name'=>'j_tags_b','def'=>null),
			array('name'=>'j_tag_ids','def'=>null),
	
	
	);
	
	///編集用バリデーション
	public $edit_validate = array(
	
			'rec_title' => array(
					'maxLength'=>array(
							'rule' => array('maxLength', 50),
							'message' => 'タイトルは50文字以内で入力してください。',
							'allowEmpty' => true
					)
			),
	
			// 			'rec_date'=> array(
			// 					'rule' => array( 'datetime', 'ymd'),
			// 					'message' => '▲日付形式【yyyy-mm-dd】で入力してください。',
			// 					'allowEmpty' => true
			// 			),
	
	
			'note' => array(
					'maxLength'=>array(
							'rule' => array('maxLength', 1000),
							'message' => 'ノートは1000文字以内で入力してください。',
							'allowEmpty' => true
					)
			),
	
	
			'tags' => array(
					'maxLength'=>array(
							'rule' => array('maxLength', 255),
							'message' => 'タグは255文字以内で入力してください。',
							'allowEmpty' => true
					)
			),
	
	
	
	
			'ref_url' => array(
					'maxLength'=>array(
							'rule' => array('maxLength', 2083),
							'message' => '参照URLは2083文字以内で入力してください。',
							'allowEmpty' => true
					)
			),
	
	
			'nendo' => array(
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
	
	
			'sort_no' => array(
					'naturalNumber'=>array(
							'rule' => array('naturalNumber', true),  // 0以上の整数
							'message' => 'ソート番号は数値を入力してください',
							'allowEmpty' => true
					),
			),
	
	
			'no_a' => array(
					'naturalNumber'=>array(
							'rule' => array('naturalNumber', true),  // 0以上の整数
							'message' => '番号Aは数値を入力してください',
							'allowEmpty' => true
					),
			),
	
	
			'no_b' => array(
					'naturalNumber'=>array(
							'rule' => array('naturalNumber', true),  // 0以上の整数
							'message' => '番号Bは数値を入力してください',
							'allowEmpty' => true
					),
			),
	
	
			'parent_id' => array(
					'naturalNumber'=>array(
							'rule' => array('naturalNumber', true),  // 0以上の整数
							'message' => '親IDは数値を入力してください',
							'allowEmpty' => true
					),
			),
	
	
	
	
	);


	/**
	 * indexページのアクション
	 *
	 * indexページでは記録Y一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
    public function index() {
    	
		$res=$this->index_before('RecY',$this->request->data);//indexアクションの共通先処理(CrudBaseController)
		$kjs=$res['kjs'];
		$errMsg=$res['errMsg'];
		$paginations=$res['paginations'];
		$saveKjFlg=$res['saveKjFlg'];
		
		//■■■□□□■■■□□□■■■□□□
// 		//日付系の検索条件デフォルト
// 		if($this->kensakuJoken[1]['def'] == null && $this->kensakuJoken[0]['def']==null){
		
// 			//最終行の日付と、その20件前行の日付を取得
// 			$firstDates=$this->RecY->getFirstDates($this->kensakuJoken);
		
// 			$this->kensakuJoken[0]['def']=$firstDates['d1'];
// 			$this->kensakuJoken[1]['def']=$firstDates['d2'];
// 		}
		
		//初期フラグがONである場合、最新30件
		if(!empty($this->request->query['i'])){
			//最新30件アクション
			$ary=$this->new30Action($kjs);
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
				
		}
		
		//最新30件ボタンが押された場合の処理
		if(!empty($this->data['new30'])){
			//最新30件アクション
			$ary=$this->new30Action($kjs);
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		
		//季節ボタン1ボタンが押された場合の処理
		if(!empty($this->data['season1'])){
		
			$ary=$this->seasonAction($kjs,'2014-7-1 00:00:00','2014-9-30 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		
		//季節ボタン2ボタンが押された場合の処理
		if(!empty($this->data['season2'])){
		
			$ary=$this->seasonAction($kjs,'2014-10-1 00:00:00','2014-12-31 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		//季節ボタン3ボタンが押された場合の処理
		if(!empty($this->data['season3'])){
		
			$ary=$this->seasonAction($kjs,'2015-1-1 00:00:00','2015-3-31 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		//季節ボタン4ボタンが押された場合の処理
		if(!empty($this->data['season4'])){
		
			$ary=$this->seasonAction($kjs,'2015-4-1 00:00:00','2015-6-30 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
	
		if(!empty($this->data['season5'])){
		
			$ary=$this->seasonAction($kjs,'2015-7-1 00:00:00','2015-9-30 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		
		
		if(!empty($this->data['season6'])){
		
			$ary=$this->seasonAction($kjs,'2015-10-1 00:00:00','2015-12-31 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		
		if(!empty($this->data['season7'])){
		
			$ary=$this->seasonAction($kjs,'2016-1-1 00:00:00','2016-3-31 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}
		
		
		if(!empty($this->data['season8'])){
		
			$ary=$this->seasonAction($kjs,'2016-4-1 00:00:00','2016-6-30 23:59:59');
			$pageParam=$ary['pageParam'];
			$kjs=$ary['kjs'];
		}

		//一覧データを取得
    	$data=$this->RecY->findData($kjs,$paginations['page_no'],$paginations['limit'],$paginations['find_order']);

    	$this->Session->write('rec_y_kjs',$kjs);//CSV用にセッションセット


    	$res=$this->index_after($kjs);//indexアクションの共通後処理
    	$pages=$res['pages'];

    	// 検索条件情報からデフォルト検索JSONを取得する
    	$defKjsJson=$this->getDefKjsJson();
    	
    	$datetimeList=$this->createDateTimeList();//日時系検索用のセレクト選択肢
    	
    	//カテゴリ1選択肢リストを取得
    	$categoryOptions=$this->Category->getCategoryOptions();
    	
    	$this->set(array(
				'header' => 'header_demo',
    			'title_for_layout'=>'記録Y',
    			'data'=> $data,
    			'kjs'=>$kjs,
    			'table_fields'=>$this->table_fields,
    			'pages'=>$pages,
    			'errMsg'=>$errMsg,
    			'saveKjFlg'=>$saveKjFlg,
    			'defKjsJson'=>$defKjsJson,
    			'datetimeList'=>$datetimeList,
    			'categoryOptions'=>$categoryOptions,
    	));


    }



    //最新30件アクション
    private function new30Action($kjs){
    
    
    
    	//最終行の日付と、その20件前行の日付を取得
    	$firstDates=$this->RecY->getFirstDates($this->kensakuJoken,30);
    	$kjs['kj_rec_date1']=$firstDates['d1'];
    	$kjs['kj_rec_date2']=$firstDates['d2'];
    	$kjs['kj_tag_id'] = null;
    	$kjs['kj_limit'] = 30;
    
    	//ページネーションパラメータを取得
    	$pageParam=$this->getPageParamForSubmit($kjs);
    
    	$ary=array('kjs'=>$kjs,'pageParam'=>$pageParam);
    	return $ary;
    }

	//季節ボタンアクション
    private function seasonAction($kjs,$kj_rec_date1,$kj_rec_date2){

	   	$kjs['kj_rec_date1']=$kj_rec_date1;
	   	$kjs['kj_rec_date2']=$kj_rec_date2;
    	$kjs['kj_tag_id'] = null;
	   	//$kjs['kj_limit'] = 30;

    	//ページネーションパラメータを取得
		$pageParam=$this->getPageParamForSubmit($kjs);

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
    	 
    	//セッションから読取
    	$kjs=$this->Session->read('rec_y_kjs');
    	
    	
    	//DBからデータ取得
    	$data=$this->RecY->findData($kjs,null,null,null);
    	if(empty($data)){
    		return array();
    	}
    	 
    	 
    	//列名配列を取得
    	$clms=array_keys($data[0]);
    	 
    	//データの先頭行に列名配列を挿入
    	array_unshift($data,$clms);
    	 
    	 
    	//CSVファイル名を作成
    	$date = new DateTime();
    	$strDate=$date->format("Y-m-d");
    	$fn='rec_y'.$strDate.'.csv';
    	 
    	 
    	//CSVダウンロード
    	App::uses('CsvDownloader','Vendor/Wacg');
    	$csv= new CsvDownloader();
    	$csv->output($fn, $data);
    	
    

    }

    /**
     * 詳細画面
     * 
     * 記録Y情報の詳細を表示します。
     * この画面から入力画面に遷移できます。
     * 
     */
    public function detail() {
    	
    	$res=$this->edit_before('RecY');
    	$ent=$res['ent'];
    	
    	$this->set(array(
    			'header' => 'header_demo',
    			'title_for_layout'=>'記録Y・詳細',
    			'ent'=>$ent,
    	));
    
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

    	$res=$this->edit_before('RecY');
    	$noData=$res['noData'];
    	$ent=$res['ent'];
    	$errMsg=$res['errMsg'];
    	$mode=$res['mode'];
    	
    	//記録タグテーブルからタグテキストと連結タグＩＤを取得する。
    	$rs=$this->RecTag->getTagJDate($ent['id']);
    	$ent['j_tags']=$rs['j_tags'];
    	$ent['j_tag_ids']=$rs['j_tag_ids'];

    	//カテゴリセレクト選択肢情報を取得
    	$categoryOptions1=$this->Category->getCategoryOptions();
    	$categoryOptions2=$this->Category2->getCategoryOptions();
    	
    	//リファラを取得
    	$referer = ( !empty($this->params['url']['referer']) ) ? $this->params['url']['referer'] : null;

    	$this->set(array(
				'header' => 'header_demo',
    			'title_for_layout'=>'記録Y・編集',
    			'ent'=>$ent,
    			'noData'=>$noData,
    			'mode'=>$mode,
    			'errMsg'=>$errMsg,
				'referer'=>$referer,
    			'categoryOptions1'=>$categoryOptions1,
    			'categoryOptions2'=>$categoryOptions2,
    	));

    }
    
     /**
     * 登録完了画面
     * 
     * 入力画面の更新ボタンを押し、DB更新に成功した場合、この画面に遷移します。
     * 入力エラーがある場合は、入力画面へ、エラーメッセージと共にリダイレクトで戻ります。
     */
    public function reg(){
    	
   
    	$res=$this->reg_before('RecY');
    	$ent=$res['ent'];
    	$mode=$res['mode'];

    	//更新関係のパラメータをエンティティにセットする。
    	$ent=$this->setUpdateInfo($ent,$mode);


    	//リファラを取得
    	$referer = ( !empty($this->request->data['RecY']['referer']) ) ? $this->request->data['RecY']['referer'] : null;

    	$regMsg="<p id='reg_msg'>更新しました。</p>";

    	//オリジナルバリデーション■■■□□□■■■□□□■■■□□□
    	//$xFlg=$this->validRecY();
    	$xFlg=true;
    	if($xFlg==false){

    		//エラーメッセージと一緒に編集画面へ、リダイレクトで戻る。
    		$this->errBackToEdit("オリジナルバリデーションのエラー");
    	}
    	
  	
    	//★DB保存
    	$this->RecY->begin();//トランザクション開始
    	$ent=$this->RecY->saveEntity($ent);//登録
    	$this->RecY->commit();//コミット
    	
    	$this->redirect('/rec_y');
    	
		//■■■□□□■■■□□□■■■□□□
    	$this->set(array(
				'header' => 'header_demo',
    			'title_for_layout'=>'記録Y・登録完了',
    			'ent'=>$ent,
    			'mode'=>$mode,
    			'regMsg'=>$regMsg,
				'referer'=>$referer,
    	));

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