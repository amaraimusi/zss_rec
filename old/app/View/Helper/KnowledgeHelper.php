<?php
/**
 * 心得画面用のヘルパークラス
 *
 */
class KnowledgeHelper extends AppHelper{
	
	public function tdKlText(&$ent,&$klCategoryList){
		$field = 'kl_text';
		$str_len = 2000;
		$v = $ent[$field];
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
		
		// 内容リンク
		$contents_url_html = "";
		if(!empty($ent['contents_url'])){
			$contents_url_html = "&nbsp;<a href='{$ent['contents_url']}' class='btn btn-info btn-xs livipage'>内容</a>";
		}
		
		// 文献
		$doc_html = "";
		if(!empty($ent['doc_text'])){
			$doc_text = $ent['doc_text'];
			$doc_text= str_replace("\n", '<br>', $doc_text);
			$doc_name = $ent['doc_name'];
			if(empty($doc_name)) $doc_name = '文献';
			$doc_xid = 'doc'.$ent['id'];
			$doc_html = "
				<a href='#{$doc_xid}' class='btn btn-info btn-xs livipage' >{$doc_name}</a>
				<div id='{$doc_xid}' style='display:none'>{$doc_text}</div>
			";
		}
		
		// 覚えボタン
		$learn_html = "
			&nbsp;<input type='button' value='覚:{$ent['level']}' 
			onclick='learnAction(this,{$ent['id']})' class='btn btn-primary btn-xs learn_btn' 
			style='display:none' /><span class='learned' style='display:none;color:gray'></span>";
		
		// カテゴリ　kl_category
		$kl_category = $ent['kl_category'];
		$kl_category_name = '';
		if(!empty($klCategoryList[$kl_category])){
			$kl_category_name = $klCategoryList[$kl_category];
		}
		
		
		// 出力
		echo "
			<td>
				<input type='hidden' name='{$field}' value='{$v}' />
				<span class='{$field}'>{$v2}</span>
				{$contents_url_html}{$doc_html}{$learn_html}
				&nbsp;<span style='color:#cccccc'>#{$kl_category_name}</span>
			</td>";

	}
	

}