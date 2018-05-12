<!DOCTYPE html>
<html lang="ja">
<head>
	<?php echo $this->Html->charset(); ?>
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="/sample/style2/css/bootstrap.min.css" rel="stylesheet">
		<link href="/sample/style2/css/common2.css" rel="stylesheet">

		<script src="/sample/style2/js/jquery-1.11.1.min.js"></script>
		<script src="/sample/style2/js/bootstrap.min.js"></script>
	
	<title>
		<?php 
			if(empty($title)){
				$title = 'ワクガンス 記録';
			}
			echo $title;
		?>
	</title>
	
	<?php
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="container">

		<div id="header">
			<h1><?php echo $title;?></h1>
		</div>
		
		
		
		
		
		
<?php echo $this->Flash->render(); ?>

<?php echo $this->fetch('content'); ?>

		
		
		
		
		
		
		
		<div id="footer">
			(C) kenji uehara 
			<?php 
				if(!empty($write_date)){
					echo ' '.$write_date;
				}
				$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version());
				echo ' '.$cakeVersion 
			?>
		</div>
		
	</div><!-- container -->
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
