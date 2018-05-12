<?php

App::uses('AppController', 'Controller');


class DeveloperController extends AppController {

	///使用しているモデル
	public $uses = array ('Devloper','User');
	
	function index() {
		$this->set(array(
				'title'=>'開発者メニュー'
		));
	}
	
	public function data_tool(){
		
		$data = array();
		$renameFlg = false;
		$img_path = '/photos/sample';
		$img_path = '/zss_rec/app/webroot/img/n2016/134';//■■■□□□■■■□□□■■■□□□
		
		// 画像リスト表示ボタンが押された場合
		if(!empty($this->data['imgListSubmit'])){
			
			// 画像パスを取得
			$img_path = $this->request->data['DataSet']['img_path'];
			
			// 画像リスト表示処理
			$res = $this->imgListSubaction($img_path);
			$data = $res['data'];
			//$renameFlg = $res['renameFlg'];
			$renameFlg = true;

			// 画像データをセッションに保存
			$this->Session->write('dev_img_data',$data);

		}
		
		
		// ファイル名変更ボタンが押された場合
		if(!empty($this->data['renameSubmit'])){
			// 画像パスを取得
			$img_path = $this->request->data['DataSet']['img_path'];
			
			// 画像データをセッションから取得する
			$data = $this->Session->read('dev_img_data');
			$this->renameSubaction($data,$img_path);
			
			// 画像リスト表示処理
			$res = $this->imgListSubaction($img_path);
			$data = $res['data'];
			$renameFlg = $res['renameFlg'];
			
			// 画像データをセッションに保存
			$this->Session->write('dev_img_data',$data);
		}
		
		$this->set(array(
				'data'=>$data,
				'renameFlg'=>$renameFlg,
				'img_path'=>$img_path,
		));
		
		
	}
	
	/**
	 * ファイル名変更サブアクション
	 * @param array 画像データ
	 * @param $img_path string 画像パス
	 */
	private function renameSubaction($data,$img_path){
		
		$img_path = $_SERVER['DOCUMENT_ROOT'].$img_path;
		
		foreach($data as $ent){
			if(!empty($ent['rename_flg'])){
				$oldname = $img_path.'/'.$ent['fn'];
				$newname = $img_path.'/'.$ent['fn_chg'];
				rename ( $oldname , $newname );
			}
			
		}
	}
	
	
	/**
	 *  画像リスト表示処理
	 *  @param string $img_path 画像パス
	 *  @return array レスポンス
	 *   - data 画像データ
	 *   - renameFlg ファイル名変更フラグ
	 */
	private function imgListSubaction($img_path){
	
	
		$img_path = $_SERVER['DOCUMENT_ROOT'].$img_path;
	
		// 画像パスが存在しない場合
		if($this->is_dir_ex($img_path)==false){
			$res['data'] = array();
			$res['renameFlg'] = false;
			return $res;
		}
	
		$renameFlg = false;
	
		// 画像パスからファイル名リストを取得する
		$fnList = $this->scandir2($img_path);
	
		$data = array();// 画像データ
	
		foreach($fnList as $fn){
	
				
			$ffn = $img_path . '/' . $fn;
			$exifData = exif_read_data($ffn);
			$date1 = $this->extrDateTimeFromExif($exifData,'Y-m-d_His');
			
			
			// ファイル名に日時が含まれている場合、日時部分を置換する
			$fn_chg=$fn;
			$re = '/([0-9]{4})(\/|-|年)([0-9]{1,2})(\/|-|月)([0-9]{1,2})_[0-9]{6}/';
			if(preg_match($re, $fn_chg)){
				$fn_chg = preg_replace($re, $date1, $fn_chg);
			}
			
			// ファイル名に日時が含まれてないなら、先頭に日時を追加する。
			else{
				$fn_chg = $date1.'_'.$fn;
			}
			

			
			//現ファイル名と変更ファイル名が同じならファイル変更フラグをFalse、異なるならTrueにする。
			$renameFlg = false;
			if($fn!=$fn_chg){
				$renameFlg = true;
			}
			
			// 画像エンティティへセット
			$ent = array();// 画像エンティティ
			$ent['fn'] = $fn;
			$ent['fn_chg'] = $fn_chg;
			$ent['rename_flg'] = $renameFlg;
			$data[] = $ent;
				
		}
	
		$res['data'] = $data;
		$res['renameFlg'] = $renameFlg;
	
		return $res;
	}
	
	
	// Exifデータから日付を抽出する。
	private function extrDateTimeFromExif($exifData,$format='Y-m-d H:i:s'){
		$date1 = null;
		$keys = array(
 				'DateTimeOriginal',
 				'DateTimeDigitized',
				'DateTime',
		);
	
		foreach($keys as $k){
			if(!empty($exifData[$k])){
				$date1 = $exifData[$k];
				break;
			}
		}
		if($date1 != null){
			$date1  = date($format, strtotime($date1));
		}
	
		return $date1;
	
	
	}

	
	/**
	 * 日時チェック 閏年対応
	 * @param  $strDate　日付文字列
	 * @return boolean　可否
	 */
	private function isDate($strDateTime){



		//日時を　年月日時分秒に分解する。
		$aryA =preg_split( '|[ /-]|', $strDateTime );

		//3つの部分に分かれていないならエラー。
		if(count($aryA)!=3){
			return false;
		}

		foreach ($aryA as $key => $val){

			//▼正数以外が混じっているば、即座にfalseを返して処理終了
			if (!preg_match("/^[0-9]+$/",$val)){
				return false;
			}
			$aryA[$key]=trim($val);
		}



		//▼グレゴリオ暦と整合正が取れてるかチェック。（閏年などはエラー）
		if(!checkdate($aryA[1],$aryA[2],$aryA[0])){
			return false;
		}



		return true;


	}


	/**
	 * scandir関数の拡張関数。「.」「..」となっているファイル名は除外する。
	 * @param  $dir_name	ディレクトリ名
	 * @return ファイル名の配列
	 */
	private function scandir2($dir_name){
		$files = scandir($dir_name);
		
		// 「.」,「..」名のファイルを除去、および日本語ファイルに対応。
		$files2 = array();
		foreach($files as $file){
			if($file=='.' || $file=='..'){
				continue;
			}
			$file = mb_convert_encoding($file, 'UTF-8', 'SJIS');
			$files2[] = $file;
		}

	
		return $files2;
	}
	
	
	/**
	 * 日本語ディレクトリの存在チェック
	 * @param  $dn	ディレクトリ名
	 * @return boolean	true:存在	false:未存在
	 */
	function is_dir_ex($dn){
		
		$dn=mb_convert_encoding($dn,'SJIS','UTF-8');
		if (is_dir($dn)){
			return true;
		}else{
			return false;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 *
	 * PHP環境情報表示アクション
	 *
	 * phpinfoによるPHP環境情報を表示します。
	 *
	 */
	function php_info() {
	
	
	}
	
	
	/**
	 * DB情報を表示する
	 */
	function db_info(){
	
		if(empty($this->DbInfo)){
			App::uses('DbInfo','Model');
			$this->DbInfo=new DbInfo();
		}
	
		//DB名
		$db_name="zss_rec";
		if($_SERVER['SERVER_NAME'] == 'amaraimusi.sakura.ne.jp'){
			$db_name="amaraimusi_zss_rec";
		}
	
		// テーブル一覧を取得
		$tblList=$this->DbInfo->getTblList($db_name);
	
		//フィールド情報の取得
		$fieldData2=array();//フィールド情報2
		foreach($tblList as $tbl){
				
			//テーブル名からフィールドデータを取得してフィールド情報2に追加する。
			$fieldData=$this->DbInfo->getFieldData($tbl);
			$fieldData2[]=$fieldData;
		}
	
	
	
	
		$this->set(array(
				'tblList'=>$tblList,
				'fieldData2'=>$fieldData2,
		));
	
	}
	
	
	
	
	
	/**
	 * Crud作成支援ツール
	 *
	 * CrudBase用にテーブルからコードを自動生成する。
	 */
	function crud_tool(){
	
		$tblName=null;
		$dbName=null;
		$data=array();
	
		if(!empty($this->request->data)){
			App::uses('CrudTool','Model');
			$crudTool = new CrudTool();
		
		
			$tblName = $this->request->data['CrudTool']['tbl_name'];
			$dbName = $this->request->data['CrudTool']['db_name'];

			$data = $crudTool->autoCreate($tblName,$dbName);
			
		}
	
			
		$this->set(array(
				'tblName'=>$tblName,
				'dbName'=>$dbName,
				'data'=>$data,
		));
	
	
	
	}
	


	
	
	
	
}
