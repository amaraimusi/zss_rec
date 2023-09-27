<?php
App::uses('Model', 'Model');
App::uses('CrudBase', 'Model');

/**
 * 農業記録XのCakePHPモデルクラス
 *
 * @date 2015-9-16 | 2018-4-24 複製したとき、順番はそのまま
 * @version 3.0.3
 *
 */
class RecX extends AppModel {

	public $name='RecX';
	
	// 関連付けるテーブル CBBXS-1040
	public $useTable = 'recs';

	// CBBXE


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	
	public function __construct() {
		parent::__construct();
		
		// CrudBaseロジッククラスの生成
		if(empty($this->CrudBase)) $this->CrudBase = new CrudBase();
	}
	
	/**
	 * 農業記録Xエンティティを取得
	 *
	 * 農業記録Xテーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 農業記録XID
	 * @return array 農業記録Xエンティティ
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
			$ent=$data['RecX'];
		}
		



		return $ent;
	}

	/**
	 * 農業記録X画面の一覧に表示するデータを、農業記録Xテーブルから取得します。
	 * 
	 * @note
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（農業記録Xテーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param array $kjs 検索条件情報
	 * @param int $page_no ページ番号
	 * @param int $row_limit 表示件数
	 * @param string sort ソートフィールド
	 * @param int sort_desc ソートタイプ 0:昇順 , 1:降順
	 * @return array 農業記録X画面一覧のデータ
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
		
		$option['table']=$dbo->fullTableName($this->RecX);
		$option['alias']='RecX';
		
		$query = $dbo->buildStatement($option,$this->RecX);
		
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
			$cnds[]="RecX.id = {$kjs['kj_id']}";
		}
		if(!empty($kjs['kj_rec_title'])){
			$cnds[]="RecX.rec_title LIKE '%{$kjs['kj_rec_title']}%'";
		}
		if(!empty($kjs['kj_rec_date'])){
			$kj_rec_date = $kjs['kj_rec_date'];
			$dtInfo = $this->CrudBase->guessDatetimeInfo($kj_rec_date);
			$cnds[]="DATE_FORMAT(RecX.rec_date,'{$dtInfo['format_mysql_a']}') = DATE_FORMAT('{$dtInfo['datetime_b']}','{$dtInfo['format_mysql_a']}')";
		}
		if(!empty($kjs['kj_note'])){
			$cnds[]="RecX.note LIKE '%{$kjs['kj_note']}%'";
		}
		if(!empty($kjs['kj_category_id2']) || $kjs['kj_category_id2'] ==='0' || $kjs['kj_category_id2'] ===0){
			$cnds[]="RecX.category_id2 = {$kjs['kj_category_id2']}";
		}
		if(!empty($kjs['kj_category_id1']) || $kjs['kj_category_id1'] ==='0' || $kjs['kj_category_id1'] ===0){
			$cnds[]="RecX.category_id1 = {$kjs['kj_category_id1']}";
		}
		if(!empty($kjs['kj_tags'])){
			$cnds[]="RecX.tags LIKE '%{$kjs['kj_tags']}%'";
		}
		if(!empty($kjs['kj_photo_fn'])){
			$cnds[]="RecX.photo_fn LIKE '%{$kjs['kj_photo_fn']}%'";
		}
		if(!empty($kjs['kj_photo_dir'])){
			$cnds[]="RecX.photo_dir LIKE '%{$kjs['kj_photo_dir']}%'";
		}
		if(!empty($kjs['kj_ref_url'])){
			$cnds[]="RecX.ref_url LIKE '%{$kjs['kj_ref_url']}%'";
		}
		if(!empty($kjs['kj_nendo1'])){
			$cnds[]="RecX.nendo >= {$kjs['kj_nendo1']}";
		}
		if(!empty($kjs['kj_nendo2'])){
			$cnds[]="RecX.nendo <= {$kjs['kj_nendo2']}";
		}
		if(!empty($kjs['kj_sort_no']) || $kjs['kj_sort_no'] ==='0' || $kjs['kj_sort_no'] ===0){
			$cnds[]="RecX.sort_no = {$kjs['kj_sort_no']}";
		}
		if(!empty($kjs['kj_no_a1'])){
			$cnds[]="RecX.no_a >= {$kjs['kj_no_a1']}";
		}
		if(!empty($kjs['kj_no_a2'])){
			$cnds[]="RecX.no_a <= {$kjs['kj_no_a2']}";
		}
		if(!empty($kjs['kj_no_b1'])){
			$cnds[]="RecX.no_b >= {$kjs['kj_no_b1']}";
		}
		if(!empty($kjs['kj_no_b2'])){
			$cnds[]="RecX.no_b <= {$kjs['kj_no_b2']}";
		}
		if(!empty($kjs['kj_parent_id']) || $kjs['kj_parent_id'] ==='0' || $kjs['kj_parent_id'] ===0){
			$cnds[]="RecX.parent_id = {$kjs['kj_parent_id']}";
		}
		if(!empty($kjs['kj_probe_id']) || $kjs['kj_probe_id'] ==='0' || $kjs['kj_probe_id'] ===0){
			$cnds[]="RecX.probe_id = {$kjs['kj_probe_id']}";
		}
		if(!empty($kjs['kj_publish']) || $kjs['kj_publish'] ==='0' || $kjs['kj_publish'] ===0){
			$cnds[]="RecX.publish = {$kjs['kj_publish']}";
		}
		if(!empty($kjs['kj_create_date'])){
			$kj_create_date = $kjs['kj_create_date'];
			$dtInfo = $this->CrudBase->guessDatetimeInfo($kj_create_date);
			$cnds[]="DATE_FORMAT(RecX.create_date,'{$dtInfo['format_mysql_a']}') = DATE_FORMAT('{$dtInfo['datetime_b']}','{$dtInfo['format_mysql_a']}')";
		}
		if(!empty($kjs['kj_update_date'])){
			$kj_update_date = $kjs['kj_update_date'];
			$dtInfo = $this->CrudBase->guessDatetimeInfo($kj_update_date);
			$cnds[]="DATE_FORMAT(RecX.update_date,'{$dtInfo['format_mysql_a']}') = DATE_FORMAT('{$dtInfo['datetime_b']}','{$dtInfo['format_mysql_a']}')";
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
	 * 農業記録Xエンティティを農業記録Xテーブルに保存します。
	 *
	 * @param array $ent 農業記録Xエンティティ
	 * @param array $option オプション
	 *  - form_type フォーム種別  new_inp:新規入力 , copy:複製 , edit:編集
	 *  - ni_tr_place 新規入力追加場所フラグ 0:末尾 , 1:先頭
	 * @return array 農業記録Xエンティティ（saveメソッドのレスポンス）
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
						'conditions' => "id={$ent['RecX']['id']}"
				));

		$ent=$ent['RecX'];
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

	// CBBXE


}