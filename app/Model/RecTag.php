<?php
App::uses('Model', 'Model');


/**
 * 記録タグのモデルクラス
 *
 * 記録タグ画面用のDB関連メソッドを定義しています。
 * 記録タグテーブルと関連付けられています。
 *
 * @date 2015/09/16	新規作成
 * @author k-uehara
 *
 */
class RecTag extends Model {


	/// 記録タグテーブルを関連付け
	public $name='RecTag';


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	/**
	 * 記録タグエンティティを取得
	 *
	 * 記録タグテーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 記録タグID
	 * @return array 記録タグエンティティ
	 */
	public function findEntity($id){

		$conditions='id = '.$id;

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
						'conditions' => $conditions,
				)
		);

		$ent=array();
		if(!empty($data)){
			$ent=$data['RecTag'];
		}
		



		return $ent;
	}

	/**
	 * 記録タグ画面の一覧に表示するデータを、記録タグテーブルから取得します。
	 * 
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（記録タグテーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param $kjs 検索条件情報
	 * @param $page_no ページ番号
	 * @param $limit 表示件数
	 * @param $findOrder ソート情報
	 * @return 記録タグ画面一覧のデータ
	 */
	public function findData($kjs,$page_no,$limit,$findOrder){

		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='id';
		}
		
		$offset=null;
		if(!empty($limit)){
			$offset=$page_no * $limit;
		}
		
		$option=array(
					'conditions' => $conditions,
					'limit' =>$limit,
					'offset'=>$offset,
					'order' => $findOrder,
				);
		
		//$this->dumpSql($option);■■■□□□■■■□□□■■■□□□
		
		//DBからデータを取得
		$data = $this->find('all',$option);

		//データ構造を変換（2次元配列化）
		$data2=array();
		foreach($data as $i=>$tbl){
			foreach($tbl as $ent){
				foreach($ent as $key => $v){
					$data2[$i][$key]=$v;
				}
			}
		}
		
		return $data2;
	}
	
	/**
	 * SQLのダンプ
	 * @param  $option
	 */
	private function dumpSql($option){
		$dbo = $this->getDataSource();
		
		$option['table']=$dbo->fullTableName($this->RecTag);
		$option['alias']='RecTag';
		
		$query = $dbo->buildStatement($option,$this->RecTag);
		
		Debugger::dump($query);
	}



	/**
	 * 検索条件情報からWHERE情報を作成。
	 * @param  $kjs	検索条件情報
	 * @return WHERE情報
	 */
	private function createKjConditions($kjs){

		$cnds=null;
		
		// --- Start kjConditions	

		if(!empty($kjs['kj_id'])){
			$cnds[]="RecTag.id = '{$kjs['kj_id']}'";
		}
		if(!empty($kjs['kj_rec_id'])){
			$cnds[]="RecTag.rec_id = '{$kjs['kj_rec_id']}'";
		}
		if(!empty($kjs['kj_tag_id'])){
			$cnds[]="RecTag.tag_id = '{$kjs['kj_tag_id']}'";
		}
		if(!empty($kjs['kj_updated'])){
			$cnds[]="RecTag.updated = '{$kjs['kj_updated']}'";
		}
	
		// --- End kjConditions
		
		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}

	/**
	 * エンティティをDB保存
	 *
	 * 記録タグエンティティを記録タグテーブルに保存します。
	 *
	 * @param array $ent 記録タグエンティティ
	 * @return array 記録タグエンティティ（saveメソッドのレスポンス）
	 */
	public function saveEntity($ent){


		//DBに登録('atomic' => false　トランザクションなし）
		$ent = $this->save($ent, array('atomic' => false,'validate'=>'true'));

		//DBからエンティティを取得
		$ent = $this->find('first',
				array(
						'conditions' => "id={$ent['RecTag']['id']}"
				));
		
		$ent=$ent['RecTag'];
		
		return $ent;
	}





	/**
	 * 全データ件数を取得
	 *
	 * limitによる制限をとりはらった、検索条件に紐づく件数を取得します。
	 *  全データ件数はページネーション生成のために使われています。
	 *
	 * @param array $kjs 検索条件情報
	 * @return int 全データ件数
	 */
	public function findDataCnt($kjs){


		//DBから取得するフィールド
		$fields=array('COUNT(id) AS cnt');
		$conditions=$this->createKjConditions($kjs);

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
						'fields'=>$fields,
						'conditions' => $conditions,

				)
		);

		$cnt=$data[0]['cnt'];


		return $cnt;
		
		//■■■□□□findDataが複雑である場合は、こちらのコードで件数を取得。処理速度は上記と比べて、若干遅い。
		//$data=$this->findData($kjs,null,null,null);
		//$cnt=count($data);

		return $cnt;
	}
	
	
	
	
	
	
	
	
	/**
	 * 記録ＩＤからタグデータを取得する。
	 * @param $rec_id	記録ＩＤ
	 * @return タグデータ
	 */
	public function getTagData($rec_id){
	
	
	
		//SELECT情報
		$fields=array(
				'RecTag.id AS rec_tag_id',
				'RecTag.rec_id',
				'RecTag.tag_id',
				'Tag.name',
		);
	
		//WHERE情報
		$conditions=array(
				"RecTag.rec_id = '{$rec_id}'",
		);
	
		//ORDER情報
		$order=array('RecTag.id');
	
		//JOIN情報
		$joins = array(
				array(
						'type'       => 'left',
						'table'      => 'tags',
						'alias'      => 'Tag',
						'conditions' => array(
								'RecTag.tag_id = Tag.id',
						),
				),
	
		);
	
		//オプション
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
				'joins'=>$joins,
				'order'=>$order,
				'recursive' => -1,
		);
	
		//DBから取得
		$data=$this->find('all',$option);
	
		$data2=array();
		if(!empty($data)){
			foreach($data as $ent){
				$ent2=Hash::merge($ent['RecTag'],$ent['Tag']);
				$data2[]=$ent2;
			}
		}
	
		return $data2;
	
	}
	
	
	
	
	/**
	 * 記録タグテーブルから連結タグ名と連結タグＩＤを取得する。
	 * @param $rec_id 記録ＩＤ
	 * @return タグテキスト
	 */
	public function getTagJDate($rec_id){
	
	
		//SELECT情報
		$fields=array(
				'RecTag.tag_id',
				'Tag.name',
		);
	
		//WHERE情報
		$conditions=array(
				"RecTag.rec_id = '{$rec_id}'",
		);
	
	
	
		//JOIN情報
		$joins = array(
				array(
						'type'       => 'left',//innerも指定可能
						'table'      => 'tags',
						'alias'      => 'Tag',
						'conditions' => array(
								'RecTag.tag_id = Tag.id',
						),
				),
				//他に連結するテーブルがあれば上記のような配列を連結
	
		);
	
		//オプション
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
				'joins'=>$joins,
				'recursive' => -1,
		);
	
		//DBから取得
		$data=$this->find('all',$option);
	
		//連結タグＩＤと連結タグ名を作成する。
		$rs=array('j_tag_ids'=>null , 'j_tags' => null);
		if(!empty($data)){
			$tag_ids=Hash::extract($data, '{n}.RecTag.tag_id');
			$j_tag_ids =join(',',$tag_ids);
			$rs['j_tag_ids']=$j_tag_ids;
	
			$tags=Hash::extract($data, '{n}.Tag.name');
			$j_tags =join(',',$tags);
			$rs['j_tags']=$j_tags;
		}
	
		return $rs;
	
	}
	
	
	/**
	 * タグデータを保存
	 *
	 * @param $ent 記録エンティティ
	 * @return true:DB保存成功  false:DB保存失敗
	 */
	public function saveTags($ent){
	
		$rec_id=$ent['id'];//記録ＩＤ
		$j_tags=trim($ent['j_tags']);//入力タグテキスト
		$j_tag_ids=$ent['j_tag_ids'];//変換前タグＩＤリスト
	
		// 記録タグテーブルから,旧タグIDリストに紐づくレコードを削除。
		$ret=$this->deleteOldIds($rec_id,$j_tag_ids);
		if($ret==false){
			return $ret;
		}
	
		//入力タグが空なら処理抜け。
		if(empty($j_tags)){
			return true;
		}
	
		//タグテキストのスペース、全角スペース、「、」をコンマに置換する。
		$j_tags=str_replace(' ', ',', $j_tags);
		$j_tags=str_replace('　', ',', $j_tags);
		$j_tags=str_replace('、', ',', $j_tags);
	
		//タグテキストをコンマで分解してタグリストを取得する。
		$tags=explode(',', $j_tags);
	
		//タグリストから空白を削除
		$tags = array_filter($tags);
	
	
		// タグリストが空である場合、ここで処理終了。return true;
		if(empty($tags)){
			return true;
		}
	
		// タグリストに新規タグがあれば、タグテーブルにINSERTする。
		$ret=$this->insertNewTags($tags);
		if($ret==false){
			return $ret;
		}
	
		// タグテーブルからタグリストに紐づくタグIDリストを取得する。
		$tagData=$this->getTagsByTagNames($tags);
	
		$data=array();// 記録タグデータを初期化
	
		// タグデータの件数分、以下の処理を繰り返す。
		foreach($tagData as $i=>$tag_ent){
	
	
			// 記録タグエンティティに記録IDとタグIDをセット
			$ent=array();
			$ent['rec_id']=$rec_id;
			$ent['tag_id']=$tag_ent['Tag']['id'];
	
			$data[]=$ent;
		}
	
		// 記録タグデータをDB保存
		$ret =$this->saveAll($data);
	
		return $ret;
	
	
	}
	
	
	
	/**
	 *  タグリストに新規タグがあれば、タグテーブルにINSERTする。
	 * @param $tags タグリスト
	 * @return true:DB保存成功    false:DB保存失敗
	 */
	private function insertNewTags($tags){
	
		//タグ名リストからタグデータを取得する。
		$preTags=$this->getTagsByTagNames($tags);
	
		// 既存データを既存タグ名配列に変換
		$preTagNames=Hash::combine($preTags, '{n}.Tag.id','{n}.Tag.name');
	
	
		$data=array(); // タグデータを初期化
	
		// タグリストの件数分、以下の処理を行う
		foreach($tags as $tag_name){
			// タグ名が既存タグ名配列内に存在しない場合、タグデータに追加する。
			$r=array_search($tag_name, $preTagNames);
			if($r===false){
	
				$ent=array();
				$ent['name']=$tag_name;
				$data[]=$ent;
	
			}
		}
	
		//タグデータが空でないならDB保存
		$ret=true;
		if(!empty($data)){
			$ret=$this->Tag->saveAll($data);
		}
		return $ret;
	}
	
	
	/**
	 * タグ名リストからタグデータを取得する。
	 * @param $j_tags タグ名リスト
	 * @return $タグデータ
	 */
	private function getTagsByTagNames($tags){
		// タグ連結文字（カンマ区切り）に変換
		$j_tags="'".implode("','",$tags)."'";
	
	
		// タグテーブルから既存データを取得
		if(empty($this->Tag)){
			$this->Tag=ClassRegistry::init('Tag');
		}
	
		$option=array('conditions'=>array("name IN({$j_tags})"),'recursive' => -1,);
		$data=$this->Tag->find('all',$option);
	
		return $data;
	}
	
	
	/**
	 *  記録タグテーブルから,旧タグIDリストに紐づくレコードを削除。
	 * @param  記録ID 旧タグID連結
	 * @param  $j_tag_ids 旧タグID連結
	 * @return true:成功  false:失敗
	 */
	private function deleteOldIds($rec_id,$j_tag_ids){
		if(empty($j_tag_ids)){
			return true;
		}
	
		//削除タグIDリストから記録タグID連結を取得
		$option=array('conditions'=>array("rec_id={$rec_id}","tag_id IN({$j_tag_ids})"),'recursive' => -1,);
		$data=$this->find('all',$option);
		$ids=Hash::extract($data, '{n}.RecTag.id');
		$j_ids=join(",",$ids);
	
		//記録タグID連結に紐づくレコードを削除
		$ret=$this->deleteAll(array("id IN ({$j_ids})"), false);
	
		return $ret;
	}














}