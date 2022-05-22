<?php

/**
 * ログイン時バリデーション
 * @param [ポスト変数] $request $_POST
 * @param array $errors それぞれのエラー格納
 * @return array $errors
 */
function validationLogin($request)
{
	$errors = [];
	if (empty($request['email']) || !filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = 'メールアドレスを正しい形式で入力してください。';
	}
	if (empty($request['password']) || !preg_match('/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,}$/i', $request['password'])) {

		$errors['password'] = 'パスワードは半角英数字が混在している8文字以上を入力してください。';
	}
	return $errors;
}

/**
 * ログイン処理
 * @param [ポスト変数] $request $_POST
 * @param array $params postで受け取ったデータを配列に格納
 * @param array $messages それぞれのコメント格納
 * @return array $messages
 */
function login($request)
{
	require 'db_connection.php';
	$pdo = dbConnection();
	$messages = [];

	$params = [
		'email' => $request['email'],
		'password' => $request['password'],
	];

	$sql = "SELECT * FROM users WHERE email=:email";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':email', $params['email']);
	$stmt->execute();
	$row = $stmt->fetch();
	if (empty($row)) {
		$messages['email'] = '登録されていないメールアドレスです。';
	} elseif (!password_verify($params['password'], $row['password'])) {
		$messages['password'] = 'パスワードが間違っています。';
	}
	if (empty($messages)) {
		session_regenerate_id(true);
		$_SESSION['id'] = $row['id'];
		$_SESSION['name'] = $row['name'];
		$_SESSION['email'] = $row['email'];
		if (!empty($row['img_path'])) {
			$_SESSION['img_path'] = $row['img_path'];
		}
		$_SESSION['info'] = 'ログイン';
		if (!empty($_SESSION['category'])) {
			$url = $_SESSION['category'];
			header("Location: $url");
		} else {
			header("Location: main.php");
		}
	}
	return $messages;
}
