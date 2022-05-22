<?php

/**
 * 仮登録時バリデーション
 * @param [ポスト変数] $request $_POST
 * @param array $errors エラー格納
 * @return array $errors
 */
function validationPreUser($request)
{
	$errors = [];
	// メールアドレス欄
	if (empty($request['new_email']) || !filter_var($request['new_email'], FILTER_VALIDATE_EMAIL)) {
		$errors['new_email'] = 'メールアドレスを正しい形式で入力してください。';
	}
	return $errors;
}

/**
 * 仮登録時データベース保存
 * @param [ポスト変数] $request $_POST
 * @param array $params postで受け取ったデータを配列に格納
 * @param array $messages それぞれのコメント格納
 * @return array $messages
 */
function insertPreUser($request)
{
	require_once 'db_connection.php';
	$messages = [];

	$urltoken = hash('sha256', uniqid(rand(), 1));
	$url = "http://localhost/board/registration.php?urltoken=" . $urltoken;


	$params = [
		'id' => null,
		'email' => $request['new_email'],
		'urltoken' => $urltoken,
		'date' => null,
		'flag' => '0',
	];

	$sql = "SELECT * FROM users WHERE email=:email";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':email', $params['email']);
	$stmt->execute();
	$member = $stmt->fetch();
	if (!empty($member)) {
		$messages['email'] = 'すでに登録されているメールアドレスです。';
		exit;
	} else {
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
			$sql = 'insert into pre_users (' . $columns . ')values(' . $values . ')';

			$stmt = $pdo->prepare($sql);
			$stmt->execute($params);
			$pdo->commit();
		} catch (PDOException $e) {
			$pdo->rollBack();
			echo $e->getMessage();
			exit;
		}
	}

	/* メール送信処理 */

	$mailTo = $params['email'];

	//Return-Pathに指定するメールアドレス
	$returnEmail = 'test@test.com';
	$name = 'F FRee Board';
	$subject = '【F Free Board】本登録URLのお知らせ';

	$body = <<< EOM
       このたびは仮ご登録いただきありがとうございます。
       24時間以内に下記のURLからご登録下さい。
       {$url}
EOM;

	mb_language('ja');
	mb_internal_encoding('UTF-8');

	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name) . ' <' . $returnEmail . '>';

	if (mb_send_mail($mailTo, $subject, $body, $header, '-f' . $returnEmail)) {

		//セッション変数を全て解除
		$_SESSION = [];

		//クッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}
		//セッションを破棄する
		session_destroy();
		$_SESSION['info'] =  'ご入力いただいたメールアドレスに確認メールを送付させていただきました。24時間以内に確認メールに記載されているURLにアクセスくださいますようお願いいたします。';
	} else {
		$messages['email-error'] = 'メールの送信に失敗しました。';
	}

	return $messages;
}
