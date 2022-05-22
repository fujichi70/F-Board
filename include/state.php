	<?php if (!empty($_SESSION['info']) && !empty($_SESSION['name'])) : ?>
		<div class="alert"><img src="./img/login.png" alt="ログインしました。"></div>
		<p class="info-text">ようこそ！ <?php echo h($_SESSION['name']); ?> さん</p>
		<?php $_SESSION['info'] = null; ?>
	<?php elseif (!empty($_SESSION['info'])) : ?>
		<div class="alert"><img src="./img/logout.png" alt="ログアウトしました。"></div>
		<?php $_SESSION['info'] = null; ?>
	<?php endif; ?>