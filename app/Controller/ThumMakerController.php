<?php

App::uses('AppController', 'Controller');

/**
 * サムネイル作成のコントローラ
 * @version 1.0
 * @date 2017-4-12 新規作成
 */
class ThumMakerController extends AppController {


	public $uses = array('ThumMaker');

	/**
	 * 初期表示
	 */
	public function index() {
		if($_SERVER['HTTP_HOST']!='localhost'){
			echo 'Local only';
			die;
		}
		


		
		$this->set(array(
				'title'=>'サムネイル作成'
		));
	}
	
	
	/**
	 * 画像ファイル名一覧を表示する（AJAX）
	 */
	public function get_img_list(){
		App::uses('Sanitize', 'Utility');

		$this->autoRender = false;//ビュー(ctp)を使わない。
		
		$json_param=$_POST['key1'];
		
		$param=json_decode($json_param,true);//JSON文字を配列に戻す
		
		
		// 原寸画像ディレクトリパスとサムネイルディレクトリパスを取得する
		$orig_dp = $param['orig_dp'];
		$thum_dp = $param['thum_dp'];
		
		// 共通アクション。ファイル名データなどを取得する
		$res = $this->commonAction1($orig_dp,$thum_dp);
		$fnData = $res['fnData'];
		$errMsg = $res['errMsg'];
		

		//データ加工や取得
		$data=array('errMsg'=>$errMsg,'fnData'=>$fnData,);
		
		//サニタイズ（XSS対策）
		$data=Sanitize::clean($data, array('encode' => true));
		
		$json_data=json_encode($data);//JSONに変換
		
		return $json_data;
	}
	
	
	
	
	
	
	
	
	
	/**
	 * サムネイルを作成する（AJAX）
	 */
	public function make_thum(){
		App::uses('Sanitize', 'Utility');
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
	
		$json_param=$_POST['key1'];
	
		$param=json_decode($json_param,true);//JSON文字を配列に戻す
	
	
		// 原寸画像ディレクトリパスとサムネイルディレクトリパスを取得する
		$orig_dp = $param['orig_dp'];
		$thum_dp = $param['thum_dp'];
		$thum_width = $param['thum_width'];
		$thum_heith = $param['thum_heith'];
		
		// ファイル名データを取得する
		$fnData = $this->ThumMaker->getFnData($orig_dp,$thum_dp);
		
		// サムネイルを作成する
		$this->ThumMaker->makeThumByFnData($fnData,$orig_dp,$thum_dp,$thum_width,$thum_heith);

		
		// 共通アクション。ファイル名データなどを取得する
		$res = $this->commonAction1($orig_dp,$thum_dp);
		$fnData = $res['fnData'];
		$errMsg = $res['errMsg'];
	
	
		//データ加工や取得
		$data=array('errMsg'=>$errMsg,'fnData'=>$fnData,);
	
		//サニタイズ（XSS対策）
		$data=Sanitize::clean($data, array('encode' => true));
	
		$json_data=json_encode($data);//JSONに変換
	
		return $json_data;
	}
	

	
	/**
	 * 共通アクション。ファイル名データなどを取得する
	 * @param 原寸ディレクトリパス $orig_dp
	 * @param サムネイルディレクトリパス $thum_dp
	 */
	private function commonAction1($orig_dp,$thum_dp){
		// ファイル名データを取得する
		$fnData = array();
		$errMsg = null;
		if($this->ThumMaker->checkImgDirPath($orig_dp)){
			$fnData = $this->ThumMaker->getFnData($orig_dp,$thum_dp);
		}else{
			$errMsg = "オリジナル画像ディレクトリパスの指定先は実在しません";
		}
		
		return array(
				'fnData'=>$fnData,
				'errMsg'=>$errMsg
		);
	}
	
	
	
	
	
	
	
}
