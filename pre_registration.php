<?php

session_start();
require 'functions.php';
require 'functions_pre.php';

header('X-FRAME-OPTIONS:DENY');

setToken();

$errors = [];
$messages = [];

// トークン確認→バリデーション→仮登録
if (!empty($_POST['preregister-btn--confirm'])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$errors = checkToken();
		if (!empty($_SESSION['token'])) {
			$errors = validationPreUser($_POST);
			if (empty($errors)) {
				$messages = insertPreUser($_POST);
			}
		}
	}
}

?>


<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>F Free Board - 仮登録画面</title>
	<link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="wrapper">

		<!-- ページ共通（ナビバー） -->
		<?php include "./include/header.php"; ?>

		<?php if (empty($_SESSION['id'])) : ?>
			<div class="preregister-box">
				<h2 class="common-title">新規会員登録</h2>
				<p class="preregister-text--free">登録も登録後もずっと無料で利用可能！</p>

				<form method="post" action="">
					<dl class="input-area">
						<dt><label class="text" for="email" class="info"><span class="min">メール</span>アドレス</label></dt>
						<dd>
							<input class="input" type="email" name="new_email" placeholder="aaa@test.com" value="<?php if (!empty($_POST['new_email'])) {
																														$new_email = h($_POST['new_email']);
																														$new_email = i($new_email);
																														echo $new_email;
																													} ?>">
						</dd>
					</dl>
					<?php if (!empty($errors['new_email']) && !empty($_POST['register-btn--confirm'])) {
						echo '<p class="error">' . $errors['new_email'] . '</p>';
					} ?>
					<div class="btn-parts"><input type="submit" name="preregister-btn--confirm" class="btn" value="確認メール送付"></div>
				</form>


				<p class="preregister-text--explain">アドレス入力→確認メール受信→メールのURLにアクセス→本登録→登録完了！</p>

				<div class="other-btn">
					<a class="btn back" href="main.php">トップページに戻る</a>
					<a class="btn" href="login.php">会員の方はこちら</a>
				</div>

				<?php if (!empty($messages)) : ?>
					<p>
						<?php
						foreach ($messages as $message) {
							echo '<p class="error">' . $message . '</p>';
						}
						?>
					</p>
				<?php endif; ?>

			</div><!-- .preregister-box -->
		<?php else : ?>
			<p>ログイン中です。ログアウトしますか？</p>
			<div id="logout">
				<form action="logout.php" method="POST">
					<input type="submit" name="logout" value="ログアウト" class="btn">
				</form>
			</div>
		<?php endif; ?>
	</div><!-- .wrapper -->

	<div class="main-img">
		<img src="img/cheers.jpg" alt="コーヒーの写真">
	</div>


	<script>
		$(function() {
			// ハンバーガーメニュー
			$('.hamburger').click(function() {
				$(this).toggleClass('active');

				if ($(this).hasClass('active')) {
					$('.header-menu').addClass('active');
				} else {
					$('.header-menu').removeClass('active');
				}
			});
		});
	</script>
</body>

</html>