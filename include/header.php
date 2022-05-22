	<header id="header">
		<div class="header-flex">
			<!-- <div class="title-box"> -->
				<a class="title-box" href="main.php">
					<h1 class="main-title">F Free Board</h1>
					<p class="sub-title">なんでもお話し・相談できるサイト</p>
				</a>
			<!-- </div> -->
			<div class="hamburger">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>

		<nav class="header-menu">
			<ul>
				<?php if (!empty($_SESSION['id'])) : ?>
					<li>
						<h2 class="profile-title">プロフィール</h2>
						<div class="profile-box">
							<div class="profile-text">
								<p class="profile-name"><?php echo h($_SESSION['name']); ?>さん</p>
								<a href="edit.php" class="edit-btn">編集</a>
							</div>
							<div class="profile-img"><img class="profile-img--parts" src="<?php echo h($_SESSION['img_path']); ?>"></div>
							<form action="" method="post">
								<div class="logout-btn--parts"><input type="submit" name="logout" value="ログアウト" class="btn logout-btn"></div>
							</form>
						</div>
					</li>
				<?php endif; ?>
				<li><a class="header-list" href="main.php">TOP</a></li>
				<li><a class="header-list" href="recipe.php">レシピ相談</a></li>
				<li><a class="header-list" href="love.php">恋愛相談</a></li>
				<li><a class="header-list" href="couple.php">夫婦生活相談</a></li>
				<li><a class="header-list" href="childcare.php">育児相談</a></li>
				<li><a class="header-list" href="work.php">仕事相談</a></li>
				<li><a class="header-list" href="friends.php">友人関係相談</a></li>
			</ul>
		</nav>
	</header>