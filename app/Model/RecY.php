<?php
App::uses('Model', 'Model');

/**
 * 記録Yのモデル
 * ★概要
 *
 * ★履歴
 * 2014/8/25	新規作成
 * @author k-uehara
 *
 */
class RecY extends Model {

	var $name='RecY';

	var $useTable='recs';

	//検索入力のバリデーション
	public $validate = null;


	//独自バリデーションルール・新規モードの場合だけ、日にちの存在チェックをする。
	function checkOnly(){
		//チェックしたいルールを書く


		$mode=$_POST['data']['RecY']['mode'];
		if($mode=='edit'){
			return true;
		}else if($mode=='new'){
			if (!empty($this->data['RecY']['sale_date'])){

				$sale_date=$this->data['RecY']['sale_date'];

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
			'rec_title',
			'rec_date',
			'note',
			'category_id2',
			'category_id1',
			'tags',
			'photo_fn',
			'photo_dir',
			'ref_url',
			'nendo',
			'sort_no',
			'no_a',
			'no_b',
			'parent_id',
			'publish',

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


		$ent=$data['RecY'];



		return $ent;
	}


	function findData($kjs,$page_no,$limit,$findOrder){


// 		//SELECT情報
// 		$fields=array(
// 			'id',
// 			'rec_title',
// 			'rec_date',
// 			'note',
// 			'category_id2',
// 			'category_id1',
// 			'tags',
// 			'photo_fn',
// 			'photo_dir',
// 			'ref_url',
// 			'nendo',
// 			'sort_no',
// 			'no_b',
// 			'publish',
// 			'create_date',
// 			'update_date',
// 		);

		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='RecY.rec_date';
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


		if(!empty($kjs['kj_rec_date1'])){
			$cnds[]="RecY.rec_date >= '{$kjs['kj_rec_date1']}'";
		}

		if(!empty($kjs['kj_rec_date2'])){
			$cnds[]="RecY.rec_date <= '{$kjs['kj_rec_date2']}'";
		}

		if(!empty($kjs['kj_category_id1'])){
			$cnds[]="RecY.category_id1 >= '{$kjs['kj_category_id1']}'";
		}

		if(!empty($kjs['kj_category_id2'])){
			$cnds[]="RecY.category_id2 >= '{$kjs['kj_category_id2']}'";
		}

		if(!empty($kjs['kj_rec_title'])){
			$cnds[]="RecY.rec_title LIKE '%{$kjs['kj_rec_title']}%'";
		}

		if(!empty($kjs['kj_note'])){
			$cnds[]="RecY.note LIKE '%{$kjs['kj_note']}%'";
		}

		if(!empty($kjs['kj_tags'])){
			$cnds[]="RecY.tags LIKE '%{$kjs['kj_tags']}%'";
		}

		if(!empty($kjs['kj_tag_id'])){
			$str_rec_ids=$this->getIdsByTagId($kjs['kj_tag_id']);
			if(!empty($str_rec_ids)){
				$cnds[]="RecY.id IN ({$str_rec_ids})";
			}
		}


		//$cnds[]="RecY.publish = 1";

		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}

	/**
	 * タグＩＤから記録ＩＤ連結を取得
	 * 
	 * @param $tag_id　タグＩＤ
	 * @return 記録ＩＤ連結
	 */
	private function getIdsByTagId($tag_id){
		if(empty($this->RecTag)){
			App::uses('RecTag','Model');
			$this->RecTag=new RecTag();
		}

		//SELECT情報
		$fields=array(
			'rec_id',

		);

		//WHERE情報
		$conditions=array(
			"tag_id = '{$tag_id}'",
		);


		//オプション
		$option=array(
			'fields'=>$fields,
			'conditions'=>$conditions,
			'recursive' => -1,
		);

		//DBから取得
		$data=$this->RecTag->find('list',$option);

		$str=null;
		
		if(!empty($data)){

			$str = join(',',$data);
		}

		return $str;
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




	/**
	 * 最終行の日付と、その20件前行の日付を取得
	 * @param $kjs	検索条件情報
	 * @return
	 */
	public function getFirstDates($kjs){

		//SELECT情報
		$fields=array(
				'id',
				'rec_date',

		);

		//条件を作成
		$limit=30;
		$conditions=$this->createKjConditions($kjs,$limit);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='RecY.rec_date DESC';
		}

		//DBからデータを取得
		$data = $this->find(
				'all',
				Array(
						//'fields' => $fields,
						'conditions' => $conditions,
						'limit' =>$limit,
						'order' => $findOrder,
				)
		);

		$d1=null;
		$d2=null;
		$lastIndex=count($data)-1;
		if($lastIndex >= 1){
			$d2=$data[0]['RecY']['rec_date'];
			$d1=$data[$lastIndex]['RecY']['rec_date'];
		}


		$ret=array('d1'=>$d1,'d2'=>$d2);


		return $ret;
	}

}