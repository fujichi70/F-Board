<?php

/**
 * 会員登録編集確認
 * @param [ポスト変数] $request $_POST
 * @param array $errors それぞれのエラー格納
 * @return array $errors
 */
function validationEditUser($request)
{
	$errors = [];
	// ニックネーム名欄
	if (empty($request['name']) || mb_strlen($request['name']) >= 20) {
		$errors['name'] = 'ニックネームを20文字以内で入力してください。';
	}
	// メールアドレス欄
	if (empty($request['email']) || !filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = 'メールアドレスを正しい形式で入力してください。';
	}
	// パスワード欄
	if (empty($request['password']) || empty($request['password2'])) {
		$errors['password'] = 'パスワードを入力してください。';
		if ($request['password'] !== $request['password2']) {
			$errors['password'] = '確認用パスワードと一致しておりません。';
		} elseif (!preg_match('/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,}$/i', $request['password'])) {
			$errors['password'] = '半角英数字が混在している8文字以上のパスワードをご入力ください。';
		}
	}
	return $errors;
}

/**
 * 会員登録編集時データベース保存
 * @param [ポスト変数] $request $_POST
 * @param array $params postで受け取ったデータを配列に格納
 * @param array $columns $paramsのキーに'=:'と','付与しデータベース更新
 * @param array $_SESSION['name'] ログイン中の名前情報更新のため格納
 * @param array $_SESSION['email'] ログイン中のメールアドレス情報更新のため格納
 * @param array $_SESSION['img_path'] ログイン中の画像情報更新のため格納
 */
function updateUser($request)
{

	require 'db_connection.php';
	$pdo = dbConnection();

	$id = $_SESSION['id'];

	$params = [];
	if (!empty($request['img_name'])) {
		$params = [
			'name' => $request['name'],
			'email' => $request['email'],
			'password' => password_hash($request['password'], PASSWORD_DEFAULT),
			'img_name' => $request['img_name'],
			'img_path' => $request['img_path'],
			'updated_at' => date('Y-m-d H:i:s'),
		];
	} else {

		$params = [
			'name' => $request['name'],
			'email' => $request['email'],
			'password' => password_hash($request['password'], PASSWORD_DEFAULT),
			'updated_at' => date('Y-m-d H:i:s'),
		];
	}

	$columns = '';

	foreach (array_keys($params) as $key) {
		$columns .= $key . '=:' . $key . ',';
	}
	$columns = substr($columns, 0, -1);

	$sql = 'UPDATE users SET ' . $columns . ' WHERE id=:id';

	try {
		$pdo->beginTransaction();
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':name', $params['name']);
		$stmt->bindValue(':email', $params['email']);
		$stmt->bindValue(':password', $params['password']);
		if (!empty($request['img_name'])) {
			$stmt->bindValue(':img_name', $params['img_name']);
			$stmt->bindValue(':img_path', $params['img_path']);
		}
		$stmt->bindValue(':updated_at', $params['updated_at']);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$pdo->commit();

		// 編集をログイン中の情報に反映
		$_SESSION['name'] = $params['name'];
		$_SESSION['email'] = $params['email'];
		if (!empty($params['img_path'])) {
			$_SESSION['img_path'] = $params['img_path'];
		}
	} catch (PDOException $e) {
		$pdo->rollback();
		echo $e->getMessage();
		exit;
	}


}
