<?php
class RecXHelper extends AppHelper {
	
	
	/**
	 * タグリストからタグズ要素を生成する
	 * @param bool $auth_flg 認証フラグ 0：未認証 1:認証中
	 * @param array $tags タグリスト
	 * @return string タグズ要素(html)
	 */
	public function createTagsElm($auth_flg,$tags){
		$tagsElm="";
		
		// 認証中
		if(!empty($auth_flg)){
			// データ有り
			if(!empty($tags)){
				
				$label = "<label>タグ：</label>";// ラベルを表示する
				$tag_links = $this->makeTagLinks($tags);// タグリンク要素を表示
				$inp = $this->makeInpForTagsElm($tags);// 入力用要素を表示
				$tagsElm = $label . $tag_links . $inp;
				
			}
			
			// データ空
			else{
				$label = "<label>タグ：</label>";// ラベルを表示する
				$tag_links = '';// タグリンク要素は非表示
				$inp = $this->makeInpForTagsElm($tags);// 入力用要素を表示
				$tagsElm = $label . $tag_links . $inp;
			}
		}
		
		// 未認証
		else{
			// データ有り
			if(!empty($tags)){
				$label = "<label>タグ：</label>";// ラベルを表示する
				$tag_links = $this->makeTagLinks($tags);// タグリンク要素を表示
				$inp = '';// 入力用要素は表示しない
				$tagsElm = $label . $tag_links . $inp;
			}
			
			// データ空
			else{
				$label = '';// ラベルを表示しない
				$tag_links = '';// タグリンク要素は非表示
				$inp = '';// 入力用要素は表示しない
				$tagsElm = $label . $tag_links . $inp;
			}
			
		}
		
		
		
		return $tagsElm;
	}
	

	/**
	 * タグリンク要素を作成する
	 * 
	 * @param array $tags タグリスト
	 * @return string タグリンク要素
	 */
	private function makeTagLinks($tags){
		$tag_links='';
		foreach($tags as $tag_ent){
			$tag_links.="<a class='tags' href='rec_x?tag_search=1&kj_tag_id={$tag_ent['tag_id']}' >{$tag_ent['name']}</a>     ";
		}
		return $tag_links;
	}
	
	/**
	 * 入力用要素を表示
	 * @param array $tags タグリスト
	 * @return 入力用要素
	 */
	private function makeInpForTagsElm($tags){
		$tag_join='';
		if(!empty($tags)){
			$tagNames=[];
			foreach($tags as $tag_ent){
				$tagNames[] = $tag_ent['name'];
			}
			$tag_join = join(',',$tagNames);
		}

		$inp = "<span class='tags sec_inp_edit'>$tag_join</span>";
		
		return $inp;
	}
	
	
	
	
	
	
	


	/**
	 * 個番要素
	 * @param 認証フラグ $auth_flg
	 * @param 個番ID $probe_id
	 * @param 個名 $probe_name
	 * @param ツールチップ $tooltip
	 */
	public function createProbeElm($auth_flg,$probe_id,$probe_name,$tooltip){
		$probeElm="";
		
		// 認証中
		if(!empty($auth_flg)){
			// データ有り
			if(!empty($probe_id)){
		
				$link = $this->makeLinkForProbeElm($probe_id,$probe_name,$tooltip);// リンク要素を表示
				$inp = $this->makeInpForProbeElm($probe_id);// 入力要素を表示
				$probeElm = $link . $inp;
		
			}
				
			// データ空
			else{
				$link = '';// リンク要素は非表示
				$inp = $this->makeInpForProbeElm($probe_id);// 入力要素を表示
				$probeElm = $link . $inp;
			}
		}
		
		// 未認証
		else{
			// データ有り
			if(!empty($probe_id)){
				$link = $this->makeLinkForProbeElm($probe_id,$probe_name,$tooltip);// リンク要素を表示
				$inp = '';// 入力要素は表示しない
				$probeElm = $link . $inp;
			}
				
			// データ空
			else{
				$link = '';// リンク要素は非表示
				$inp = '';// 入力要素は表示しない
				$probeElm = $link . $inp;
			}
				
		}
		
		
		
		return $probeElm;
	}
	
	
	/**
	 * リンク要素を表示
	 * @param int $probe_id 個番
	 * @param string $probe_name 個名
	 * @return リンク要素
	 */
	public function makeLinkForProbeElm($probe_id,$probe_name,$tooltip){
		$webroot = $this->webroot;
		$link = "<label>個番：</label><a href='{$webroot}rec_x?probe_flg=1&kj_probe_id={$probe_id}' target='blank' title='{$tooltip}'>{$probe_id}:{$probe_name}</a>";
		return $link;
	}
	
	/**
	 * 入力要素を表示
	 * @param int $probe_id 個番
	 * @return 入力要素
	 */
	public function makeInpForProbeElm($probe_id){
		$inp = "<span class='probe_id sec_inp_edit'>{$probe_id}</span>";
		return $inp;
	}
	
	
	
	
}