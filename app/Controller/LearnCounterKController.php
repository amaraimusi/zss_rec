<?php

App::uses('AppController', 'Controller');

/**
 * 学習カウンター
 * @date 2016-9-25 新規作成
 */
class LearnCounterKController extends AppController {


	public $uses = array('LearnCounterK');
	
	
	
	
	/**
	 * AJAXによるデータ取得
	 */
	public function ajax_get_data(){
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
	
		$json=$_POST['key1'];
		$xids=json_decode($json,true);
		

		// xidリストからデータを取得する
		$data = $this->LearnCounterK->getData($xids);
	
		$json_data=json_encode($data);//JSONに変換
	
		return $json_data;
	}
	
	

	
	/**
	 * 全データのAJAX保存
	 */
	public function all_ajax_save(){
		
		$this->autoRender = false;//ビュー(ctp)を使わない。
		
		$json_param=$_POST['key1'];
		
		
		$data=json_decode($json_param,true);//JSON文字を配列に戻す
		

		// 更新ユーザーなど共通フィールドをデータにセットする。
		$data=$this->setCommonToData($data);
	
		// DBへ保存
		$this->LearnCounterK->saveData($data);
		
		
		return 'success';
	}

	
	
	
	
	
	
	
}
