<?php
App::uses('Model', 'Model');


class LearnCounterK extends Model {

	var $name='LearnCounterK';

	var $useTable=null;// ■■■□□□■■■□□□■■■□□□

	//検索入力のバリデーション
	public $validate = null;

	
	
	/**
	 * xidを基準としたデータ保存
	 * 
	 * xidはHTML要素のID属性
	 * @param array $data
	 * @return DBレスポンス
	 */
	public function saveData($data){
	

		
		
		// XIDリストを抜き出す
		$xids=array_keys($data);
		
	
		// XIDリストに紐づくデータをデータ2として取得する
		$data2 = $this->getData($xids);
		
		
		// データ2にデータをマージする。
		$data2 = $this->mergeData($data2,$data);
		
		
		$rs=$this->saveAll($data2, array('atomic' => false,'validate'=>false));
		
		return $rs;
		
	}
	


	// データ2にデータをマージする。
	private function mergeData($data2,$data){
		
		foreach($data as $xid => $ent){
			$ent['xid'] = $xid;
			unset($ent['id']);
			$data[$xid] = $ent;
		}
		
		$data2=Hash::combine($data2, '{n}.xid','{n}');

		$data3=Hash::merge($data2,$data);
		$data3 = array_values($data3);// キーを振りなおす

		return $data3;
	}
	
	
	/**
	 * XIDリストに紐づくデータを取得する
	 * @param array $xids XIDリスト
	 * @return XIDリストに紐づくデータ
	 */
	public function getData($xids){

		
		//WHERE情報
		$conditions=array('xid'=>$xids);
		

		
		//オプション
		$option=array(
				'conditions'=>$conditions,
		);
		
		//DBから取得
		$data=$this->find('all',$option);
		
		//2次元配列に構造変換する。
		if(!empty($data)){
			$data=Hash::extract($data, '{n}.LearnCounterK');
		}
		
		
		
		return $data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}