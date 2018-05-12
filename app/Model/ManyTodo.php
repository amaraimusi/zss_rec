<?php
App::uses('Model', 'Model');


class ManyTodo extends Model {

	var $name='ManyTodo';

	var $useTable='many_todos';

	//検索入力のバリデーション
	public $validate = null;


	
	// データを加工する
	public function prosData($data){
		
		// Todoリスト文字列とTodo日付を取得する
		$todo_date = $data['todo_date'];
		$todos_text=$data['todos_text'];
		

		// Todoリスト文字列を改行コードで分割し、Todoリストを取得する。
		$list =preg_split( "/\n/", $todos_text );
		
	
		// 空白行を除去する
		$list = array_filter($list, "strlen");
		$list = array_values($list);
		
		
		
		//Todoリストをループしてデータを組み立てる
		$data2=array();
		foreach($list as $todo_text){
			$ent=array(
					'todo_date'=>$todo_date,
					'todo_text'=>$todo_text,
			);
			$data2[] = $ent;
		}
		

		
		
		return $data2;
		
		
	}
	
	
	/**
	 * 集計情報を取得する
	 */
	public function calcAgg(){
		
		// 全合計数を取得する
		$res = $this->query('SELECT count(id) as all_total FROM many_todos');
		$agg['all_total'] = $res[0][0]['all_total'];
		
		// 本日・Todo数を取得
		$today = date('Y-m-d');// 本日を取得する
		$agg['today_cnt'] = $this->getCntByDate($today);
		
		// 先日・Todo数を取得
		$yesterday = $this->calcYesterday($today);//明日を取得する
		$agg['yesterday_cnt'] = $this->getCntByDate($yesterday);
		
		// 二日前Todo数を取得
		$day2 = $this->calcAnyday($today,-2);//明日を取得する
		$agg['day2_cnt'] = $this->getCntByDate($day2);
		
		
		// 日Todo差を算出
		$agg['t_y_diff'] = $agg['today_cnt'] - $agg['yesterday_cnt'];
		
		
		// 今週平均を取得する
		$agg['t_week_cnt'] = $this->aggWeek($today,array('monday_flg'=>1));
		
		
		// 先週平均を取得する
		$agg['l_week_cnt'] = $this->aggLastWeekCnt($today);
		
		// 週平均差を算出
		$agg['week_diff'] = $agg['t_week_cnt'] - $agg['l_week_cnt'];

		// 今月平均を集計する
		$agg['t_month_avg'] = $this->aggMonthAvg($today);
		
		// 先月平均を集計する
		$agg['l_month_avg'] = $this->aggLastMonthAvg($today);
		
		$agg['month_diff'] = $agg['t_month_avg'] - $agg['l_month_avg'];
		
		// 最終入力日を取得する
		$agg['last_date'] = $this->getLastData();
		
		return $agg;
	}
	
	
	/**
	 * 最終入力日を取得する
	 * @return 最終入力日
	 */
	private function getLastData(){
		$res = $this->query("SELECT MAX(todo_date) AS last_date FROM many_todos");
		$last_date = $res[0][0]['last_date'];

		return $last_date;
	}
	
	
	
	
	/**
	 * 日付による件数取得
	 * @param unknown $d 日付
	 * @return 件数
	 */
	private function getCntByDate($d){
		$res = $this->query("SELECT count(id) as cnt FROM many_todos WHERE todo_date = '{$d}'");
		$cnt = $res[0][0]['cnt'];
		return $cnt;
	}
	
	/**
	 * 明日を取得する
	 * @param date or string  $today 本日
	 * @return 明日
	 */
	private function calcYesterday($today){
		$date = new DateTime($today);
		$date->modify("-1 day");
		$yesterday = $date->format("Y-m-d");
		return $yesterday;
	}
	
	/**
	 * n日前を取得する
	 * @param date or string  $today 本日
	 * @return n日前
	 */
	private function calcAnyday($today,$n){
		$date = new DateTime($today);
		$date->modify($n." day");
		$d = $date->format("Y-m-d");
		return $d;
	}

	
	/**
	 * 先週平均を取得する
	 * @param unknown $date1
	 * @return 先週平均
	 */
	private function aggLastWeekCnt($date1){
		
		// 先週の週末日を取得する(今週の日曜日）
		$date2 = date('Y-m-d', strtotime("-7 day", strtotime($date1)));
		$lastWeekDate = $this->getLastWeekDateForMonth($date2);
		
		// 週の集計データを取得する
		$avg = $this->aggWeek($lastWeekDate,array('monday_flg'=>1));
		
		return $avg;
		
	}
	
	/**
	 * 週の集計データを取得する
	 * 
	 * 指定した日付の週の平均日数を取得する。
	 * 週始めはデフォルトで日曜日。オプションで月曜日を週始めにすることもできる。
	 * 
	 * @param date or string $date1 指定日付
	 * @param array $option オプション 省略可
	 *  - monday_flg true:月曜日を週始めにする
	 * 
	 * @return array 平均日数
	 */
	private function aggWeek($date1,$option=array()){
		
		// 月曜日始めフラグを取得する
		$monday_flg=0;
		if(!empty($option['monday_flg'])){
			$monday_flg=1;
		}
		
		// 指定日付から週始日を取得する
		$d1 = $this->beginWeekDate($date1,$monday_flg);
		

		// 週始日から指定日付までの日数を算出
		$diff_cnt = $this->diffDay($date1,$d1);
		$diff_cnt++;
		
		// SQLクエリを作成
		$sql="SELECT count(id) as cnt FROM many_todos WHERE todo_date >= '{$d1}' AND todo_date <= '{$date1}'";
		$res = $this->query($sql);
		
		// 合計数を取得する
		$cnt = $res[0][0]['cnt'];
		
		// 平均日数を算出
		$avg = $cnt / $diff_cnt;
		$avg = round($avg,1);//四捨五入
		
		return $avg;
		
	}
	
	
	// 週末日を取得する(今週の日曜日）
	private function getLastWeekDateForMonth($date1){
		
		// 週始めの日付を取得
		$d1 = $this->beginWeekDate($date1,1);
		$d2 = date('Y-m-d', strtotime("6 day", strtotime($d1)));
		
		return $d2;
	}
	
	
	/**
	 * 週始めの日付を取得
	 *
	 * 指定日付の週の週始めを取得する。
	 * 週始めは日曜日である。ただし月曜日フラグをONにした場合、月曜日が週始めとなる。
	 *
	 * @param date or string $date1 指定日付
	 * @param bool $monday_flg 月曜日フラグ  省略可  true:月曜日が週始めとなる。
	 * @param string $format 日付フォーマット【省略可】
	 */
	function beginWeekDate($date1,$monday_flg=0,$format='Y-m-d') {
		$w = date("w",strtotime($date1));
	
		if($w==0){
			if(!empty($monday_flg)){
				$bwDate = date($format, strtotime("-6 day", strtotime($date1)));
			}else{
				$bwDate = date($format, strtotime($date1));
			}
				
		}else{
			if(!empty($monday_flg)){
				$w--;
			}
			$bwDate = date($format, strtotime("-{$w} day", strtotime($date1)));
		}
	
		return $bwDate;
	}
	
	/**
	 * 2つの日付の日数差を算出する
	 * 
	 * diff = d2 - d1
	 * 
	 * @param date or string $d2
	 * @param date or string $d1
	 * @return 日数差
	 */
	function diffDay($d2,$d1){
	
		$u1=strtotime($d1);
		$u2=strtotime($d2);
	
		//日数を算出
		$diff=$u2-$u1;
		$d_cnt=$diff/86400;
	
		return $d_cnt;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// 今月平均を集計する
	private function aggMonthAvg($date1){
		
		// 月初を取得
		$d1 = $this->getBeginningMonthDate($date1);
		
		
		$d2 = $date1;

		
		// 日数を取得する
		$diff_cnt = $this->diffDay($date1,$d1);
		$diff_cnt++;
		
		// SQLクエリを作成
		$sql="SELECT count(id) as cnt FROM many_todos WHERE todo_date >= '{$d1}' AND todo_date <= '{$d2}'";
		$res = $this->query($sql);
		
		// 合計数を取得する
		$cnt = $res[0][0]['cnt'];
		
		// 平均日数を算出
		$avg = $cnt / $diff_cnt;
		$avg = round($avg,1);//四捨五入
		
		return $avg;
		
	}
	
	
	/**
	 * 引数日付から月初めの日付を取得する。
	 * @param $ymd
	 */
	private function getBeginningMonthDate($ymd) {
		$ym = date("Y-m",strtotime($ymd));
		$d=$ym.'-01';
		return $d;
	}
	
	
	
	
	// 先月平均を集計する
	private function aggLastMonthAvg($date1){
		
		//先月初日を取得する
		$d1 = $this->getBeginningMonthDate($date1);// 月初を取得
		$d1 = date('Y-m-d', strtotime("-1 month", strtotime($d1)));
		
		// 先月末日を取得する
		$d2 = date('Y-m-t',strtotime($d1));
		
		// 月平均を集計する
		$avg = $this->aggMonthAvg($d2);
		
		return $avg;
		
	}
	
	
	
	
	/**
	 * ボーナスデータをDBから取得する
	 * @return ボーナスデータ
	 */
	public function getBonusData(){
		if(empty($this->AnyJson)){
			App::uses('AnyJson','Model');
			$this->AnyJson=ClassRegistry::init('AnyJson');
		}
	
		// JSONをDBから取得する
		$bonusJson = $this->AnyJson->findJson('many_todo_bonus','kani');
	
		// JSONが空であるならデフォルトのボーナスデータを取得する。
		$data = null;
		if(empty($bonusJson)){
			$data = $this->getDefBonusData();
		}else{
			$data=json_decode($bonusJson,true);
		}
		
		
		// ボーナスデータからボーナス金を算出する。
		$data = $this->calcBonusAmount($data);
	
		return $data;
	
	}
	
	/**
	 * デフォルトのボーナスデータを取得する。
	 * @return デフォルトのボーナスデータ
	 */
	private function getDefBonusData(){
	
		$data = array(
				'p_money'=>0,
				'threshold_date'=>date('Y-m-d'),
				'rate'=>35,
		);
	
		return $data;
	}
	
	
	


	/**
	 * ボーナスデータからボーナス金を算出する。
	 * @param array $data ボーナスデータ
	 * @return ボーナス金とTodo数をセットしたボーナスデータ
	 */
	private function calcBonusAmount($data){
		$p_money = $data['p_money'];
		$threshold_date = $data['threshold_date'];
		$rate = $data['rate'];
		
		// しきい日付以降のTodo数を取得する
		$todo_cnt = $this->getTodoCount($threshold_date);
		
		// ボーナス金＝しきい日付以降のTodo数 × ボーナス率 ＋ ポジション金
		$data['bonus_amt'] = $todo_cnt * $rate + $p_money;
		$data['todo_cnt'] = $todo_cnt;
		
		return $data;
	}
	
	
	/**
	 * しきい日付以降のTodo数を取得する
	 * @param string $threshold_date しきい日付
	 * @return Todo数
	 */
	private function getTodoCount($threshold_date){

		
		//SELECT情報
		$fields=array(
				'COUNT(id) AS todo_cnt',
		);
		
		//WHERE情報
		$conditions=array(
				"todo_date >= '{$threshold_date}'",
		);
		
		//オプション
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
		);
		
		//DBから取得
		$data=$this->find('first',$option);
		
		$todo_cnt = 0;
		if(!empty($data)){
			$todo_cnt = $data[0]['todo_cnt'];
		}
		
		return $todo_cnt;
	}
	
	
	
	
	
	
	
}




























