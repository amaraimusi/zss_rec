/**
 * SectionEditK.js | div内の節文を編集可能にし、その節文をAjaxで送受信する
 * 
 * @version 1.2
 * zss_recのみxssSanitaizeDecodeにてバグ
 * @date 2016-5-25 v1.2 XSS記号を含む文字が送信できないバグを修正
 * @param string parentElem 親要素
 * @param array dataElems 値要素群
 * @param string save_url Ajax通信先のURL(保存用）
 * @param string read_url Ajax通信先のURL(読込用） ※省略時は読込表示機能はなし。
 * 
 */
function sectionEditK(parentElem,dataElems,save_url,read_url){
	
	var sek = new SectionEditK();
	
	// 編集更新機能
	sek.sekEdit(parentElem,dataElems,save_url);
	
	// 読込表示機能
	if(read_url != false){
		sek.sekRead(parentElem,dataElems,read_url);
	}
}

/**
 * SectionEditKクラス
 * 
 * SectionEditK.jsは指定したdiv要素内を文章を編集可能にし、その文章をAjaxで送信することができる。
 * 主に2種類の機能があり、読込表示機能（sekRead）と編集更新機能（sekEdit）がある。
 * 
 * 読込表示機能
 * Ajaxで取得したデータをページに表示する機能である。
 * 少ないコードでこの機能を実装することができる。
 * 
 * 編集更新機能
 * div要素内を編集可能にし、さらに更新ボタンも追加する。
 * 更新ボタンを押すと、指定要素内の文章をAjaxでサーバーへ送信する。
 */
var SectionEditK = function(){


	/**
	 * 編集更新機能
	 * 
	 * div要素内を編集可能にし、さらに更新ボタンも追加する。
	 * 更新ボタンを押すと、指定要素内の文章をAjaxでサーバーへ送信する。
	 * 
	 * @param string parentElem 親要素
	 * @param array dataElems 値要素
	 * @param string url Ajax通信先のURL
	 */
	this.sekEdit = function(parentElem,dataElems,url){
		
	
		// 値要素を入力可能にする。
		for(var i=0;i<dataElems.length;i++){
			var elem = dataElems[i];
			var elmObj = $(parentElem + ' ' + elem);
			
			// タグ名を取得する。
			var tag = elmObj.prop("tagName");
			
			// INPUT系要素以外なら入力可能にする。
			if(tag != 'INPUT' && tag != 'TEXTAREA' && tag != 'SELECT' ){
				// 入力可能にする。
				elmObj.attr('contenteditable','true');
			}
	
			
		}
		
		// 親要素の下側に更新ボタンを追加する。
		var parent_elm = parentElem.substring(1,parentElem.length);
		
	
		// ガジェット要素名を取得する
		var gadgets = getGadgetElems(parent_elm);
		var updateBtnId = gadgets.updateBtnId;
		var msgId =  gadgets.msgId;
		
		var btn_html="<div><input id='" + updateBtnId +"' type='button' value='更新' class='btn btn-primary btn-xs'>" + 
			"<div id='" + msgId +"' style='color:#05d67d'></div></div>";
		
		$(parentElem).append(btn_html);
		
		// 更新ボタンにクリックイベントを追加
		$('#' + updateBtnId).click(function(e){
			
			// 更新ボタンクリックイベント
			updateBtnClick(parentElem,dataElems,url,updateBtnId,msgId);
		});
		
		
	}
	
	
	
	
	/**
	 * 読込表示機能
	 * 
	 * Ajaxで取得したデータをページに表示する機能である。
	 * 少ないコードでこの機能を実装することができる。
	 * 
	 * @param string parentElem 親要素
	 * @param array dataElems 値要素
	 * @param string url Ajax通信先のURL
	 */
	this.sekRead = function(parentElem,dataElems,url){
		
		// ガジェット要素名を取得する
		var parent_elm = parentElem.substring(1,parentElem.length);
		var gadgets = getGadgetElems(parent_elm);
		var updateBtnId = '#' + gadgets.updateBtnId;
		var msgId = '#' + gadgets.msgId;
		
	
		
		$(updateBtnId).hide(); // 更新ボタンを隠す
		$(msgId).html('Now loading...');
	
		// 値要素からデータを取得する
		var data = getDataFromElem(parentElem,dataElems);
		
		// データセットに親要素とデータをセットする
		var dataSet = {};
		dataSet['parent'] = parentElem;
		dataSet['data'] = data;
	
		var json_str = JSON.stringify(dataSet);//データをJSON文字列にする。
	
		//☆AJAX非同期通信
		$.ajax({
			type: "POST",
			url: url,
			data: "key1="+json_str,
			cache: false,
			dataType: "text",
			success: function(str_json, type) {
	
	
				var data;
				try{
					data=$.parseJSON(str_json);//パース

					// 値要素群にデータをセットする
					setDataToElem(parentElem,dataElems,data);
					
					$(msgId).html('');
				}catch(e){
					var err = "sekRead:jsonパース失敗：<span style='color:red'>" + str_json + "</span>";
					$(msgId).html(err);
					
				}
	
				$(updateBtnId).show(); //更新ボタンを再表示する
	
			},
			error: function(xmlHttpRequest, textStatus, errorThrown){
				$(msgId).html(xmlHttpRequest.responseText);//詳細エラーの出力
				alert(textStatus);
				
				$(updateBtnId).show(); //更新ボタンを再表示する
				
				
			}
		});
	
		
	}
	
	
	
	
	/**
	 * ガジェット要素名リストを取得する
	 * 
	 * @param string parent_elm 親要素名
	 * @return object ガジェット要素名リスト
	 */
	function getGadgetElems(parent_elm){
	
		var gadgets ={
				'updateBtnId':parent_elm + '_update_btn_sek',
				'msgId':parent_elm + '_msg_sek'
		};
	
		return gadgets;
		
	}
	
	
	/**
	 * 更新ボタンクリックイベント
	 * @param string parentElem 親要素
	 * @param array dataElems 値要素
	 * @param string url Ajax通信先のURL
	 * @param string updateBtnId 更新ボタンセレクタ
	 * @param string msgId メッセージセレクタ
	 */
	function updateBtnClick(parentElem,dataElems,url,updateBtnId,msgId){
		
		updateBtnId = '#' + updateBtnId;
		msgId = '#' + msgId;
		
		
		$(updateBtnId).hide(); // 更新ボタンを隠す
		$(msgId).html('Now update...');
		
	
		// 値要素からデータを取得する
		var data = getDataFromElem(parentElem,dataElems);

		// データセットに親要素とデータをセットする
		var dataSet = {};
		dataSet['parent'] = parentElem;
		dataSet['data'] = data;
	
		var json_str = JSON.stringify(dataSet);//データをJSON文字列にする。
	

		//☆AJAX非同期通信
		$.ajax({
			type: "POST",
			url: url,
			data: "key1="+json_str,
			cache: false,
			dataType: "text",
			success: function(str_json, type) {
	
	
				var data;
				try{
					data=$.parseJSON(str_json);//パース
					
					// 値要素群にデータをセットする
					setDataToElem(parentElem,dataElems,data);
					
					$(msgId).html('');
				}catch(e){
					var err = "jsonパース失敗：<span style='color:red'>" + str_json + "</span>";
					$(msgId).html(err);
				}
	
				$(updateBtnId).show(); //更新ボタンを再表示する
	
			},
			error: function(xmlHttpRequest, textStatus, errorThrown){
				$(msgId).html(xmlHttpRequest.responseText);//詳細エラーの出力
				alert(textStatus);
				
				$(updateBtnId).show(); //更新ボタンを再表示する
				
			}
		});
	}
	
	
	
	/**
	 * 値要素群からデータを取得する
	 * @param string 親要素
	 * @param array 値要素配列
	 * @return object データ
	 * 
	 */
	function getDataFromElem(parentElem,dataElems){
	
		var data = {};//データ
		
		// 値要素群をループして、データを取得する
		for(var i=0;i<dataElems.length;i++){
			var elem = dataElems[i];
			var elmObj = $(parentElem + ' ' + elem);
			
			// キーを取得する
			var key = elem.substring(1,elem.length);
			
			// タグ名を取得する。
			var tag = elmObj.prop("tagName");
	
			// 値を取得する
			var val;
			if(tag == 'INPUT' || tag == 'TEXTAREA' || tag == 'SELECT' ){
				val = elmObj.val();
			}else{
				val = elmObj.html();
			}
			
			// XSSサニタイズされているとAjax送信されないので、XSSデコードを施す。
			val = xssSanitaizeDecode(val);

			
			// データへ追加
			data[key] = val;
		}
		
		return data;
	}
	
	/**
	 * 値要素群にデータをセットする
	 * 
	 * @param string 親要素
	 * @param array 値要素配列
	 * @param object data データ
	 */
	function setDataToElem(parentElem,dataElems,data){
		// 値要素群をループして、データして値要素に値をセットする。
		for(var i=0;i<dataElems.length;i++){
			var elem = dataElems[i];
			var elmObj = $(parentElem + ' ' + elem);
			
			// キーを取得する
			var key = elem.substring(1,elem.length);
			
			// データからキーに紐づく値を取得する
			var val = null;
			if (data[key] !== undefined){
				val = data[key];
			}
			
			// タグ名を取得する。
			var tag = elmObj.prop("tagName");
			
			if (val !== null){
				// 値を値要素にセットする
				if(tag == 'INPUT' || tag == 'TEXTAREA' || tag == 'SELECT' ){
					elmObj.val(val);
				}else{
					elmObj.html(val);
				}
			}
	
	
	
		}
	}
	
	//XSSサニタイズデコード
	function xssSanitaizeDecode(str){
		
		// 応急処置
		try{
			return str.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#39;/g, '\'').replace(/&#039;/g, '\'');
		}catch(e){
			return str;
		}
		
	}


	// XSSサニタイズ
	function xssSanitaizeEncode(str){
		return str.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
	}
	
	
}






























