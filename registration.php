        <?php

		session_start();
		require 'functions.php';
		require 'functions_registration.php';
		require 'functions_img.php';

		header('X-FRAME-OPTIONS: DENY');

		setToken();

		$page_flag = 0;
		// 登録画面：$page_flag = 0;
		// 登録確認画面：$page_flag = 1;
		// 登録完了画面：$page_flag = 2;

		$errors = [];

		// insertPreUserCheck();
		if (empty($errors) && !empty($_POST['confirm-btn'])) {
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$errors = checkToken();
				if (empty($errors)) {
					$errors = validationRegister($_POST);
					imgCheck($errors, $result);
					if (empty($errors)) {
						$page_flag = 1;
					}
				}
			}
		}

		if (!empty($_POST["register-btn"])) {
			if (!empty($_POST['img_path'])) {
				if (rename($_POST['upload_path'], $_POST['img_path'])) {
					insertUser($_POST);
					$filePath = glob("upload_img/*");
					foreach ($filePath as $value) {
						unlink($value);
					}
					$page_flag = 2;
				} else {
					$page_flag = 1;
				}
			} else {
				insertUser($_POST);
				$page_flag = 2;
			}
		}
		if (!empty($_POST["back"])) {
			$page_flag = 0;
		}



		?>

        <!DOCTYPE html>
        <html lang="ja">

        <head>
        	<meta charset="UTF-8">
        	<meta http-equiv="X-UA-Compatible" content="IE=edge">
        	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        	<title>F Free Board - 会員本登録画面</title>
        	<link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
        	<link rel="stylesheet" href="style.css">
        	<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>

        </head>

        <body>
        	<div class="wrapper">

        		<!-- ページ共通（ナビバー） -->
        		<?php include "./include/header.php"; ?>

        		<section id="registration">
        			<?php if ($page_flag === 1) : ?>
        				<?php if ($_POST['csrf'] === $_SESSION['token']) : ?>
        					<h2 class="common-title">会員本登録確認</h2>

        					<div class="registration-confirm--box">
        						<p class="registration-confirm--text">メールアドレス</p>
        						<?php echo h($_POST['email']); ?>
        					</div>

        					<div class="registration-confirm--box">
        						<p class="registration-confirm--text">ニックネーム</p>
        						<?php echo h($_POST['name']); ?>
        					</div>

        					<?php if (!empty($result)) : ?>
        						<div class="registration-confirm--box">
        							<p class="registration-confirm--text">プロフィール画像</p>
        							<img class="registration-confirm--img" src="<?php echo h($result['upload_path']); ?>" alt="プロフィール画像">
        						</div>
        					<?php endif; ?>

        					<form method="POST" action="">
        						<div class="registration-confirm--btns">
        							<input type="submit" name="back" class="btn back" value="戻る">
        							<input type="submit" name="register-btn" class="btn" value="登録する">
        						</div>

        						<input type="hidden" name="email" value="<?php echo h($_POST['email']); ?>">
        						<input type="hidden" name="name" value="<?php echo h($_POST['name']); ?>">
        						<input type="hidden" name="password" value="<?php echo h($_POST['password']); ?>">
        						<input type="hidden" name="password2" value="<?php echo h($_POST['password2']); ?>">
        						<?php if (!empty($result)) : ?>
        							<input type="hidden" name="img_name" value="<?php echo h($result['img_name']); ?>">
        							<input type="hidden" name="upload_path" value="<?php echo h($result['upload_path']); ?>">
        							<input type="hidden" name="img_path" value="<?php echo h($result['img_path']); ?>">
        						<?php endif; ?>
        						<input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']); ?>">
        					</form>

        				<?php endif; ?>


        			<?php elseif ($page_flag === 2) : ?>
        				<?php if ($_POST['csrf'] === $_SESSION['token']) : ?>
        					<h2 class="common-title">会員本登録完了</h2>

        					<div class="registration-complete--textbox">
        						<p class="registration-complete--thanks">Thank You!!</p>
        						<p class="registration-complete--text">登録が完了しました！ご登録いただきありがとうございます。<br>恐れ入りますがトップページで再度ログインをお願いいたします。</p>
        					</div>
        					<a class="btn" href="main.php">トップページに戻る</a>
        				<?php else : ?>
        					<p>エラーが発生しました。恐れ入りますが、最初から登録をお願いいたします。</p>
        				<?php endif; ?>

        			<?php else : ?>
        				<h2 class="common-title">会員本登録</h2>
        				<p class="register-text">このたびは仮登録いただきありがとうございます！<br>本登録が完了いたしましたら投稿可能となりますのでどうぞよろしくお願いいたします。</p>

        				<form method="post" action="" enctype="multipart/form-data">

        					<!-- <label for="email" class="text">仮登録中のメールアドレス</label>
		        			<div class="member-email"><?php echo h($email); ?></div>
		        			<input type="hidden" name="email" value="<?php h($email); ?>"> -->

        					<!-- 実装時消す -->
        					<dl class="input-area">
        						<dt><label for="email" class="text">メールアドレス</label></dt>
        						<dd>
        							<input type="text" name="email" id="email" class="input" placeholder="aaa@text.com" value="<?php if (!empty($_POST['email'])) {
																																	echo h($_POST['email']);
																																} ?>">
        						</dd>
        					</dl>

        					<dl class="input-area">
        						<dt><label for="name" class="text">ニックネーム</label></dt>
        						<dd>
        							<input type="text" name="name" id="name" class="input" placeholder="20文字以内" value="<?php if (!empty($_POST['name'])) {
																															echo h($_POST['name']);
																														} ?>">
        						</dd>
        					</dl>
        					<?php if (!empty($errors['name']) && !empty($_POST['confirm-btn'])) {
								echo '<p class="error">' . $errors['name'] . '</p>';
							} ?>

        					<dl class="input-area">
        						<dt><label for="password" class="text">パスワード</label></dt>
        						<dd>
        							<input type="password" name="password" id="password" class="input" placeholder="英数字混在8文字以上" value="<?php if (!empty($_POST['password'])) {
																																			echo h($_POST['password']);
																																		} ?>">
        						</dd>
        					</dl>

        					<dl class="input-area">
        						<dt><label for="password2" class="text">確認用パスワード</label></dt>
        						<dd>
        							<input type="password" name="password2" id="password2" class="input" placeholder="上記と同じ英数字混在8文字以上" value="<?php if (!empty($_POST['password2'])) {
																																					echo h($_POST['password2']);
																																				} ?>">
        						</dd>
        					</dl>
        					<?php if (!empty($errors['password']) && !empty($_POST['confirm-btn'])) {
								echo '<p class="error">' . $errors['password'] . '</p>';
							} ?>

        					<div class="image-box">
        						<div class="image-text">プロフィール画像　※3MB以内の画像をアップロードください。</div>
        						<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        						<label class="image-select"><input type="file" name="img" class="js-upload-file" accept="image.*">ファイルを選択</label>
        						<br>
        						<!-- 戻るボタン -->
        						<?php if (!empty($_POST['img_path'])) : ?>
        							<img class="image-pre" src="<?php echo h($_POST['img_path']); ?>" alt="プロフィール画像">
        							<div class="js-upload-filename">ファイルが未選択です</div>
        							<!-- バリデーションエラー -->
        						<?php elseif (!empty($result['img_path'])) : ?>
        							<img class="image-pre" src="<?php echo h($result['img_path']); ?>" alt="プロフィール画像">
        							<div class="js-upload-filename"></div>
        						<?php else : ?>
        							<div class="js-upload-filename">ファイルが未選択です</div>
        						<?php endif; ?>
        					</div>

        					<input type="submit" name="confirm-btn" class="btn" value="登録確認画面へ">
        					<input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
        				</form>

        			<?php endif; ?>
        		</section>
        	</div> <!-- .wrapper -->

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