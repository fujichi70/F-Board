<?php

// xss対策
function h($str)
{
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function i($str)
{
	return preg_replace('/[[:cntrl:]]/u', '', $str);
}

/**
 * トークン発行
 * @param integer $_SESSION['token'] ランダムな32桁の数字を格納
 */
function setToken() {
	if (!isset($_SESSION['token'])) {
		$token = bin2hex(random_bytes(32));
		$_SESSION['token'] = $token;
	}
}

/**
 * トークン確認
 * 誤りだった場合飛んできたページに戻す、またはトップに戻す
 * 二重送信防止のため、確認後トークンを空にする
 */
function checkToken()
{
	if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['csrf'])) {
		if (!empty($_POST['category'])) {
			header('Location: ' . $_POST['category'] . '.php');
		} else {
			header('Location: main.php');
		}
		$_POST = '';
	} else {
		unset($_SESSION['token']);
	}
}


/**
 * ページのURL取得
 * @param string $category 元いたページのurl取得
 * @param string $_SESSION['category'] $category格納 
 */
function categoryUrlGet () {
	unset($_SESSION['category']);
	// パス取得
	$category = $_SERVER['REQUEST_URI'];
	// 先頭の'/board/'を削除
	$category = str_replace('/board/', '', $category);

	$_SESSION['category'] = $category;
}
