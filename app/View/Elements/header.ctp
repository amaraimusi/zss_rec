	<div class="navbar navbar-default navbar-fixed-top">
	  <div class="container">
		<div class="navbar-header">
		  <?php echo $this->Html->link('CakeDemo', '/', array('class' => 'navbar-brand', 'escape' => false)); ?>
		  <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		</div>
		<div class="navbar-collapse collapse" id="navbar-main">
<?php 

?>
			<ul class="nav navbar-nav ">
				<?php 
				// ユーザー権限が管理者以上であるなら「ユーザー管理」画面へのリンクを表示する
				if(!empty($userInfo['authority']['level'])){
					$level = $userInfo['authority']['level'];
					if($level >= 30){
						echo "<li><a href='user_mng'>ユーザー管理</a></li>";
					}
				}
				?>
				<li class="top_menu_pull" style="z-index:2;">
					<a href="#" >設定</a>
					<ul>
						<li>XXX</li>
						<li>XXX</li>
					</ul>
				</li>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				<li class="top_menu_pull" style="z-index:2;">
				<?php if(!empty($userInfo)){
					echo "<a href='#'>{$userInfo['username']}</a>";
					echo "<ul>";
					echo "<li>{$userInfo['authority']['wamei']}</li>";
					echo "</ul>";
				} ?>

				
				<li><?php echo $this->Html->link('ログアウト', '/users/logout'); ?></li>
			</ul>
		</div>
	  </div>
	</div>
