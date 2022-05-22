<?php

/**
 * 本登録時プロフィール画像のチェック
 * @param array $file $_FILESで受け取ったデータを格納
 * @param string $file_name $_FILESで受け取ったデータ名を格納
 * @param string $tmp_path $_FILESで受け取ったパス名を格納
 * @param string $file_error $_FILESで受け取ったエラーを格納
 * @param string $file_size $_FILESで受け取った画像のサイズを格納
 * @param string $upload_dir イメージ画像を保管するフォルダのパスを格納
 * @param string $img_name イメージの画像名がかぶらないよう画像名の前に0-99のランダムな数字を付与
 * @param string $upload_path 保管フォルダとイメージ画像名
 * @return array $errors エラー格納（参照渡し）
 * @return array $result $img_nameと$upload_pathを格納
 */
function imgCheck(&$errors, &$result)
{
	if (!empty($_FILES)) {

		$file = $_FILES['img'];
		$file_name = basename($file['name']);
		$tmp_path = $file['tmp_name'];
		$file_error = $file['error'];
		$file_size = $file['size'];
		$upload_dir = 'upload_img/';
		$img_name = mt_rand(0, 99) . $file_name;
		$upload_path = $upload_dir . $img_name;
		$img_path = 'storage_img/' . $img_name;

		// バリデーション
		if (is_uploaded_file($tmp_path)) {
			// ファイルサイズチェック
			if ($file_size > 3145728 || $file_error == 2) {
				$errors['file_size'] = 'ファイルのサイズは3MB以内でお願いします。';
			}

			// 拡張子チェック
			$file_type = ['jpg', 'jpeg', 'gif', 'png', 'image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
			$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

			if (!in_array(strtolower($file_ext), $file_type)) {
				$errors['file_ext'] = '対応していないデータです。jpg,png,gifのいずれかの拡張子データを添付してください。';
			}

			if (empty($errors)) {
				if (move_uploaded_file($tmp_path, $upload_path)) {
					$result = [
						'img_name' => $img_name,
						'upload_path' => $upload_path,
						'img_path' => $img_path,
					];
				} else {
					$errors['upload'] = 'アップロードに失敗しました。';
				}
			}
		}
	}
}