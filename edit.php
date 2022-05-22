<?php

require 'functions.php';
require 'functions_img.php';
require 'update_registration.php';
require 'functions_logout.php';

session_start();
header('X-FRAME-OPTIONS:DENY');

setToken();

$page_flag = 0;
// 編集画面：$page_flag = 0;
// 編集確認画面：$page_flag = 1;
// 編集完了画面：$page_flag = 2;

$errors = [];

if (!empty($_POST['edit-confirm--btn'])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		checkToken();
		setToken();
		$errors = validationEditUser($_POST);
		imgCheck($errors, $result);
		if (empty($errors)) {
			$page_flag = 1;
		}
	} else {
		exit;
	}
}

if (!empty($_POST["edit-btn"])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		checkToken();
		if (empty($errors)) {
			updateUser($_POST);
			$page_flag = 2;
		}
	}
}
if (!empty($_POST["back"])) {
	$page_flag = 0;
}


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
	<title>F Free Board - 編集画面</title>
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

	<section id="edit">
		<div class="wrapper">

			<?php if (!empty($_SESSION['id'])) : ?>
				<?php if ($page_flag === 1) : ?>
					<?php if ($_POST['csrf'] === $_SESSION['token']) : ?>
						<h2 class="common-title">会員登録編集確認</h2>

						<div class="registration-confirm--box">
							<p class="registration-confirm--text">ニックネーム</p>
							<?php echo h($_POST['name']); ?>
						</div>

						<div class="registration-confirm--box">
							<p class="registration-confirm--text">メールアドレス</p>
							<?php echo h($_POST['email']); ?>
						</div>

						<div class="registration-confirm--box">
							<p class="registration-confirm--text">プロフィール画像</p>
							<?php if (!empty($result['img_path'])) : ?>
								<img class="registration-confirm--img" src="<?php echo h($result['img_path']); ?>" alt="プロフィール画像">
							<?php elseif (!empty($_SESSION['img_path'])) : ?>
								<img class="registration-confirm--img" src="<?php echo h($_SESSION['img_path']); ?>" alt="プロフィール画像">
							<?php else : ?>
								<p>なし</p>
							<?php endif; ?>
						</div>

						<form method="POST" action="">
							<div class="registration-confirm--btns">
								<input type="submit" name="back" class="btn back" value="戻る">
								<input type="submit" name="edit-btn" class="btn" value="登録する">
							</div>

							<input type="hidden" name="email" value="<?php echo h($_POST['email']); ?>">
							<input type="hidden" name="name" value="<?php echo h($_POST['name']); ?>">
							<input type="hidden" name="password" value="<?php echo h($_POST['password']); ?>">
							<input type="hidden" name="password2" value="<?php echo h($_POST['password2']); ?>">
							<?php if (!empty($result)) : ?>
								<input type="hidden" name="img_name" value="<?php echo h($result['img_name']); ?>">
								<input type="hidden" name="img_path" value="<?php echo h($result['img_path']); ?>">
							<?php endif; ?>
							<input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']); ?>">
						</form>

					<?php endif; ?>
					<!-- csrf -->


				<?php elseif ($page_flag === 2) : ?>
					<?php if ($_POST['csrf'] === $_SESSION['token']) : ?>
						<h2 class="common-title">編集完了</h2>

						<div class="registration-complete--textbox">
							<p class="registration-complete--thanks">Complete!</p>
							<p class="registration-complete--text">編集が完了しました！</p>
						</div>
						<a class="btn" href="main.php">トップページに戻る</a>
						<?php
						unset($_SESSION);
						?>
					<?php endif; ?>

				<?php else : ?>
					<h2 class="common-title">編集画面</h2>
					<form action="" method="post" enctype="multipart/form-data">
						<dl class="input-area">
							<dt><label for="name" class="text">ニックネーム</label></dt>
							<dd>
								<input type="text" name="name" id="name" class="input" placeholder="20文字以内" value="<?php echo h($_SESSION['name']); ?>">
							</dd>
						</dl>
						<?php if (!empty($errors['name']) && !empty($_POST['edit-confirm--btn'])) {
							echo '<p class="error">' . $errors['name'] . '</p>';
						} ?>

						<dl class="input-area">
							<dt><label for="email" class="text">メールアドレス</label></dt>
							<dd>
								<input type="text" name="email" id="email" class="input" placeholder="aaa@text.com" value="<?php echo h($_SESSION['email']); ?>">
							</dd>
						</dl>
						<?php if (!empty($errors['email']) && !empty($_POST['edit-confirm--btn'])) {
							echo '<p class="error">' . $errors['email'] . '</p>';
						} ?>

						<dl class="input-area">
							<dt><label for="password" class="text">パスワード</label></dt>
							<dd>
								<input type="password" name="password" id="password" class="input" placeholder="英数字混在8文字以上">
							</dd>
						</dl>

						<dl class="input-area">
							<dt><label for="password2" class="text">確認用パスワード</label></dt>
							<dd>
								<input type="password" name="password2" id="password2" class="input" placeholder="上記と同じ英数字混在8文字以上">
							</dd>
						</dl>
						<?php if (!empty($errors['password']) && !empty($_POST['edit-confirm--btn'])) {
							echo '<p class="error">' . $errors['password'] . '</p>';
						} ?>

						<div class="image-box">
							<div class="image-text">プロフィール画像　※3MB以内の画像をアップロードください。</div>
							<input type="hidden" name="MAX_FILE_SIZE" value="3145728">

							<label class="image-select"><input type="file" name="img" class="js-upload-file" accept="image.*">ファイルを選択</label>
							<br>
							<?php if (!empty($_SESSION['img_path'])) : ?>
								<img class="image-pre" src="<?php echo h($_SESSION['img_path']); ?>" alt="プロフィール画像">
								<div class="js-upload-filename"></div>
							<?php elseif (!empty($_POST['img_path'])) : ?>
								<img class="image-pre" src="<?php echo h($_POST['img_path']); ?>" alt="プロフィール画像">
								<div class="js-upload-filename"></div>
								<!-- バリデーションエラー -->
							<?php elseif (!empty($result['img_path'])) : ?>
								<img class="image-pre" src="<?php echo h($result['img_path']); ?>" alt="プロフィール画像">
								<div class="js-upload-filename"></div>
							<?php else : ?>
								<div class="js-upload-filename"></div>
							<?php endif; ?>
						</div>

						<input type="submit" name="edit-confirm--btn" class="btn" value="編集確認画面へ">
						<input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
					</form>
				<?php endif; ?>
				<!-- $page_flag -->

			<?php else : ?>
				<p>ログインしていません。ログインしますか？</p>
				<div class="">
					<a class="btn" href="login.php">ログイン</a>
					<a class="btn" href="pre_registration.php">会員登録</a>
				</div>
			<?php endif; ?>

		</div><!-- .wrapper -->
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

			// ログインorログアウト時にアラート表示
			if (!$('.alert').hasClass('on')) {
				$('.alert').addClass('on');
				setTimeout(function() {
					$('.alert').removeClass('on')
				}, 3000);

			}

			// サムネイル表示
			$('.js-upload-filename').after('<span class="image-thumb"></span>');

			// アップロードするファイルを選択
			$('.js-upload-file').change(function() {
				$('.image-pre').remove();
				let file = $(this).prop('files')[0];

				// 画像以外は処理を停止
				if (!file.type.match('image.*')) {
					// クリア
					$(this).val('');
					$('span').html('');
					return;
				}

				// 画像表示
				let reader = new FileReader();
				reader.onload = function() {
					let img_src = $('<img>').attr('src', reader.result);
					$('.image-thumb').html(img_src);
				}
				reader.readAsDataURL(file); //画像を表示
				$('.js-upload-filename').text(file.name); //ファイル名を出力
				$('.js-upload-fileclear').show(); //クリアボタンを表示
			});

		});
	</script>

</body>

</html>