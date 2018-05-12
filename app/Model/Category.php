<?php
App::uses('Model', 'Model');

/**
 * カテゴリのモデル
 *
 * ★履歴
 * 2014/10/6	新規作成
 * @author k-uehara
 *
 */
class Category extends Model {

	var $name='Category';

	var $useTable='category1_ids';


	/**
	 * カテゴリ１セレクトボックス用の選択肢情報を取得する。
	 *
	 * @return multitype:カテゴリ１選択肢情報
	 */
	public function getCategoryOptions(){


		//SELECT情報
		$fields=array(
				'id',
				'name',
		);


		//ORDER情報
		$order=array('Category.id');

		//オプション
		$option=array(
				'fields'=>$fields,
				'order'=>$order,
				'recursive' => -1,
		);

		//DBから取得
		$data=$this->find('all',$option);

		$options=array();

		//セレクトボックス用に構造変換。ついでにサニタイズ
		if(!empty($data)){
			foreach($data as &$ent){
				$options[$ent['Category']['id']]=Sanitize::html($ent['Category']['name']);
			}
			unset($ent);
		}

		return $options;
	}


	/**
	 * カテゴリ名をマッピング
	 * @param  $data データ
	 * @return データ
	 */
	public function mapping($data,$model_name1="RecCrud1"){

		$id_key='category_id1';
		$name_key='category_name1';


		$id_key2='id';
		$name_key2='name';
		$model_name2="Category";


		// データからIDリストを取得
		$ids=array();
		foreach($data as $ent){
			$id=$ent[$model_name1][$id_key];
			if(!empty($ent[$model_name1][$id_key])){
				$ids[]=$ent[$model_name1][$id_key];
			}elseif($ent[$model_name1][$id_key]===0 || $ent[$model_name1][$id_key]==='0'){
				$ids[]=$ent[$model_name1][$id_key];
			}
		}

		// IDリストから重複するIDリストを削除
		$ids=array_unique($ids);

		if(empty($ids)){

			foreach($data as &$ent){
				$ent[$model_name1][$name_key]=null;
			}
			unset($ent);
			return $data;
		}

		// IN句をIDリストから作成する。
		$str_ids=join($ids,',');

		//元データをDBから取得
		$fields=array($id_key2,$name_key2);
		$conditions=array("id IN ({$str_ids})");
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
				'recursive' => -1,
		);
		$mapData=$this->find('all',$option);

		//マッピングデータを作成
		$map=array();

		//マッピングデータを作成。ついでにサニタイズ
		if(!empty($mapData)){
			foreach($mapData as &$map_ent){
				$map[$map_ent[$model_name2][$id_key2]]=Sanitize::html($map_ent[$model_name2][$name_key2]);
			}
			unset($ent);
		}


		// データ件数分ループして、IDに紐づくマッピングのデータをセット
		foreach($data as &$ent){
			if(!empty($map[$ent[$model_name1][$id_key]])){
				$ent[$model_name1][$name_key]=$map[$ent[$model_name1][$id_key]];
			}else{
				$ent[$model_name1][$name_key]=null;
			}
		}
		unset($ent);

		return $data;


	}

}