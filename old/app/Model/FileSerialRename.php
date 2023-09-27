<?php
App::uses('Model', 'Model');

/**
 * 連番でファイル名変更
 * 
 * @date 2016-8-30
 * @author k-uehara
 *
 */
class FileSerialRename  extends Model{

	public $useTable = false;
	
	
	
	
	
	
	
	/**
	 * 指定ディレクトリからファイルデータを取得する
	 * @param array $param パラメータ
	 */
	public function getFileData($param){
		
		$fp = $param['fp'];
		$sort_field = $param['sort_field'];
		$sort_asc = $param['sort_asc'];
		
		// 指定ディレクトリ内のファイル詳細情報を取得する
		$data = $this->scandirEx($fp,'\\');
		
		// 特定のフィールドでデータを並べ替える
		$data = $this->sortData($data,$sort_field,$sort_asc);
		
		// ひな形から変更ファイル名を作成する
		$data = $this->makeRename($data,$param['hinagata']);
		
		
		return $data;
	}
	
	


	/**
	 * ひな形から変更ファイル名を作成する
	 * @param array $data ファイルデータ
	 * @param string $hinagata ひな形
	 * @return 変更ファイル名をセットしたファイルデータ
	 */
	private function makeRename($data,$hinagata){
		
		$c = 1;
		foreach($data as $i=>$ent){
			
			$rename = null;
			if(empty($ent['dir_flg'])){
				$rename = str_replace('%', $c, $hinagata);
				$rename .= '.'.$ent['ext'];
				$c ++;
			}
			
			$data[$i]['rename'] = $rename;

			
		}
		
		return $data;
		
	}
	
	
	/**
	 * 特定のフィールドでデータを並べ替える
	 * @param array $data データ（2次元配列）
	 * @param strong $sortField 並べ替えフィールド名
	 * @param int $orderFlg 0:昇順  , 1:降順
	 */
	private function sortData($data,$sortField,$orderFlg){
		$sfList=array();// ソートフィールドリスト
		foreach($data as $key=> $ent){
			$sfList[$key]=$ent[$sortField];
		}
	
		$sortFlg = SORT_ASC;
		if(!empty($orderFlg)){
			$sortFlg = SORT_DESC;
		}
	
		array_multisort($sfList,$sortFlg,$data);
	
		return $data;
	}
	
	
	
	/**
	 * 指定ディレクトリ内のファイル詳細情報を取得する
	 *
	 * @note
	 * PHP 5.6 に対応している。
	 * 日本語ディレクトリ、ファイル名に対応している。
	 * ファイル名、フルパス、拡張子、ファイル更新日時、ディレクトリ判定フラグ、画像判定フラグを取得する。
	 *
	 * @date 2016-8-31
	 * @version 1.0
	 *
	 * @param string $dir ディレクトリのフルパスまたはURL
	 * @param string $sep フルパスまたはURLの区分。スラッシュまたはバックスラッシュを指定する。
	 * @return ファイルデータ
	 */
	 private function scandirEx($dir,$sep='/'){
	
	
		// ディレクトリが存在するか判定する（日本語フォルダに対応）
		$dir2=mb_convert_encoding($dir,'SJIS','UTF-8');
		if (!is_dir($dir2)){
			return [];
		}
	
	
		// ディレクトリからファイル一覧を取得する
		$files = scandir($dir2);
	
		// 画像拡張子リスト
		$imgExts = array('png','gif','jpg','jpeg','bpg','tiff','bmp','svg');
	
		// ファイル一覧をループする
		$data = [];
		foreach($files as $proto_fn){
				
			// 「.」,「..」は取得対象外なので無視する。
			if($proto_fn=='.' || $proto_fn=='..'){
				continue;
			}
	
			// 日本語ファイルに対応する
			$fn = mb_convert_encoding($proto_fn, 'UTF-8', 'SJIS');
				
			// ファイル名
			$ent['file_name'] = $fn;
	
			// フルパス
			$fp = $dir.$sep.$fn;
			$ent['fp'] = $fp;
	
			// 拡張子
			$path_param = pathinfo($fn);
			$ext = null;
			if(!empty($path_param['extension'])){
				$ext = $path_param['extension'];
			}
			$ent['ext'] = $ext;
	
			// ディレクトリフラグ   false:ファイル  ,  true:ディレクトリ
			$fp2 = mb_convert_encoding($fp, 'SJIS', 'UTF-8');
			$ent['dir_flg'] = is_dir($fp2);
	
			// 更新日時
			$ent['update_dt'] = date("Y-m-d H:i:s", filemtime($dir2.$sep.$proto_fn));
				
			// 画像フラグ
			$img_flg = false;
			if(!empty($ext)){
				$ext2 = mb_strtolower($ext);
				if (in_array($ext2,$imgExts)){
					$img_flg = true;
				}
			}
			$ent['img_flg'] = $img_flg;
				
				
	
	
			$data[] = $ent;
		}
	
	
	
		return $data;
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * コピーしながらファイル名の変更を行う
	 * @param array $param パラメータ
	 * @param array $fileData ファイルデータ
	 * @return 成功フラグ
	 */
	public function rename($param,$fileData){
		
		// renameフォルダを作成する
		$renameDir = $param['fp'].DS.'rename';
		$renameDir = mb_convert_encoding($renameDir, 'SJIS', 'UTF-8');
		if (!is_dir($renameDir)){
			mkdir($renameDir);
		}
		
		
		// renameフォルダにファイル名変更コピーをする。
		foreach($fileData as $ent){
			
			if(!empty($ent['dir_flg'])){
				continue;
			}
			
			$sourceFn = $ent['fp'];
			$copyFn = $renameDir.DS.$ent['rename'];
			$sourceFn=mb_convert_encoding($sourceFn,'SJIS','UTF-8');
			copy($sourceFn,$copyFn);//ファイルをコピーする。
			
		}
		
		return 1;
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
};