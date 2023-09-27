<?php

App::uses('AppController', 'Controller');

/**
 * CRUD自動作成  | BakeCrudBase
 * 
 * @note
 * お手本ソースとDBテーブルからCRUDを自動作成する。
 * 作成CRUDは、CakePHP2とCrudBaseに依存している。
 * 
 * @date 2016-8-23
 * @author k-uehara
 *
 */
class BakeCrudBaseController extends AppController {

	///使用しているモデル
	public $uses = array ('BakeCrudBase','User');
	

	
	
	/**
	 * メインページ
	 *
	 * @note
	 * CRUD自動作成の根幹をなすページ
	 */
	function index(){
		
		
		$conf=[
				'tehon_proj_fp'=>'C:\xampp\htdocs\cake_demo',
				'tehon_code_c'=>'Neko',
				'tehon_code_s'=>'neko',
				'tehon_wamei'=>'ネコ',
				'out_proj_fp'=>'C:\xampp\htdocs\shop_box_a',
				'db_name'=>'shop_box',
				'tbl_name'=>'items',
				'out_wamei'=>'商品',
				];
	

		$data=array();
	
		if(!empty($this->request->data)){
			
			// リクエストデータから設定データへセットする
			$conf = $this->getConfFromPost($conf);
			
			// ★ CRUD自動生成処理
			$this->BakeCrudBase->autoCreate($conf);
			
			$this->Session->write('back_crud_base_conf',$conf);

		}else{
			if($this->Session->read('back_crud_base_conf')){
				$conf = $this->Session->read('back_crud_base_conf');
			}
		}
	
			
		$this->set(array(
				'title'=>'CRUD自動作成  | BakeCrudBase',
				'conf'=>$conf,
				'data'=>$data,
		));
	
	
	
	}
	
	
	/**
	 * リクエストデータから設定データへセットする
	 * @param array $conf 設定データ
	 * @return リクエストデータをセットした設定データ
	 */
	private function getConfFromPost($conf){

		// リクエストから設定データへセットする
		$req = $this->request->data['BakeCrudBase'];
			
		$conf['tehon_proj_fp'] = $req['tehon_proj_fp'];
		$conf['tehon_code_c'] = $req['tehon_code_c'];
		$conf['tehon_wamei'] = $req['tehon_wamei'];
		$conf['out_proj_fp'] = $req['out_proj_fp'];
		$conf['db_name'] = $req['db_name'];
		$conf['tbl_name'] = $req['tbl_name'];
		$conf['out_wamei'] = $req['out_wamei'];
			
		// お手本コード（スネーク記法）に変換およびセット
		$conf['tehon_code_s'] = $this->snakize($conf['tehon_code_c']);
		
		// テーブル名から2種の出力モデルコードを作成する。
		$tbl_name = $conf['tbl_name'];
		$out_code_s = mb_substr($tbl_name,0,mb_strlen($tbl_name)-1);//末尾の一文字「s」を削る
		$out_code_c = $this->camelize($out_code_s);//キャメルケースにスネークケースから変換する
		$conf['out_code_s'] = $out_code_s;
		$conf['out_code_c'] = $out_code_c;

		
		return $conf;
	}
	

	
	
	/**
	 * スネークケースにキャメルケースから変換
	 * @param string $str キャメルケース
	 * @return string スネークケース
	 */
	function snakize($str) {
		$str = preg_replace('/[A-Z]/', '_\0', $str);
		$str = strtolower($str);
		return ltrim($str, '_');
	}
	
	

	
	/**
	 * キャメルケースにスネークケースから変換する
	 *
	 * 先頭も大文字になる。
	 *
	 * @param string $str スネークケースの文字列
	 * @return キャメルケースの文字列
	 */
	private function camelize($str) {
		$str = strtr($str, '_', ' ');
		$str = ucwords($str);
		return str_replace(' ', '', $str);
	}
	
	
	
}
