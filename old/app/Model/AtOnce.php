<?php
App::uses('Model', 'Model');


class AtOnce extends Model {

	var $name='AtOnce';

	var $useTable='recs';

	//検索入力のバリデーション
	public $validate = null;


	/**
	 * 一括新規登録 | 一意フィールド指定型
	 * 
	 * 複数行からなるデータを一括登録する。
	 * ただし、指定したフィールドの値で重複があれば、その行の登録はしない。
	 * 重複とは、登録するフィールド値がDB側のフィールドに既に存在する状態のこと。
	 * 
	 * @param array $data recs用のデータ
	 * @param string $unique_field 一意フィールド
	 * @return レスポンス 
	 *  - dbRes saveAllのレスポンス
	 *  - regCnt 登録件数 ： 実際に登録したデータの行数
	 */
	public function regUfAtOnce($data,$unique_field){
		// データから一意フィールドの列を、一意リストとして抜き出す。
		$ul=Hash::extract($data, '{n}.'.$unique_field);
		

		// 一意リストから重複調査SQL文を作成および実行し、重複リストを取得する。
		$fields=array($unique_field);
		$conditions=array(
				array($unique_field => $ul),
		);
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
		);
		$overList=$this->find('list',$option);


		// データから重複リストに該当する行を除去する。（重複分の除去）
		$data2 = array();
		$regCnt=0;//登録行数
		if(empty($overList)){
			$data2 = $data;
			$regCnt=count($data);
		}else{
			foreach($data as $ent){
				$uv = $ent[$unique_field];
				$flg=in_array($uv,$overList);
				if($flg==false){
					$data2[] = $ent;
					$regCnt++;
				}
			}
		}
		
		// DBへ保存する
		$dbRes=null;
		if(!empty($data2)){
			$this->begin();//トランザクション開始
			$dbRes = $this->saveAll($data2);//複数行をまとめて削除
			$this->commit();
		}
		
		$res=array(
				'dbRes'=>$dbRes,
				'regCnt'=>$regCnt,
		);
		
		return $res;
	}
	

	
	
	
	/**
	 * 次の番号Aを取得する。
	 * 
	 * 次の番号A＝最大の番号A＋1
	 */
	public function getNextNoA(){

		
		//SELECT情報
		$fields=array(
				'MAX(no_a) as no_a',
		);
		
		//オプション
		$option=array(
				'fields'=>$fields,
		);
		
		//DBから取得
		$res=$this->find('first',$option);
		
		$no_a=1;
		if(isset($res)){
			$no_a = $res[0]['no_a'];
			$no_a ++;
		}

		return $no_a;
	}
	
	
	
	/**
	 * 画像ファイルからrecsデータを取得する
	 * @param array $param パラメータ
	 * @return recsのデータ
	 */
	public function getDataFromImg($param){
		

		// 画像パスを取得
		$nendo = $param['nendo'];
		$no_a = $param['no_a'];
		//$img_path = 'img/n'.$nendo.'/'.$no_a;
		$img_path = '../../../photos/halther/n'.$nendo.'/'.$no_a;
		
		// 画像データを取得する
		$imgRes = $this->imgListSubaction($img_path);
		$imgData = $imgRes['data'];
		
		// 画像データからrecsデータを作成する
		$data = $this->createDataFromImg($imgData,$img_path,$param);
		
		return $data;
	}
	
	
	// 画像データからrecsデータを作成する
	private function createDataFromImg($imgData,$img_path,$param){
		$data = array();
		
		foreach($imgData as $imgEnt){
			
			// 写真ファイル名を取得
			$photo_fn = $imgEnt['fn'];
			
			
			// 写真ファイル名から日付を抽出。（ファイル名に日付が含まれていなければファイルの更新日をセット）
			$rec_date = $this->extrDateFromPhotoFn($photo_fn,$img_path);
			
			// デフォルトエンティティを取得する
			$ent = $this->getDefEnt();
			
			$photo_dir = '/n'.$param['nendo'].'/'.$param['no_a'].'/';
			
				
			
			// 各種フィールドにセット
			$ent['rec_date'] = $rec_date;// 記録日付
			$ent['photo_fn'] = $photo_fn;// 写真ファイル名
			$ent['photo_dir'] = $photo_dir;// 写真ディレクトリ
			$ent['nendo'] = $param['nendo'];// 年度
			$ent['no_a'] = $param['no_a'];// 番号A
			
			$data[] = $ent;
			
		}
		
		
		return $data;
	}
	
	// 写真ファイル名から日時を抽出。（ファイル名に日付が含まれていなければファイルの更新日をセット）
	private function extrDateFromPhotoFn($photo_fn,$img_path){
		
		// ファイル名から日付を抽出
		$re = '/([1-9][0-9]{3})(\/|-|年)(0[1-9]{1}|1[0-2]{1}|[1-9]{1})(\/|-|月)(3[0-1]{1}|[1-2]{1}[0-9]{1}|0[1-9]{1}|[1-9]{1})_([0-9]{6})/';
		preg_match($re, $photo_fn,$match);
		
		$dtm = null;
		
		// 日付の抽出に成功した場合、一致文字から日時を組み立てる。
		if(!empty($match)){
			$str = $match[0];
			$ary=explode('_',$str);
			$times=str_split($ary[1],2);
			$time_str = join(':',$times);
			$dtm = $ary[0].' '.$time_str;
			
		}
		
		// 日付の抽出に失敗した場合、ファイル更新日を日時として取得する
		else{
			// ファイルの更新日を取得する
			$ffn = $img_path.'/'.$photo_fn;
			$dtm = filemtime ( $ffn );
			$dtm = date('Y-m-d H:i:s',$dtm);// 日付フォーマットを変換
			
		}

		return $dtm;
		
	}
	
	
	// デフォルトエンティティを取得する
	private function getDefEnt(){
		$tm = 	date('Y-m-d H:i:s');
		$ent = array(
			'rec_title'=>null,
			'rec_date'=>$tm,
			'note'=>null,
			'category_id2'=>0,
			'category_id1'=>1,
			'tags'=>null,
			'photo_fn'=>null,
			'photo_dir'=>null,
			'ref_url'=>null,
			'nendo'=>date('Y'),
			'sort_no'=>0,
			'no_a'=>0,
			'no_b'=>0,
			'parent_id'=>0,
			'publish'=>1,
			'create_date'=>$tm,
			'update_date'=>$tm,
		);
		
		return $ent;
	}
	
	
	
	
	
	/**
	 *  画像リスト表示処理
	 *  @param string $img_path 画像パス
	 *  @return array レスポンス
	 *   - data 画像データ
	 *   - renameFlg ファイル名変更フラグ
	 */
	public function imgListSubaction($img_path){

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
	
			$ent = array();// 画像エンティティ
				
			$ent['fn'] = $fn;
				
			// ファイル名から日付部分を抽出
			$ary=explode("_",$fn);
			$d =$ary[0];
				
			// 日付型であるなら、ファイル名変更フラグプロパティにtrueをセット、そうでないならfalseをセット。
			$rename_flg = true;
			if($this->isDate($d)==true){
				$rename_flg = false;
			}
				
			$ent['rename_flg'] = $rename_flg;
				
	
	
				
			$fn_chg = "";//変更ファイル名
				
			// ファイル変更する必要がある場合
			if($rename_flg == true){
	
				// ファイル更新日から変更ファイル名を取得する
				$ffn = $img_path . '/' . $fn;
				$ut = filemtime($ffn);
				$dtm=date('Y-m-d_His',$ut);
				$fn_chg = $dtm . '_' . $fn;
	
				$renameFlg = true;
			}
				
				
			$ent['fn_chg'] = $fn_chg;
	
			$data[] = $ent;
				
		}
	
		$res['data'] = $data;
		$res['renameFlg'] = $renameFlg;
	
		return $res;
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
	 * 戻しボタンが押された場合の処理
	 * @param int $no_a 番号A
	 * 
	 */
	public function restoreByNoA($no_a){
		$this->begin();//トランザクション開始
		$this->deleteAll(array('no_a'=>$no_a));//複数行をまとめて削除
		$this->commit();
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}