<?php

require 'functions.php';
require 'functions_login.php';
require 'functions_logout.php';

session_start();

header('X-FRAME-OPTIONS: DENY');

setToken();

$errors = [];
$messages = [];

var_dump($_SESSION);
// トークン確認→バリデーション→ログイン
if (!empty($_POST['login-btn--confirm'])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		checkToken();
		setToken();
		$errors = validationLogin($_POST);
		if (empty($errors)) {
			$messages = login($_POST);
		}
	}
}

if (!empty($_POST['logout'])) {
	logout();
	setToken();
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>F Free Board - ログイン画面</title>
	<link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="wrapper">
		<!-- ページ共通（ナビバー） -->
		<?php include "./include/header.php"; ?>

		<?php if (empty($_SESSION['id'])) : ?>
			<div class="member-box">
				<h2 class="common-title">ログイン</h2>
				<form method="post" action="">
					<dl class="input-area">
						<dt><label class="text" for="email" class="info"><span class="min">メール</span>アドレス</label></dt>
						<dd>
							<input class="input" type="email" name="email" placeholder="aaa@test.com" value="<?php if (!empty($_POST['email'])) {
																													$email = h($_POST['email']);
																													$email = i($email);
																													echo $email;
																												} ?>">
						</dd>
					</dl>
					<?php if (!empty($errors['email']) && !empty($_POST['login-btn--confirm'])) {
						echo '<p class="error">' . $errors['email'] . '</p>';
					} ?>
					<dl class="input-area">
						<dt><label for="name" class="text">パスワード</label></dt>
						<dd>
							<input class="input" type="password" name="password" placeholder="英数字混在8文字以上" value="<?php if (!empty($_POST['password'])) {
																														echo h($_POST['password']);
																													} ?>">
						</dd>

					</dl>
					<?php if (!empty($errors['password']) && !empty($_POST['login-btn--confirm'])) {
						echo '<p class="error">' . $errors['password'] . '</p>';
					} ?>
					<?php if (!empty($errors['token']) && !empty($_POST['login-btn--confirm'])) {
						echo '<p class="error">' . $errors['token'] . '</p>';
					} ?>

					<div class="btn-parts"><input type="submit" name="login-btn--confirm" class="btn" value="ログイン"></div>
					<?php if (!empty($_POST['category'])) : ?>
						<input type="hidden" name="category" value="<?php echo h($_POST['category']); ?>">
					<?php endif; ?>
					<input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
				</form>

				<?php if (!empty($messages)) : ?>
					<p>
						<?php
						foreach ($messages as $message) {
							echo '<p class="error">' . $message . '</p>';
						}
						?>
					</p>
				<?php endif; ?>
			</div><!-- .member-box -->

			<div class="other-btn">
				<a class="btn back" href="main.php">トップページに戻る</a>
				<a class="btn" href="pre_registration.php">会員登録はこちらから</a>
			</div>

		<?php else : ?>
			<p>ログイン中です。ログアウトしますか？</p>
			<div id="logout">
				<form action="" method="POST">
					<input type="submit" name="logout" value="ログアウト" class="btn">
				</form>
			</div>
		<?php endif; ?>
	</div><!-- .wrapper -->

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