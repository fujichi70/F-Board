<?php

require 'functions.php';
require 'db_connection.php';

session_start();

header('X-FRAME-OPTIONS:DENY');

$pdo = dbConnection();

$page_flag = 0;
// 削除確認画面： $page_flag = 0;
// 削除完了画面： $page_flag = 1;

if (!empty($_POST['delete'])) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		checkToken();
		setToken();
		$id = h($_POST['id']);

		$params = [
			'id' => $id,
			'deleted_flag' => '1',
		];

		$category_parts = $_SESSION['category'];
		$category_parts = str_replace('.php', '', $category_parts);

		try {
			$pdo->beginTransaction();
			$sql = "UPDATE " . $category_parts . "_boards SET deleted_flag=:deleted_flag WHERE id=:id";
			$stmt = $pdo->prepare($sql);
			$stmt->execute($params);
			$data = $pdo->commit();
			if ($data) {
				$page_flag = 1;
			}
		} catch (PDOException $e) {
			$pdo->rollBack();
			echo $e->getMessage();
			exit;
		}
	} else {
		exit;
	}
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>F Free Board - 削除画面</title>
	<link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
</head>

<body>
	<div class="wrapper">
		<?php if ($page_flag === 0) : ?>
			<?php if (($_SESSION['token'] === $_POST['csrf']) && $_SESSION['id'] == $_POST['user_id']) : ?>
				<h1 class="common-title">削除内容</h1>
				<div class="deleted-box">
					<dl class="deleted-items">
						<dt class="deleted-text">氏名：</dt>
						<dd class="deleted-contents"><?php echo h($_POST['name']); ?></dd>
					</dl>
					<dl class="deleted-items">
						<dt class="deleted-text">内容：</dt>
						<dd class="deleted-contents"><?php echo nl2br(h($_POST['text'])); ?></dd>
					</dl>
				</div>
				<p class="deleted-confirm">本当に削除してもよろしいですか？</p>
				<div class="deleted-btns">
					<a href="<?php echo h($category_parts); ?>.php" class="btn back">戻る</a>
					<form action="" method="post">
						<input type="submit" name="delete" class="btn" value="削除">
						<input type="hidden" name="id" value="<?php echo h($_POST['id']); ?>">
						<input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
					</form>
				</div>
			<?php else : ?>
				<p>エラーが発生しました。恐れ入りますが、最初からやり直しをお願いいたします。</p>
				<a href="<?php echo h($category_parts); ?>.php" class="btn back">トップページに戻る</a>
			<?php endif; ?>
		<?php elseif ($page_flag === 1) : ?>
			<h1 class="common-title">削除完了</h1>
			<p class="deleted-complete--text">削除しました。</p>
			<a href="<?php echo h($category_parts); ?>.php" class="btn back">戻る</a>
		<?php endif; ?>
	</div><!-- .wrapper -->

	<script>
		$(function() {
			$('.hamburger').click(function() {
				$(this).toggleClass('active');

				if ($(this).hasClass('active')) {
					$('.category--menu').addClass('active');
				} else {
					$('.category--menu').removeClass('active');
				}
			});
		});
	</script>
</body>

</html>