<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset("utf-8"); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css(array(
				'bootstrap.min.css',
				'bootstrap-theme.min.css',
				'jquery-ui.min.css',
				'Layouts/default',
				'common1'
		));

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->Html->script(array(
			'jquery-2.1.4.min.js',
			'jquery-ui.min.js',
			'bootstrap.min.js',
			'Layouts/default',
			));

		echo $this->fetch('script');
	?>


</head>
<body>

	<div class="container">

		<div id="header">
			<div class="row">
				<div  class="col-lg-12" >
					<h1>
						<?php
						if(empty($main_title)){
							echo 'CAKE DEMO';
						}else{
							echo $main_title;
						}
						?>
					</h1>

				</div>

			</div>
			<div class="row">
				<div  class="col-xs-12" style="text-align:right" >
					<?php
					if($logout_flg==true){
						echo $this->Html->link('ログアウト', '/users/logout');
					}
					?>
				</div>
			</div>
		</div>

		<?php echo $this->Session->flash(); ?>

		<?php echo $this->fetch('content'); ?>

		<div class="row">
			<div id="footer"  class="col-md-12" style="text-align:center">
				(c)wacgance 2015
			</div>
		</div>
		<?php echo $this->element('sql_dump'); ?>
	</div>




</body>
</html>
