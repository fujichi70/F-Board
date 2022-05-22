<?php

require 'functions.php';
require 'db_connection.php';
require 'functions_post.php';
require 'functions_logout.php';

$pdo = dbConnection();
session_start();

header('X-FRAME-OPTIONS:DENY');

setToken();
$errors = [];
$message = '';

categoryUrlGet();

if (!empty($_POST['submit'])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		checkToken();
		setToken();
		$errors = validationPost($_POST);
		if (empty($errors)) {
			$message = insertPost($_POST);
			if (!empty($message)) {
				$_POST = '';
			}
		}
	} else {
		exit;
	}
}

$sql = 'SELECT work_boards.id, work_boards.user_id, work_boards.text, work_boards.created_at, work_boards.deleted_flag, users.name, users.img_path FROM users INNER JOIN work_boards ON users.id = work_boards.user_id WHERE deleted_flag=0 ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute();

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
	<title>F Free Board - 仕事相談</title>
	<link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>

</head>

<body>
	<!-- ページ共通（ログイン時orログアウト時表示） -->
	<?php include "./include/state.php"; ?>

	<!-- ページ共通（ナビバー） -->
	<?php include "./include/header.php"; ?>

	<div class="category-box">
		<div class="work-text"><h1 class="common-text">仕事や仕事仲間との関係にお悩みを持つ皆さんで相談しあいましょう！</h1></div>
		<div class="category-img--parts"><img class="category-img" src="img/work.jpg" alt="仕事関係画像"></div>
	</div>

	<section id="category">
		<h2 class="common-title">投稿</h2>

		<!-- 投稿一覧表示 -->
		<?php while ($row = $stmt->fetch()) : ?>
			<div class="article-box">
				<div class="article-group">
					<div class="article-user">
						<?php if (!empty($row['img_path'])) : ?>
							<img class="article-user--img" src="<?php echo h($row['img_path']); ?>" alt="">
						<?php else : ?>
							<img class="article-user--img" src="./img/noimage.png" alt="">
						<?php endif; ?>
						<p class="article-user--name"><?php echo h($row['name']); ?></p>
					</div>
					<div class="article-text">
						<div class="article-text--box">
							<p class="article-text--parts"><?php echo nl2br($row['text']); ?></p>
						</div>
					</div>
				</div><!-- .article-group -->
				<div class="article-parts">
					<?php if (!empty($_SESSION['id'])) :
						if ($_SESSION['id'] === $row['user_id']) : ?>
							<form action="deleted.php" method="post">
								<input type="submit" class="deleted-btn" name="deleted_btn" value="削除する">
								<input type="hidden" name="id" value="<?php echo $row["id"] ?>">
								<input type="hidden" name="user_id" value="<?php echo $row["user_id"] ?>">
								<input type="hidden" name="name" value="<?php echo $row["name"] ?>">
								<input type="hidden" name="text" value="<?php echo $row["text"] ?>">
								<input type="hidden" name="category" value="work">
								<input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
							</form>
						<?php endif; ?>
					<?php endif; ?>
					<p class="article-date">投稿日時： <span class="article-date--parts"><?php echo $row['created_at']; ?></span></p>
				</div><!-- .article-parts -->
			</div><!-- .article-box -->
		<?php endwhile; ?>

		<!-- 投稿（ログイン時のみ表示） -->
		<?php if (isset($_SESSION['id'])) : ?>
			<div class="post-box">
				<form action="" method="post">
					<div class="post-group">
						<textarea class="post-input--area" cols="50" maxlength="600" name="text" placeholder="ここに入力"><?php if (!empty($_POST['text'])) {
																															echo h($_POST['text']);
																														} ?></textarea>
						<input type="hidden" name="category" value="work">
						<input type="submit" class="btn post-input--btn" name="submit" value="投稿">
						<input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
					</div><!-- .post-group -->
				</form>
				<?php if (!empty($message)) : ?>
					<div class="alert"><img src="./img/post-complete.png" alt="投稿完了"></div>
					<?php $message = ""; ?>
				<?php endif; ?>
				<?php if (!empty($_POST["submit"]) && !empty($errors)) {
					echo '<p class="error">' . $errors['text'] . '</p>';
				}
				?>
			</div><!-- .post-box -->
		<?php endif; ?>

		<!-- ページ共通 -->
		<?php include "./include/footer.php"; ?>

	</section>

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

			// 投稿エリア高さ調整
			$('.post-input--area')
				.focus()
				.on('input', function() {
					while ($(this).outerHeight() < this.scrollHeight) {
						$(this).height($(this).height() + 1)
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