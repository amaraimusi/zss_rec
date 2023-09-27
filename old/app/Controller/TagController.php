<?php
App::uses('AppController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * タグ
 * ★履歴
 * 2015/2/14	新規作成
 * @author k-uehara
 *
 */
class TagController extends AppController {


	public $name = 'Tag';
	public $uses = array('Tag','Category','Category2');
	//public $components=null;//ログイン認証不要
	//public $logout_flg=false;//ログアウトリンクを非表示

	///検索条件定義
	public $kensakuJoken=array(
			array('name'=>'kj_name','def'=>null),
			array('name'=>'kj_del_flg','def'=>0),
			array('name'=>'kj_limit','def'=>30),
	);


	///検索条件のセッション保存フラグ//■■■□□□■■■□□□
	public $kj_session_flg=false;

	///検索条件のバリデーション
	public $kjs_validate = array(


		'kj_name' => array(
				'maxLength'=>array(
						'rule' => array('maxLength', 50),
						'message' => 'タグは50文字以内で入力してください。',
						'allowEmpty' => true
				)
		),

	);



	///編集エンティティ定義
	public $entity_info=array(

			array('name'=>'id','def'=>null),
			array('name'=>'name','def'=>null),
			array('name'=>'del_flg','def'=>0),
			array('name'=>'updated','def'=>0),
			
	);

	///編集用バリデーション
	public $edit_validate = array(

			'name' => array(
					'maxLength'=>array(
							'rule' => array('maxLength', 50),
							'message' => 'タグは50文字以内で入力してください。',
							'allowEmpty' => true
					)
			),



	);




	// -- ▽ 内部処理用
	private $m_kj_keys;//検索条件キーリスト
	private $m_kj_defs;//検索条件デフォルト値
	private $m_edit_keys;//編集エンティティキーリスト
	private $m_edit_defs;//編集エンティティのデフォルト値

    public function index() {


    	//session_start();


    	//サニタイズクラスをインポート
    	App::uses('Sanitize', 'Utility');

    	//検索条件情報をPOST,GET,SESSION,デフォルトのいずれから取得。
		$kjs=$this->getKjs('Tag');

    	//パラメータのバリデーション
    	$errMsg=$this->valid($kjs,$this->kjs_validate);

    	//入力エラーがあった場合。
    	if(isset($errMsg)){
    		//再表示用の検索条件情報をSESSION,あるいはデフォルトからパラメータを取得する。
    		$kjs= $this->getKjsSD('Tag');
    	}


    	//検索ボタンが押され場合
    	if(!empty($this->data['search'])){

    		//ページネーションパラメータを取得
			$pageParam=$this->getPageParamForSubmit($kjs);


    	}else{
    		//ページネーション用パラメータを取得
			$pageParam=$this->getPageParam();
			if(empty($pageParam['limit'])){
				$pageParam['limit']=$kjs['kj_limit'];
			}

    	}


		//削除ボタンが押された場合の処理
		if(!empty($this->data['del_submit'])){
			//削除アクション
    		$this->delAction();

		}



    	//セッションにパラメータをセット
		$this->setParamToSession($kjs,'Tag');

    	//limitとorder部分を作成
    	$pg=new PagenationForCake();
    	$los=$pg->createLimitAndOrder($pageParam);

    	$data=$this->Tag->findData($kjs,$pageParam['page_no'],$pageParam['limit'],$los['find_order']);



    	//////////////////  ページネーション情報を取得 //////////////
    	$baseUrl=$this->webroot.'tag';
    	$dataCnt=$this->Tag->findDataCnt($kjs);//検索データ数を取得



    	//HTMLテーブルのフィールド
    	$fields=array(
    			'Tag.id'=>'ID',
    			'Tag.name'=>'タグ名',
    			'Tag.del_flg'=>'有無',
    			'Tag.updated'=>'更新日',
    	);
    	//HTMLテーブルのフィールド
    	$pages=$pg->createPagenationData($dataCnt,$baseUrl , null,$fields);
    	$page_first_link=$pg->FirstLink;
    	$page_prev_link=$pg->PrevLink;
    	$page_next_link=$pg->NextLink;
    	$page_last_link=$pg->LastLink;


    	//カテゴリ1選択肢リストを取得
    	$categoryOptions=$this->Category->getCategoryOptions();


    	$this->set(array(
    			'title_for_layout'=>'ワクガンス農業記録　タグの編集（管理）',
    			'main_title'=>'ワクガンス農業記録　タグの編集（管理）',
    			'data'=> $data,
    			'kjs'=>$kjs,
    			'pages'=>$pages,
    			'page_first_link'=>$page_first_link,
    			'page_prev_link'=>$page_prev_link,
    			'page_next_link'=>$page_next_link,
    			'page_last_link'=>$page_last_link,
    			'errMsg'=>$errMsg,
    			'categoryOptions'=>$categoryOptions,
    	));


    }





    /**
     * 編集画面
     */
    public function edit() {

    	App::uses('Sanitize', 'Utility');//インクルード

    	$err=$this->Session->read('tag_err');
    	$this->Session->delete('tag_err');
    	$noData=false;
    	$ent=null;
    	$errMsg=null;
    	$mode=null;

    	//入力エラー情報が空なら通常の遷移
    	if(empty($err)){

    		$id=$this->getGet('id');//GETからIDを取得

    		//IDがnullなら新規登録モード
    		if(empty($id)){

    			//記録2エンティティにデフォルトをセットする。
    			$ent=$this->getDefaultEntity();

    			$mode='new';//モード（new:新規追加  edit:更新）

    			//IDに数値がある場合、編集モード。
    		}else if(is_numeric($id)){


    			//IDに紐づくエンティティをDBより取得
    			$ent=$this->Tag->findEntity($id);

 

    			$mode='edit';//モード（new:新規追加  edit:更新）

    		}else{

    			//数値以外は「NO DATA」表示
    			$noData=true;
    		}

    	}

    	//入力エラーによる再遷移の場合
    	else{

    		$ent=$err['ent'];
    		$mode=$err['mode'];
    		$errMsg=$err['errMsg'];

    	}

    	//カテゴリセレクト選択肢情報を取得
    	$categoryOptions1=$this->Category->getCategoryOptions();
    	$categoryOptions2=$this->Category2->getCategoryOptions();


    	$this->set(array(
    			'title_for_layout'=>'タグ',
    			'ent'=>$ent,
    			'noData'=>$noData,
    			'mode'=>$mode,
    			'errMsg'=>$errMsg,
    			'categoryOptions1'=>$categoryOptions1,
    			'categoryOptions2'=>$categoryOptions2,
    	));

    }




    //登録
    public function reg(){


    	//リロードチェック
    	App::uses('ReloadCheck','Vendor/Wacg');
    	$rc=new ReloadCheck();
    	if ($rc->check()!=1){//1以外はリロードと判定し、一覧画面へリダイレクトする。
    		return $this->redirect(array('controller' => 'tag', 'action' => 'index'));
    	}

    	App::uses('Sanitize', 'Utility');//インクルード


    	//POSTから記録2エンティティを取得する。
    	$ent=$this->getEntityFromPost();

    	$mode=$this->data['Tag']['mode'];


    	$errMsg=$this->valid($ent,$this->edit_validate);


    	if(isset($errMsg)){

    		//エラー情報をセッションに書き込んで、編集画面にリダイレクトで戻る。
    		$err=array('mode'=>$mode,'ent'=>$ent,'errMsg'=>$errMsg);
    		$this->Session->write('tag_err',$err);
    		$this->redirect(array('action' => 'edit'));

    		return null;
    	}



    	//新規モードの場合、新IDを取得する。
    	if(empty($ent['id'])){
    		$mode='new';//モード（new:新規追加  edit:更新）
    		$ent['id']=$this->Tag->findNewId();//新IDを取得する。
    	}else{
    		$mode='edit';//モード（new:新規追加  edit:更新）
    	}


    	$regMsg="<p id='reg_msg'>更新しました。</p>";

    	//更新する。
    	$this->Tag->begin();//トランザクション開始
    	$this->Tag->saveEntity($ent);//登録
    	$this->Tag->commit();//コミット

    	$this->set(array(
    			'title_for_layout'=>'タグ',
    			'ent'=>$ent,
    			'noData'=>false,
    			'mode'=>$mode,
    			'regMsg'=>$regMsg,
    	));

    }


    /**
     *
     * ノートの改行文字を<br>に変換
     * サニタイズされた改行コードを<br>にする。
     * 通常改行コードは\r\nであるが、Sanitaize::clearnなどを実行されると\\r\\nとなる。
     * @param $data
     */
    private function lineBreak($data){
    	foreach($data as $i=>$ent){
    		$note=$ent['Tag']['note'];
    		$note=str_replace('\\r\\n', '<br>', $note);
    		$data[$i]['Tag']['note']=$note;

    	}
    	return $data;
    }
    /**
     * サニタイズされた改行コードを「\r\n」に戻す
     * @param $ent
     */
    private function lineBreakForTextarea($ent){

    	//&#13;	改行コード

		$note=$ent['note'];
		$note=str_replace('\\r\\n', '&#13;', $note);
		$ent['note']=$note;


    	return $ent;
    }




    /**
     * 検索条件のバリデーション
     * @param  $data バリデーション対象データ
     * @param  $validate バリデーション情報
     * @return 正常な場合、nullを返す。異常値がある場合、エラーメッセージを返す。
     */
    private function valid($data,$validate){

    	$errMsg=null;
    	//▽バリデーション（入力チェック）を行い、正常であれば、改めて検索条件情報を取得。
    	$this->Tag->validate=$validate;

    	$this->Tag->set($data);
    	if (!$this->Tag->validates($data)){

			////入力値に異常がある場合。（エラーメッセージの出力仕組みはcake phpの仕様に従う）
    		$errors=$this->Tag->validationErrors;//入力チェックエラー情報を取得
    		if(!empty($errors)){

    			foreach ($errors  as  $err){

    				foreach($err as $val){

    					$errMsg.= $val.' ： ';

    				}
    			}

    		}

    	}

    	return $errMsg;
    }


    //POST,またはSESSION,あるいはデフォルトからパラメータを取得する。
    private function getKjs($formKey){

    	$def=$this->getDefKjs();//デフォルトパラメータ
    	$keys=$this->getKjKeys();//検索条件キーリストを取得
    	$kjs=$this->getParams($keys,$formKey,$def);

    	foreach($kjs as $k=>$v){
    		$kjs[$k]=trim($v);
    	}

    	return $kjs;


    }

    //検索条件キーリストを取得
    private function getKjKeys(){

    	if(empty($this->m_kj_keys)){
    		foreach($this->kensakuJoken as $ent){
    			$this->m_kj_keys[]=$ent['name'];
    		}
    	}

    	return $this->m_kj_keys;
    }

    //デフォルト検索条件を取得
    private function getDefKjs(){

	    if(empty($this->m_kj_defs)){
	    	foreach($this->kensakuJoken as $ent){
	    		$this->m_kj_defs[$ent['name']]=$ent['def'];
	    	}
	    }

	    return $this->m_kj_defs;

    }

    //SESSION,あるいはデフォルトからパラメータを取得する。
    private function getKjsSD($formKey){

    	$def=$this->getDefKjs();//デフォルトパラメータ
    	$keys=$this->getKjKeys();
    	$kjs=$this->getParamsSD($keys,$formKey,$def);

    	return $kjs;
    }

    //POSTからデータを取得。ついでにサニタイズする。
    private function getPost($key){
    	$v=null;
    	if(isset($this->data['Tag'][$key])){
    		$v=$this->data['Tag'][$key];
    		if(is_string($v)){
    			$v=Sanitize::escape($v);//SQLインジェクションのサニタイズ
    		}
    	}
    	return $v;
    }

    //ページネーション用パラメータを取得
    private function getPageParam(){
    	//GETよりパラメータを取得する。
    	$pageParam=$this->params['url'];

    	//空ならセッションから取得する。
    	if(empty($pageParam) && $this->kj_session_flg==true){
    		$pageParam=$this->Session->read('tag_page_param');
//     		if(!empty($_SESSION['tag_page_param'])){
//     			$pageParam=$_SESSION['tag_page_param'];
//     		}

    	}

    	$defs=$this->getDefKjs();//デフォルト情報を取得

    	//空ならデフォルトをセット
    	if(empty($pageParam)){
    		$pageParam['page_no']=0;
    		$pageParam['limit']=$defs['kj_limit'];
    		$pageParam['sort']='Tag.id';
    		$pageParam['sort_type']=0;//0:昇順 1:降順

    	}

    	//セッションに詰める。
    	if($this->kj_session_flg==true){
    		$this->Session->write('tag_page_param',$pageParam);//セッションへの書き込み
    		//$_SESSION['tag_page_param']=$pageParam;
    	}

    	return $pageParam;
    }

    //ページ用のパラメータを取得
    private function getPageParamForSubmit($kjs){
    	$d=$this->params['url'];
    	$d['limit']=$kjs['kj_limit'];
    	$d['page_no']=0;
    	if($this->kj_session_flg==true){
    		$this->Session->write('tag_page_param',$d);//セッションへの書き込み
    		//$_SESSION['tag_page_param']=$d;
    	}
    	return $d;
    }


    //削除アクション
    private function delAction(){

    	if(empty($this->request->data['Tag'])){
    		return;
    	}

    	$data=$this->request->data['Tag'];
    	//更新する。
    	foreach($data as $ent){
    		if($ent['sel']==1){
    			$this->Tag->del($ent['id']);//DBから削除
    		}
    	}


    }









	////////////共通処理///////////////////////////////////

    /**
     * //SESSION,あるいはデフォルトからパラメータを取得する。
     * @param  $keys	パラメータキーリスト
     * @param  $formKey	フォームキー
     * @param  $def		デフォルトパラメータ
     * @return パラメータ
     */
    private function getParamsSD($keys,$formKey,$def){

    	$ses=null;
    	if($this->kj_session_flg==true){
    		$ses=$this->Session->read($formKey);
    		//$ses=$_SESSION[$formKey];
    	}

    	$prms=null;
    	foreach($keys as $key){
    		$prms[$key]=$this->getParamSD($key, $formKey,$ses,$def);
    	}
    	return $prms;

    }

    //SESSION,あるいはデフォルトからパラメータを取得する。
    private function getParamSD($key,$formKey,$ses,$def){

    	$v=null;

    	if(isset($ses)){
    		$v=$ses[$key];
    	}else{
    		$v=$def[$key];
    	}

    	return $v;
    }

    /**
     * セッションにパラメータをセット
     */
    private function setParamToSession($kjs,$formKey){

    	if($this->kj_session_flg==true){
    		$this->Session->write($formKey,$kjs);//セッションへの書き込み
    		//$_SESSION[$formKey]=$kjs;
    	}
    }


    /**
     * POST,GET,SESSION,デフォルトのいずれかからパラメータリストを取得する。
     * @param  $keys		キーリスト
     * @param  $formKey	フォームキー
     * @param  $def		デフォルトパラメータ
     * @return パラメータリスト
     */
    private function getParams($keys,$formKey,$def){

    	$ses=null;
    	if($this->kj_session_flg==true){
    		$ses=$this->Session->read($formKey);//セッションのパラメータを取得
//     		if(!empty($_SESSION[$formKey])){
//     			$ses=$_SESSION[$formKey];
//     		}





    	}

    	$prms=null;
    	foreach($keys as $key){
    		$prms[$key]=$this->getParam($key, $formKey,$ses,$def);
    	}
    	return $prms;
    }

    /**
     * POST,GET,SESSION,デフォルトのいずれかからパラメータを取得する。
     * @param  $key	パラメータのキー
     * @param  $formKey	フォームキー
     * @param  $ses		セッションパラメータ
     * @param  $def		デフォルトパラメータ
     *
     * @return パラメータ
     */
    private function getParam($key,$formKey,$ses,$def){
    	$v=null;

    	//POSTからデータ取得を試みる。
    	if(isset($this->data[$formKey][$key])){
    		$v=$this->data[$formKey][$key];
    		$v=Sanitize::escape($v);//SQLインジェクションのサニタイズ

    	}

    	//GETからデータ取得を試みる。
    	elseif(isset($this->params['url'][$key])){
    		$v=$this->params['url'][$key];
    		$v=Sanitize::escape($v);

    	}

    	//SESSIONからデータを読み取る。
    	elseif(isset($ses[$key])){
			$v=$ses[$key];
    	}

		//デフォルトのパラメータをセット
		else{
			$v=$def[$key];
		}


    	return $v;
    }















    ////////// 編集画面用 ///////////////////////



    //POSTからデータを取得。ついでにサニタイズする。
    private function getGet($key){
    	$v=null;
    	if(isset($this->params['url'][$key])){
    		$v=$this->params['url'][$key];
    		$v=Sanitize::escape($v);//SQLインジェクションのサニタイズ
    	}
    	return $v;
    }

    //デフォルトエンティティを取得する。
    private function getDefaultEntity(){


    	if(empty($this->m_edit_defs)){
    		foreach($this->entity_info as $ent){
    			$this->m_edit_defs[$ent['name']]=$ent['def'];
    		}
    	}

    	return $this->m_edit_defs;

    }

    //編集エンティティのキーリストを取得する。
    private function getKeysForEdit(){
    	if(empty($this->m_edit_keys)){
    		foreach($this->entity_info as $ent){
    			$this->m_edit_keys[]=$ent['name'];
    		}
    	}

    	return $this->m_edit_keys;
    }

    /**
     * アップロードファイルを保存する。
     * @param  $file_info アップロードファイル情報
     * @param string $path ファイル保存先フォルダ
     */
    private function uploadFileCopy($file_info,$path='img/RecCrud2'){

    	//アップロードする画像ファイルの名前を指定する。
    	$fn=$path.'/'.$file_info['name'];

    	//一時ファイルからコピー
    	move_uploaded_file($file_info['tmp_name'], $fn);

    }





    ////////// 登録結果画面用 ///////////////////////

    //POSTからエンティティを取得する。
    private function getEntityFromPost(){

    	$keys=$this->getKeysForEdit();
    	foreach($keys as $key){
    		$v=$this->getPost($key);
    		if(is_string($v)){
    			$ent[$key]=trim($v);
    		}
    		//ファイルアップロード系の場合
    		else{

    			$ent[$key]=$v;

    		}

    	}

    	return $ent;
    }




}