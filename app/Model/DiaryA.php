<?php
App::uses('Model', 'Model');


/**
 * 日誌Aのモデルクラス
 *
 * 日誌A画面用のDB関連メソッドを定義しています。
 * 日誌Aテーブルと関連付けられています。
 *
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class DiaryA extends AppModel {


	/// 日誌Aテーブルを関連付け
	public $name='DiaryA';


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	/**
	 * 日誌Aエンティティを取得
	 *
	 * 日誌Aテーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 日誌AID
	 * @return array 日誌Aエンティティ
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
			$ent=$data['DiaryA'];
		}
		



		return $ent;
	}

	/**
	 * 日誌A画面の一覧に表示するデータを、日誌Aテーブルから取得します。
	 * 
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（日誌Aテーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param $kjs 検索条件情報
	 * @param $page_no ページ番号
	 * @param $limit 表示件数
	 * @param $findOrder ソート情報
	 * @return 日誌A画面一覧のデータ
	 */
	public function findData($kjs,$page_no,$limit,$findOrder){

		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='diary_date DESC';
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
		
		$option['table']=$dbo->fullTableName($this->DiaryA);
		$option['alias']='DiaryA';
		
		$query = $dbo->buildStatement($option,$this->DiaryA);
		
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
			$cnds[]="DiaryA.id = '{$kjs['kj_id']}'";
		}
		if(!empty($kjs['kj_category'])){
			$cnds[]="DiaryA.category = '{$kjs['kj_category']}'";
		}
		if(!empty($kjs['kj_diary_date1'])){
			$cnds[]="DiaryA.diary_date >= '{$kjs['kj_diary_date1']}'";
		}
		if(!empty($kjs['kj_diary_date2'])){
			$cnds[]="DiaryA.diary_date <= '{$kjs['kj_diary_date2']}'";
		}
		if(!empty($kjs['kj_diary_dt'])){
			$cnds[]="DiaryA.diary_dt = '{$kjs['kj_diary_dt']}'";
		}
		if(!empty($kjs['kj_diary_note'])){
			$cnds[]="DiaryA.diary_note LIKE '%{$kjs['kj_diary_note']}%'";
		}
		if(!empty($kjs['kj_delete_flg'])){
			$cnds[]="DiaryA.delete_flg = '{$kjs['kj_delete_flg']}'";
		}
		if(!empty($kjs['kj_update_user'])){
			$cnds[]="DiaryA.update_user = '{$kjs['kj_update_user']}'";
		}
		if(!empty($kjs['kj_ip_addr'])){
			$cnds[]="DiaryA.ip_addr = '{$kjs['kj_ip_addr']}'";
		}
		if(!empty($kjs['kj_created'])){
			$kj_created=$kjs['kj_created'].' 00:00:00';
			$cnds[]="DiaryA.created >= '{$kj_created}'";
		}
		if(!empty($kjs['kj_modified'])){
			$kj_modified=$kjs['kj_modified'].' 00:00:00';
			$cnds[]="DiaryA.modified >= '{$kj_modified}'";
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
	 * 日誌Aエンティティを日誌Aテーブルに保存します。
	 *
	 * @param array $ent 日誌Aエンティティ
	 * @return array 日誌Aエンティティ（saveメソッドのレスポンス）
	 */
	public function saveEntity($ent){


		//DBに登録('atomic' => false　トランザクションなし）
		$ent = $this->save($ent, array('atomic' => false,'validate'=>'true'));

		//DBからエンティティを取得
		$ent = $this->find('first',
				array(
						'conditions' => "id={$ent['DiaryA']['id']}"
				));
		
		$ent=$ent['DiaryA'];
		
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