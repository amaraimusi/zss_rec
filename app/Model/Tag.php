<?php
App::uses('Model', 'Model');

/**
 * タグのモデル
 * ★概要
 *
 * ★履歴
 * 2015/2/14	新規作成
 * @author k-uehara
 *
 */
class Tag extends Model {

	var $name='Tag';

	var $useTagle='tags';

	//検索入力のバリデーション
	public $validate = null;


	//独自バリデーションルール・新規モードの場合だけ、日にちの存在チェックをする。
	function checkOnly(){
		//チェックしたいルールを書く


		$mode=$_POST['data']['Tag']['mode'];
		if($mode=='edit'){
			return true;
		}else if($mode=='new'){
			if (!empty($this->data['Tag']['sale_date'])){

				$sale_date=$this->data['Tag']['sale_date'];

				//存在するか確認
				$count = $this->find('count',
						array('conditions' => array('sale_date' => $sale_date)
						));

				//1件でもあればfalseを返す
				return $count == 0;
			}else{
				return false;
			}
		}else{
			return true;
		}


	}


	function findEntity($id){


		//DBから取得するフィールド
		$fields=array(
			'id',
			'name',
			'del_flg',
			'updated',

		);


		$conditions='id = '.$id;

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
					'fields'=>$fields,
					'conditions' => $conditions,
				)
		);


		$ent=$data['Tag'];



		return $ent;
	}


	function findData($kjs,$page_no,$limit,$findOrder){




		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='Tag.id';
		}

		//DBからデータを取得
		$data = $this->find(
				'all',
				Array(
					//'fields' => $fields,
					'conditions' => $conditions,
					'limit' =>$limit,
					'offset'=>$page_no*$limit,
					'order' => $findOrder,
				)
		);



		return $data;
	}



	/**
	 * 検索条件情報からWHERE情報を作成。
	 * @param  $kjs	検索条件情報
	 * @return WHERE情報
	 */
	private function createKjConditions($kjs){

		$cnds=null;




		if(!empty($kjs['kj_name'])){
			$cnds[]="Tag.name LIKE '%{$kjs['kj_name']}%'";
		}

		if(!empty($kjs['kj_del_flg'])){
			$cnds[]="Tag.del_flg = {$kjs['kj_del_flg']}";
		}


		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}


	/**
	 * エンティティをDBに登録する。
	 * @param  $ent
	 */
	public function saveEntity($ent){
		//SQLインジェクションのサニタイズ
		//App::uses('Sanitize', 'Utility');
		//$ent = Sanitize::clean($ent, array('encode' => false));

		//DBに登録
		$rets = $this->saveAll($ent, array('atomic' => false,'validate'=>'true'));


		return $rets;
	}



	//新IDを取得
	function findNewId(){


		//DBから取得するフィールド
		$fields=array('MAX(id) AS new_id');


		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
					'fields'=>$fields,

				)
		);

		$newId=$data[0]['new_id'];
		$newId++;

		return $newId;
	}



	//全データ数を取得
	function findDataCnt($kjs){


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

		$cnt=0;
		if(!empty($data)){
			$cnt=$data[0]['cnt'];
		}


		return $cnt;
	}

	//IDに紐づくレコードを削除する。
	public function del($id){
		if(!empty($id)){
			$this->delete($id,array('atomic' => true));
		}
	}






}