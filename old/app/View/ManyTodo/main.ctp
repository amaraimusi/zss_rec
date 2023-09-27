<?php 
$this->assign('script', $this->Html->script(array(
		'jquery-ui.min',
		'ManyTodo/main.js?ver=1.1',
),array('charset'=>'utf-8')));

$this->assign('css', $this->Html->css(array(
		'jquery-ui.min',
),array('charset'=>'utf-8')));
?>

<div id="msg" class="success"></div>
<div id="err" class="err"></div>

<div style="float:left">
<table class="tbl2">

	
	<tbody>
		<tr>
			<td>全合計数</td>
			<td id="all_total"><?php echo $agg['all_total']?></td>
			<td></td><td></td><td></td><td></td>
		</tr>
		<tr>
			<td>先月平均</td>
			<td id="l_month_avg"><?php echo $agg['l_month_avg']?></td>
			<td>今月平均</td><td id="t_month_avg"><?php echo $agg['t_month_avg']?></td>
			<td>月平均差</td><td id="month_diff"><?php echo $agg['month_diff']?></td>
		</tr>
		<tr>
			<td>先週平均</td><td id="l_week_cnt"><?php echo $agg['l_week_cnt']?></td>
			<td>今週平均</td>
			<td id="t_week_cnt"><?php echo $agg['t_week_cnt']?></td>
			<td>週平均差</td>
			<td id="week_diff"><?php echo $agg['week_diff']?></td>
		</tr>
		<tr>
			<td>二日前・TODO数</td>
			<td id="day2_cnt"><?php echo $agg['day2_cnt']?></td>
			<td>先日・TODO数</td>
			<td id="yesterday_cnt"><?php echo $agg['yesterday_cnt']?></td>
			<td>本日・TODO数</td>
			<td id="today_cnt"><?php echo $agg['today_cnt']?></td>
			<td>日TODO差</td>
			<td id="t_y_diff"><?php echo $agg['t_y_diff']?></td>
		</tr>
	</tbody>
</table>

<div>
	<div>最終更新日：<time><?php echo $agg['last_date']?></time></div>
</div>
</div>

<div style="float:left">
	<p>ボーナス金：<?php echo $bonusData['bonus_amt']?>円</p>
	<div>TODO数：<?php echo $bonusData['todo_cnt']?></div>
	<input type="button" value="設定" class="btn btn-info btn-xs" onclick="$('#bonus_div').toggle()" />
	<div id="bonus_div" style="display:none">
		ボーナス金＝しきい日付以降のTODO数 × ボーナス率 ＋ ポジション金<br>
		<table><tbody>
			<tr><td>ポジション金</td><td><input type="text" name="p_money" value="<?php echo $bonusData['p_money']?>" /></td></tr>
			<tr><td>しきい日付</td><td>
				<input id="bonus_threshold_date" type="text" name="threshold_date" value="<?php echo $bonusData['threshold_date']?>" />
			</td></tr>
			<tr><td>ボーナス率</td><td><input type="text" name="rate" value="<?php echo $bonusData['rate']?>" /></td></tr>
		</tbody></table>
		<input type="button" value="更新" class="btn btn-success btn-xs" onclick="bonus_reg()" />
		<span id="bonus_res" class="text-success"></span>
	</div>
</div>


<div style="clear:both;"></div>
<div id="form_x" style="margin-top:20px;">

<input id="todo_date" type="text" value="<?php echo date('Y-m-d')?>" />
削除フラグ：<input id="delete_flg" type="checkbox" value="1"  />
<input type="button" id="reg_btn" value="登録" onclick="reg()" class="btn btn-success"/>
<br>
<textarea  id="todos_text" style="width:100%;height:500px"></textarea><br>


</div>



<div class="yohaku"></div>
<ol class="breadcrumb">
	<li><a href="/">ホーム</a></li>
	<li><a href="/zss_rec/developer/">開発者用目次</a></li>
	<li>Many Todo</li>
</ol>



		

