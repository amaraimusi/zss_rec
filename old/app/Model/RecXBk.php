<?php
App::uses('Model', 'Model');

/**
 * 記録Xのモデル
 * ★概要
 *
 * ★履歴
 * 2014/8/25	新規作成
 * @author k-uehara
 *
 */
class RecX extends Model {

	var $name='RecX';

	var $useTable='recs';

	//検索入力のバリデーション
	public $validate = null;


	//独自バリデーションルール・新規モードの場合だけ、日にちの存在チェックをする。
	function checkOnly(){
		//チェックしたいルールを書く


		$mode=$_POST['data']['RecX']['mode'];
		if($mode=='edit'){
			return true;
		}else if($mode=='new'){
			if (!empty($this->data['RecX']['sale_date'])){

				$sale_date=$this->data['RecX']['sale_date'];

				//存在するか確認
				$count = $this->find('count',
						array('conditions' => array('sale_date' => $sale_date)
						));

				//1件でもあればfalseを返す
				return $count == 0;
			}else{
				return false;
			}
		}else{
			return true;
		}


	}


	function findEntity($id){


		//DBから取得するフィールド
		$fields=array(
			'id',
			'rec_title',
			'rec_date',
			'note',
			'category_id2',
			'category_id1',
			'tags',
			'photo_fn',
			'photo_dir',
			'ref_url',
			'nendo',
			'sort_no',
			'no_a',
			'no_b',
			'parent_id',
			'publish',

		);


		$conditions='id = '.$id;

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
					'fields'=>$fields,
					'conditions' => $conditions,
				)
		);


		$ent=$data['RecX'];



		return $ent;
	}


	function findData($kjs,$page_no,$limit,$findOrder){
		
		//SELECT情報
		$fields=array(
				'RecX.*',
				'Probe.probe_name',
				'Probe.probe_note',
		);


		//条件を作成
		$conditions=$this->createKjConditions($kjs);

		
		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='RecX.rec_date';
		}
		
		
		//JOIN情報
		$joins = array(
				array(
						'type'       => 'left',
						'table'      => 'probes',
						'alias'      => 'Probe',
						'conditions' => array(
								'RecX.probe_id = Probe.id',
						),
				),
	
		);
		
		
		//DBからデータを取得
		$data = $this->find(
				'all',
				Array(
					'fields' => $fields,
					'conditions' => $conditions,
					'joins'=>$joins,
					'limit' =>$limit,
					'offset'=>$page_no*$limit,
					'order' => $findOrder,
				)
		);

	
		//2次元配列に構造変換する。
		$data2=array();
		foreach($data as $i=>$tbl){
			foreach($tbl as $ent){
				foreach($ent as $key => $v){
					$data2[$i][$key]=$v;
				}
			}
		}
		
		// ■■■□□□■■■□□□■■■□□□
// 		if(!empty($data)){
// 			$data=Hash::extract($data, '{n}.RecX');
// 		}


		return $data2;
	}



	/**
	 * 検索条件情報からWHERE情報を作成。
	 * @param  $kjs	検索条件情報
	 * @return WHERE情報
	 */
	private function createKjConditions($kjs){

		$cnds=null;


		if(!empty($kjs['kj_rec_date1'])){
			$cnds[]="RecX.rec_date >= '{$kjs['kj_rec_date1']}'";
		}

		if(!empty($kjs['kj_rec_date2'])){
			$cnds[]="RecX.rec_date <= '{$kjs['kj_rec_date2']}'";
		}

		if(!empty($kjs['kj_category_id1'])){
			$cnds[]="RecX.category_id1 >= '{$kjs['kj_category_id1']}'";
		}

		if(!empty($kjs['kj_category_id2'])){
			$cnds[]="RecX.category_id2 >= '{$kjs['kj_category_id2']}'";
		}

		if(!empty($kjs['kj_rec_title'])){
			$cnds[]="RecX.rec_title LIKE '%{$kjs['kj_rec_title']}%'";
		}

		if(!empty($kjs['kj_note'])){
			$cnds[]="RecX.note LIKE '%{$kjs['kj_note']}%'";
		}

		if(!empty($kjs['kj_tags'])){
			$cnds[]="RecX.tags LIKE '%{$kjs['kj_tags']}%'";
		}

		if(!empty($kjs['kj_tag_id'])){
			$str_rec_ids=$this->getIdsByTagId($kjs['kj_tag_id']);
			if(!empty($str_rec_ids)){
				$cnds[]="RecX.id IN ({$str_rec_ids})";
			}
		}

		if(!empty($kjs['kj_no_a'])){
			$cnds[]="RecX.no_a = '{$kjs['kj_no_a']}'";
		}

		if(!empty($kjs['kj_no_b'])){
			$cnds[]="RecX.no_b = '{$kjs['kj_no_b']}'";
		}

		if(!empty($kjs['kj_probe_id'])){
			$cnds[]="RecX.probe_id = '{$kjs['kj_probe_id']}'";
		}
		


		$cnds[]="RecX.publish = 1";

		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}

	/**
	 * タグＩＤから記録ＩＤ連結を取得
	 * 
	 * @param $tag_id　タグＩＤ
	 * @return 記録ＩＤ連結
	 */
	private function getIdsByTagId($tag_id){
		if(empty($this->RecTag)){
			App::uses('RecTag','Model');
			$this->RecTag=new RecTag();
		}

		//SELECT情報
		$fields=array(
			'rec_id',

		);

		//WHERE情報
		$conditions=array(
			"tag_id = '{$tag_id}'",
		);


		//オプション
		$option=array(
			'fields'=>$fields,
			'conditions'=>$conditions,
			'recursive' => -1,
		);

		//DBから取得
		$data=$this->RecTag->find('list',$option);

		$str=null;
		
		if(!empty($data)){

			$str = join(',',$data);
		}

		return $str;
	}
	


	/**
	 * エンティティをDBに登録する。
	 * @param  $ent
	 */
	public function saveEntity($ent){
		//SQLインジェクションのサニタイズ
		App::uses('Sanitize', 'Utility');
		$ent = Sanitize::clean($ent, array('encode' => false));

		//DBに登録
		$rets = $this->saveAll($ent, array('atomic' => false,'validate'=>'true'));


		return $rets;
	}



	//新IDを取得
	function findNewId(){


		//DBから取得するフィールド
		$fields=array('MAX(id) AS new_id');


		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
					'fields'=>$fields,

				)
		);

		$newId=$data[0]['new_id'];
		$newId++;

		return $newId;
	}



	//全データ数を取得
	function findDataCnt($kjs){


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

		$cnt=0;
		if(!empty($data)){
			$cnt=$data[0]['cnt'];
		}


		return $cnt;
	}

	//IDに紐づくレコードを削除する。
	public function del($id){
		if(!empty($id)){
			$this->delete($id,array('atomic' => true));
		}
	}



	/**
	 * 最終行の日付と、その20件前行の日付を取得
	 * @param $kjs	検索条件情報
	 * @return
	 */
	public function getFirstDates($kjs){

				//SELECT情報
				$fields=array(
					'id',
					'rec_date',

				);

		//条件を作成
		$limit=30;
		$conditions=$this->createKjConditions($kjs,$limit);

		//ORDERのデフォルトをセット
		if(empty($findOrder)){
			$findOrder='RecX.rec_date DESC';
		}

		//DBからデータを取得
		$data = $this->find(
				'all',
				Array(
						//'fields' => $fields,
						'conditions' => $conditions,
						'limit' =>$limit,
						'order' => $findOrder,
				)
		);

		$d1=null;
		$d2=null;
		$lastIndex=count($data)-1;
		if($lastIndex >= 1){
			$d2=$data[0]['RecX']['rec_date'];
			$d1=$data[$lastIndex]['RecX']['rec_date'];
		}


		$ret=array('d1'=>$d1,'d2'=>$d2);


		return $ret;
	}



	
	
	/**
	 * 季節ボタンデータを取得する
	 * @param string/date $first_date 最初回日付
	 * @return 季節ボタンデータ
	 */
	public function getSeasonBtnData($first_date){
	
		$end_date = date('Y-m-d');
		$month_range=3;
		$format='Y-m-d H:i:s';
		$dates = $this->splitByMonthRange($first_date,$end_date,$month_range,$format);

		$data = array();
		foreach($dates as $d){
			$u=strtotime($d);
			
			$m = intval(date('m', $u));
			$seasonName = $this->getSeasonNameByMont($m);
			$y = date('Y', $u);
			$label_name=$y.$seasonName;
			$name = 'season'.date('Y-m-d',$u);
			$ent = array(
					'label_name' => $label_name,
					'f_date'=> $d,
					'name' => $name,
			);
			$data[] = $ent;
			
		}
		
		return $data;
		
	}
	
	/**
	 * 月から季節を取得する
	 */
	private function getSeasonNameByMont($m){
		if($m>=1 && $m<=3){
			return '冬';
		}
		if($m>=4 && $m<=6){
			return '春';
		}
		if($m>=7 && $m<=9){
			return '夏';
		}
		if($m>=10 && $m<=12){
			return '秋';
		}
		return 'none';
	}
	
	
	
	/**
	 * 期間を指定月間で分割
	 *
	 * @param string/date $first_date 期間の開始日(月の第一日）
	 * @param string/date  $end_date 期間の終了日
	 * @param int  $month_range 指定月間
	 * @param string $format 返りデータの日付フォーマット（省略可、秒単位まで指定可）
	 * @return array 分割日付リスト
	 */
	function splitByMonthRange($first_date,$end_date,$month_range,$format='Y-m-d'){
		$start = new DateTime($first_date);
		$end = new DateTime($end_date);
		$interval = DateInterval::createFromDateString($month_range.' month');
		$period = new DatePeriod($start,$interval,$end);
		$dates = array();
		foreach($period as $d){
			$dates[] = $d->format($format);
		}
		return $dates;
	}
	
	
	
}