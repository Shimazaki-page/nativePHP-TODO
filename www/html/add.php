<?php
require_once 'function.php';

session_start();

//フォームのバリデーションチェック
if (isset($_POST)) {
    $errors = validate($_POST);

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location:./index.php');
        exit();
    }
} else {
    header('Content-Type: text/plain; charset=UTF-8', true, 400);
    exit('不正なリクエストです。');
}

//バリデーションメソッド
function validate($post)
{
    $errors = array();

    if (empty($post['text'])) {
        $errors[] = "やることを入力してね";
    }

    if (empty($post['name'])) {
        $errors[] = "なまえ入れてね";
    } elseif (mb_strlen($post['name']) > 20) {
        $errors[] = "なまえは20文字以下で入力してね";
    }

    return $errors;
}

//csrf対策としてトークン確認
$post_token = $_POST['token'];
$session_token = $_SESSION['csrf_token'];

if (empty($post_token) || $post_token !== $session_token) {
    unset($_SESSION['csrf_token']);
    header('Content-Type: text/plain; charset=UTF-8', true, 400);
    exit('不正なリクエストです。');
} else {
    unset($_SESSION['csrf_token']);
}

try {
    $pdo=getDatabase();

//    PDOエラーの時に例外を投げるように設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //XSS対策としてhtmlspecialcharsを使う
    $text = e($_POST['text']);
    $name = e($_POST['name']);

    //プリペアドステートメントを使いSQLインジェクション対策を行う
    $sql = "INSERT INTO todo (name, text, created_at) VALUES (:name, :text, now())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':text', $text, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $pdo = null;

    $_SESSION['success'] = "投稿できました！";
    header('Location:./index.php');
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
}
