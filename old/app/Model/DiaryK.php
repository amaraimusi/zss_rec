<?php
App::uses('Model', 'Model');


/**
 * 日誌のモデルクラス
 *
 * 日誌画面用のDB関連メソッドを定義しています。
 * 日誌テーブルと関連付けられています。
 *
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class DiaryK extends AppModel {


	/// 日誌テーブルを関連付け
	public $name='DiaryK';


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	/**
	 * 日誌エンティティを取得
	 *
	 * 日誌テーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 日誌ID
	 * @return array 日誌エンティティ
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
			$ent=$data['DiaryK'];
		}
		



		return $ent;
	}

	/**
	 * 日誌画面の一覧に表示するデータを、日誌テーブルから取得します。
	 * 
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（日誌テーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param $kjs 検索条件情報
	 * @param $page_no ページ番号
	 * @param $limit 表示件数
	 * @param $findOrder ソート情報
	 * @return 日誌画面一覧のデータ
	 */
	public function findData($kjs,$page_no,$limit,$findOrder){

		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='diary_date desc';
		}
		
		$offset=null;
		if(!empty($limit)){
			$offset=$page_no * $limit;
		}
		
		$option=array(
					'conditions' => $conditions,
					'limit' =>$limit,
					'offset'=>$offset,
					'order' => $findOrder,
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
	 * SQLのダンプ
	 * @param  $option
	 */
	private function dumpSql($option){
		$dbo = $this->getDataSource();
		
		$option['table']=$dbo->fullTableName($this->DiaryK);
		$option['alias']='DiaryK';
		
		$query = $dbo->buildStatement($option,$this->DiaryK);
		
		Debugger::dump($query);
	}



	/**
	 * 検索条件情報からWHERE情報を作成。
	 * @param  $kjs	検索条件情報
	 * @return WHERE情報
	 */
	private function createKjConditions($kjs){

		$cnds=null;
		
		// --- Start kjConditions	

		if(!empty($kjs['kj_id'])){
			$cnds[]="DiaryK.id = '{$kjs['kj_id']}'";
		}
		if(!empty($kjs['kj_diary_date1'])){
			$cnds[]="DiaryK.diary_date >= '{$kjs['kj_diary_date1']}'";
		}
		if(!empty($kjs['kj_diary_date2'])){
			$cnds[]="DiaryK.diary_date <= '{$kjs['kj_diary_date2']}'";
		}
		if(!empty($kjs['kj_diary_note'])){
			$cnds[]="DiaryK.diary_note LIKE '%{$kjs['kj_diary_note']}%'";
		}
	
		// --- End kjConditions
		
		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}

	/**
	 * エンティティをDB保存
	 *
	 * 日誌エンティティを日誌テーブルに保存します。
	 *
	 * @param array $ent 日誌エンティティ
	 * @return array 日誌エンティティ（saveメソッドのレスポンス）
	 */
	public function saveEntity($ent){


		//DBに登録('atomic' => false　トランザクションなし）
		$ent = $this->save($ent, array('atomic' => false,'validate'=>'true'));

		//DBからエンティティを取得
		$ent = $this->find('first',
				array(
						'conditions' => "id={$ent['DiaryK']['id']}"
				));
		
		$ent=$ent['DiaryK'];
		
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














}