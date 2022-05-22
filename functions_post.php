<?php

/**
 * 投稿バリデーション
 * @param [ポスト変数] $request $_POST
 * @param array $errors それぞれのエラー格納
 * @return array $errors
 */
function validationPost($request)
{

	$errors = [];

	// テキスト欄
	if (empty($request['text'])) {
		$errors['text'] = '入力してください。';
	}

	if (mb_strlen($request['text']) >= 2000) {
		$errors['text'] = '2000文字以内で入力してください。';
	}

	return $errors;
}


/**
 * 投稿時データベース保存
 * @param array $params postで受け取ったデータを配列に格納
 * @param array $count $paramsの個数格納
 * @param array $columns $paramsのキーに','付与しデータベースに格納
 * @param array $values $paramsのキーに':'付与しデータベースに格納
 * @param array $message 処理終了したら投稿完了のメッセージを格納
 * @return string $message
 */
function insertPost($request)
{
	$pdo = dbConnection();

	$params = [
		'id' => null,
		'user_id' => $_SESSION['id'],
		'text' => h($request['text']),
		'created_at' => null,
		'deleted_flag' => 0,
	];

	$count = 0;
	$columns = '';
	$values = '';

	foreach (array_keys($params) as $key) {
		if ($count++ > 0) {
			$columns .= ',';
			$values .= ',';
		}
		$columns .= $key;
		$values .= ':' . $key;
	}

	try {
		$pdo->beginTransaction();
		$sql = 'insert into ' . $request['category'] . '_boards (' . $columns . ')values(' . $values . ')';

		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$res = $pdo->commit();
		if ($res) {
			$message = '投稿完了！';
			return $message;
		}
	} catch (PDOException $e) {
		$pdo->rollBack();
		echo $e->getMessage();
		exit;
	}
}
