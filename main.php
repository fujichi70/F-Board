<?php

require 'functions.php';
require 'functions_logout.php';

session_start();
header('X-FRAME-OPTIONS:DENY');

setToken();

// ログアウト
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
	<title>F Free Board</title>
	<link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
</head>

<body>

	<!-- ページ共通（ログイン時orログアウト時表示） -->
	<?php include "./include/state.php"; ?>

	<!-- ページ共通（ナビバー） -->
	<?php include "./include/header.php"; ?>

	<main id="main">
		<div class="main-img-parts">
			<img class="main-img" src="img/main_visual.jpg" alt="コーヒーの写真">
		</div>
		<section id="concept">
			<div class="wrapper">
				<h2 class="concept-title">コンセプト</h2>
				<p class="concept-text">日常生活を送るうえで必ず選択はつきもの。<br>悩んでいるとき、誰かに相談したい！と思ってしまうときありますよね。<br>そう思ったとき、このサイトで気軽にささっと相談してみませんか？</p>
			</div>
		</section>
	</main>

	<section id="categories">
		<div class="wrapper">
			<p class="common-text">閲覧はだれでも可能！<br>カテゴリーからお好きな話題等を選択してください。<br>
				また、会員登録いただけましたら投稿も可能になります！<br>ログイン中にカテゴリー選択後ページ下部に投稿画面が表示されます。</p>

			<h1 class="common-title">categories</h1>
			<div class="grid">
				<a class="categories-group" href="recipe.php">
					<div class="categories-box">
						<img class="categories-img" src="img/recipe.jpg" alt="">
						<div class="categories-text">レシピ相談</div>
					</div>
				</a>
				<a class="categories-group" href="love.php">
					<div class="categories-box">
						<img class="categories-img" src="img/love.jpg" alt="">
						<div class="categories-text">恋愛相談</div>
					</div>
				</a>
				<a class="categories-group" href="couple.php">
					<div class="categories-box">
						<img class="categories-img" src="img/couple.jpg" alt="">
						<div class="categories-text">夫婦生活相談</div>
					</div>
				</a>
				<a class="categories-group" href="childcare.php">
					<div class="categories-box">
						<img class="categories-img" src="img/childcare.jpg" alt="">
						<div class="categories-text">育児相談</div>
					</div>
				</a>
				<a class="categories-group" href="work.php">
					<div class="categories-box">
						<img class="categories-img" src="img/work.jpg" alt="">
						<div class="categories-text">仕事相談</div>
					</div>
				</a>
				<a class="categories-group" href="friends.php">
					<div class="categories-box">
						<img class="categories-img" src="img/friends.jpg" alt="">
						<div class="categories-text">友人関係相談</div>
					</div>
				</a>
			</div><!-- grid -->
		</div><!-- .wrapper -->

	</section>
	<!-- ページ共通 -->
	<?php include "./include/footer.php"; ?>

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

			// ログインorログアウト時にアラート表示
			if (!$('.alert').hasClass('on')) {
				$('.alert').addClass('on');
				setTimeout(function() {
					$('.alert').removeClass('on')
				}, 3000);

			}
		});
	</script>

</body>

</html>