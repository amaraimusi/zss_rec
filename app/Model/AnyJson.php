<?php

class AnyJson extends AppModel{
	
	/**
	 * DBからJSON文字列を取得する
	 * @param string $key_code キーコード
	 * @param string $update_user 更新ユーザー
	 * @return JSON文字列
	 */
	public function findJson($key_code,$update_user){
		//SELECT情報
		$fields=array('json_str');
		
		//WHERE情報
		$conditions=array(
				"key_code = '{$key_code}'",
				"update_user = '{$update_user}'",
				"delete_flg = 0",
		);
		
		//オプション
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
		);
		
		//DBからJSON文字列を取得する
		$data=$this->find('first',$option);
		$json_str = "";
		if(!empty($data)){
			$json_str=$data['AnyJson']['json_str'];
		}
		
		return $json_str;
	}
	
	
	
	
	/**
	 * キーコードと更新ユーザーからエンティティを取得する
	 * @param string $key_code キーコード
	 * @param string $update_user 更新ユーザー
	 * @return エンティティ
	 */
	public function findEntity($key_code,$update_user){

	
		//WHERE情報
		$conditions=array(
				"key_code = '{$key_code}'",
				"update_user = '{$update_user}'",
				"delete_flg = 0",
		);
	
		//オプション
		$option=array(
				'conditions'=>$conditions,
		);
	
		//DBからJSON文字列を取得する
		$ent=$this->find('first',$option);
		if(!empty($ent)){
			$ent=$ent['AnyJson'];
		}
	
		return $ent;
	}
	
	
	
	/**
	 * データをJSON化してDBに保存する
	 * @param string $key_code キーコード
	 * @param string $update_user 更新ユーザー
	 * @param array $data ここで指定したデータをJSON化する。
	 * 
	 */
	public function saveAnyData($key_code,$update_user,$data){
		
		// データをJSON化する
		$json_str = json_encode($data,true);
		
		//キーコードと更新ユーザーを指定してエンティティを取得する
		$ent = $this->findEntity($key_code, $update_user);
	
		//エンティティにキーコード、更新ユーザー、JSON文字列をセットする
		$ent['key_code'] = $key_code;
		$ent['update_user'] = $update_user;
		$ent['json_str'] = $json_str;
		
		//エンティティに共通パラメータをセットする
		$this->setCommonToEntity($ent,$update_user);
		
		//DB保存
		$this->save($ent);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
}