<?php
App::uses('FormHelper', 'View/Helper');

/**
 * CrudBase用ヘルパー
 * 
 * @note
 * 検索条件入力フォームや、一覧テーブルのプロパティのラッパーを提供する
 * 
 * 
 * @version 1.6.2
 * @date 2016-7-27 | 2017-4-28 tdNoteのバグを修正
 * @author k-uehara
 *
 */

class CrudBaseHelper extends FormHelper {

	private $_mdl=""; // モデル名
	private $_mdl_snk=""; // モデル名（スネーク記法）
	private $_dateTimeList=array(); // 日時選択肢リスト
	private $param; // 各種パラメータ
	private $kjs; // 検索条件情報
	
	// 列並びモード用
	private $_clmSortTds = array(); // 列並用TD要素群
	private $_clmSortMode = 0;		// 列並モード
	private $_field_data;			// フィールドデータ
	
	
	
	/**
	 * モデル名のセッター
	 * 
	 * input系メソッドに影響します。
	 * 
	 * @param string $modelName モデル名
	 */
	public function setModelName($modelName){
		$this->_mdl = $modelName.'.';
		$this->_mdl_snk = $this->snakize($modelName); //スネーク記法に変換
		
	}
	
	/**
	 * CSSファイルリストを取得する
	 * @return array CSSファイルリスト
	 */
	public function getCssList(){
	    return array(
	        'jquery.datetimepicker.min',   // 日時ピッカー
	        'clm_show_hide',				// 列表示切替
	        'YmpickerWrap',					// 年月ピッカーのラッパー
	        'nouislider.min',				// 数値範囲入力スライダー・noUiSlider
	        'CrudBase/NoUiSliderWrap',				// noUiSliderのラップ
	        'CrudBase/index'				// CRUD indexページ共通
	    );
	}
	
	/**
	 * JSファイルのインクルード
	 */
	public function getJsList(){
	    return array(
	        'clm_show_hide',				// 列表示切替
	        'date_ex',						// 日付関連関数集
	        'jquery.ui.ympicker',			// 年月選択ダイアログ
	        'nouislider.min',				// 数値範囲入力スライダー・noUiSlider
	        'jquery.datetimepicker.full.min',// 日時ピッカー
	        'CrudBase/NoUiSliderWrap',		// noUiSliderのラップ
	        'CrudBase/YmpickerWrap',		// 年月ピッカーのラッパークラス
	        'CrudBase/CrudBaseBase.js?ver=2.3.1',
	        'CrudBase/CrudBaseAutoSave.js?ver=1.0',
	        'CrudBase/CrudBaseRowExchange.js?ver=1.2',
	        'CrudBase/CrudBaseGadgetKj.js?ver=1.0',
	        'CrudBase/CrudBase.js?ver=2.0',
	        'livipage',						// ページ内リンク先プレビュー
	        'ProcessWithMultiSelection',	// 一覧のチェックボックス複数選択による一括処理
	        'CrudBase/ImportFu.js',			// インポート・ファイルアップロードクラス
	        'CrudBase/index'                // CRUD indexページ共通
	    );
	}
	
	/**
	 * スネーク記法のモデル名を取得する
	 * @return string スネーク記法のモデル名
	 */
	public function getModelNameSnk(){
		return $this->_mdl_snk;
	}
	
	/**
	 * 各種パラメータのセッター
	 * 
	 * @param array $param パラメータ
	 */
	public function setParam($param){
		$this->param = $param;
	}
	
	
	/**
	 * crudBaseDataのセッター
	 * @param array $kjs 検索条件データ
	 */
	public function setKjs(&$kjs){
	    $this->kjs = &$kjs;
	}
	
	
	/**
	 * 新規入力ボタンを作成
	 * @param array $option オプション【省略可】
	 * - class クラス属性の値
	 * - onclick onclickイベントにセットするJS関数（CRUDタイプがajax型である場合のみ有効)
	 * - display_name ボタンの表示名
	 */
	public function newBtn($option = null){
	
		if(empty($option)){
			$option = array();
		}
		
		if(empty($option['class'])){
			$option['class'] = 'btn btn-warning btn-sm';
		}
		
		if(empty($option['onclick'])){
			$option['onclick'] = 'newInpShow(this);';
		}
		
		if(empty($option[$this->_mdl_snk])){
			$option[$this->_mdl_snk] = '新規入力';
		}
		
		$crudType = $this->param['crudType'];
		$class = $option['class'];
		$btn_name = $option[$this->_mdl_snk];
		
		// CRUDタイプがajax型である場合
		if(empty($crudType)){
			$onclick = $option['onclick'];
			echo "<button type='button' class='{$class}' onclick='{$onclick}' />{$btn_name}</button>";
			
		}
		
		// CRUDタイプがsubmit型である場合
		else{
			$path = $this->Html->webroot.$this->_mdl_snk;
			echo "<a href='{$path}/edit' class='{$class}'>{$btn_name}</a>";
		}

	}
	
	/**
	 * 検索用のid入力フォームを作成
	 * 
	 * @param array $kjs 検索条件データ
	 */
	public function inputKjId($kjs){

	    echo "<div class='kj_div kj_wrap' data-field='kj_id'>\n";
		echo $this->input($this->_mdl.'kj_id', array(
				'id' => 'kj_id',
				'value' => $kjs['kj_id'],
				'type' => 'text',
				'label' => false,
				'placeholder' => '-- ID --',
				'style'=>'width:100px',
				'title'=>'IDによる検索',
				'maxlength'=>8,
		));
		
		echo "</div>\n";
				
	}
	
	
	/**
	 * 検索用のテキスト入力フォームを作成
	 * 
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 * @param string $wamei フィールド和名
	 * @param int $width 入力フォームの横幅（省略可）
	 * @param string $title ツールチップメッセージ（省略可）
	 * @param int $maxlength 最大文字数(共通フィールドは設定不要）
	 */
	public function inputKjText($kjs,$field,$wamei,$width=200,$title=null,$maxlength=255){
		
		if($title==null){
			$title = $wamei."で検索";
		}
		
		// maxlengthがデフォルト値のままなら、共通フィールド用のmaxlength属性値を取得する
		if($maxlength==255){
			$maxlength = $this->getMaxlenIfCommonField($field,$maxlength);
		}

		echo "<div class='kj_div kj_wrap' data-field='{$field}'>\n";
		echo $this->input($this->_mdl.$field, array(
				'id' => $field,
				'value' => $kjs[$field],
				'type' => 'text',
				'label' => false,
				'placeholder' => $wamei,
				'style'=>"width:{$width}px",
				'title'=>$title,
				'maxlength'=>$maxlength,
		));
		echo "</div>\n";
	}
	
	/**
	 * 共通フィールド用のmaxlength属性値を取得する
	 * 
	 * @param string $field フィールド名
	 * @return maxlength属性値;
	 */
	private function getMaxlenIfCommonField($field,$maxlength){
		
		if($field == 'kj_update_user'){
			$maxlength = 50;
		}else if($field == 'kj_user_agent'){
			$maxlength = 255;
		}else if($field == 'kj_ip_addr'){
			$maxlength = 16;
		}
		
		return $maxlength;
	}
	
	
	/**
	 * 検索用のhiddenフォームを作成
	 *
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 */
	public function inputKjHidden($kjs,$field){
	

		echo $this->input($this->_mdl.$field, array(
            'id' => $field,
            'value' => $kjs[$field],
            'type' => 'hidden',
		    'data-field' => $field,
		    'class' => 'kj_wrap',
		));
		
	}
	
	
	
	
	
	/**
	 * 検索用のセレクトフォームを作成
	 * 
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 * @param string $wamei フィールド和名
	 * @param string $list 選択肢リスト
	 * @param int $width 入力フォームの横幅（省略可）
	 * @param string $title ツールチップメッセージ（省略可）
	 */
	public function inputKjSelect($kjs,$field,$wamei,$list,$width=150,$title=null){
		
		if($title==null){
			$title = $wamei."で検索";
		}
		
		echo "<div class='kj_div kj_wrap' data-field='{$field}'>\n";
		echo $this->input($this->_mdl.$field, array(
				'id' => $field,
				'type' => 'select',
				'options' => $list,
				'empty' => "-- {$wamei} --",
				'default' => $kjs[$field],
				'label' => false,
				'style'=>"width:{$width}px",
				'title'=>$title,
		));	
		echo "</div>\n";
	}


	
	
	
	/**
	 * 検索用の更新日時セレクトフォームを作成
	 * @param array $kjs 検索条件データ
	 */
	public function inputKjModified($kjs){
	
		$this->inputKjDateTimeA($kjs,'kj_modified','更新日時');
	}
	
	
	/**
	 * 検索用の日時入力フォームを作成
	 *
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 * @param string $wamei フィールド和名
	 * @param int $width 入力フォームの横幅（省略可）
	 * @param string $title ツールチップメッセージ（省略可）
	 * @param int $maxlength 最大文字数(共通フィールドは設定不要）
	 */
	public function inputKjDateTime($kjs,$field,$wamei,$width=200,$title=null,$maxlength=255){
	    
	    if($title==null){
	        $title = $wamei."で検索";
	    }
	    
	    // maxlengthがデフォルト値のままなら、共通フィールド用のmaxlength属性値を取得する
	    if($maxlength==255){
	        $maxlength = $this->getMaxlenIfCommonField($field,$maxlength);
	    }
	    
	    echo "<div class='kj_div kj_wrap' data-field='{$field}' data-gadget='datetimepicker' >\n";
	    echo $this->input($this->_mdl.$field, array(
	        'id' => $field,
	        'value' => $kjs[$field],
	        'type' => 'text',
	        'label' => false,
	        'placeholder' => $wamei,
	        'style'=>"width:{$width}px",
	        'title'=>$title,
	        'maxlength'=>$maxlength,
	    ));
	    echo "</div>\n";
	}

	
	
	
	
	/**
	 * 検索用の生成日時セレクトフォームを作成
	 * @param array $kjs 検索条件データ
	 */
	public function inputKjCreated($kjs){
	
		$this->inputKjDateTimeA($kjs,'kj_created','生成日時');
	}
	

	
	
	
	/**
	 * 検索用の日時セレクトフォームを作成
	 *
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 * @param string $wamei フィールド和名
	 * @param string $list 選択肢リスト（省略可）
	 * @param int $width 入力フォームの横幅（省略可）
	 * @param string $title ツールチップメッセージ（省略可）
	 */
	public function inputKjDateTimeA($kjs,$field,$wamei,$list=array(),$width=200,$title=null){
	
		if($title==null){
			$title = $wamei."で検索";
		}
		
		if(empty($list)){
			$list = $this->getDateTimeList();
		}
	
		echo "<div class='kj_div kj_wrap' data-field='{$field}' >\n";
		echo $this->input($this->_mdl.$field, array(
				'id' => $field,
				'type' => 'select',
				'options' => $list,
				'empty' => "-- {$wamei} --",
				'default' => $kjs[$field],
				'label' => false,
				'style' => "width:{$width}px",
				'title' => $title,
		));
		echo "</div>\n";
	}
	
	
	
	
	
	/**
	 * 検索用の削除フラグフォームを作成
	 *
	 * @param array $kjs 検索条件データ
	 * 
	 */	
	public function inputKjDeleteFlg($kjs){
	    echo "<div class='kj_div kj_wrap' data-field='kj_delete_flg'>\n";
		echo $this->input($this->_mdl.'kj_delete_flg', array(
			'id' => 'kj_delete_flg',
			'type' => 'select',
			'options' => array(
			    -1=>'すべて表示',
				0=>'有効',
				1=>'削除',
			),
			'default' => $kjs['kj_delete_flg'],
			'label' => false,
		));
		echo "</div>\n";
	}
	
	
	
	
	
	/**
	 * 検索用のフラグフォームを作成
	 *
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 * @param string $wamei フィールド和名
	 *
	 */
	public function inputKjFlg($kjs,$field,$wamei){
	    echo "<div class='kj_div kj_wrap' data-field='{$field}'>\n";
	    echo $this->input($this->_mdl.$field, array(
	        'id' => $field,
	        'type' => 'select',
	        'options' => array(
	            -1=>'すべて表示',
	            0=>'無効',
	            1=>'有効',
	        ),
	        'default' => $kjs[$field],
	        'label' => false,
	    ));
	    echo "</div>\n";
	}
	
	
	
	
	
	/**
	 * 検索用の表示件数セレクトを作成
	 *
	 * @param array $kjs 検索条件データ
	 * 
	 */	
	public function inputKjLimit($kjs){
	    echo "<div class='kj_div kj_wrap' data-field='row_limit'>\n";
		echo $this->input($this->_mdl.'row_limit', array(
				'id' => 'row_limit',
				'type' => 'select',
				'options' => array(
						5=>'5件表示',
						10=>'10件表示',
						20=>'20件表示',
						50=>'50件表示',
						100=>'100件表示',
						200=>'200件表示',
						500=>'500件表示',
				),
				'default' => $kjs['row_limit'],
				'label' => false,
				'style' => 'height:27px'
		));
		echo "</div>\n";
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 * 月・日付範囲検索
	 * 
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名
	 * @param string $wamei フィールド和名
	 */
	public function inputKjMoDateRng($kjs,$field,$wamei){


		$kj_date_ym = $field.'_ym';
		$kj_date1 = $field.'1';
		$kj_date2 = $field.'2';
		$kj_dates = $field.'s';
		
		$kj_ym_value = $kjs[$kj_date1];
		if(!empty($kj_ym_value)){
			$kj_ym_value=date('Y/m',strtotime($kj_ym_value));
		}
		
		echo "<div class='kj_div kj_wrap' data-field='{$field}' data-gadget='mo_date_rng'>";
		echo "<div class='kj_div' style='margin-right:2px'>";
		echo $this->input($kj_date_ym, array(
				'id' => $kj_date_ym,
				'value' => $kj_ym_value,
				'type' => 'text',
				'label' => false,
				'placeholder' => '-- '.$wamei.'年月 --',
				'style'=>'width:100px;',
		));
		echo "</div>";
		
		
		
		
		echo "<div class='kj_div'>";
		echo "	<input type='button' class='ympicker_toggle_btn' value='' onclick=\"$('.{$kj_dates}').fadeToggle()\" title='日付範囲入力を表示します' />";
		echo "</div>";
		
		
		
		
		echo "<div class='kj_div {$kj_dates}' style='display:none'>";
		echo $this->input($this->_mdl.$kj_date1, array(
				'id' => $kj_date1,
				'value' => $kjs[$kj_date1],
				'type' => 'text',
				'label' => false,
				'placeholder' => '-- '.$wamei.'【範囲1】--',
				'style'=>'width:150px',
				'title'=>'入力日以降を検索',
		));
		echo "</div>";
		
		
		
		echo "<div class='kj_div {$kj_dates}' style='display:none'>";
		echo $this->input($this->_mdl.$kj_date2, array(
				'id' => $kj_date2,
				'value' => $kjs[$kj_date2],
				'type' => 'text',
				'label' => false,
				'placeholder' => '-- '.$wamei.'【範囲2】--',
				'style'=>'width:150px',
				'title'=>'入力日以前を検索',
		));
		echo "</div>";
		echo "</div>";

		
	}
	
	
	
	
	
	
	
	
	/**
	 * 
	 * 検索用の年月入力フォームを作成
	 * 
	 * @param array $kjs 検索条件データ
	 * @param string $field フィールド名（ kj_ を付けないこと）
	 * @param string $wamei フィールド和名
	 */
	public function inputKjNouislider($kjs,$field,$wamei){
		//<!-- 数値範囲入力スライダー・noUiSlider -->
		$detail_noui = $field.'_detail';
		
		echo "<div class='kj_div kj_wrap' data-field='{$field}' data-gadget='nouislider'><table><tr><td>";
		echo "		<span class='nusr_label'><{$wamei}による範囲検索</span>&nbsp;";
		echo "		<span id='{$field}_preview' class='nusr_preview'></span>";
		echo "	</td></tr>";
		
		
		echo "	<tr><td><div id='{$field}_slider' title='{$wamei}による範囲検索'></div></td>";
		echo "	<td><input type='button' class='nusr_toggle_btn' value='' onclick=\"$('#{$detail_noui}').fadeToggle()\" title='日付範囲入力を表示します'></td>";
		echo "	</tr>";
		
		
		
		echo "	<tr id='{$detail_noui}' class='nusr_detail'><td>";
		echo "	<div class='kj_div'>";
			
		$key='kj_'.$field.'1';
		echo $this->input($this->_mdl.$key, array(
			'id' => $key,
			'value' => $kjs[$key],
			'type' => 'number',
			'label' => false,
			'style'=>'width:50px',
			'title'=>$wamei.'による範囲検索',
		));
			
		echo "	</div><div class='kj_div'>～</div><div class='kj_div'>";
		
		$key='kj_'.$field.'2';
		echo $this->input($this->_mdl.$key, array(
			'id' => $key,
			'value' => $kjs[$key],
			'type' => 'number',
			'label' => false,
			'style'=>'width:50px',
			'title'=>$wamei.'による範囲検索',
		));
		
		echo "	</div></td><td></td></tr></table></div>";
		
		
	}
	
	
	
	
	
	/**
	 * 検索入力保存の入力要素を作成する
	 * @param bool $saveKjFlg
	 * @param string $hidden true:Hidden要素 , false:チェックボックス
	 */
	public function inputKjSaveFlg($saveKjFlg,$hidden=false){
	
		if(!empty($hidden)){
			echo $this->input($this->_mdl."saveKjFlg", array('value' => $saveKjFlg,'type' => 'hidden',));
			return;
		}
		
		echo "<div class='kj_div kj_wrap' data-field='saveKjFlg' style='margin-top:4px;'>";
		echo $this->input($this->_mdl."saveKjFlg",array(
				'type'=>'checkbox',
				'value' => 1,
				'checked'=>$saveKjFlg,
				'label'=>'検索入力保存',
				'div'=>false,
		));
		echo '</div>';
		

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * プロパティをtdタグを付加して出力する
	 * 
	 */
	public function tdPlain($v,$field=''){
		
		if(is_array($v)){
			$v = $v[$field];
		}
		
		$td = "<td><input type='hidden' name='{$field}' value='{$v}'  /><span class='{$field}' >{$v}</span></td>\n";
		$this->setTd($td,$field);
	}
	public function tpPlain($v,$wamei){
		$this->tblPreview($v,$wamei);
	}
	

	

	public function tdStr($v,$field=''){
		
		if(is_array($v)){
			$v = $v[$field];
		}
		
		$v = h($v);
		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}' >{$v}</span></td>\n";
		$this->setTd($td,$field);
	
	}
	// TD:改行用文字列表示
	public function tdStrRN($v,$field=''){
	
		if(is_array($v)){
			$v = $v[$field];
		}
		
		$v = h($v);
		
		$v = nl2br($v);// 改行置換
		
		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}'>{$v}</span>\n";
		
		$this->setTd($td,$field);
	
	}
	
	public function tpStr($v,$wamei){
		$v = h($v);
		$this->tblPreview($v,$wamei);
	}
	
	
	
	
	
	
	/**
	 * idを詳細ページリンク付のコードに変換する
	 *
	 * @param string $v ID
	 */
	public function propId($v){
		$this->_check1();
		
		$detalUrl=$this->Html->webroot.$this->_mdl_snk.'/detail?id='.$v;
		$v = "<a href='{$detalUrl}' >{$v}</a>";
	
		return $v;
	}
	
	/**
	 * ID用のTD用をを作成する
	 * @param string $v 表示する値
	 * @param string $field フィールド名
	 * @param array $option オプション
	 *  - checkbox_name チェックボックス名プロパティ   このプロパティに値をセットすると、複数選択による一括処理用のチェックボックスが作成される。
	 */
	public function tdId($v,$field='id',$option=array()){
		
		if(is_array($v)){
			$v = $v[$field];
		}
		
		
		// 複数選択による一括処理用のチェックボックスHTMLを組み立てる
		$cbHtml = ''; // チェックボックスHTML
		if(!empty($option['checkbox_name'])){
			$cbHtml = "<input type='checkbox' name='{$option['checkbox_name']}' /> ";
		}
		
		
		// TD要素を組み立てる
		$td = "<td>{$cbHtml}<input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}' >{$v}</span></td>\n";
		
		$this->setTd($td,$field);
		

	}
	

	
	public function tpId($v,$wamei='ID'){
		$v = $this->propId($v);
		$this->tblPreview($v,$wamei);
	}	
	

	
	
	
	/**
	 * プロパティをリスト内の値に置き換える
	 *
	 * @param string $v プロパティ
	 * @param array $list リスト
	 */
	public function propList($v,$list){
	
		if(isset($list[$v])){
			$v = $list[$v];
		}else{
			$v="";
		}
	
		return $v;
	
	}
	public function tdList($v,$field="",$list=array()){
		
		if(is_array($v)){
			$v = $v[$field];
		}

		$v2 = $this->propList($v,$list);
		$v2 = h($v2);
		
		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}'>{$v2}</span></td>\n";
		$this->setTd($td,$field);
	
	}
	public function tpList($v,$wamei,$list){
		$v = $this->propList($v,$list);
		$this->tblPreview($v,$wamei);
	
	}
	
	
	
	
	
	
	
	
	/**
	 * プロパティを日本円表記に変換する。
	 * 
	 * @param string $v プロパティ
	 * @return string 日本円表示の文字列
	 */
	public function propMoney($v){
		if(!empty($v) || $v===0){
			$v= '&yen'.number_format($v);
		}
		
		return $v;
	}
	public function tdMoney($v,$field=''){
		
		if(is_array($v)){
			$v = $v[$field];
		}
		
		$v2 = $this->propMoney($v);
		
		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}'>{$v2}</span></td>\n";
		$this->setTd($td,$field);
	}
	public function tpMoney($v,$wamei){
		$v = $this->propMoney($v);
		$this->tblPreview($v,$wamei);

	}
	
	
	
	/**
	 * ノートなどの長文を冒頭だけ表示する
	 *
	 * @param string $v プロパティまたはエンティティ
	 * @param string $field フィールド名
	 * @param int $strLen 表示文字数（バイト）(省略時は無制限に文字表示）
	 */
	public function tdNote($v,$field='',$str_len = null){
		
		if(is_array($v)){
			$v = $v[$field];
		}
		

		$v2="";
		if(!empty($v)){
			$v = h($v);
			if($str_len === null){
				$v2 = $v;
			}else{
				if(mb_strlen($v) > $str_len){
					$v2=mb_strimwidth($v, 0, $str_len, "...");
				}else{
					$v2 = $v;
				}
			}
			$v2= str_replace('\\r\\n', ' ', $v2);
			$v2= str_replace('\\', '', $v2);
		}

		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}'>{$v2}</span></td>\n";
		$this->setTd($td,$field);
	}
	
	
	/**
	 * ノートなどの長文を改行を含めてそのまま表示する
	 *
	 * @param string $v プロパティまたはエンティティ
	 * @param string $field フィールド名
	 */
	public function tdNotePlain($v,$field=''){
		
		if(is_array($v)){
			$v = $v[$field];
		}

		if(!empty($v)){
			$v = h($v);
			$v=nl2br($v);
		
		}

		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><div class='{$field}' >{$v}</div></td>\n";
		$this->setTd($td,$field);
	}
	
	
	
	
	
	public function tpNote($v,$wamei){
	
		if(!empty($v)){
			$v= str_replace('\\r\\n', '<br>', h($v));
			$v= str_replace('\\', '', $v);
		}
	
		$this->tblPreview($v,$wamei);
	
	}
	
	
	/**
	 * テキストエリア用の文字列変換
	 * @param string $v 文字列（改行OK)
	 * @return string テキストエリア用に加工した文字列
	 */
	public function convNoteForTextarea($v){
		if(!empty($v)){
		
			//サニタイズされた改行コードを「&#13;」に置換
			$v = str_replace('\\r\\n', '&#13;', h($v));
			$v = str_replace('\\', '', $v);
		
		}
		
		return $v;
	}
	
	
	
	/**
	 * 削除フラグの表記を変換する
	 * 
	 * @param string $v 削除フラグ
	 */
	public function propDeleteFlg($v){
		
		if($v==0){
			$v="<span style='color:#23d6e4;'>有効</span>";
		}elseif($v==1){
			$v="<span style='color:#b4b4b4;'>削除</span>";
		}
		
		return $v;
	}
	
	
	
	/**
	 * フラグの表記を変換する
	 *
	 * @param string $v 削除フラグ
	 */
	public function propFlg($v){
	    
	    if($v==0){
	        $v="<span style='color:#b4b4b4;'>無効</span>";
	    }elseif($v==1){
	        $v="<span style='color:#23d6e4;'>有効</span>";
	    }
	    
	    return $v;
	}
	
	
	public function tdAdd($html,$field){
		$this->setTd($html,$field);
	}
	
	public function tdDeleteFlg($v,$field=''){
		
		if(is_array($v)){
			$v = $v[$field];
		}
		if(empty($v)){
			$v = 0;
		}
		
		$v2 = $this->propDeleteFlg($v);
		$td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}'>{$v2}</span></td>\n";
		
		$this->setTd($td,$field);
	}
	public function tpDeleteFlg($v,$wamei='削除フラグ'){
		$v = $this->propDeleteFlg($v);
		
		$this->tblPreview($v,$wamei);

	}
	
	
	public function tdFlg($v,$field=''){
	    
	    if(is_array($v)){
	        $v = $v[$field];
	    }
	    if(empty($v)){
	        $v = 0;
	    }
	    
	    $v2 = $this->propFlg($v);
	    $td = "<td><input type='hidden' name='{$field}' value='{$v}' /><span class='{$field}'>{$v2}</span></td>\n";
	    
	    $this->setTd($td,$field);
	}
	public function tpFlg($v,$wamei='削除フラグ'){
	    $v = $this->propFlg($v);
	    
	    $this->tblPreview($v,$wamei);
	    
	}
	
	
	/**
	 * サムネイルと拡大画像プレビュー Lity.js用
	 * @param array $ent エンティティ
	 * @param string $field ﾌｨｰﾙﾄﾞ
	 * @param string $thum_dir_path サムネイル画像のディレクトリパス
	 * @param string $orig_dir_path オリジナル画像のディレクトリパス
	 * @param bool $chash false:キャッシュから画像を読み込まない(デフォルトはtrue)
	 * @param string $no_img_fp 画像ファイルが存在しないときに表示する画像パス（省略可）
	 */
	public function tdThumImgForLity($ent,$field,$thum_dir_path,$orig_dir_path,$chash=true,$no_img_fp=""){

		$dt = "";
		if(empty($chash)){
			$dt = '?'.date('Ymdhis');
		}
		
		$fn = $ent[$field];
		

		
		$thum_fp="";
		$orig_fp="";
		if(empty($fn)){
			if(empty($no_img_fp)){
				$no_img_fp = 'img/icon/no_image.png';
			}
			$thum_fp = $no_img_fp;
			$orig_fp = $no_img_fp;
		}else{
			$thum_fp = $thum_dir_path.$fn.$dt;
			$orig_fp = $orig_dir_path.$fn.$dt;
			
		}
		
		$td_html = "<td>".
		  		"<input type='hidden' name='{$field}' value='{$fn}' />".
		  		"<a href='{$orig_fp}' data-lity='data-lity' />".
		  		"<img src='{$thum_fp}' data-file-preview = '{$field}' class='{$field}_display' title='{$fn}' /></a>".
		  		"</td>";
		
		
		$this->setTd($td_html,$field);
	}
	
	
	
	/**
	 * プロパティのプレビュー表示
	 * @param string $v プロパティの値
	 * @param string $wamei プロパティ和名
	 */
	public function tblPreview($v,$wamei){
		echo "<tr>\n";
		echo "	<td>{$wamei}</td>\n";
		echo "	<td>{$v}</td>\n";
		echo "</tr>\n";
	}
	
	
	
	
	
	/**
	 * 編集用のテキストボックスを作成
	 * @param array $ent エンティティ
	 * @param string $field フィールド名
	 * @param string $wamei 和名
	 * @param int $width 入力フォーム幅
	 * @param array option ベースoption。文字列を指定するとtitle属性になる
	 */
	public function editText($ent,$field,$wamei,$width=200,$option=array()){
	

		if(!is_array($option)){
			$title = $option;
			$option = array('title'=>$title);
		}
	
		echo
		"<tr>".
		" 	<td>{$wamei}</td>".
		" 	<td>";
	
		$option['id'] = $field;
		$option['value'] = $ent[$field];
		$option['type'] = 'text';
		$option['label'] = false;
		$option['div'] = false;
		$option['placeholder'] = "-- {$wamei} --";
		
		if(!empty($option['style'])){
			$option['style'] = "width:{$width}px;";
		}
		
	
		echo $this->input($this->_mdl.$field,$option);
	
		echo
		" 	</td>".
		" </tr>";
	}
	
	
	
	
	
	/**
	 * 編集用のセレクトボックスを作成
	 * @param array $ent エンティティ
	 * @param string $field フィールド名
	 * @param string $wamei 和名
	 * @param array $list 選択肢リスト
	 * @param int $width 入力フォーム幅
	 * @param string $title ツールチップ（省略可）
	 */
	public function editSelect($ent,$field,$wamei,$list,$width=200,$title=null){
		
		
		echo 
			"<tr>".
			" 	<td>{$wamei}</td>".
			" 	<td>";
		

		$option = array(
				'id'=>$field,
		 		'type' => 'select',
		 		'options' => $list,
				'default' => $ent[$field],
				'label' => false,
				'div' =>false,
		 		'empty' => "-- {$wamei} --",
				'style'=>"width:{$width}px;",
		);
		
		if(!empty($title)){
			$option['title'] = $title;
		}
		
		echo $this->input($this->_mdl.$field,$option);

		echo
			" 	</td>".
			" </tr>";
	}
	
	
	
	
	
	/**
	 * 編集用のテキストエリアを作成
	 * @param array $ent エンティティ
	 * @param string $field フィールド名
	 * @param string $wamei 和名
	 * @param int $width 入力フォーム横幅
	 * @param int $height 入力フォーム縦幅
	 * @param string $title ツールチップ（省略可）
	 */
	public function editTextArea($ent,$field,$wamei,$width=400,$height=100,$title=null){
		
		// テキストエリア用に改行やサニタイズを施す
		$note = $this->convNoteForTextarea($ent[$field]);
		
		echo 
			"<tr>".
			" 	<td>{$wamei}</td>".
			" 	<td>";
		
		$option = array(
				'id'=>$field,
				'value' => $note,
				'type' => 'textarea',
				'placeholder' => "-- {$wamei} --",
				'label' => false,
				'div'=>false,
				'escape'=>false,
				'style'=>"width:{$width}px;height:{$height}px;",
		);
		
		
		if(!empty($title)){
			$option['title'] = $title;
		}
		
		echo $this->input($this->_mdl.$field,$option);

		echo
			" 	</td>".
			" </tr>";
	}
	
	
	
	
	/**
	 * 編集用の削除フラグチェックボックスを作成
	 * @param array $ent エンティティ
	 * @param string $mode モード    new:新規モード , edit:編集モード
	 */
	public function editDeleteFlg($ent,$mode){

		
		$wamei = "";
		if($mode=='edit'){
			$wamei = "削除";
		}
			
		echo
		"<tr>".
		" 	<td>{$wamei}</td>".
		" 	<td>";
		
		if($mode=='edit'){
			echo $this->input($this->_mdl.'delete_flg', array(
					'checked'=>$ent['delete_flg'],
					'type' => 'checkbox',
					'label' => false,
					'div' =>false,
					'title' => '削除にすると表示されません。',
			));
		}else{
			echo $this->input('delete_flg', array('value' => $ent['delete_flg'],'type' => 'hidden',));
		}
		
		
		
		echo
		" 	</td>".
		" </tr>";
	}

	
	
	
	/**
	 * 行の編集ボタンを作成する
	 * @param int $id ID
	 * @param string $css_class CSSスタイル（省略可）
	 * @param $onclick 編集フォームを呼び出すjs関数（CRUDタイプがajax型である場合。省略可)
	 */
	public function rowEditBtn($id,$css_class=null,$onclick=null){

		if(empty($css_class)){
			$css_class='row_edit_btn btn btn-primary btn-xs';
		}
		
		if(empty($onclick)){
			$onclick="editShow(this);";
		}
		
		$crudType = $this->param['crudType'];
		
		// CRUDタイプがajax型である場合
		if(empty($crudType)){
			echo "<input type='button' value='編集'  class='{$css_class}' onclick='{$onclick}' />";
		}
		
		// CRUDタイプがsubmit型である場合
		else{
			$url=$this->Html->webroot.$this->_mdl_snk.'/edit?id='.$id;
			echo "<a href='{$url}' class='{$css_class}'>編集</a>";
		}

	}
	
	
	
	
	/**
	 * 行の複製ボタンを作成する
	 * @param int $id ID
	 * @param string $css_class CSSスタイル（省略可）
	 * @param $onclick 複製フォームを呼び出すjs関数（CRUDタイプがajax型である場合。省略可)
	 */
	public function rowCopyBtn($id,$css_class=null,$onclick=null){
	    
	    if(empty($css_class)){
	        $css_class='row_copy_btn btn btn-primary btn-xs';
	    }
	    
	    if(empty($onclick)){
	        $onclick="copyShow(this);";
	    }
	    
	    $crudType = $this->param['crudType'];
	    
	    echo "<input type='button' value='複製'  class='{$css_class}' onclick='{$onclick}' />";
	    
	}
	
	
	
	
	
	
	/**
	 * 行のプレビューボタンを作成する
	 * @param int $id ID
	 * @param string $css_class CSSスタイル（省略可）
	 */
	public function rowPreviewBtn($id,$css_class=null){
		
		if(empty($css_class)){
			$css_class='row_preview_btn btn btn-info btn-xs';
		}
		
		
		$crudType = $this->param['crudType'];
		
		// CRUDタイプがajax型である場合
		if(empty($crudType)){
			// ajax型にはプレビュー表示の概念が存在しないのでプレビューボタンを作成しない。
				
		}
		
		// CRUDタイプがsubmit型である場合
		else{
			$url=$this->Html->webroot.$this->_mdl_snk.'/detail?id='.$id;
			echo "<a href='{$url}' class='{$css_class}'>詳細</a>";
		}
		
		
		
	}
	
	
	
	
	/**
	 * 行の削除ボタンを作成する
	 * @param int $id ID
	 * @param string $css_class CSSスタイル（省略可）
	 * @param $onclick 削除フォームを呼び出すjs関数（CRUDタイプがajax型である場合。省略可)
	 */
	public function rowDeleteBtn(&$ent,$option=array()){
	    
	    $css_class = 'row_delete_btn btn btn-warning btn-xs';
	    if(isset($option['css_class'])) $css_class = $option['css_class'];
	    
	    $onclick="deleteAction(this);";
	    if(isset($option['onclick'])) $css_class = $option['onclick'];
	    
	    $crudType = $this->param['crudType'];
	    
	    // 検索条件データの削除フラグが0(有効)でなければ削除ボタンを表示しない。
	    $style='';
	    if($ent['delete_flg'] == 1) $style = "style='display:none'";
		
		// CRUDタイプがajax型である場合
		if(empty($crudType)){
			echo "<input type='button' value='削除'  class='{$css_class}' onclick='{$onclick}' {$style} />";	
		}

	}
	
	
	
	
	/**
	 * 行の有効ボタンを作成する
	 * @param int $id ID
	 * @param string $css_class CSSスタイル（省略可）
	 * @param $onclick 有効フォームを呼び出すjs関数（CRUDタイプがajax型である場合。省略可)
	 */
	public function rowEnabledBtn(&$ent,$option=array()){
	    
	    $css_class = 'row_enabled_btn btn btn-success btn-xs';
	    if(isset($option['css_class'])) $css_class = $option['css_class'];
	    
	    $onclick="enabledAction(this);";
	    if(isset($option['onclick'])) $css_class = $option['onclick'];
	    
	    $crudType = $this->param['crudType'];
	    
	    // 検索条件データの有効フラグが1(無効)でなければ有効ボタンを表示しない。
	    $style='';
	    if($ent['delete_flg'] != 1) $style = "style='display:none'";
	    
	    // CRUDタイプがajax型である場合
	    if(empty($crudType)){
	        echo "<input type='button' value='有効'  class='{$css_class}' onclick='{$onclick}' {$style} />";
	    }
	    
	}
	
	
	
	
	/**
	 * 行の抹消ボタンを作成する
	 * 
	 * @note
	 * 検索条件データの削除フラグが1(削除)でなければ抹消ボタンを表示しない。
	 * 
	 * @param array $ent エンティティ
	 * @param array $option
	 *  - css_class CSSスタイル（省略可）
	 *  - onclick 抹消フォームを呼び出すjs関数（CRUDタイプがajax型である場合。省略可)
	 */
	public function rowEliminateBtn(&$ent,$option=array()){
	    

	    
	    $css_class = 'row_eliminate_btn btn btn-danger btn-xs';
	    if(isset($option['css_class'])) $css_class = $option['css_class'];
	    
	    $onclick="eliminateShow(this);";
	    if(isset($option['onclick'])) $css_class = $option['onclick'];

	    $crudType = $this->param['crudType'];
	    
	    $style='';
	    if($ent['delete_flg'] != 1) $style = "style='display:none'";
	    
	    // CRUDタイプがajax型である場合
	    if(empty($crudType)){
	        echo "<input type='button' value='抹消'  class='{$css_class}' onclick='{$onclick}' {$style} title='データベースからも消去します。復元できません。' />";
	    }
	    
	}
	
	
	
	
	
	
	
	
	
	
	/**
	 * 更新情報を表示する
	 * @param array $ent エンティティ
	 */
	public function updateInfo($ent){
		
		echo "<table class='tbl_sm'><tbody>\n";
		

		$this->_updateInfoTr($ent,'id','ID');
		$this->_updateInfoTr($ent,array('update_user','user_name','user','updater','modified_user'),'前回更新者');
		$this->_updateInfoTr($ent,array('update_ip_addr','ip_addr','user_ip_addr'),'前回更新IPアドレス');
		$this->_updateInfoTr($ent,'created','生成日時');
		$this->_updateInfoTr($ent,'modified','前回更新日時');
		
		echo "</tbody></table>\n";
	}
	private function _updateInfoTr($ent,$field,$fieldName){
		
		$ary = array();
		if (!is_array($field)){
			$ary[] = $field;
		}else{
			$ary = $field;
		}
		
		foreach($ary as $f){
			if(!empty($ent[$f])){
				echo "<tr><td>{$fieldName}</td><td>{$ent[$f]}</td></tr>\n";
				break;
			}
		}
	}
	
	
	
	
	
	/**
	 * 日時選択肢リストを取得する
	 * 
	 * @return array 日時選択肢リスト
	 */
	private function getDateTimeList(){

		
		if(!empty($this->_dateTimeList)){
			return $this->_dateTimeList;
		}
			
		$d1=date('Y-m-d');//本日
		$d2=$this->getBeginningWeekDate($d1);//週初め日付を取得する。
		$d3 = date('Y-m-d', strtotime("-10 day"));//10日前
		$d4 = $this->getBeginningMonthDate($d1);//今月一日を取得する。
		$d5 = date('Y-m-d', strtotime("-30 day"));//30日前
		$d6 = date('Y-m-d', strtotime("-50 day"));//50日前
		$d7 = date('Y-m-d', strtotime("-100 day"));//100日前
		$d8 = date('Y-m-d', strtotime("-180 day"));//180日前
		$d9 = $this->getBeginningYearDate($d1);//今年元旦を取得する
		$d10 = date('Y-m-d', strtotime("-365 day"));//365日前
			
		$list= array(
				$d1=>'本日',
				$d2=>'今週（日曜日から～）',
				$d3=>'10日以内',
				$d4=>'今月（今月一日から～）',
				$d5=>'30日以内',
				$d6=>'50日以内',
				$d7=>'100日以内',
				$d8=>'半年以内（180日以内）',
				$d9=>'今年（今年の元旦から～）',
				$d10=>'1年以内（365日以内）',
		);
		
		$this->_dateTimeList = $list;
	
		return $list;
			
	}
	
	/**
	 * 引数日付の週の週初め日付を取得する。
	 * 週初めは日曜日とした場合。
	 * @param $ymd
	 * @return DateTime 週初め
	 */
	private function getBeginningWeekDate($ymd) {
			
		$w = date("w",strtotime($ymd));
		$bwDate = date('Y-m-d', strtotime("-{$w} day", strtotime($ymd)));
		return $bwDate;
			
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
	
	/**
	 * 引数日付から元旦日を取得する。
	 * @param $ymd
	 */
	private function getBeginningYearDate($ymd) {
	
		$y = date("Y",strtotime($ymd));
		$d=$y.'-01-01';
			
		return $d;
	
	}
	
	
	/**
	 * スネークケースにキャメルケースから変換
	 * @param string $str キャメルケース
	 * @return string スネークケース
	 */
	private function snakize($str) {
		$str = preg_replace('/[A-Z]/', '_\0', $str);
		$str = strtolower($str);
		return ltrim($str, '_');
	}
	
	
	
	private function _check1(){
		if(empty($this->_mdl)){
			throw new Exception('setModelNameの呼出しが事前に必要です。');
		}
	}
	
	
	/**
	 * td要素出力を列並モードに対応させる
	 * @param array $field_data フィールドデータ
	 */
	public function startClmSortMode($field_data){
		$this->_clmSortMode = 1; // 列並モード ON
		$this->_field_data = $field_data; // フィールドデータをセット
		
	}
	
	
	/**
	 * 列並に合わせてTD要素群を出力する
	 */
	public function tdsEchoForClmSort(){

		foreach($this->_field_data as $f_ent){
			$field = $f_ent['id'];
			if(!empty($this->_clmSortTds[$field])){
				echo $this->_clmSortTds[$field];
			}
		}
		
		// クリア
		$this->_clmSortTds = array();
		
	}
	
	/**
	 * 列並用TD要素群にTD要素をセット
	 * 
	 * 列並モードがOFFならTD要素をそのまま出力する。
	 * 
	 * @param string $td TD要素文字列
	 * @param string $field フィールド名
	 */
	private function setTd($td,$field){
		if($this->_clmSortMode && !empty($field) ){
			$this->_clmSortTds[$field] = $td;
		}else{
			echo $td;
		}
	}
	
	
	/**
	 * シンプルなSELECT要素を作成
	 * @param string $name SELECTのname属性
	 * @param string $value 初期値
	 * @param array $list 選択肢
	 * @param array $option オプション  要素の属性情報
	 * @param array $empty 未選択状態に表示する選択肢名。nullをセットすると未選択項目は表示しない
	 * 
	 */
	public function selectX($name,$value,$list,$option=null,$empty=null){
		
		// オプションから各種属性文字を作成する。
		$optionStr = "";
		if(!empty($option)){
			foreach($option as $attr_name => $v){
				$str = $attr_name.'="'.$v.'" ';
				$optionStr.= $str;
			}
		}
		
		
		$def_op_name = '';
		
		echo "<select  name='{$name}' {$optionStr} >\n";
		
		if($empty!==null){
			$selected = '';
			if($value===null){
				$selected='selected';
			}
			echo "<option value='' {$selected}>{$empty}</option>\n";
		}
		
		foreach($list as $v=>$n){
			$selected = '';
			if($value==$v){
				$selected='selected';
			}
			
			$n = str_replace(array('<','>'),array('&lt;','&gt;'),$n);

			echo "<option value='{$v}' {$selected}>{$n}</option>\n";
			
		}
		
		echo "</select>\n";
	}
	
	
	/**
	 * シンプルなCHECKBOX要素を作成
	 * @param string $name CHECKBOXのname属性
	 * @param string $value 初期値
	 * @param array $option オプション  要素の属性情報
	 * 
	 */
	public function checkboxX($name,$value,$option=null){
		
		// オプションから各種属性文字を作成する。
		$optionStr = "";
		if(!empty($option)){
			foreach($option as $attr_name => $v){
				$str = $attr_name.'="'.$v.'" ';
				$optionStr.= $str;
			}
		}
		
		$checked = '';
		if(!empty($value)){
			$checked = 'checked';
		}
		
		echo "<input type='checkbox' name='{$name}' {$checked} {$optionStr} />\n";
		
	}
	
	
	/**
	 * 配列型用RADIO要素を作成
	 * @param string $name RADIOのname属性
	 * @param string $value 初期値
	 * @param array $list 選択肢
	 * @param array $option オプション  要素の属性情報
	 * 
	 */
	public function radioForMult($name,$value,$list,$option=null){
		
		// オプションから各種属性文字を作成する。
		$optionStr = "";
		if(!empty($option)){
			foreach($option as $attr_name => $v){
				$str = $attr_name.'="'.$v.'" ';
				$optionStr.= $str;
			}
		}
		
		
		$def_op_name = '';
		
		echo "<select name='{$name}' {$optionStr} >\n";
		

		
		foreach($list as $v=>$n){
			$selected = '';
			if($value===$v){
				$selected='selected';
			}
			$n = str_replace(array('<','>'),array('&lt;','&gt;'),$n);
			echo "<option value='{$v}' {$selected}>{$n}</option>\n";
			
		}
		
		echo "</select>\n";
	}
	
	
	
	
	
	
	
	/**
	 * グループ分類SELECT要素を作成する
	 * @param int $x_name name属性
	 * @param string $value 初期の値
	 * @param array $grpList グループ分類リスト
	 * 	- グループ分類リストの構造例
	 * 	(int) 17 => array(
	 *		'label' => '桃太郎',
	 *		'optgroup_value' => 98,
	 *		'list' => array(
	 *			(int) 118 => 'body1.png',
	 *			(int) 119 => 'eye1.png',
	 *	(int) 22 => array(
	 *		'label' => '怪しい影',
	 *		'optgroup_value' => 99,
	 *		'list' => array(
	 *			(int) 144 => 'silhouette.png',
	 * @param array $option オプション。主にSELECT要素の各種属性値
	 * - empty 未選択のテキストをセットする。（値要素は空値である。）
	 */
	public function selectOptgroup($x_name,$value,$grpList,$param=null){
		
		
		// オプションからselect要素の属性群文字列を作成する
		$attr_str = "";
		$empty = null;
		if($param){
			foreach($param as $attr_key => $attr_value){
				if($attr_key == 'empty'){
					$empty = $attr_value;
					continue;
				}
				$attr_str .= ' '.$attr_key.'="'.$attr_value.'"';
			}
		}
		
		// ヘッド部分を作成
		$h_head = "<select name=\"{$x_name}\" {$attr_str}>\n";
		
		
		
		// 未選択部分の作成
		$h_data = "";
		if(!empty($empty)){
			$h_data = "<option value=\"\">".$empty."</option>\n";
		}
		
		// リスト部分を作成
		foreach($grpList as $c_i =>$ent){
			
			// グループラベルを取得、およびoptgroup要素を組み立て
			$label = $ent['label'];
			if(empty($label)){
				$label = '未分類';
			}
			
			// グループオプション属性の組み立て
			$optgroup_value_str = "";
			if(isset($ent['optgroup_value'])){
				$optgroup_value_str = "data-value = '{$ent['optgroup_value']}'";
			}
			
			$h_data .= "<optgroup label=\"{$label}\" {$optgroup_value_str}>\n";
			
			// option要素を組み立てる
			$list = $ent['list'];
			foreach($list as $opt_val => $opt_name){
				$selected = "";
				if($opt_val == $value){
					$selected = "selected";
				}
				
				$h_data .= "<option value=\"{$opt_val}\" {$selected}>{$opt_name}</option>\n";
			}
			
			$h_data .= "</optgroup>\n";
			
		}
		
		
		// フッター部分を追加
		$html = $h_head.$h_data."</select>\n";
		
		return $html;
		
		
	}
	
	
	
	/**
	 * CSVボタンとそれに属するプルダウンメニューを作成する
	 * @param string $csv_dl_url
	 */
	public function makeCsvBtns($csv_dl_url){
		
		$html = "
		<a href='{$csv_dl_url}' class='btn btn-default btn-xs'><span class='glyphicon glyphicon-save'></span>CSVエクスポート</a>
		<input type='button' value='CSVインポート' class='btn btn-default btn-xs' onclick='jQuery(\"#csv_fu_div\").toggle(300);' />
		<div id='csv_fu_div' style='display:none'><input type='file' id='csv_fu' /></div>
		";
		
		echo $html;
		
	}
	
	
	/**
	 * Cakeに対応したhidden要素を作成
	 *
	 * @param string $field 要素のフィールド（キー）
	 * @param string $value 値
	 */
	public function hiddenX($field,$value){
	    
	    echo $this->input($this->_mdl.$field, array(
	        'id' => $field,
	        'value' => $value,
	        'type' => 'hidden',
	    ));
	    
	}
	
	
	
	
	
	
	
	
	
}