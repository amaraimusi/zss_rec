<?php
App::uses('Model', 'Model');

/**
 * テーブル作成処理のモデル
 */
class ThumMaker extends Model {

	var $name='ThumMaker';

	public $useTable = false; // DBテーブルと関連付けない

	//検索入力のバリデーション
	public $validate = null;

	
	
	/**
	 * ディレクトリパスの存在チェック
	 * @param string $dp ディレクトリパス
	 * @return true:ディレクトリは存在する  false:存在しない
	 */
	public function checkImgDirPath($dp){
		$dp = trim($dp);
		
		if(empty($dp)){
			return false;
		}
		
		return $this->is_dir_ex($dp);
		
	}
	
	
	
	

	/**
	 * ファイル名データを取得する
	 * @param string $orig_dp オリジナル画像ディレクトリパス
	 * @param string $thum_dp サムネイルディレクトリパス
	 * @return 画像ファイルリスト
	 */
	public function getFnData($orig_dp,$thum_dp){
		$fnList = $this->scandir2($orig_dp);
		
		$data = array();
		foreach($fnList as $fn){
			$ent = array();
			$ent['orig_fn'] = $fn;
			$thumFp = $thum_dp."\\".$fn;
			if(is_file($thumFp)){
				$ent['thum_fn'] = $thumFp;
			}else{
				$ent['thum_fn'] = '';
			}
			$data[] = $ent;
		}
		
		return $data;
	}
	
	
	/**
	 * scandir関数の拡張関数。
	 *
	 * @note
	 * 「.」「..」となっているファイル名は除外する。
	 * 日本語ファイル名に対応するためUTF-8に変換している。
	 * そのため、当関数で取得したファイル名でWindows上のファイルを扱う場合、Shift-JISに戻す必要がある。
	 * WindowsのファイルはShift-JISで扱わねばならないためである。
	 *
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
	private function is_dir_ex($dn){
		$dn=mb_convert_encoding($dn,'SJIS','UTF-8');
		if (is_dir($dn)){
			return true;
		}else{
			return false;
		}
	}
	
	
	
	/**
	 * サムネイルを作成する
	 * @param array $fnData ファイル名データ
	 * @param string $orig_dp 原寸画像ディレクトリパス
	 * @param string $thum_dp サムネイルディレクトリパス
	 * 
	 */
	public function makeThumByFnData($fnData,$orig_dp,$thum_dp,$thum_width,$thum_heith){
		
		
		App::uses('ThumbnailEx','Vendor/Wacg');
		$thumbnailEx = new ThumbnailEx();
		
		// サムネイルディレクトリを作成する
		$thumbnailEx->makeDirEx($thum_dp);
		
		foreach($fnData as $ent){
			$fn = $ent['orig_fn'];
			$orig_fp = $orig_dp ."\\". $fn;
			$thum_fp = $thum_dp ."\\". $fn;
			
			// サムネイルを作成する
			$thumbnailEx->createThumbnail($orig_fp, $thum_fp,$thum_width,$thum_heith);
			
		}
	}
	
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}