<?php
App::uses('Model', 'Model');
App::uses('CrudBase', 'Model');

/**
 * 心得メインのCakePHPモデルクラス
 *
 * @date 2015-9-16 | 2018-4-24 複製したとき、順番はそのまま
 * @version 3.0.3
 *
 */
class Knowledge extends AppModel {

	public $name='Knowledge';
	
	// 関連付けるテーブル CBBXS-1040
	public $useTable = 'knowledges';

	// CBBXE


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	
	public function __construct() {
		parent::__construct();
		
		// CrudBaseロジッククラスの生成
		if(empty($this->CrudBase)) $this->CrudBase = new CrudBase();
	}
	
	/**
	 * 心得メインエンティティを取得
	 *
	 * 心得メインテーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 心得メインID
	 * @return array 心得メインエンティティ
	 */
	public function findEntity($id){

		$conditions='id = '.$id;

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
						'conditions' => $conditions,
				)
		);

		$ent=array();
		if(!empty($data)){
			$ent=$data['Knowledge'];
		}
		



		return $ent;
	}

	/**
	 * 心得メイン画面の一覧に表示するデータを、心得メインテーブルから取得します。
	 * 
	 * @note
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（心得メインテーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param array $kjs 検索条件情報
	 * @param int $page_no ページ番号
	 * @param int $row_limit 表示件数
	 * @param string sort ソートフィールド
	 * @param int sort_desc ソートタイプ 0:昇順 , 1:降順
	 * @return array 心得メイン画面一覧のデータ
	 */
	public function findData($kjs,$page_no,$row_limit,$sort_field,$sort_desc){

		//条件を作成
		$conditions=$this->createKjConditions($kjs);
		
		// オフセットの組み立て
		$offset=null;
		if(!empty($row_limit)) $offset = $page_no * $row_limit;
		
		// ORDER文の組み立て
		$order = $sort_field;
		if(empty($order)) $order='sort_no';
		if(!empty($sort_desc)) $order .= ' DESC';
		
		$option=array(
            'conditions' => $conditions,
            'limit' =>$row_limit,
            'offset'=>$offset,
            'order' => $order,
        );
		
		//DBからデータを取得
		$data = $this->find('all',$option);

		//データ構造を変換（2次元配列化）
		$data2=array();
		foreach($data as $i=>$tbl){
			foreach($tbl as $ent){
				foreach($ent as $key => $v){
					$data2[$i][$key]=$v;
				}
			}
		}
		
		return $data2;
	}
	
	
	/**
	 * 一覧データを取得する
	 */
	public function findData2(&$crudBaseData){

		$kjs = $crudBaseData['kjs'];//検索条件情報
		$pages = $crudBaseData['pages'];//ページネーション情報

		$data = $this->findData($kjs,$pages['page_no'],$pages['row_limit'],$pages['sort_field'],$pages['sort_desc']);
		
		return $data;
	}

	
	
	/**
	 * SQLのダンプ
	 * @param  $option
	 */
	private function dumpSql($option){
		$dbo = $this->getDataSource();
		
		$option['table']=$dbo->fullTableName($this->Knowledge);
		$option['alias']='Knowledge';
		
		$query = $dbo->buildStatement($option,$this->Knowledge);
		
		Debugger::dump($query);
	}



	/**
	 * 検索条件情報からWHERE情報を作成。
	 * @param array $kjs	検索条件情報
	 * @return string WHERE情報
	 */
	private function createKjConditions($kjs){

		$cnds=null;
		
		$this->CrudBase->sql_sanitize($kjs); // SQLサニタイズ
		
		// CBBXS-1003
		if(!empty($kjs['kj_id']) || $kjs['kj_id'] ==='0' || $kjs['kj_id'] ===0){
			$cnds[]="Knowledge.id = {$kjs['kj_id']}";
		}
		if(!empty($kjs['kj_kl_text'])){
			$cnds[]="Knowledge.kl_text LIKE '%{$kjs['kj_kl_text']}%'";
		}
		if(!empty($kjs['kj_xid'])){
			$cnds[]="Knowledge.xid LIKE '%{$kjs['kj_xid']}%'";
		}
		if(!empty($kjs['kj_kl_category']) || $kjs['kj_kl_category'] ==='0' || $kjs['kj_kl_category'] ===0){
			$cnds[]="Knowledge.kl_category = {$kjs['kj_kl_category']}";
		}
		if(!empty($kjs['kj_contents_url'])){
			$cnds[]="Knowledge.contents_url LIKE '%{$kjs['kj_contents_url']}%'";
		}
		if(!empty($kjs['kj_doc_name'])){
			$cnds[]="Knowledge.doc_name LIKE '%{$kjs['kj_doc_name']}%'";
		}
		if(!empty($kjs['kj_doc_text'])){
			$cnds[]="Knowledge.doc_text LIKE '%{$kjs['kj_doc_text']}%'";
		}
		if(!empty($kjs['kj_dtm'])){
			$kj_dtm = $kjs['kj_dtm'];
			$dtInfo = $this->CrudBase->guessDatetimeInfo($kj_dtm);
			$cnds[]="DATE_FORMAT(Knowledge.dtm,'{$dtInfo['format_mysql_a']}') = DATE_FORMAT('{$dtInfo['datetime_b']}','{$dtInfo['format_mysql_a']}')";
		}
		if(!empty($kjs['kj_level1'])){
			$cnds[]="Knowledge.level >= {$kjs['kj_level1']}";
		}
		if(!empty($kjs['kj_level2'])){
			$cnds[]="Knowledge.level <= {$kjs['kj_level2']}";
		}
		if(!empty($kjs['kj_sort_no']) || $kjs['kj_sort_no'] ==='0' || $kjs['kj_sort_no'] ===0){
			$cnds[]="Knowledge.sort_no = {$kjs['kj_sort_no']}";
		}
		$kj_delete_flg = $kjs['kj_delete_flg'];
		if(!empty($kjs['kj_delete_flg']) || $kjs['kj_delete_flg'] ==='0' || $kjs['kj_delete_flg'] ===0){
			if($kjs['kj_delete_flg'] != -1){
			   $cnds[]="Knowledge.delete_flg = {$kjs['kj_delete_flg']}";
			}
		}
		if(!empty($kjs['kj_update_user'])){
			$cnds[]="Knowledge.update_user LIKE '%{$kjs['kj_update_user']}%'";
		}
		if(!empty($kjs['kj_ip_addr'])){
			$cnds[]="Knowledge.ip_addr LIKE '%{$kjs['kj_ip_addr']}%'";
		}
		if(!empty($kjs['kj_created'])){
			$kj_created=$kjs['kj_created'].' 00:00:00';
			$cnds[]="Knowledge.created >= '{$kj_created}'";
		}
		if(!empty($kjs['kj_modified'])){
			$kj_modified=$kjs['kj_modified'].' 00:00:00';
			$cnds[]="Knowledge.modified >= '{$kj_modified}'";
		}

		// CBBXE
		
		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}

	/**
	 * エンティティをDB保存
	 *
	 * 心得メインエンティティを心得メインテーブルに保存します。
	 *
	 * @param array $ent 心得メインエンティティ
	 * @param array $option オプション
	 *  - form_type フォーム種別  new_inp:新規入力 , copy:複製 , edit:編集
	 *  - ni_tr_place 新規入力追加場所フラグ 0:末尾 , 1:先頭
	 * @return array 心得メインエンティティ（saveメソッドのレスポンス）
	 */
	public function saveEntity($ent,$option=array()){

		// 新規入力であるなら新しい順番をエンティティにセットする。
		if($option['form_type']=='new_inp' ){
			if(empty($option['ni_tr_place'])){
				$ent['sort_no'] = $this->CrudBase->getLastSortNo($this); // 末尾順番を取得する
			}else{
				$ent['sort_no'] = $this->CrudBase->getFirstSortNo($this); // 先頭順番を取得する
			}
		}
		

		//DBに登録('atomic' => false　トランザクションなし。saveでSQLサニタイズされる）
		$ent = $this->save($ent, array('atomic' => false,'validate'=>false));

		//DBからエンティティを取得
		$ent = $this->find('first',
				array(
						'conditions' => "id={$ent['Knowledge']['id']}"
				));

		$ent=$ent['Knowledge'];
		if(empty($ent['delete_flg'])) $ent['delete_flg'] = 0;

		return $ent;
	}

	


	/**
	 * 全データ件数を取得
	 *
	 * limitによる制限をとりはらった、検索条件に紐づく件数を取得します。
	 *  全データ件数はページネーション生成のために使われています。
	 *
	 * @param array $kjs 検索条件情報
	 * @return int 全データ件数
	 */
	public function findDataCnt($kjs){

		//DBから取得するフィールド
		$fields=array('COUNT(id) AS cnt');
		$conditions=$this->createKjConditions($kjs);

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
						'fields'=>$fields,
						'conditions' => $conditions,
				)
		);

		$cnt=$data[0]['cnt'];
		return $cnt;
	}
	
	
	// CBBXS-1021
	/**
	 * カテゴリリストをDBから取得する
	 */
	public function getKlCategoryList(){
		if(empty($this->KlCategory)){
			App::uses('KlCategory','Model');
			$this->KlCategory=ClassRegistry::init('KlCategory');
		}
		$fields=array('id','kl_category_name');//SELECT情報
		$conditions=array("delete_flg = 0");//WHERE情報
		$order=array('sort_no');//ORDER情報
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
				'order'=>$order,
		);

		$data=$this->KlCategory->find('all',$option); // DBから取得
		
		// 構造変換
		if(!empty($data)){
			$data = Hash::combine($data, '{n}.KlCategory.id','{n}.KlCategory.kl_category_name');
		}
		
		return $data;
	}

	// CBBXE


}