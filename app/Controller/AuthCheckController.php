<?php

App::uses('AppController', 'Controller');

/**
 * Ajax向けの認証チェックコントローラ
 * 
 * 
 * @date 2016-7-20 新規作成
 */
class AuthCheckController extends AppController {


	public $name = 'AuthCheck';
	
	public function beforeFilter() {
		$this->Auth->allow(); // 認証と未認証の両方に対応したページする。
		parent::beforeFilter();//基本クラスのメソッドを呼び出し。
	}
	
	public function ajax_auth_check() {
		$this->autoRender = false;
		
		if(!empty($this->Auth->user('id'))){
			
			$data=[
				'username'=>$this->Auth->user('username'),
				'role'=>$this->Auth->user('role'),
				];
			$json_str=json_encode($data);
			echo $json_str;
		}else{
			echo 'none';
		}

	}
	
	
}
