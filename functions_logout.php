<?php

/**
 * ログアウト
 * @param string $_SESSION['info'] ログアウトメッセージ
 * @return void
 */
function logout()
{
	if (!empty($_SESSION['id'])) {

		$_SESSION = [];

		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}

		session_destroy();
		$_SESSION['info'] = 'ログアウト';
	}
}