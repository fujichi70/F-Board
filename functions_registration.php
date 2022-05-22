<?php

/**
 * 本登録前の仮登録確認
 * @param string $urltoken 仮登録時に発行したトークンをGET変数で受け取る
 * @param array $errors それぞれのエラー格納
 * @param array $_SESSION['email] 仮登録されていて本登録されていないメールアドレスを格納
 * @return array $errors
 */
function insertPreUserCheck()
{
    require 'db_connection.php';

    $pdo = dbConnection();
    $errors = [];

    $urltoken = isset($_GET[urltoken]) ? $_GET[urltoken] : NULL;

    //メール入力判定
    if ($urltoken == '') {
        $errors['urltoken'] = "恐れ入りますが再度仮登録からやりなおしてください。";
    } else {
        try {
            $pdo->beginTransaction();
            //flagが0の未登録者・仮登録日から24時間以内
            $stmt = $pdo->prepare("SELECT mail FROM pre_users WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 24 hour");
            $stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
            $stmt->execute();
            $pdo->commit();
            //レコード件数取得
            $row_count = $stmt->rowCount();

            //24時間以内に仮登録され、本登録されていないトークンの場合
            if ($row_count == 1) {
                $email_array = $stmt->fetch();
                $email = $email_array[email];
                $_SESSION['email'] = $email;
            } else {
                $errors['time-over'] = "このURLは有効期限が過ぎた等の問題があります。恐れ入りますが再度仮登録からやりなおしてください。";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo $e->getMessage();
            exit;
        }
    }
    return $errors;
}

/**
 * 本登録時バリデーション
 * @param [ポスト変数] $request $_POST
 * @param array $errors それぞれのエラー格納
 * @return array $errors
 */
function validationRegister($request)
{
    require 'db_connection.php';

    $errors = [];
    // ニックネーム名欄
    if (empty($request['name']) || mb_strlen($request['name']) >= 20) {
        $errors['name'] = 'ニックネームを20文字以内で入力してください。';
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
 * 本登録時データベース保存
 * @param [ポスト変数] $request $_POST
 * @param array $params postで受け取ったデータを配列に格納
 * @param array $count $paramsの個数格納
 * @param array $columns $paramsのキーに','付与しデータベースに格納
 * @param array $values $paramsのキーに':'付与しデータベースに格納
 * @param array $messages 処理終了したら投稿完了のメッセージを格納
 * @return array $messages
 */
function insertUser($request)
{
    require 'db_connection.php';
    $pdo = dbConnection();

    $params = [
        'id' => null,
        'name' => $request['name'],
        'email' => $request['email'],
        'password' => password_hash($request['password'], PASSWORD_DEFAULT),
        'img_name' => !empty($request['img_name']) ? $request['img_name'] : null,
        'img_path' => !empty($request['img_path']) ? $request['img_path'] : null,
        'created_at' => null,
        'updated_at' => null,
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
        $sql = 'insert into users (' . $columns . ')values(' . $values . ')';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $res = $pdo->commit();
        if ($res) {
            $sql = "SELECT * FROM users WHERE email=:email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $params['email']);
            $stmt->execute();
            $member = $stmt->fetch();
            if (password_verify($request['password'], $member['password'])) {
                session_regenerate_id(true);
                $_SESSION['id'] = $member['id'];
                $_SESSION['name'] = $member['name'];
                $_SESSION['email'] = $member['email'];
                if (!empty($member['img_path'])) {
                    $_SESSION['img_path'] = $member['img_path'];
                }
            }
        }
    } catch (PDOException $e) {
        $pdo->rollback();
        echo $e->getMessage();
        exit;
    }
}
