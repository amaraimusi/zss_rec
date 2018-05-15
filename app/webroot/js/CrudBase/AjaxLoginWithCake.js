/**
 * CakePHPによるAjax認証
 * 
 * @date 2016-9-12 | 2018-5-15
 */

var AjaxLoginWithCake = function(){
	
	/**
	 * 認証フォーム付
	 */
	this.loginCheckEx = function(callBack,option){

		var rGet = getUrlQuery();// GETパラメータを取得
		
		// aパラメータがONの場合に認証機能を有効にする。
		if(_isSet(rGet['a'])){
			
			// To Set value to empty value
			option = setOptionButEmpty(option);
			
			this.loginCheck(callBack,formShow,option);
		}

	}
	
	/**
	 * 認証チェック
	 */
	this.loginCheck = function(callBack,formShowCb,option){
		
		var data={'dummy':1};

		var json_str = JSON.stringify(data);//データをJSON文字列にする。

		// AJAX
		$.ajax({
			type: "POST",
			url: option.path + "/ajax_login_with_cake/login_check",
			data: "key1="+json_str,
			cache: false,
			dataType: "text",
			success: function(str_json, type) {

				try{

					var res=$.parseJSON(str_json);//パース
					formShowCb(res,option);//フォームにログインボタンやメッセージを表示する
					callBack(res.auth_flg);// クライアントからのコールバック関数を実行する
				}catch(e){
					throw new Error(str_json);
					alert('エラー');
				}

			},
			error: function(xmlHttpRequest, textStatus, errorThrown){
				throw new Error(xmlHttpRequest.responseText);
				alert(textStatus);
			}
		});
		
	};
	
	// To Set value to empty value
	function setOptionButEmpty(option){
		
		if(option == undefined){
			option = {};
		}
		if(_isEmpty(option['form_slt'])){
			option['form_slt'] = "#ajax_login_with_cake";
		}
		
		if(_isEmpty(option['path'])){
			option['path'] = "/zss_rec";
		}
		
		return option;
	}
	
	/**
	 * フォームにログインボタンやメッセージを表示する
	 */
	function formShow(res,option){

		var form_slt = option['form_slt'];
		var formElm = $(form_slt);
		
		
		if(res.auth_flg == 1 ){
			formElm.html("<span class='text-success'>認証中です </span><a href='users/logout' id='logout_btn' class='btn btn-default btn-xs'>ログアウト</a>");
		}
		
		else{
			var loginRapUrl = option.path + "/ajax_login_with_cake/login_rap";
			var loginBtnHtml = "<a href='" + loginRapUrl + "' class='btn btn-primary' >ログイン</a>";
			formElm.html(loginBtnHtml);
		}
		
	}
	
	
	
	/**
	 * URLクエリデータを取得する
	 * 
	 * @return object URLクエリデータ
	 */
	function getUrlQuery(){
		query = window.location.search;
		
		if(query =='' || query==null){
			return {};
		}
		var query = query.substring(1,query.length);
		var ary = query.split('&');
		var data = {};
		for(var i=0 ; i<ary.length ; i++){
			var s = ary[i];
			var prop = s.split('=');
			
			data[prop[0]]=prop[1];
	
		}	
		return data;
	}
	
	/**
	 * 空チェック
	 */
	function _isEmpty(v){
		if(v =='' || v==null || v == false){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 値セットチェック
	 */
	function _isSet(v){
		if(v =='' || v==null || v == false){
			return false;
		}else{
			return true;
		}
	}
	
	
};