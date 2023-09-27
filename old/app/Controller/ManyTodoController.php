<?php

App::uses('AppController', 'Controller');

/**
 * Many Todoのコントローラ
 * @date 2016-6-14 新規作成
 */
class ManyTodoController extends AppController {


	public $uses = array('ManyTodo','AnyJson');

	
	function main() {
	
		// 集計情報を取得する
		$agg = $this->ManyTodo->calcAgg();
		

		// ボーナスデータをDBから取得する
		$bonusData = $this->ManyTodo->getBonusData();

		
		$this->set(array(
				'title'=>'Many Todo',//所有金
				'agg'=>$agg,//しきい日付
				'bonusData'=>$bonusData,//レート金額
		));
	}
	
	

	
	
	/**
	 * AjaxによるDB登録
	 * @return string
	 */
	function reg_by_ajax(){
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
		
		// 認証されていないなら、エラーを返す。
		if(empty($this->Auth->user('id'))){
			return 'Not authenticated';
		}
	
		//AJAXのPOSTからJSON文字を取得およびパースを行い、データを取得する。
		$json_param=$_POST['key1'];
		$data=json_decode($json_param,true);
	

		
		// 日付入力チェック
		if($this->isDate($data['todo_date'],true) == false){
			return 'Not Todo Date';
		}
		
		// Todo日付と削除フラグを取得
		$todo_date = $data['todo_date'];
		$delete_flg = $data['delete_flg'];
		
		// データを加工する
		$data = $this->ManyTodo->prosData($data);

		
// 		// DBインジェクション対策：おかしなところに円マークがつく
// 		App::uses('Sanitize', 'Utility');
// 		$data=Sanitize::clean($data, array('encode' => false));

		
		$this->ManyTodo->begin();//トランザクション開始
		
		// 削除フラグがONなら、Todo日付に紐づくレコードをすべて削除する
		if($delete_flg){
			$this->ManyTodo->deleteAll(array('todo_date'=>$todo_date));
		}
		
		// DBへデータを登録
		if(!empty($data)){
			$rs=$this->ManyTodo->saveAll($data, array('atomic' => false,'validate'=>'true'));
		}
		
		
		$this->ManyTodo->commit();//コミット
		
		// 集計データを取得する
		$agg = $this->ManyTodo->calcAgg();
		$agg_json=json_encode($agg);
		
		
		return $agg_json;
	}
	
	
	
	//日付チェック
	private function isDate($strDateTime,$reqFlg=false){
	
		//空値且つ、必須入力がnullであれば、trueを返す。
		if(empty($strDateTime) && empty($reqFlg)){
			return true;
		}
	
		//空値且つ、必須入力がtrueであれば、falseを返す。
		if(empty($strDateTime) && !empty($reqFlg)){
			return false;
		}
	
	
		//日時を　年月日時分秒に分解する。
		$aryA =preg_split( '|[ /:_-]|', $strDateTime );
		if(count($aryA)!=3){
			return false;
		}
	
		foreach ($aryA as $key => $val){
	
			//▼正数以外が混じっているば、即座にfalseを返して処理終了
			if (!preg_match("/^[0-9]+$/", $val)) {
				return false;
			}
	
		}
	
		//▼グレゴリオ暦と整合正が取れてるかチェック。（閏年などはエラー） ※さくらサーバーではemptyでチェックするとバグになるので注意。×→if(empty(checkdate(12,11,2012))){・・・}
		if(checkdate($aryA[1],$aryA[2],$aryA[0])==false){
			return false;
		}
	

	
		return true;
	}
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * ボーナスデータをAnyJsonテーブルへ更新する。
	 * Ajax。
	 */
	public function bonus_reg(){
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
	
		$json_param=$_POST['key1'];
	
		// ボーナスデータを取得する
		$data=json_decode($json_param,true);
		
		
		// ボーナスデータをJSON化してDBに保存する
		$key_code = 'many_todo_bonus';
		$update_user = 'kani';
		$this->AnyJson->saveAnyData($key_code,$update_user,$data);


		$res = array('success'=>true);
		$json_data=json_encode($res);//JSONに変換
	
		return $json_data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
