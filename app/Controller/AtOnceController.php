<?php

App::uses('AppController', 'Controller');

/**
 * 一括処理のコントローラ
 * @date 2016-6-8 新規作成
 */
class AtOnceController extends AppController {


	public $uses = array('AtOnce');

	
	function index() {
	
	
	}
	
	/**
	 * 一括新規登録 | 一意フィールド指定型
	 * 
	 * new reg unique field
	 * 
	 */
	function new_reg_uf(){
		
		$req=$this->request->data;

		
		$msg='';//汎用メッセージ
		
		// デフォルトパラメータを取得する
		$param=$this->getParamForNrUf();
		
		$data = array();// recsのデータ
		
		
		// データ表示ボタンが押された場合の処理
		if(isset($req['show_data_btn'])){
			$param = $req['AtOnce'];
			$data = $this->nrUfShowData($param);
			$msg="データを表示します。";
		}
		
		// 一括登録ボタンが押された場合の処理
		if(isset($req['reg_at_once_btn'])){
			$data = $this->Session->Read('new_reg_uf_data');
			$res=$this->AtOnce->regUfAtOnce($data,'photo_fn');
			$regCnt = $res['regCnt'];
			
			$msg="recsテーブルに{$regCnt}件、一括登録しました。";
		}
		
		// 戻しボタンが押された場合の処理
		if(isset($req['restore_btn'])){
			$param = $req['AtOnce'];
			$this->AtOnce->restoreByNoA($param['no_a']);
			$msg="recsテーブルのデータを元に戻しました。";
		}
		
		// セッションクリアボタンが押された場合
		if(isset($req['ses_clear_btn'])){
			$this->Session->delete('new_reg_uf_data');
			$msg="現在未実装の機能です。";
		}
		
		
		$whiteList = array('rec_date','photo_fn','photo_dir','nendo','no_a','create_date');//ホワイトリスト
		
		$this->set(array(
				'msg'=>$msg,
				'param'=>$param,
				'data'=>$data,
				'whiteList'=>$whiteList,
		));
		
		
	}
	

	
	
	/**
	 * データ表示ボタンが押された場合の処理
	 * @param array $param パラメータ
	 */
	private function nrUfShowData($param){
		
		// 画像ファイルからrecsデータを取得する
		$data = $this->AtOnce->getDataFromImg($param);
		
		// 画像データをセッションに保存
		$this->Session->write('new_reg_uf_data',$data);
		
		return $data;
	}

	// デフォルトパラメータを取得する
	private function getParamForNrUf(){

		// recsテーブルから次の番号Aを取得する。
		$no_a = $this->AtOnce->getNextNoA();
			
		// デフォルトからパラメータを取得する。ついでにセッションへセット
		$param = array('no_a'=>$no_a,'nendo'=>date('Y'));

		return $param;

		
	}
	

	
	
	
	
	
	
	
}
