

<h2>DB情報</h2>



<h3>テーブル一覧</h3>
<ol>
	<?php 
	foreach($tblList as $tbl){
		echo "<li><a href='#{$tbl}'>{$tbl}</a></li>";
	}
	?>
</ol>

<hr>
<?php 
$keys=array('Field','Type','Null','Key','Default','Comment');
foreach($tblList as $i=> $tbl){
	echo "<h3 id='{$tbl}'>{$tbl}</h3>";
	
	$fieldData=$fieldData2[$i];
	$trs='';
	foreach($fieldData as $ent){
		$tds='';
		foreach($keys as $k){
			$td="<td>{$ent[$k]}</td>";
			$tds.=$td;
		}
		$trs.="<tr>{$tds}</tr>";
	}
	
	echo 
		"<table class='table'>".
		" <thead>".
		" 	<tr>".
		" 		<th>フィールド</th><th>型</th><th>Null</th><th>主キー</th><th>デフォルト</th><th>コメント</th>".
		" 	</tr>".
		" </thead>".
		" <tbody>".
		$trs.
		" </tbody>".
		" </table>";
	
	
	echo "<hr>";
	
}
?>


<table class='table'>
<thead>
	<tr>
		<th>フィールド</th><th>型</th><th>Null</th><th>主キー</th><th>デフォルト</th><th>コメント</th>
	</tr>
</thead>
<tbody>
</tbody>
</table>