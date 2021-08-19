<?php
session_start();

try {
    $user = "root";
    $password = "secret";
    $pdo = new PDO("mysql:host=db; dbname=keiziban; charset=utf8", $user, $password);

//    PDOエラーの時に例外を投げるように設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//XSS対策としてhtmlspecialcharsを使う
    $id = htmlspecialchars($_POST['id']);

//プリペアドステートメントを使いSQLインジェクション対策を行う
    $sql = "DELETE FROM todo WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pdo = null;

    $_SESSION['success'] = "削除できました！";
    header('Location:./index.php');
} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
}
