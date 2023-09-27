<?php

App::uses('AppController', 'Controller');


class FileSerialRenameController extends AppController {

	///使用しているモデル
	public $uses = array ('FileSerialRename');
	
	

	/**
	 * 連番でファイル名変更
	 * File name change in the serial number
	 * 
	 * @date 2016-8-30
	 */
	public function file_serial_rename(){
		
		
		if($_SERVER['HTTP_HOST']!='localhost'){
			echo 'Local only';
			die;
		}

		// パラメータをセッションから取得
		$param = $this->Session->read('file_serial_rename_ses');
		
		
		$phase = 'init'; // 初期表示
		
		// ファイル一覧表示ボタン押下
		if(!empty($this->request->data['submit_file_list'])){
			$phase = 'file_list'; // ファイル一覧表示
		}
		
		// 名前変更ボタン押下
		if(!empty($this->request->data['submit_rename'])){
			$phase = 'rename'; // ファイル名変更
		}
		
		// セッションクリアボタン押下
		if(!empty($this->request->data['submit_session_clear'])){
			$this->Session->delete('file_serial_rename_ses');
			$this->Session->delete('file_serial_rename_data');
		}
		
		$fileData = [];// ファイルデータ
		$success = 0;
		switch ($phase) {
			case 'init':
				

				$param = $this->fsrInit($param);
				
				// $this->fsrInit();
				break;
				
			case 'file_list':
				
				// 連番でファイル名変更:ファイル一覧表示フェーズ
				$res = $this->fsrFileList($param);
				$param = $res['param'];
				$fileData = $res['fileData'];
				
				break;
				
			case 'rename':
				
				$success = $this->fsrRename($param);
				break;
			
			default:
				echo 'NO PAGE';
				die;
		}
		


		
		
		
		$this->set([
			'title'=>'連番でファイル名変更',
			'phase'=>$phase,
			'param'=>$param,
			'fileData'=>$fileData,
			'success'=>$success,
				
		]);	
	}
	
	/**
	 * 連番でファイル名変更:ファイル一覧表示フェーズ
	 * @param array $param パラメータ
	 */
	private function fsrInit($param){
		// パラメータが空ならデフォルト定義する。
		if(empty($param)){
			$param = [
					'fp'=>'C:\Users\k-uehara\Downloads\ebi',
					'hinagata'=>'sec1a%',
					'sort_field'=>'update_dt',
					'sort_asc'=>0,
			];
		}
		
		return $param;
	}
	
	
	/**
	 * 連番でファイル名変更:ファイル一覧表示フェーズ
	 * @param array $param パラメータ
	 */
	private function fsrFileList($param){
		
		// パラメータをセッションに保存
		$param = $this->request->data['FileSerialRename'];
		$this->Session->write('file_serial_rename_ses',$param);
		
		
		// 指定ディレクトリからファイルデータを取得する
		$fileData = $this->FileSerialRename->getFileData($param);
		$this->Session->write('file_serial_rename_data',$fileData);
		
		
		$res=[
				'param'=>$param,
				'fileData'=>$fileData,
			];
		return $res;
		
	}
	
	/**
	 * 連番でファイル名変更:ファイル名変更フェーズ
	 * @param array $param パラメータ
	 */
	private function fsrRename($param){
		
		
		$fileData = $this->Session->read('file_serial_rename_data');
		
		// コピーしながらファイル名の変更を行う
		$success = $this->FileSerialRename->rename($param,$fileData);
	
		$this->Session->delete('file_serial_rename_data');
		
		
		return $success;
		
		
	}
	
	
	
	
}
