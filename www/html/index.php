<?php
session_start();

//セッション確認
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
} elseif (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

//csrf対策としてトークン生成
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;
var_dump($token);
var_dump($_SESSION['csrf_token']);

try {
    $user = "root";
    $password = "secret";

    //PDOオブジェクト生成
    $pdo = new PDO("mysql:host=db; dbname=keiziban; charset=utf8", $user, $password);

//    PDOエラーの時に例外を投げるように設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM todo";
    $query = $pdo->query($sql);

    if (!empty($query)) {
        $list = $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $list = null;
    }

    $pdo = null;

} catch (PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>TODOリスト</title>
    <meta name="description" content="TODOリストだよ">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./scss/style.css">
</head>
<body>
<header>TODOリスト</header>
<div class="todo">
    <!--    フラッシュメッセージ-->
    <?php if (isset($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="todo__flash-error"><?php echo $error ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="todo__flash-success"><?php echo $success ?></div>
    <?php endif; ?>

    <h1 class="todo__top-title">これからやることは・・・</h1>
    <?php if (empty($list)): ?>
        <p class="todo__no-card-text">とくにない・・・</p>
    <?php else: ?>
        <?php foreach ($list as $item): ?>
            <div class="todo__card">
                <p class="todo__card-text"><?php echo $item['text'] ?></p>
                <div class="todo__card-footer">
                    <p class="todo__card-name"><?php echo $item['name'] ?></p>
                    <form class="todo__card-delete-form" method="post" action="delete.php">
                        <input type="hidden" name="id" value="<?php echo $item['id'] ?>">
                        <input type="hidden" name="token" value="<?php echo $token ?>">
                        <input class="todo__delete-button" type="submit" value="削除">
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <h2 class="todo__form-title">やることあったっけ・・・?</h2>
    <form class="todo__add-form" method="post" action="add.php">
        <div>
            <label for="text">やること</label>
            <input type="text" id="text" name="text">
        </div>
        <div>
            <label for="name">なまえ</label>
            <input type="text" id="name" name="name">
        </div>
        <input type="hidden" name="token" value="<?php echo $token ?>">
        <input class="todo__add-button" type="submit" value="追加">
    </form>
</div>
</body>
</html>