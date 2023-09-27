<?php
App::uses('Model', 'Model');


/**
 * 個体のモデルクラス
 *
 * 個体画面用のDB関連メソッドを定義しています。
 * 個体テーブルと関連付けられています。
 *
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class Probe extends Model {


	/// 個体テーブルを関連付け
	public $name='Probe';


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	/**
	 * 個体エンティティを取得
	 *
	 * 個体テーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 個体ID
	 * @return array 個体エンティティ
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
			$ent=$data['Probe'];
		}
		



		return $ent;
	}

	/**
	 * 個体画面の一覧に表示するデータを、個体テーブルから取得します。
	 * 
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（個体テーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param $kjs 検索条件情報
	 * @param $page_no ページ番号
	 * @param $limit 表示件数
	 * @param $findOrder ソート情報
	 * @return 個体画面一覧のデータ
	 */
	public function findData($kjs,$page_no,$limit,$findOrder){

		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='id';
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
		
		//$this->dumpSql($option);■■■□□□■■■□□□■■■□□□
		
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
		
		$option['table']=$dbo->fullTableName($this->Probe);
		$option['alias']='Probe';
		
		$query = $dbo->buildStatement($option,$this->Probe);
		
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
			$cnds[]="Probe.id = '{$kjs['kj_id']}'";
		}
		if(!empty($kjs['kj_probe_name'])){
			$cnds[]="Probe.probe_name LIKE '%{$kjs['kj_probe_name']}%'";
		}
		if(!empty($kjs['kj_hatake_id'])){
			$cnds[]="Probe.hatake_id = '{$kjs['kj_hatake_id']}'";
		}
		if(!empty($kjs['kj_rx'])){
			$cnds[]="Probe.rx = '{$kjs['kj_rx']}'";
		}
		if(!empty($kjs['kj_ry'])){
			$cnds[]="Probe.ry = '{$kjs['kj_ry']}'";
		}
		if(!empty($kjs['kj_probe_note'])){
			$cnds[]="Probe.probe_note LIKE '%{$kjs['kj_probe_note']}%'";
		}
		if(!empty($kjs['kj_delete_flg'])){
			$cnds[]="Probe.delete_flg = '{$kjs['kj_delete_flg']}'";
		}
		if(!empty($kjs['kj_modified'])){
			$kj_modified=$kjs['kj_modified'].' 00:00:00';
			$cnds[]="Probe.modified >= '{$kj_modified}'";
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
	 * 個体エンティティを個体テーブルに保存します。
	 *
	 * @param array $ent 個体エンティティ
	 * @return array 個体エンティティ（saveメソッドのレスポンス）
	 */
	public function saveEntity($ent){


		//DBに登録('atomic' => false　トランザクションなし）
		$ent = $this->save($ent, array('atomic' => false,'validate'=>'true'));

		//DBからエンティティを取得
		$ent = $this->find('first',
				array(
						'conditions' => "id={$ent['Probe']['id']}"
				));
		
		$ent=$ent['Probe'];
		
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
		
		//■■■□□□findDataが複雑である場合は、こちらのコードで件数を取得。処理速度は上記と比べて、若干遅い。
		//$data=$this->findData($kjs,null,null,null);
		//$cnt=count($data);

		return $cnt;
	}














}