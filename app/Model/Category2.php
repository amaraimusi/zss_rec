<?php
App::uses('Model', 'Model');

/**
 * カテゴリ2のモデル
 *
 * ★履歴
 * 2014/10/6	新規作成
 * @author k-uehara
 *
 */
class Category2 extends Model {

	var $name='Category2';

	var $useTable='category2_ids';


	/**
	 * カテゴリ2セレクトボックス用の選択肢情報を取得する。
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
		$order=array('Category2.id');

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
				$options[$ent['Category2']['id']]=Sanitize::html($ent['Category2']['name']);
			}
			unset($ent);
		}

		return $options;
	}


}